<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Konekt\PdfInvoice\InvoicePrinter;
use App\Models\Customer;
use App\Mail\SelledInvoice;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SelledItems;
use App\Models\User;
use App\Models\Utility;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SaleExport;

class SaleController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('Manage Sales')) {
            return view('sales.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create(Request $request)
    {
        $sess = session()->get('sales');

        if (Auth::user()->can('Manage Sales') && isset($sess) && !empty($sess) && count($sess) > 0) {
            $user = Auth::user();

            $settings = Utility::settings();

            $customer = Customer::where('name', '=', $request->vc_name)->where('created_by', $user->getCreatedBy())->first();

            $details = [
                'invoice_id' => $user->sellInvoiceNumberFormat($this->invoiceSellNumber()),
                'customer' => $customer != null ? $customer->toArray() : [],
                'user' => $user != null ? $user->toArray() : [],
                'date' => date('Y-m-d'),
                'pay' => 'show',
            ];

            if (!empty($details['customer'])) {
                $details['customer']['state'] = $details['customer']['state'] != '' ? ", " . $details['customer']['state'] : '';

                $customerdetails = '<h2 class="h6 font-weight-normal"><b>' . ucfirst($details['customer']['name']) . '</b>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['phone_number'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['address'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['city'] . $details['customer']['state'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['country'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $details['customer']['zipcode'] . '</p></h2>';
            } else {
                $customerdetails = '<h2 class="h6"><b>' . __('Walk-in Customer') . '</b><h2>';
            }

            $settings['company_telephone'] = $settings['company_telephone'] != '' ? ", " . $settings['company_telephone'] : '';
            $settings['company_state']     = $settings['company_state'] != '' ? ", " . $settings['company_state'] : '';

            $userdetails = '<h2 class="h6"><b>' . ucfirst($details['user']['name']) . ' </b> <h2  class="h6 font-weight-normal">' . '<p class="m-0 h6 font-weight-normal">' . $settings['company_name'] . $settings['company_telephone'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $settings['company_address'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $settings['company_city'] . $settings['company_state'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $settings['company_country'] . '</p>' . '<p class="m-0 h6 font-weight-normal">' . $settings['company_zipcode'] . '</p></h2>';

            $details['customer']['details'] = $customerdetails;

            $details['user']['details'] = $userdetails;

            $mainsubtotal = 0;
            $sales        = [];

            foreach ($sess as $key => $value) {
                $subtotal = $value['price'] * $value['quantity'];
                $tax      = ($subtotal * $value['tax']) / 100;

                $sales['data'][$key]['name']       = $value['name'];
                $sales['data'][$key]['quantity']   = $value['quantity'];
                $sales['data'][$key]['price']      = Auth::user()->priceFormat($value['price']);
                $sales['data'][$key]['tax']        = $value['tax'] . '%';
                $sales['data'][$key]['tax_amount'] = Auth::user()->priceFormat($tax);
                $sales['data'][$key]['subtotal']   = Auth::user()->priceFormat($value['subtotal']);
                $mainsubtotal                      += $value['subtotal'];
            }
            $sales['total'] = Auth::user()->priceFormat($mainsubtotal);


            return view('sales.show', compact('sales', 'details'));
        } else {
            return response()->json(
                [
                    'error' => __('Add some products to cart!'),
                ],
                '404'
            );
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('Manage Sales')) {
            $user_id = Auth::user()->getCreatedBy();

            $customer_id      = Customer::customer_id($request->vc_name);
            $branch_id        = $request->branch_id != '' ? $request->branch_id : 0;
            $cash_register_id = $request->cash_register_id != '' ? $request->cash_register_id : 0;
            $invoice_id       = $this->invoiceSellNumber();
            $sales            = session()->get('sales');

            if (isset($sales) && !empty($sales) && count($sales) > 0) {
                $result = DB::table('sales')->where('invoice_id', $invoice_id)->where('created_by', $user_id)->get();
                if (count($result) > 0) {
                    return response()->json(
                        [
                            'code' => 200,
                            'success' => __('Payment is already completed!'),
                        ]
                    );
                } else {
                    $sale = new Sale();

                    $sale->invoice_id       = $invoice_id;
                    $sale->customer_id      = $customer_id;
                    $sale->branch_id        = $branch_id;
                    $sale->cash_register_id = $cash_register_id;
                    $sale->created_by       = $user_id;

                    $sale->save();

                    foreach ($sales as $key => $value) {
                        $product_id = $value['id'];

                        $product = Product::whereId($product_id)->where('created_by', $user_id)->first();

                        $original_quantity = ($product == null) ? 0 : (int)$product->quantity;

                        $product_quantity = $original_quantity - $value['quantity'];

                        if ($product != null && !empty($product)) {
                            Product::where('id', $product_id)->update(['quantity' => $product_quantity]);
                        }

                        $tax_id = Product::tax_id($product_id);

                        $selleditems = new SelledItems();

                        $selleditems->sell_id    = $sale->id;
                        $selleditems->product_id = $product_id;
                        $selleditems->price      = $value['price'];
                        $selleditems->quantity   = $value['quantity'];
                        $selleditems->tax_id     = $tax_id;
                        $selleditems->tax        = $value['tax'];

                        $selleditems->save();
                    }

                    session()->forget('sales');

                    if ($sale->customer != null) {
                        $sale_id              = Crypt::encrypt($sale->id);
                        $sale->customer_name  = ucfirst($sale->customer->name);
                        $sale->customer_email = $sale->customer->email;
                        $sale->url            = route('get.sales.invoice', $sale_id);

                        try {
                            Mail::to($sale->customer_email)->send(new SelledInvoice($sale));
                        } catch (\Exception $e) {
                            $smtp_error = "<br><span class='text-danger'>" . __('E-Mail has been not sent due to SMTP configuration') . '</span>';
                        }
                    }

                    return response()->json(
                        [
                            'code' => 200,
                            'success' => __('Payment completed successfully!') . ((isset($smtp_error)) ? $smtp_error : ''),
                        ]
                    );
                }
            } else {
                return response()->json(
                    [
                        'code' => 404,
                        'success' => __('Items not found!'),
                    ]
                );
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function invoiceSellNumber()
    {
        if (Auth::user()->can('Manage Purchases')) {
            $latest = Sale::where('created_by', '=', Auth::user()->getCreatedBy())->latest()->first();

            return $latest ? $latest->invoice_id + 1 : 1;
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Sale $sale)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(Sale $sale)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function update(Request $request, Sale $sale)
    {
        if (Auth::user()->can('Manage Sales')) {
            $user_id = Auth::user()->getCreatedBy();

            if ($request->has('product') && $request->has('quantity')) {
                $products   = $request->product;
                $quantities = $request->quantity;

                $sale->customer_id = $request->customer_id;

                if (Auth::user()->isOwner()) {

                    $sale->branch_id        = $request->branch_id;
                    $sale->cash_register_id = $request->cash_register_id;
                }

                $sale->save();

                if (count($products) == count($quantities)) {
                    SelledItems::where('sell_id', $sale->id)->delete();

                    for ($i = 0; $i < count($products); $i++) {
                        $product_id = $products[$i];
                        $quantity   = (int)$quantities[$i];

                        $product = Product::whereId($product_id)->where('created_by', $user_id)->first();

                        $tax   = ($product->taxes == null) ? 0 : (float)$product->taxes->percentage;
                        $price = $product->sale_price;

                        $tax_id = Product::tax_id($product_id);

                        $ri             = new SelledItems();
                        $ri->sell_id    = $sale->id;
                        $ri->product_id = $product_id;
                        $ri->price      = $price;
                        $ri->quantity   = $quantity;
                        $ri->tax_id     = $tax_id;
                        $ri->tax        = $tax;
                        $ri->save();
                    }

                    return redirect()->route('reports.sales')->with('success', __('Sales Order updated successfully.'));
                }
            } else {
                return redirect()->back()->with('error', __('Please add some Products!'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Sale $sale)
    {
        if (Auth::user()->can('Manage Sales') && isset($sale)) {
            SelledItems::where('sell_id', $sale->id)->delete();
            $sale->delete();
        }

        return redirect()->route('reports.sales')->with('success', __('Sales Order deleted successfully.'));
    }

    public function salesItems(Request $request)
    {
        $sale_id = $request->id;
        if (Auth::user()->can('Manage Sales') && $request->ajax() && isset($sale_id) && !empty($sale_id)) {
            $items = SelledItems::select('selled_items.*', 'products.name as productname', 'products.quantity as maxquantity')->join('products', 'products.id', '=', 'selled_items.product_id')->where('products.created_by', '=', Auth::user()->getCreatedBy())->where('selled_items.sell_id', '=', $sale_id)->get();

            foreach ($items as $key => $item) {
                $subtotal = $item->price * $item->quantity;
                $tax      = ($subtotal * $item->tax) / 100;

                $items[$key]['subtotal'] = $subtotal + $tax;
            }

            return json_encode($items);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function selledInvoice($sell_id)
    {
        $sell_id = Crypt::decrypt($sell_id);

        $sell = Sale::find($sell_id);

        if (!empty($sell)) {
            $user     = User::select('*')->where('id', $sell->created_by)->first();
            $settings = Utility::settings($user->id);

            $invoice_id    = $user->sellInvoiceNumberFormat($sell->invoice_id);
            $invoice_color = $user->sellInvoiceColor();

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

            $customer = $sell->customer;

            if ($customer != null) {
                $customer->state = $customer->state != '' ? ", " . $customer->state : '';

                $customerdetails = [
                    ucfirst($customer->name),
                    $customer->phone_number,
                    $customer->address,
                    $customer->city . $customer->state,
                    $customer->country,
                    $customer->zipcode,
                ];
            } else {
                $customerdetails = [
                    __('Walk-in Customer'),
                    '',
                    '',
                    '',
                    '',
                    '',
                ];
            }

            $items = SelledItems::select('selled_items.*', 'products.name as productname')->join('products', 'products.id', '=', 'selled_items.product_id')->where('products.created_by', '=', $user->getCreatedBy())->where('selled_items.sell_id', '=', $sell->id)->get();

            $invoice = new InvoicePrinter("A4", $user->currencySymbol(), $user->lang);

            $invoice->setLogo(asset(Storage::url('logo/logo-invoice.png')));
            $invoice->setColor($invoice_color);
            $invoice->setType($invoice_id);
            $invoice->setDate($user->dateFormat($sell->created_at));
            $invoice->setTime($user->timeFormat($sell->created_at));

            $invoice->setFrom($userdetails);

            $invoice->setTo($customerdetails);

            $total = 0;

            foreach ($items as $key => $item) {
                $subtotal = $item->price * $item->quantity;
                $tax      = ($subtotal * $item->tax) / 100;

                $total += $st = $subtotal + $tax;
                $invoice->addItem($item->productname, "", $item->quantity, $item->price, $item->tax, $tax, $st);
            }

            $invoice->addTotal("Total", $total, true);

            if ($sell->status == 1) {
                $invoice->addBadge(__('Partially Paid'));
            } else if ($sell->status == 2) {
                $invoice->addBadge(__('Paid'));
            } else {
                $invoice->addBadge(__('Unpaid'));
            }

            $invoice->addTitle("Important Notice");

            $invoice->addParagraph("No item will be replaced or refunded if you don't have the invoice with you.");

            $invoice->setFooternote(URL::to('/'));

            $name = 'sellpdf/sell_' . md5(time()) . '.pdf';

            $invoice->render('I', $name);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function printSaleInvoice($id)
    {
        $sale_id = Crypt::decrypt($id);
        $sale    = Sale::findOrFail($sale_id);

        if ($sale) {
            $user = User::select('*')->where('id', $sale->created_by)->first();

            $selleditems = SelledItems::select('selled_items.*', 'products.name as productname')->join('products', 'products.id', '=', 'selled_items.product_id')->where('products.created_by', '=', $user->getCreatedBy())->where('selled_items.sell_id', '=', $sale->id)->get();

            $total = 0;

            foreach ($selleditems as $key => $item) {
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

            $sale->items    = $items;
            $sale->subtotal = $user->priceFormat($total);

            $settings                      = Utility::settings($user->id);
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

            $customer = $sale->customer;

            if ($customer != null) {
                $customer->state = $customer->state != '' ? ", " . $customer->state : '';

                $customerdetails = [
                    ucfirst($customer->name),
                    $customer->phone_number,
                    $customer->address,
                    $customer->city . $customer->state,
                    $customer->country,
                    $customer->zipcode,
                ];
            } else {
                $customerdetails = [
                    __('Walk-in Vendor'),
                    '',
                    '',
                    '',
                    '',
                    '',
                ];
            }
            $color = '#' . $settings['sale_invoice_color'];

            //Set your logo
            // $logo         = asset(\Storage::url('/'));
            // $company_logo = Utility::getValByName('company_logo_dark');
            $logo=\App\Models\Utility::get_file('/');
            $company_logo = Utility::get_company_logo();
            $img          = asset($logo . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));

            $font_color = Utility::getFontColor($color);

            return view('sales.templates.' . $settings['sale_invoice_template'], compact('sale', 'color', 'font_color', 'settings', 'user', 'userdetails', 'customerdetails', 'img'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function previewSelledInvoice($template, $color)
    {
        $settings = Utility::settings();

        $sale = new Sale();
        $user = Auth::user();

        $customerdetails = [
            ucfirst('Client'),
            '+216 654654',
            'Hankenshire',
            'New York' . 'New York',
            'USA',
            '999999',
        ];

        $items = [];
        for ($i = 1; $i <= 3; $i++) {
            $item             = new \stdClass();
            $item->name       = 'Item ' . $i;
            $item->quantity   = 2;
            $item->price      = '$100.00';
            $item->tax        = '0%';
            $item->tax_amount = '$0.0';
            $item->subtotal   = '$200.00';
            $items[]          = $item;
        }

        $sale->invoice_id = 1;
        $sale->items      = $items;
        $sale->subtotal   = '$600.00';
        $sale->created_at = date('Y-m-d H:i:s');

        $preview    = 1;
        $color      = '#' . $color;
        $font_color = Utility::getFontColor($color);

        //Set your logo
        // $logo         = asset(\Storage::url('/'));
        // $company_logo = Utility::getValByName('company_logo_dark');
        $logo=\App\Models\Utility::get_file('/');
        $company_logo = Utility::get_company_logo();
        $img          = asset($logo . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));

        return view('sales.templates.' . $template, compact('sale', 'preview', 'color', 'font_color', 'settings', 'user', 'customerdetails', 'img'));
    }
    public function export()
    {
        $name = 'Sale_' . date('Y-m-d i:h:s');
        $data = Excel::download(new SaleExport(), $name . '.xlsx'); ob_end_clean();

        return $data;
    }
}
