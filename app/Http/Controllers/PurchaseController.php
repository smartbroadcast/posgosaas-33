<?php

namespace App\Http\Controllers;

use App\Exports\PurchaseExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Konekt\PdfInvoice\InvoicePrinter;
use App\Mail\PurchasedInvoice;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchasedItems;
use App\Models\User;
use App\Models\CashRegister;
use App\Models\Utility;
use App\Models\Vendor;
use Maatwebsite\Excel\Facades\Excel;
// use App\Exports\PurchaseExport;

class PurchaseController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('Manage Purchases')) {
            $user_id = Auth::user()->getCreatedBy();

            $brands = Brand::where('created_by', $user_id)->pluck('name', 'id');
            $brands->prepend(__('Select Brand'), '');

            $cashregister = CashRegister::where('created_by', $user_id)->pluck('name', 'id');
            $cashregister->prepend(__('Select CashRegister'), '');

            return view('purchases.index',compact('brands','cashregister'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create(Request $request)
    {
        $sess = session()->get('purchases');

        if (Auth::user()->can('Manage Purchases') && !empty($sess) && count($sess) > 0) {
            $user = Auth::user();

            $settings = Utility::settings();

            $vendor = Vendor::where('name', '=', $request->vc_name)->where('created_by', $user->getCreatedBy())->first();
            $details = [
                'invoice_id' => $user->purchaseInvoiceNumberFormat($this->invoicePurchaseNumber()),
                'vendor' => $vendor != null ? $vendor->toArray() : [],
                'user' => $user != null ? $user->toArray() : [],
                'date' => date('Y-m-d'),
                'pay' => 'show',
            ];

            if (!empty($details['vendor'])) {
                $details['vendor']['state'] = $details['vendor']['state'] != '' ? ", " . $details['vendor']['state'] : '';

                $vendordetails = '<h2 class="h6">' . ucfirst($details['vendor']['name']) . '</h2><h2  class="h6 font-weight-normal">' . '<p class="m-0">' . $details['vendor']['phone_number'] . '</p>' . '<p class="m-0">' . $details['vendor']['address'] . '</p>' . '<p class="m-0">' . $details['vendor']['city'] . $details['vendor']['state'] . '</p>' . '<p class="m-0">' . $details['vendor']['country'] . '</p>' . '<p class="m-0">' . $details['vendor']['zipcode'] . '</p>';
            } else {
                $vendordetails = '<h2 class="h6">' . __('Walk-in Vendor') . '<h2>';
            }

            $settings['company_telephone'] = $settings['company_telephone'] != '' ? ", " . $settings['company_telephone'] : '';
            $settings['company_state']     = $settings['company_state'] != '' ? ", " . $settings['company_state'] : '';

            $userdetails = '<h2 class="h6 font-weight-normal">' . ucfirst($details['user']['name']) . '<h2 class="h6 font-weight-normal">' . '<p class="m-0">' . $settings['company_name'] . $settings['company_telephone'] . '</p>' . '<p class="m-0">' . $settings['company_address'] . '</p>' . '<p class="m-0">' . $settings['company_city'] . $settings['company_state'] . '</p>' . '<p class="m-0">' . $settings['company_country'] . '</p>' . '<p class="m-0">' . $settings['company_zipcode'] . '</p>';

            $details['vendor']['details'] = $vendordetails;

            $details['user']['details'] = $userdetails;

            $mainsubtotal = 0;
            $purchases    = [];

            foreach ($sess as $key => $value) {
                $subtotal = $value['price'] * $value['quantity'];
                $tax      = ($subtotal * $value['tax']) / 100;

                $purchases['data'][$key]['name']       = $value['name'];
                $purchases['data'][$key]['quantity']   = $value['quantity'];
                $purchases['data'][$key]['price']      = Auth::user()->priceFormat($value['price']);
                $purchases['data'][$key]['tax']        = $value['tax'] . '%';
                $purchases['data'][$key]['tax_amount'] = Auth::user()->priceFormat($tax);
                $purchases['data'][$key]['subtotal']   = Auth::user()->priceFormat($value['subtotal']);
                $mainsubtotal                          += $value['subtotal'];
            }
            $purchases['total'] = Auth::user()->priceFormat($mainsubtotal);

            return view('purchases.show', compact('purchases', 'details'));
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
       
        if (Auth::user()->can('Manage Purchases')) {
            $user_id = Auth::user()->getCreatedBy();

            $vendor_id        = Vendor::vendor_id($request->vc_name);
            $branch_id        = $request->branch_id != '' ? $request->branch_id : 0;
            $cash_register_id = $request->cash_register_id != '' ? $request->cash_register_id : 0;
            $invoice_id       = $this->invoicePurchaseNumber();
            $purchases        = session()->get('purchases');

            if (isset($purchases) && !empty($purchases) && count($purchases) > 0) {
                $result = DB::table('purchases')->where('invoice_id', $invoice_id)->where('created_by', $user_id)->get();
                if (count($result) > 0) {
                    return response()->json(
                        [
                            'code' => 200,
                            'success' => __('Payment is already completed!'),
                        ]
                    );
                } else {
                    $purchase = new Purchase();

                    $purchase->invoice_id       = $invoice_id;
                    $purchase->vendor_id        = $vendor_id;
                    $purchase->branch_id        = $branch_id;
                    $purchase->cash_register_id = $cash_register_id;
                    $purchase->created_by       = $user_id;

                    $purchase->save();

                    foreach ($purchases as $key => $value) {
                        $product_id = $value['id'];

                        $product = Product::whereId($product_id)->where('created_by', $user_id)->first();

                        $original_quantity = ($product == null) ? 0 : (int)$product->quantity;

                        $product_quantity = $original_quantity + $value['quantity'];
                        if ($product != null && !empty($product)) {
                            Product::where('id', $product_id)->update(['quantity' => $product_quantity]);
                        }

                        $tax_id = Product::tax_id($product_id);

                        $purchaseditems = new PurchasedItems();

                        $purchaseditems->purchase_id = $purchase->id;
                        $purchaseditems->product_id  = $product_id;
                        $purchaseditems->price       = $value['price'];
                        $purchaseditems->quantity    = $value['quantity'];
                        $purchaseditems->tax_id      = $tax_id;
                        $purchaseditems->tax         = $value['tax'];

                        $purchaseditems->save();
                    }

                    session()->forget('purchases');

                    if ($purchase->vendor != null) {
                        $purchase_id            = Crypt::encrypt($purchase->id);
                        $purchase->vendor_name  = ucfirst($purchase->vendor->name);
                        $purchase->vendor_email = $purchase->vendor->email;
                        $purchase->url          = route('get.purchased.invoice', $purchase_id);

                        try {
                            Mail::to($purchase->vendor_email)->send(new PurchasedInvoice($purchase));
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

    function invoicePurchaseNumber()
    {
        if (Auth::user()->can('Manage Purchases')) {
            $latest = Purchase::where('created_by', '=', Auth::user()->getCreatedBy())->latest()->first();

            return $latest ? $latest->invoice_id + 1 : 1;
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Purchase $purchase)
    {

        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(Purchase $purchase)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        if (Auth::user()->can('Manage Purchases')) {
            $user_id = Auth::user()->getCreatedBy();

            if ($request->has('product') && $request->has('quantity')) {
                $products   = $request->product;
                $quantities = $request->quantity;

                $purchase->vendor_id = $request->vendor_id;

                if (Auth::user()->isOwner()) {

                    $purchase->branch_id        = $request->branch_id;
                    $purchase->cash_register_id = $request->cash_register_id;
                }

                $purchase->save();

                if (count($products) == count($quantities)) {
                    PurchasedItems::where('purchase_id', $purchase->id)->delete();

                    for ($i = 0; $i < count($products); $i++) {
                        $product_id = $products[$i];
                        $quantity   = (int)$quantities[$i];

                        $product = Product::whereId($product_id)->where('created_by', $user_id)->first();

                        $tax   = ($product->taxes == null) ? 0 : (float)$product->taxes->percentage;
                        $price = $product->purchase_price;

                        $tax_id = Product::tax_id($product_id);

                        $ri              = new PurchasedItems();
                        $ri->purchase_id = $purchase->id;
                        $ri->product_id  = $product_id;
                        $ri->price       = $price;
                        $ri->quantity    = $quantity;
                        $ri->tax_id      = $tax_id;
                        $ri->tax         = $tax;
                        $ri->save();
                    }

                    return redirect()->route('reports.purchases')->with('success', __('Purchase Order updated successfully.'));
                }
            } else {
                return redirect()->back()->with('error', __('Please add some Products!'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Purchase $purchase)
    {
        if (Auth::user()->can('Manage Purchases') && isset($purchase)) {
            PurchasedItems::where('purchase_id', $purchase->id)->delete();
            $purchase->delete();
        }

        return redirect()->route('reports.purchases')->with('success', __('Purchase Order deleted successfully.'));
    }

    public function purchasedItems(Request $request)
    {
        $purchase_id = $request->id;
        if (Auth::user()->can('Manage Purchases') && $request->ajax() && isset($purchase_id) && !empty($purchase_id)) {
            $items = PurchasedItems::select('purchased_items.*', 'products.name as productname')->join('products', 'products.id', '=', 'purchased_items.product_id')->where('products.created_by', '=', Auth::user()->getCreatedBy())->where('purchased_items.purchase_id', '=', $purchase_id)->get();

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

    public function purchasedInvoice($purchase_id)
    {

        $purchase_id = Crypt::decrypt($purchase_id);

        $purchase = Purchase::find($purchase_id);

        if (!empty($purchase)) {
            $user     = User::select('*')->where('id', $purchase->created_by)->first();
            $settings = Utility::settings($user->id);

            $invoice_id    = $user->purchaseInvoiceNumberFormat($purchase->invoice_id);
            $invoice_color = $user->purchaseInvoiceColor();

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

            $vendor = $purchase->vendor;

            if ($vendor != null) {
                $vendor->state = $vendor->state != '' ? ", " . $vendor->state : '';

                $vendordetails = [
                    ucfirst($vendor->name),
                    $vendor->phone_number,
                    $vendor->address,
                    $vendor->city . $vendor->state,
                    $vendor->country,
                    $vendor->zipcode,
                ];
            } else {
                $vendordetails = [
                    __('Walk-in Vendor'),
                    '',
                    '',
                    '',
                    '',
                    '',
                ];
            }

            $items = PurchasedItems::select('purchased_items.*', 'products.name as productname')->join('products', 'products.id', '=', 'purchased_items.product_id')->where('products.created_by', '=', $user->getCreatedBy())->where('purchased_items.purchase_id', '=', $purchase->id)->get();

            $invoice = new InvoicePrinter("A4", $user->currencySymbol(), $user->lang);

            $invoice->setLogo(asset(Storage::url('logo/logo-invoice.png')));
            $invoice->setColor($invoice_color);
            $invoice->setType($invoice_id);
            $invoice->setDate($user->dateFormat($purchase->created_at));
            $invoice->setTime($user->timeFormat($purchase->created_at));

            $invoice->setFrom($vendordetails);

            $invoice->setTo($userdetails);

            $total = 0;

            foreach ($items as $key => $item) {
                $subtotal = $item->price * $item->quantity;
                $tax      = ($subtotal * $item->tax) / 100;

                $total += $st = $subtotal + $tax;
                $invoice->addItem($item->productname, "", $item->quantity, $item->price, $item->tax, $tax, $st);
            }

            $invoice->addTotal("Total", $total, true);

            if ($purchase->status == 1) {
                $invoice->addBadge(__('Partially Paid'));
            } else if ($purchase->status == 2) {
                $invoice->addBadge(__('Paid'));
            } else {
                $invoice->addBadge(__('Unpaid'));
            }

            $invoice->addTitle("Important Notice");

            $invoice->addParagraph("No item will be replaced or refunded if you don't have the invoice with you.");

            $invoice->setFooternote(URL::to('/'));

            $name = 'purchasepdf/purchase_' . md5(time()) . '.pdf';

            $invoice->render('I', $name);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function printPurchaseInvoice($id)
    {

        $purchase_id = Crypt::decrypt($id);
        $purchase    = Purchase::findOrFail($purchase_id);

        if ($purchase) {
            $user = User::select('*')->where('id', $purchase->created_by)->first();

            $purchaseditems = PurchasedItems::select('purchased_items.*', 'products.name as productname')->join('products', 'products.id', '=', 'purchased_items.product_id')->where('products.created_by', '=', $user->getCreatedBy())->where('purchased_items.purchase_id', '=', $purchase->id)->get();

            $total = 0;

            foreach ($purchaseditems as $key => $item) {
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

            $purchase->items    = $items;
            $purchase->subtotal = $user->priceFormat($total);

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

            $vendor = $purchase->vendor;

            if ($vendor != null) {
                $vendor->state = $vendor->state != '' ? ", " . $vendor->state : '';

                $vendordetails = [
                    ucfirst($vendor->name),
                    $vendor->phone_number,
                    $vendor->address,
                    $vendor->city . $vendor->state,
                    $vendor->country,
                    $vendor->zipcode,
                ];
            } else {
                $vendordetails = [
                    __('Walk-in Vendor'),
                    '',
                    '',
                    '',
                    '',
                    '',
                ];
            }
            $color = '#' . $settings['purchase_invoice_color'];

            //Set your logo
            // $logo         = asset(\Storage::url('/'));
            $logo=\App\Models\Utility::get_file('/');
            // $company_logo = Utility::getValByName('company_logo_dark');
            $company_logo = Utility::get_company_logo();
            $img          = asset($logo . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));

            $font_color = Utility::getFontColor($color);

            return view('purchases.templates.' . $settings['purchase_invoice_template'], compact('purchase', 'color', 'font_color', 'settings', 'user', 'userdetails', 'vendordetails', 'img'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function previewPurchasedInvoice($template, $color)
    {
        $settings = Utility::settings();

        $purchase = new Purchase();
        $user     = Auth::user();

        $vendordetails = [
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

        $purchase->invoice_id = 1;
        $purchase->items      = $items;
        $purchase->subtotal   = '$600.00';
        $purchase->created_at = date('Y-m-d H:i:s');

        $preview = 1;
        $color   = '#' . $color;
        $font_color = Utility::getFontColor($color);

        //Set your logo
        $logo=\App\Models\Utility::get_file('/');
        // $company_logo = Utility::getValByName('company_logo_dark');
        $company_logo = Utility::get_company_logo();
        $img          = asset($logo . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));
        
        return view('purchases.templates.' . $template, compact('purchase', 'preview', 'font_color', 'color', 'settings', 'user', 'vendordetails', 'img'));
    }
    public function export()
    {
        $name = 'Purchase_' . date('Y-m-d i:h:s');
        $data = Excel::download(new PurchaseExport(), $name . '.xlsx');
        ob_end_clean();

        return $data;
    }
}
