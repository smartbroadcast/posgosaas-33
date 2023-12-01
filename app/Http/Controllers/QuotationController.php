<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Konekt\PdfInvoice\InvoicePrinter;
use App\Models\Customer;
use App\Mail\QuotationInvoice;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItems;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\QuotationExport;

class QuotationController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('Manage Quotations'))
        {
            Quotation::where('created_by', '=', Auth::user()->getCreatedBy())->whereDate('date', '<', date('Y-m-d'))->update(['status' => '1']);

            $quotations = Quotation::where('created_by', Auth::user()->getCreatedBy())->orderBy('id', 'DESC')->get();

            return view('quotations.index', compact('quotations'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->can('Create Quotations'))
        {
            $user_id = Auth::user()->getCreatedBy();

            $customers = Customer::where('created_by', $user_id)->pluck('name', 'id');
            $customers->prepend(__('Walk-in Customers'), 0);
            $customers->prepend(__('Select Customer'), '');

            return view('quotations.create', compact('customers'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->can('Create Quotations'))
        {
            $user_id = Auth::user()->getCreatedBy();

            $validator = Validator::make(
                $request->all(), [
                                   'date' => 'required|after_or_equal:' . date('d-m-Y'),
                                   'reference_no' => 'required',
                                   'customer_id' => 'required',
                                   'customer_email' => 'required|email',
                               ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            if($request->has('product') && $request->has('quantity'))
            {
                $products   = $request->product;
                $quantities = $request->quantity;

                $q                 = new Quotation();
                $q->invoice_id     = $this->invoiceQuotationNumber();
                $q->date           = date('Y-m-d', strtotime($request->date));
                $q->reference_no   = $request->reference_no;
                $q->customer_id    = $request->customer_id;
                $q->customer_email = $request->customer_email;
                $q->quotation_note = $request->quotation_note;
                $q->created_by     = $user_id;
                $q->save();

                if(count($products) == count($quantities))
                {
                    for($i = 0; $i < count($products); $i++)
                    {
                        $product_id = $products[$i];
                        $quantity   = (int)$quantities[$i];

                        $product = Product::whereId($product_id)->where('created_by', $user_id)->first();

                        $tax   = ($product->taxes == null) ? 0 : (float)$product->taxes->percentage;
                        $price = $product->sale_price != 0 ? $product->sale_price : $product->purchase_price;

                        $tax_id = Product::tax_id($product_id);

                        $qi               = new QuotationItems();
                        $qi->quotation_id = $q->id;
                        $qi->product_id   = $product_id;
                        $qi->price        = $price;
                        $qi->quantity     = $quantity;
                        $qi->tax_id       = $tax_id;
                        $qi->tax          = $tax;
                        $qi->save();
                    }
                }

                $invoiceId        = Crypt::encrypt($q->invoice_id);
                $q->customer_name = $q->customer == null ? __('Walk-in Customer') : $q->customer->name;
                $q->url           = route('get.quotation.invoice', $invoiceId);

                try
                {         

                    $uArr = [
                        'app_name'  =>env('APP_NAME'),
                        'app_url'=> env('APP_URL'),
                        'quotation_date' => date('Y-m-d', strtotime($request->date)),
                        'quotation_reference_no' =>$request->reference_no,
                        'quotation_customers' =>$request->customer_id,
                        'customer_email' =>$request->customer_email,
                      ];
                     
                   
                  
                      $resp = Utility::sendEmailTemplate('new_quote', [$q->id => $q->customer_email], $uArr);

                    // Mail::to($q->customer_email)->send(new QuotationInvoice($q));
                }
                catch(\Exception $e)
                {
                    $smtp_error = "<br><span class='text-danger'>" . __('E-Mail has been not sent due to SMTP configuration') . '</span>';
                }

                return redirect()->route('quotations.index')->with('success', __('Quotation send successfully.') . ((isset($smtp_error)) ? $smtp_error : ''));
            }
            else
            {
                return redirect()->back()->with('error', __('Please add some Products!'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Quotation $quotation)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(Quotation $quotation)
    {
        if(Auth::user()->can('Edit Quotations'))
        {
            $user_id = Auth::user()->getCreatedBy();

            $customers = Customer::where('created_by', $user_id)->pluck('name', 'id');
            $customers->prepend(__('Walk-in Customers'), 0);
            $customers->prepend(__('Select Customer'), '');

            return view('quotations.edit', compact('quotation', 'customers'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, Quotation $quotation)
    {
        if(Auth::user()->can('Edit Quotations'))
        {
            $user_id = Auth::user()->getCreatedBy();

            $validator = Validator::make(
                $request->all(), [
                                //    'date' => 'required|after_or_equal:' . date('d/m/Y'),
                                   'date' => 'required',
                                   'reference_no' => 'required',
                                   'customer_id' => 'required',
                                   'customer_email' => 'required|email',
                               ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }


            if($request->has('product') && $request->has('quantity'))
            {
                $products   = $request->product;
                $quantities = $request->quantity;

                $quotation->date           = date('Y-m-d', strtotime($request->date));
                $quotation->reference_no   = $request->reference_no;
                $quotation->customer_id    = $request->customer_id;
                $quotation->customer_email = $request->customer_email;
                $quotation->quotation_note = $request->quotation_note;
                $quotation->save();

                if(count($products) == count($quantities))
                {
                    QuotationItems::where('quotation_id', $quotation->id)->delete();

                    for($i = 0; $i < count($products); $i++)
                    {
                        $product_id = $products[$i];
                        $quantity   = (int)$quantities[$i];

                        $product = Product::whereId($product_id)->where('created_by', $user_id)->first();

                        $tax   = ($product->taxes == null) ? 0 : (float)$product->taxes->percentage;
                        $price = $product->sale_price != 0 ? $product->sale_price : $product->purchase_price;

                        $tax_id = Product::tax_id($product_id);

                        $ri               = new QuotationItems();
                        $ri->quotation_id = $quotation->id;
                        $ri->product_id   = $product_id;
                        $ri->price        = $price;
                        $ri->quantity     = $quantity;
                        $ri->tax_id       = $tax_id;
                        $ri->tax          = $tax;
                        $ri->save();
                    }

                    $quotation_id             = Crypt::encrypt($quotation->id);
                    $quotation->customer_name = $quotation->customer != null ? $quotation->customer->name : __('Walk-in Customer');
                    $quotation->url           = route('quotation.invoice', $quotation_id);

                    // try
                    // {
                        Mail::to($quotation->customer_email)->send(new QuotationInvoice($quotation));
                    // }
                    // catch(\Exception $e)
                    // {
                    //     $smtp_error = "<br><span class='text-danger'>" . __('E-Mail has been not sent due to SMTP configuration') . '</span>';
                    // }

                    return redirect()->route('quotations.index')->with('success', __('Quotation updated successfully.') . ((isset($smtp_error)) ? $smtp_error : ''));
                }

            }
            else
            {
                return redirect()->back()->with('error', __('Please add some Products!'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function changeQuotationStatus(Request $request, $id)
    {
        if(Auth::user()->can('Manage Quotations'))
        {
            $response = false;
            $status   = $request->has('status') ? $request->status : 0;

            $quotation = Quotation::find($id);

            if($quotation)
            {
                $quotation->status = $status;
                $quotation->save();

                $response = true;
            }

            echo json_encode($response);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Quotation $quotation)
    {
        if(Auth::user()->can('Delete Quotations'))
        {
            $quotation->delete();

            return redirect()->route('quotations.index')->with('success', __('Quotations deleted successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function invoiceQuotationNumber()
    {
        if(Auth::user()->can('Manage Quotations'))
        {
            $latest = Quotation::where('created_by', '=', Auth::user()->getCreatedBy())->latest()->first();

            return $latest ? $latest->invoice_id + 1 : 1;
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function quotationItems(Request $request)
    {
        $quotation_id = $request->id;
        if(Auth::user()->can('Manage Quotations') && $request->ajax() && isset($quotation_id) && !empty($quotation_id))
        {
            $items = QuotationItems::select('quotation_items.*', 'products.name as productname', 'products.quantity as maxquantity')->join('products', 'products.id', '=', 'quotation_items.product_id')->where('products.created_by', '=', Auth::user()->getCreatedBy())->where('quotation_items.quotation_id', '=', $quotation_id)->get();

            foreach($items as $key => $item)
            {
                $subtotal = $item->price * $item->quantity;
                $tax      = ($subtotal * $item->tax) / 100;

                $items[$key]['subtotal'] = $subtotal + $tax;
            }

            return json_encode($items);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function quotationInvoice($quotation_id)
    {
        $quotation_id = Crypt::decrypt($quotation_id);

        $quotation = Quotation::find($quotation_id);

        if(!empty($quotation))
        {
            $user = User::select('*')->where('id', $quotation->created_by)->first();

            $settings = Utility::settings($user->id);

            $invoice_id    = $user->quotationInvoiceNumberFormat($quotation->invoice_id);
            $invoice_color = $user->quotationInvoiceColor();

            $settings['company_telephone'] = $settings['company_telephone'] != '' ? ", " . $settings['company_telephone'] : '';
            $settings['company_state']     = $settings['company_state'] != '' ? ", " . $settings['company_state'] : '';

            $userdetails = [
                ucfirst($user->name),
                $settings['company_name'] . $settings['company_telephone'],
                $settings['company_address'],
                $settings['company_city'] . $settings['company_state'],
                $settings['company_country'],
                $settings['company_zipcode'],
            ];

            $customer = $quotation->customer;

            if($customer != null)
            {
                $customer->state = $customer->state != '' ? ", " . $customer->state : '';

                $customerdetails = [
                    ucfirst($customer->name),
                    $customer->phone_number,
                    $customer->address,
                    $customer->city . $customer->state,
                    $customer->country,
                    $customer->zipcode,
                ];
            }
            else
            {
                $customerdetails = [
                    __('Walk-in Customer'),
                    $quotation->customer_email,
                    '',
                    '',
                    '',
                    '',
                ];
            }

            $quotation_note = $quotation->quotation_note;

            $items = QuotationItems::select('quotation_items.*', 'products.name as productname')->join('products', 'products.id', '=', 'quotation_items.product_id')->where('products.created_by', '=', $user->getCreatedBy())->where('quotation_items.quotation_id', '=', $quotation->id)->get();

            $invoice = new InvoicePrinter("A4", $user->currencySymbol(), $user->lang);

            $invoice->setLogo(asset(Storage::url('logo/logo-invoice.png')));
            $invoice->setColor($invoice_color);
            $invoice->setType($invoice_id);
            $invoice->setReference($quotation->reference_no);
            $invoice->setDate($user->dateFormat($quotation->created_at));
            $invoice->setTime($user->timeFormat($quotation->created_at));
            $invoice->setDue($user->dateFormat($quotation->date));

            $invoice->setFrom($userdetails);

            $invoice->setTo($customerdetails);

            $total = 0;

            foreach($items as $key => $item)
            {
                $subtotal = $item->price * $item->quantity;
                $tax      = ($subtotal * $item->tax) / 100;

                $total += $st = $subtotal + $tax;
                $invoice->addItem($item->productname, "", $item->quantity, $item->price, $item->tax, $tax, $st);
            }

            $invoice->addTotal("Total", $total, true);

            if($quotation->status == 1)
            {
                $invoice->addBadge(__('Close'));
            }
            else
            {
                $invoice->addBadge(__('Open'));
            }

            $invoice->addTitle("Important Notice");

            $quotation_note = (isset($quotation_note) && !empty($quotation_note)) ? $quotation_note : "No item will be replaced or refunded if you don't have the invoice with you.";

            $invoice->addParagraph($quotation_note);

            $invoice->setFooternote(URL::to('/'));

            $name = 'quotationpdf/quotation_' . md5(time()) . '.pdf';

            $invoice->render('I', $name);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function resendQuotation(Request $request)
    {
        $quotation_id = $request->quotation_id;

        $quotation = Quotation::find($quotation_id);

        if($quotation)
        {
            $customer_email = $request->has('customer_email') ? $request->customer_email : '';

            if($quotation->customer == null)
            {

                $quotation->customer_name = __('Walk-in Customer');
                $customer_email           = $quotation->customer_email;
            }
            else
            {

                $quotation->customer_name = $quotation->customer->name;
            }

            $invoiceId      = Crypt::encrypt($quotation->invoice_id);
            $quotation->url = route('quotation.invoice', $invoiceId);

            try
            {
                Mail::to($customer_email)->send(new QuotationInvoice($quotation));
            }
            catch(\Exception $e)
            {
                $smtp_error = "<br><span class='text-danger'>" . __('E-Mail has been not sent due to SMTP configuration') . '</span>';
            }

            return response()->json(
                [
                    'code' => 200,
                    'status' => 'Success',
                    'success' => __('Quotation resent successfully.') . ((isset($smtp_error)) ? $smtp_error : ''),
                ]
            );
        }

        return response()->json(
            [
                'code' => 404,
                'status' => 'Error',
                'error' => __('This Quotation is not found!'),
            ], 404
        );
    }

    public function printQuotationInvoice($id)
    {
        $quotation_id = Crypt::decrypt($id);
        $quotation    = Quotation::findOrFail($quotation_id);

        if($quotation)
        {
            $user = User::select('*')->where('id', $quotation->created_by)->first();

            $quotationitems = QuotationItems::select('quotation_items.*', 'products.name as productname')->join('products', 'products.id', '=', 'quotation_items.product_id')->where('products.created_by', '=', $user->getCreatedBy())->where('quotation_items.quotation_id', '=', $quotation->id)->get();

            $total = 0;

            foreach($quotationitems as $key => $item)
            {
                $subtotal = $item->price * $item->quantity;
                $tax      = ($subtotal * $item->tax) / 100;

                $total += $st = $subtotal + $tax;

                $item->name       = $item->productname;
                $item->quantity   = $item->quantity;
                $item->price      = $user->priceFormat($item->price);
                $item->tax        = $item->tax . '%';
                $item->tax_amount = $user->priceFormat($tax);
                $item->subtotal   = $user->priceFormat($st);
                $items[]          = $item;
            }

            $quotation->items    = $items;
            $quotation->subtotal = $user->priceFormat($total);

            $settings = Utility::settings($user->id);


            $settings['company_telephone'] = $settings['company_telephone'] != '' ? ", " . $settings['company_telephone'] : '';
            $settings['company_state']     = $settings['company_state'] != '' ? ", " . $settings['company_state'] : '';

            $userdetails = [
                ucfirst($user->name),
                $settings['company_name'] . $settings['company_telephone'],
                $settings['company_address'],
                $settings['company_city'] . $settings['company_state'],
                $settings['company_country'],
                $settings['company_zipcode'],
            ];

            $customer = $quotation->customer;

            if($customer != null)
            {
                $customer->state = $customer->state != '' ? ", " . $customer->state : '';

                $customerdetails = [
                    ucfirst($customer->name),
                    $customer->phone_number,
                    $customer->address,
                    $customer->city . $customer->state,
                    $customer->country,
                    $customer->zipcode,
                ];
            }
            else
            {
                $customerdetails = [
                    __('Walk-in Customer'),
                    $quotation->customer_email,
                    '',
                    '',
                    '',
                    '',
                ];
            }

            $color = '#' . $settings['quotation_invoice_color'];

            //Set your logo
            // $logo         = asset(\Storage::url('/'));
            // $company_logo = Utility::getValByName('company_logo_dark');
            $logo=\App\Models\Utility::get_file('/');
            $company_logo = Utility::get_company_logo();
            $img          = asset($logo . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));

            $font_color = Utility::getFontColor($color);

            return view('quotations.templates.' . $settings['quotation_invoice_template'], compact('quotation', 'color', 'font_color', 'settings', 'user', 'userdetails', 'customerdetails', 'img'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function previewQuotationInvoice($template, $color)
    {
        $settings = Utility::settings();

        $quotation = new Quotation();
        $user      = Auth::user();

        $customerdetails = [
            ucfirst('Client'),
            '+216 654654',
            'Hankenshire',
            'New York' . 'New York',
            'USA',
            '999999',
        ];

        $items = [];
        for($i = 1; $i <= 3; $i++)
        {
            $item             = new \stdClass();
            $item->name       = 'Item ' . $i;
            $item->quantity   = 2;
            $item->price      = '$100.00';
            $item->tax        = '0%';
            $item->tax_amount = '$0.0';
            $item->subtotal   = '$200.00';
            $items[]          = $item;
        }

        $quotation->invoice_id = 1;
        $quotation->items      = $items;
        $quotation->subtotal   = '$600.00';
        $quotation->created_at = date('Y-m-d H:i:s');

        //Set your logo
        $logo=\App\Models\Utility::get_file('/');
        $company_logo = Utility::get_company_logo();;
        $img          = asset($logo . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));

        $preview  = 1;
        $color   = '#' . $color;
        $font_color = Utility::getFontColor($color);

        return view('quotations.templates.' . $template, compact('quotation', 'preview', 'color', 'font_color', 'settings', 'user', 'customerdetails', 'img'));
    }
    public function export()
    {
        $name = 'Quotation_' . date('Y-m-d i:h:s');
        $data = Excel::download(new QuotationExport(), $name . '.xlsx'); ob_end_clean();
        return $data;
    }
}
