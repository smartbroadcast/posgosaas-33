<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\Utility;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Branch;
use App\Models\CashRegister;
use App\Models\PurchasedItems;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Tax;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use \Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function create()
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function store(Request $request)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function show()
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit()
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function update(Request $request)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function destroy()
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function reportsPurchases()
    {
        if (Auth::user()->can('Manage Purchases')) {
            $user_id = Auth::user()->getCreatedBy();

            $vendors = Vendor::where('created_by', $user_id)->pluck('name', 'id');
            $vendors->prepend(__('Walk-in Vendors'), 0);
            $vendors->prepend(__('All'), '');

            $users = User::where('parent_id', $user_id)->pluck('name', 'id');
            $users->prepend(__('All'), '');

            $first_day_of_current_month = Carbon::now()->startOfMonth()->subMonth(0)->toDateString();
            $first_day_of_next_month = Carbon::now()->startOfMonth()->subMonth(-1)->toDateString();

            $start_date = $first_day_of_current_month;
            $end_date = $first_day_of_next_month;

            return view('reports.purchase', compact('vendors', 'users', 'start_date', 'end_date'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function showPurchaseInvoice(Request $request, $id)
    {
        $purchase = Purchase::find($id);
        if (Auth::user()->can('Manage Purchases') && $request->ajax() && !empty($purchase)) {
            $settings = Utility::settings();

            $details = [
                'invoice_id' => Auth::user()->purchaseInvoiceNumberFormat($purchase->invoice_id),
                'vendor' => $purchase->vendor != null ? $purchase->vendor->toArray() : [],
                'user' => $purchase->user != null ? $purchase->user->toArray() : [],
                'date' => Auth::user()->dateFormat($purchase->created_at),
                'pay' => 'hide',
            ];

            if (!empty($details['vendor'])) {
                $details['vendor']['state'] = $details['vendor']['state'] != '' ? ", " . $details['vendor']['state'] : '';

                $vendordetails =
                    '<h2 class="h6 font-weight-normal">' . ucfirst($details['vendor']['name']) . '<h2>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['vendor']['phone_number'] . '</p>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['vendor']['address'] . '</p>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['vendor']['city'] . $details['vendor']['state'] . '</p>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['vendor']['country'] . '</p>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['vendor']['zipcode'] . '</p>';
            } else {
                $vendordetails =
                    '<h2 class="h6">' . __('Walk-in Vendor') . '<h2>';
            }

            $settings['company_telephone'] = $settings['company_telephone'] != '' ? ", " . $settings['company_telephone'] : '';
            $settings['company_state']     = $settings['company_state'] != '' ? ", " . $settings['company_state'] : '';

            $userdetails =
                '<h2 class="h6">' . ucfirst($details['user']['name']) . '<h2>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_name'] . $settings['company_telephone'] . '</p>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_address'] . '</p>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_city'] . $settings['company_state'] . '</p>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_country'] . '</p>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_zipcode'] . '</p>';

            $details['vendor']['details'] = $vendordetails;

            $details['user']['details'] = $userdetails;

            $purchases = $purchase->itemsArray();

            return view('purchases.show', compact('purchases', 'details','purchase'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function editPurchaseInvoice(Request $request, $purchase_id)
    {
        if (Auth::user()->can('Manage Purchases')) {
            $purchase = Purchase::find($purchase_id);
            $user_id = Auth::user()->getCreatedBy();

            $vendors = Vendor::where('created_by', $user_id)->pluck('name', 'id');
            $vendors->prepend(__('Walk-in Vendors'), 0);

            return view('purchases.edit', compact('purchase', 'vendors'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function editSaleInvoice(Request $request, $sale_id)
    {
        if (Auth::user()->can('Manage Sales')) {
            $sale = Sale::find($sale_id);
            $user_id = Auth::user()->getCreatedBy();

            $customers = Customer::where('created_by', $user_id)->pluck('name', 'id');
            $customers->prepend(__('Walk-in Customers'), 0);

            return view('sales.edit', compact('sale', 'customers'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function reportsSales()
    {
        if (Auth::user()->can('Manage Sales')) {
            $user_id = Auth::user()->getCreatedBy();

            $customers = Customer::where('created_by', $user_id)->pluck('name', 'id');
            $customers->prepend(__('Walk-in Customers'), 0);
            $customers->prepend(__('All'), '');

            $users = User::where('parent_id', $user_id)->pluck('name', 'id');
            $users->prepend(__('All'), '');

            $first_day_of_current_month = Carbon::now()->startOfMonth()->subMonth(0)->toDateString();
            $first_day_of_next_month = Carbon::now()->startOfMonth()->subMonth(-1)->toDateString();

            $start_date = $first_day_of_current_month;
            $end_date = $first_day_of_next_month;

            return view('reports.sale', compact('customers', 'users', 'start_date', 'end_date'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function showSellInvoice(Request $request, $id)
    {
        $sell = Sale::find($id);
        if (Auth::user()->can('Manage Sales') && $request->ajax() && !empty($sell)) {
            $settings = Utility::settings();

            $details = [
                'invoice_id' => Auth::user()->sellInvoiceNumberFormat($sell->invoice_id),
                'customer' => $sell->customer != null ? $sell->customer->toArray() : [],
                'user' => $sell->user != null ? $sell->user->toArray() : [],
                'date' => Auth::user()->dateFormat($sell->created_at),
                'pay' => 'hide',
            ];

            if (!empty($details['customer'])) {
                $details['customer']['state'] = $details['customer']['state'] != '' ? ", " . $details['customer']['state'] : '';

                $customerdetails =
                    '<h2 class="h6">' . ucfirst($details['customer']['name']) . '<h2>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['customer']['phone_number'] . '</p>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['customer']['address'] . '</p>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['customer']['city'] . $details['customer']['state'] . '</p>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['customer']['country'] . '</p>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['customer']['zipcode'] . '</p>';
            } else {
                $customerdetails =
                    '<h2 class="h6">' . __('Walk-in Customer') . '<h2>';
            }

            $settings['company_telephone'] = $settings['company_telephone'] != '' ? ", " . $settings['company_telephone'] : '';
            $settings['company_state']     = $settings['company_state'] != '' ? ", " . $settings['company_state'] : '';

            $userdetails =
                '<h2 class="h6">' . ucfirst($details['user']['name']) . '<h2>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_name'] . $settings['company_telephone'] . '</p>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_address'] . '</p>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_city'] . $settings['company_state'] . '</p>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_country'] . '</p>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_zipcode'] . '</p>';

            $details['customer']['details'] = $customerdetails;

            $details['user']['details'] = $userdetails;

            $sales = $sell->itemsArray();

            return view('sales.show', compact('sales', 'details','sell'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function invoiceFilter(Request $request)
    {
        if ((Auth::user()->can('Manage Purchases') || Auth::user()->can('Manage Sales')) && $request->ajax()) {
            $data         = [];
            $invoicearray = [];

            $data['start_date'] = $request->has('start_date') ? $request->input('start_date') : '';
            $data['end_date']   = $request->has('end_date') ? $request->input('end_date') : '';
            $data['vendors']    = $request->has('vendors') ? $request->input('vendors') : 0;
            $data['customers']  = $request->has('customers') ? $request->input('customers') : 0;
            $data['user_id']    = $request->has('user_id') && $request->input('user_id') != '' ? $request->input('user_id') : Auth::user()->getCreatedBy();

            if ($data['vendors'] == 1) {
                $invoices = new Purchase();

                $data['vendor_id'] = $request->has('vendor_id') ? $request->input('vendor_id') : '';

                if ($data['vendor_id'] != '') {
                    $invoices = $invoices->where('vendor_id', '=', $data['vendor_id']);
                }
            } else if ($data['customers'] == 1) {
                $invoices = new Sale();

                $data['customer_id'] = $request->has('customer_id') ? $request->input('customer_id') : '';

                if ($data['customer_id'] != '') {
                    $invoices = $invoices->where('customer_id', '=', $data['customer_id']);
                }
            }

            $invoices = $invoices->where('created_by', '=', $data['user_id']);

            if ($data['start_date'] != '' && $data['end_date'] != '') {
                $invoices = $invoices->whereDate('created_at', '>=', $data['start_date'])->whereDate('created_at', '<=', $data['end_date']);
            } else if ($data['start_date'] != '' || $data['end_date'] != '') {
                $date     = $data['start_date'] == '' ? ($data['end_date'] == '' ? '' : $data['end_date']) : $data['start_date'];
                $invoices = $invoices->whereDate('created_at', '=', $date);
            }

            $totalItemsCount = $totalCount = 0;

            foreach ($invoices->orderBy('id', 'DESC')->get() as $key => $invoice) {
                $payment_status = ($invoice->status == 1 ? __('Partially Paid') : (($invoice->status == 2 ? __('Paid') : __('Unpaid'))));

                $payment_class = Utility::convertStringToSlug(($invoice->status == 1 ? 'Partially Paid' : (($invoice->status == 2 ? 'Paid' : 'Unpaid'))));
                $invoicearray[$key]['paymentstatus']     = '
                    <li class="nav-item dropdown display-payment" data-li-id="' . $invoice->id . '">
                        <span data-bs-toggle="dropdown" class="badge payment-label badge-lg p-2  ' . $payment_class . '">' . $payment_status . '</span>
                       <div class="dropdown-menu dropdown-list payment-status dropdown-menu-right">
                          <div class="dropdown-list-content payment-actions" data-id="' . $invoice->id . '" data-url="' . route('update.payment.status', ['slug' => ($data['vendors'] == 1 ? 'purchase' : ($data['customers'] == 1 ? 'sale' : '')), 'id' => $invoice->id]) . '">
                             <a href="#" data-status="0" data-class="unpaid" class="dropdown-item payment-action ' . ($invoice->status == 0 ? 'selected' : '') . '">' . __('Unpaid') . '
                             </a>
                             <a href="#" data-status="1" data-class="partially-paid" class="dropdown-item payment-action ' . ($invoice->status == 1 ? 'selected' : '') . '">' . __('Partially Paid') . '
                             </a>
                             <a href="#" data-status="2" data-class="paid" class="dropdown-item payment-action ' . ($invoice->status == 2 ? 'selected' : '') . '">' . __('Paid') . '
                             </a>
                          </div>
                       </div>
                    </li>';

                $model_delete_id = 'delete-form-' . $invoice->id;

                if ($data['vendors'] == 1) {

                    $invoicearray[$key]['invoice_id'] = '
                    <a class="btn btn-outline-primary" href="#" data-ajax-popup="true" data-title="' . __('Purchase Invoice') . '" data-size="lg" data-url="' . route('show.purchase.invoice', $invoice->id) . '" >' . Auth::user()->purchaseInvoiceNumberFormat($invoice->invoice_id) . '</a>
                        ';
                    $invoicearray[$key]['action']     = '

                    <div class="action-btn btn-dark ms-2">
                    <a href="' . route('get.purchased.invoice', Crypt::encrypt($invoice->id)) . '" target="_blank" class="mx-3 btn btn-sm d-inline-flex align-items-center " data-bs-toggle="tooltip"  data-title="' . __('Download') . '"    title="' . __('Download') . '"><i class="ti ti-arrow-bar-to-down text-white"></i></a>
                    </div>

                    <div class="action-btn btn-primary ms-2">
                    <a href="' . route('purchase.link.copy', Crypt::encrypt($invoice->id)) . '" class="mx-3 btn btn-sm d-inline-flex align-items-center copy_link" data-bs-toggle="tooltip"  data-title="' . __('Copy Link') . '"  title="' . __('Copy Link') . '"><i class="ti ti-link text-white"></i></a>
                    </div>
                    
                    <div class="action-btn btn-info ms-2">
                    <a href="' . route('edit.purchase.invoice', $invoice->id) . '" class="mx-3 btn btn-sm d-inline-flex align-items-center"  data-bs-toggle="tooltip"  data-title="' . __('Edit') . '"  title="' . __('Edit') . '"> <i class="ti ti-pencil text-white" title="Edit"></i></a>
                    </div>
                  
                    
                    <div class="action-btn btn-warning ms-2">
                    <a href="#" data-ajax-popup="true" data-title="' . __('Purchase Invoice') . '" data-size="lg" data-url="' . route('show.purchase.invoice', $invoice->id) . '" class="mx-3 btn btn-sm d-inline-flex align-items-center"  data-bs-toggle="tooltip"  data-title="' . __('Show') . '"   title="' . __('Show') . '"><i class="ti ti-eye text-white"></i></a>
                    </div>
                    
                    <div class="action-btn bg-danger ms-2">
                    <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center delete-icon bs-pass-para" data-toggle="tooltip"  data-bs-toggle="tooltip"  data-title="' . __('Delete') . '"  title="' . __('Delete') . '" data-confirm="' . __("Are You Sure?") . '"data-text="' .__("This action can not be undone. Do you want to continue?") . '" data-confirm-yes=' . $model_delete_id . '>
                    <i class="ti ti-trash text-white"></i>
                     </a>
                     </div>
                    
                    <form method="POST" action="' . route('purchases.destroy', $invoice->id) . '" accept-charset="UTF-8" id="' . $model_delete_id . '">
                        <input name="_method" type="hidden" value="DELETE">
                        <input name="_token" type="hidden" value="' . csrf_token() . '">
                    </form>';
                    $invoicearray[$key]['vendorname'] = $invoice->vendor != null ? ucfirst($invoice->vendor->name) : __('Walk-in Vendor');

                } else if ($data['customers'] == 1) {
                    $invoicearray[$key]['invoice_id'] = '
                        <a class="btn btn-outline-primary" href="#" data-ajax-popup="true" data-title="' . __('Sale Invoice') . '" data-size="lg" data-url="' . route('show.sell.invoice', $invoice->id) . '" >' . Auth::user()->sellInvoiceNumberFormat($invoice->invoice_id) . '</a>
                        ';
                    $invoicearray[$key]['action']       = '

                     <div class="action-btn btn-dark ms-2">
                    <a href="' . route('get.sales.invoice', Crypt::encrypt($invoice->id)) . '" target="_blank" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip"  data-title="' . __('Download') . '"   title="' . __('Download') . '"><i class="ti ti-arrow-bar-to-down text-white"></i></a>
                    </div>
 
                    <div class="action-btn btn-primary ms-2">
                    <a href="' . route('sale.link.copy', Crypt::encrypt($invoice->id)) . '" class="mx-3 btn btn-sm d-inline-flex align-items-center copy_link_sale"   data-bs-toggle="tooltip"  data-title="' . __('Copy Link') . '"  title="' . __('Copy Link') . '"><i class="ti ti-link text-white"></i></a>
                    </div>
 
                     
                    <div class="action-btn btn-info ms-2">
                    <a href="' . route('edit.sale.invoice', $invoice->id) . '" class="mx-3 btn btn-sm d-inline-flex align-items-center"  data-bs-toggle="tooltip"  data-title="' . __('Edit') . '" title="' . __('Edit') . '"><i class="ti ti-pencil text-white"></i></a>
                    </div>


                    <div class="action-btn btn-warning ms-2">
                    <a href="#" data-ajax-popup="true" data-title="' . __('Sale Invoice') . '" data-size="lg" data-url="' . route('show.sell.invoice', $invoice->id) . '" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip"  data-title="' . __('Show') . '" title="' . __('Show') . '"><i class="ti ti-eye text-white"></i></a>
                    </div>
    
                    <div class="action-btn bg-danger ms-2">
                    <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center delete-icon bs-pass-para" data-bs-toggle="tooltip"  data-title="' . __('Delete') . '"  title="' . __('Delete') . '" data-confirm="' . __("Are You Sure?") . '"data-text="' .__("This action can not be undone. Do you want to continue?") . '" data-confirm-yes=' . $model_delete_id . '>
                        <i class="ti ti-trash text-white"></i>
                    </a>
                    </div>
                    


                    <form method="POST" action="' . route('sales.destroy', $invoice->id) . '" accept-charset="UTF-8" id="' . $model_delete_id . '">
                        <input name="_method" type="hidden" value="DELETE">
                        <input name="_token" type="hidden" value="' . csrf_token() . '">
                    </form>';
                    $invoicearray[$key]['customername'] = $invoice->customer != null ? ucfirst($invoice->customer->name) : __('Walk-in Customer');
                }

                $invoicearray[$key]['id']         = $invoice->id;
                $invoicearray[$key]['username']   = ucfirst($invoice->user->name);
                $invoicearray[$key]['created_at'] = Auth::user()->datetimeFormat($invoice->created_at);
                $invoicearray[$key]['itemscount'] = $invoice->items->count();
                $invoicearray[$key]['itemstotal'] = Auth::user()->priceFormat($invoice->getTotal());

                $totalItemsCount += $invoice->items->count();
                $totalCount      += $invoice->getTotal();
            }
            $data['draw']            = 1;
            $data['recordsTotal']    = count($invoicearray);
            $data['recordsFiltered'] = count($invoicearray);
            $data['totalItemsCount'] = $totalItemsCount;
            $data['totalCount']      = Auth::user()->priceFormat($totalCount);
            $data['data']            = $invoicearray;


            return json_encode($data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function productStockAnalysisView(Request $request)
    {
        $display_status = '';

        if (Auth::user()->isOwner()) {

            $cash_registers = ['-1' => __('All')];
            $branches = $cash_registers + Branch::where('created_by', Auth::user()->getCreatedBy())->pluck('name', 'id')->toArray();
        } else if (Auth::user()->isUser()) {

            $cash_registers = [Auth::user()->cash_register_id => __('Assigned Cash Register')];
            $branches = [Auth::user()->branch_id => __('Assigned Branch')];

            $display_status = 'd-none';
        }

        $monthDates = Utility::getStartEndMonthDates();
        $start_date = $monthDates['start_date'];
        $end_date = $monthDates['end_date'];

        return view('reports.stock-analysis', compact('branches', 'cash_registers', 'start_date', 'end_date', 'display_status'));
    }

    public function productStockAnalysisFilter(Request $request)
    {
        if ($request->ajax()) {
            $data         = [];
            $invoicearray = [];

            $authuser = Auth::user();

            $data['start_date'] = $request->has('start_date') ? $request->input('start_date') : '';
            $data['end_date']   = $request->has('end_date') ? $request->input('end_date') : '';
            $data['branch_id']   = $request->has('branch_id') ? $request->input('branch_id') : '-1';
            $data['cash_register_id']   = $request->has('cash_register_id') ? $request->input('cash_register_id') : '-1';

            $productObj = Product::getallproducts();

            $productscount = $productObj->count();

            $productArray = [];

            if ($productscount > 0) {

                foreach ($productObj->get() as $key => $product) {

                    $productArray[$key]['id'] = $key + 1;
                    $productArray[$key]['product_id'] = $product->id;
                    $productArray[$key]['product_name'] = $product->name;

                    $product_quantity = $product->getProductQuantityByBranch($data);

                    if ($product_quantity > Utility::settings()['low_product_stock_threshold']) {

                        $class = 'bg-success';   
                    } else {

                        $class = 'bg-danger';
                    }

                    $quantity_html = '<span class="badge p-2 px-3 rounded ' . $class . '">' . $product_quantity . '</span>';
                  
                    $productArray[$key]['product_quantity'] = $quantity_html;
                }
            }

            $data['draw']            = 1;
            $data['recordsTotal']    = count($productArray);
            $data['recordsFiltered'] = count($productArray);
            $data['data']            = $productArray;

            return json_encode($data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function productCategoryAnalysisView(Request $request)
    {
        $authuser = Auth::user();

        $display_status = '';

        if (Auth::user()->isOwner()) {

            $cash_registers = ['-1' => __('All')];
            $branches = $cash_registers + Branch::where('created_by', Auth::user()->getCreatedBy())->pluck('name', 'id')->toArray();
        } else if (Auth::user()->isUser()) {

            $cash_registers = [Auth::user()->cash_register_id => __('Assigned Cash Register')];
            $branches = [Auth::user()->branch_id => __('Assigned Branch')];

            $display_status = 'd-none';
        }

        $monthDates = Utility::getStartEndMonthDates();
        $start_date = $monthDates['start_date'];
        $end_date = $monthDates['end_date'];

        $product_categories = ['-1' => __('All')] + Category::where('created_by', $authuser->getCreatedBy())->pluck('name', 'id')->toArray();
        return view('reports.category-analysis', compact('branches', 'cash_registers', 'product_categories', 'start_date', 'end_date', 'display_status'));
    }


    public function productCategoryAnalysisFilter(Request $request)
    {
        if ($request->ajax()) {
            $data         = [];

            $data['start_date'] = $request->has('start_date') ? $request->input('start_date') : '';
            $data['end_date']   = $request->has('end_date') ? $request->input('end_date') : '';
            $data['category_id']   = $request->has('category_id') ? $request->input('category_id') : '-1';
            $data['branch_id']   = $request->has('branch_id') ? $request->input('branch_id') : '-1';
            $data['cash_register_id']   = $request->has('cash_register_id') ? $request->input('cash_register_id') : '-1';

            $data = Category::getProductCategoryAnalysis($data);

            return json_encode($data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function productBrandAnalysisView(Request $request)
    {
        $authuser = Auth::user();

        $display_status = '';

        if (Auth::user()->isOwner()) {

            $cash_registers = ['-1' => __('All')];
            $branches = $cash_registers + Branch::where('created_by', Auth::user()->getCreatedBy())->pluck('name', 'id')->toArray();
        } else if (Auth::user()->isUser()) {

            $cash_registers = [Auth::user()->cash_register_id => __('Assigned Cash Register')];
            $branches = [Auth::user()->branch_id => __('Assigned Branch')];

            $display_status = 'd-none';
        }

        $monthDates = Utility::getStartEndMonthDates();
        $start_date = $monthDates['start_date'];
        $end_date = $monthDates['end_date'];

        $product_brands = ['-1' => __('All')] + Brand::where('created_by', $authuser->getCreatedBy())->pluck('name', 'id')->toArray();

        return view('reports.brand-analysis', compact('branches', 'cash_registers', 'product_brands', 'start_date', 'end_date', 'display_status'));
    }


    public function productBrandAnalysisFilter(Request $request)
    {
        
        // if ($request->ajax()) {
            $data         = [];

            $data['start_date'] = $request->has('start_date') ? $request->input('start_date') : '';
            $data['end_date']   = $request->has('end_date') ? $request->input('end_date') : '';
            $data['brand_id']   = $request->has('brand_id') ? $request->input('brand_id') : '-1';
            $data['branch_id']   = $request->has('branch_id') ? $request->input('branch_id') : '-1';
            $data['cash_register_id']   = $request->has('cash_register_id') ? $request->input('cash_register_id') : '-1';
            
            $data = Brand::getProductBrandAnalysis($data);
            // dd($data);  
            return json_encode($data);
        // } else {
        //     return redirect()->back()->with('error', __('Permission denied.'));
        // }
    }

    public function productTaxAnalysisView(Request $request)
    {
        $authuser = Auth::user();

        $display_status = '';

        if (Auth::user()->isOwner()) {

            $cash_registers = ['-1' => __('All')];
            $branches = $cash_registers + Branch::where('created_by', Auth::user()->getCreatedBy())->pluck('name', 'id')->toArray();
        } else if (Auth::user()->isUser()) {

            $cash_registers = [Auth::user()->cash_register_id => __('Assigned Cash Register')];
            $branches = [Auth::user()->branch_id => __('Assigned Branch')];

            $display_status = 'd-none';
        }

        $monthDates = Utility::getStartEndMonthDates();
        $start_date = $monthDates['start_date'];
        $end_date = $monthDates['end_date'];

        $vendors = ['-1' => __('All'), '0' => 'Walk-in Vendors'] + Vendor::where('created_by', $authuser->getCreatedBy())->pluck('name', 'id')->toArray();
        $customers = ['-1' => __('All'), '0' => 'Walk-in Customers'] + Customer::where('created_by', $authuser->getCreatedBy())->pluck('name', 'id')->toArray();

        return view('reports.tax-analysis', compact('branches', 'cash_registers', 'vendors', 'customers', 'start_date', 'end_date', 'display_status'));
    }

    public function productPurchaseTaxAnalysisFilter(Request $request)
    {
        if ($request->ajax()) {
            $data         = [];

            $data['start_date'] = $request->has('start_date') ? $request->input('start_date') : '';
            $data['end_date']   = $request->has('end_date') ? $request->input('end_date') : '';
            $data['branch_id']   = $request->has('branch_id') ? $request->input('branch_id') : '-1';
            $data['cash_register_id']   = $request->has('cash_register_id') ? $request->input('cash_register_id') : '-1';
            $data['vendor_id']   = $request->has('vendor_id') ? $request->input('vendor_id') : '-1';

            $data = Tax::getProductPurchaseTaxAnalysis($data);

            return json_encode($data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function productSaleTaxAnalysisFilter(Request $request)
    {
        if ($request->ajax()) {
            $data         = [];

            $data['start_date'] = $request->has('start_date') ? $request->input('start_date') : '';
            $data['end_date']   = $request->has('end_date') ? $request->input('end_date') : '';
            $data['branch_id']   = $request->has('branch_id') ? $request->input('branch_id') : '-1';
            $data['cash_register_id']   = $request->has('cash_register_id') ? $request->input('cash_register_id') : '-1';
            $data['customer_id']   = $request->has('customer_id') ? $request->input('customer_id') : '-1';

            $data = Tax::getProductSaleTaxAnalysis($data);

            return json_encode($data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function expenseAnalysisView(Request $request)
    {
        $authuser = Auth::user();

        $display_status = '';

        if (Auth::user()->isOwner()) {

            $cash_registers = ['-1' => __('All')];
            $branches = $cash_registers + Branch::where('created_by', Auth::user()->getCreatedBy())->pluck('name', 'id')->toArray();
        } else if (Auth::user()->isUser()) {

            $cash_registers = [Auth::user()->cash_register_id => __('Assigned Cash Register')];
            $branches = [Auth::user()->branch_id => __('Assigned Branch')];

            $display_status = 'd-none';
        }

        $monthDates = Utility::getStartEndMonthDates();
        $start_date = $monthDates['start_date'];
        $end_date = $monthDates['end_date'];

        $expense_categories = ['-1' => __('All')] + ExpenseCategory::where('created_by', $authuser->getCreatedBy())->pluck('name', 'id')->toArray();

        return view('reports.expense-analysis', compact('branches', 'cash_registers', 'expense_categories', 'start_date', 'end_date', 'display_status'));
    }

    public function expenseAnalysisFilter(Request $request)
    {
        if ($request->ajax()) {
            $data         = [];

            $data['start_date'] = $request->has('start_date') ? $request->input('start_date') : '';
            $data['end_date']   = $request->has('end_date') ? $request->input('end_date') : '';
            $data['expense_category_id']   = $request->has('expense_category_id') ? $request->input('expense_category_id') : '-1';
            $data['branch_id']   = $request->has('branch_id') ? $request->input('branch_id') : '-1';

            $data = Expense::getExpenseAnalysis($data);

            return json_encode($data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customerSalesAnalysisView(Request $request)
    {
        $authuser = Auth::user();

        $display_status = '';

        if (Auth::user()->isOwner()) {

            $cash_registers = ['-1' => __('All')];
            $branches = $cash_registers + Branch::where('created_by', Auth::user()->getCreatedBy())->pluck('name', 'id')->toArray();
        } else if (Auth::user()->isUser()) {

            $cash_registers = [Auth::user()->cash_register_id => __('Assigned Cash Register')];
            $branches = [Auth::user()->branch_id => __('Assigned Branch')];

            $display_status = 'd-none';
        }

        $monthDates = Utility::getStartEndMonthDates();
        $start_date = $monthDates['start_date'];
        $end_date = $monthDates['end_date'];

        $customers = ['-1' => __('All'), '0' => 'Walk-in Customers'] + Customer::where('created_by', $authuser->getCreatedBy())->pluck('name', 'id')->toArray();

        return view('reports.customer-sales', compact('branches', 'cash_registers', 'customers', 'start_date', 'end_date', 'display_status'));
    }

    public function customerSalesAnalysisFilter(Request $request)
    {
        if ($request->ajax()) {
            $data         = [];

            $data['start_date'] = $request->has('start_date') ? $request->input('start_date') : '';
            $data['end_date']   = $request->has('end_date') ? $request->input('end_date') : '';
            $data['branch_id']   = $request->has('branch_id') ? $request->input('branch_id') : '-1';
            $data['cash_register_id']   = $request->has('cash_register_id') ? $request->input('cash_register_id') : '-1';
            $data['customer_id']   = $request->has('customer_id') ? $request->input('customer_id') : '-1';

            $data = Customer::getCustomerSalesAnalysis($data);

            return json_encode($data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function vendorPurchasedAnalysisView(Request $request)
    {
        $authuser = Auth::user();

        $display_status = '';

        if (Auth::user()->isOwner()) {

            $cash_registers = ['-1' => __('All')];
            $branches = $cash_registers + Branch::where('created_by', Auth::user()->getCreatedBy())->pluck('name', 'id')->toArray();
        } else if (Auth::user()->isUser()) {

            $cash_registers = [Auth::user()->cash_register_id => __('Assigned Cash Register')];
            $branches = [Auth::user()->branch_id => __('Assigned Branch')];

            $display_status = 'd-none';
        }

        $monthDates = Utility::getStartEndMonthDates();
        $start_date = $monthDates['start_date'];
        $end_date = $monthDates['end_date'];

        $vendors = ['-1' => __('All'), '0' => 'Walk-in Vendors'] + Vendor::where('created_by', $authuser->getCreatedBy())->pluck('name', 'id')->toArray();

        return view('reports.vendor-purchases', compact('branches', 'cash_registers', 'vendors', 'start_date', 'end_date', 'display_status'));
    }

    public function vendorPurchasedAnalysisFilter(Request $request)
    {
        if ($request->ajax()) {
            $data         = [];

            $data['start_date'] = $request->has('start_date') ? $request->input('start_date') : '';
            $data['end_date']   = $request->has('end_date') ? $request->input('end_date') : '';
            $data['branch_id']   = $request->has('branch_id') ? $request->input('branch_id') : '-1';
            $data['cash_register_id']   = $request->has('cash_register_id') ? $request->input('cash_register_id') : '-1';
            $data['vendor_id']   = $request->has('vendor_id') ? $request->input('vendor_id') : '-1';

            $data = Vendor::getVendorPurchasedAnalysis($data);
            return json_encode($data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function purchasedDailyAnalysisView(Request $request)
    {
        $display_status = '';

        if (Auth::user()->isOwner()) {

            $cash_registers = ['-1' => __('All')];
            $branches = $cash_registers + Branch::where('created_by', Auth::user()->getCreatedBy())->pluck('name', 'id')->toArray();
        } else if (Auth::user()->isUser()) {

            $cash_registers = [Auth::user()->cash_register_id => __('Assigned Cash Register')];
            $branches = [Auth::user()->branch_id => __('Assigned Branch')];

            $display_status = 'd-none';
        }

        $monthDates = Utility::getStartEndMonthDates();
        $start_date = $monthDates['start_date'];
        $end_date = $monthDates['end_date'];

        return view('reports.purchased-daily', compact('branches', 'cash_registers', 'start_date', 'end_date', 'display_status'));
    }

    public function purchasedMonthlyAnalysisView(Request $request)
    {
        $display_status = '';

        if (Auth::user()->isOwner()) {

            $cash_registers = ['-1' => __('All')];
            $branches = $cash_registers + Branch::where('created_by', Auth::user()->getCreatedBy())->pluck('name', 'id')->toArray();
        } else if (Auth::user()->isUser()) {

            $cash_registers = [Auth::user()->cash_register_id => __('Assigned Cash Register')];
            $branches = [Auth::user()->branch_id => __('Assigned Branch')];

            $display_status = 'd-none';
        }

        $monthDates = Utility::getStartEndMonthDates();
        $start_date = $monthDates['start_date'];
        $end_date = $monthDates['end_date'];

        return view('reports.purchased-monthly', compact('branches', 'cash_registers', 'start_date', 'end_date', 'display_status'));
    }

    public function purchasedDailyChartFilter(Request $request)
    {
        if ($request->ajax()) {
            $data         = [];

            $data['start_date'] = $request->has('start_date') ? $request->input('start_date') : '';
            $data['end_date']   = $request->has('end_date') ? $request->input('end_date') : '';
            $data['branch_id']   = $request->has('branch_id') ? $request->input('branch_id') : '-1';
            $data['cash_register_id']   = $request->has('cash_register_id') ? $request->input('cash_register_id') : '-1';
            $data['vendor_id']   = $request->has('vendor_id') ? $request->input('vendor_id') : '-1';

            $purchasesArray = Purchase::getPurchaseReportDailyChart($data);

            return json_encode($purchasesArray);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function purchasedMonthlyChartFilter(Request $request)
    {
        if ($request->ajax()) {
            $data         = [];

            $data['branch_id']   = $request->has('branch_id') ? $request->input('branch_id') : '-1';
            $data['cash_register_id']   = $request->has('cash_register_id') ? $request->input('cash_register_id') : '-1';

            $purchasesArray = Purchase::getPurchaseReportMonthlyChart($data);

            return json_encode($purchasesArray);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function soldDailyAnalysisView(Request $request)
    {
        $display_status = '';

        if (Auth::user()->isOwner()) {

            $cash_registers = ['-1' => __('All')];
            $branches = $cash_registers + Branch::where('created_by', Auth::user()->getCreatedBy())->pluck('name', 'id')->toArray();
        } else if (Auth::user()->isUser()) {

            $cash_registers = [Auth::user()->cash_register_id => __('Assigned Cash Register')];
            $branches = [Auth::user()->branch_id => __('Assigned Branch')];

            $display_status = 'd-none';
        }

        $monthDates = Utility::getStartEndMonthDates();
        $start_date = $monthDates['start_date'];
        $end_date = $monthDates['end_date'];

        return view('reports.sold-daily', compact('branches', 'cash_registers', 'start_date', 'end_date', 'display_status'));
    }
    public function soldMonthlyAnalysisView(Request $request)
    {
        $display_status = '';

        if (Auth::user()->isOwner()) {

            $cash_registers = ['-1' => __('All')];
            $branches = $cash_registers + Branch::where('created_by', Auth::user()->getCreatedBy())->pluck('name', 'id')->toArray();
        } else if (Auth::user()->isUser()) {

            $cash_registers = [Auth::user()->cash_register_id => __('Assigned Cash Register')];
            $branches = [Auth::user()->branch_id => __('Assigned Branch')];

            $display_status = 'd-none';
        }

        $monthDates = Utility::getStartEndMonthDates();
        $start_date = $monthDates['start_date'];
        $end_date = $monthDates['end_date'];

        return view('reports.sold-monthly', compact('branches', 'cash_registers', 'start_date', 'end_date', 'display_status'));
    }

    public function soldDailyChartFilter(Request $request)
    {
        if ($request->ajax()) {
            $data         = [];

            $data['start_date'] = $request->has('start_date') ? $request->input('start_date') : '';
            $data['end_date']   = $request->has('end_date') ? $request->input('end_date') : '';
            $data['branch_id']   = $request->has('branch_id') ? $request->input('branch_id') : '-1';
            $data['cash_register_id']   = $request->has('cash_register_id') ? $request->input('cash_register_id') : '-1';
            $data['customer_id']   = $request->has('customer_id') ? $request->input('customer_id') : '-1';

            $salesArray = Sale::getSaleReportDailyChart($data);

            return json_encode($salesArray);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function soldMonthlyChartFilter(Request $request)
    {
        if ($request->ajax()) {
            $data         = [];

            $data['branch_id']   = $request->has('branch_id') ? $request->input('branch_id') : '-1';
            $data['cash_register_id']   = $request->has('cash_register_id') ? $request->input('cash_register_id') : '-1';

            $salesArray = Sale::getSaleReportMonthlyChart($data);

            return json_encode($salesArray);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function updatePaymentStatus(Request $request, $slug, $id)
    {
        $response = false;
        $status = $request->has('status') ? $request->status : 0;

        if ($slug == 'purchase') {

            $purchase = Purchase::find($id);
            $purchase->status = $status;
            $purchase->save();

            $response = true;
        } else if ($slug == 'sale') {

            $sale = Sale::find($id);
            $sale->status = $status;
            $sale->save();

            $response = true;
        }

        echo json_encode($response);
    }

    public function purchaseLink($id, Request $request)
    {
        $id = \Illuminate\Support\Facades\Crypt::decrypt($id);

        $purchase = Purchase::find($id);
        if (\Auth::check()) {

            $user = \Auth::user();
        } else {

            $user = User::where('id', $purchase->created_by)->first();
        }

        if (!empty($purchase)) {
            $settings = Utility::settings();

            $details = [
                'invoice_id' =>  $user->purchaseInvoiceNumberFormat($purchase->invoice_id),
                'vendor' => $purchase->vendor != null ? $purchase->vendor->toArray() : [],
                'user' => $purchase->user != null ? $purchase->user->toArray() : [],
                'date' => $user->dateFormat($purchase->created_at),
                'pay' => 'hide',
            ];

            if (!empty($details['vendor'])) {
                $details['vendor']['state'] = $details['vendor']['state'] != '' ? ", " . $details['vendor']['state'] : '';

                $vendordetails =
                    '<h2 class="h6 font-weight-normal">' . ucfirst($details['vendor']['name']) . '<h2>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['vendor']['phone_number'] . '</p>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['vendor']['address'] . '</p>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['vendor']['city'] . $details['vendor']['state'] . '</p>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['vendor']['country'] . '</p>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['vendor']['zipcode'] . '</p>';
            } else {
                $vendordetails =
                    '<h2 class="h6">' . __('Walk-in Vendor') . '<h2>';
            }

            $settings['company_telephone'] = $settings['company_telephone'] != '' ? ", " . $settings['company_telephone'] : '';
            $settings['company_state']     = $settings['company_state'] != '' ? ", " . $settings['company_state'] : '';

            $userdetails =
                '<h2 class="h6">' . ucfirst($details['user']['name']) . '<h2>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_name'] . $settings['company_telephone'] . '</p>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_address'] . '</p>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_city'] . $settings['company_state'] . '</p>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_country'] . '</p>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_zipcode'] . '</p>';

            $details['vendor']['details'] = $vendordetails;

            $details['user']['details'] = $userdetails;


            $purchases = $purchase->itemsArray();
            return view('purchases.purchase_invoice', compact('purchases', 'details', 'user','purchase'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function saleLink($id, Request $request)
    {
        $id = \Illuminate\Support\Facades\Crypt::decrypt($id);
        $sell = Sale::find($id);

        if (\Auth::check()) {

            $user = \Auth::user();
        } else {

            $user = User::where('id', $sell->created_by)->first();
        }

        if (!empty($sell)) {
            $settings = Utility::settings();

            $details = [
                'invoice_id' => $user->sellInvoiceNumberFormat($sell->invoice_id),
                'customer' => $sell->customer != null ? $sell->customer->toArray() : [],
                'user' => $sell->user != null ? $sell->user->toArray() : [],
                'date' => $user->dateFormat($sell->created_at),
                'pay' => 'hide',
            ];

            if (!empty($details['customer'])) {
                $details['customer']['state'] = $details['customer']['state'] != '' ? ", " . $details['customer']['state'] : '';

                $customerdetails =
                    '<h2 class="h6">' . ucfirst($details['customer']['name']) . '<h2>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['customer']['phone_number'] . '</p>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['customer']['address'] . '</p>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['customer']['city'] . $details['customer']['state'] . '</p>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['customer']['country'] . '</p>' .
                    '<p class="m-0 h6 font-weight-normal">' . $details['customer']['zipcode'] . '</p>';
            } else {
                $customerdetails =
                    '<h2 class="h6">' . __('Walk-in Customer') . '<h2>';
            }

            $settings['company_telephone'] = $settings['company_telephone'] != '' ? ", " . $settings['company_telephone'] : '';
            $settings['company_state']     = $settings['company_state'] != '' ? ", " . $settings['company_state'] : '';

            $userdetails =
                '<h2 class="h6">' . ucfirst($details['user']['name']) . '<h2>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_name'] . $settings['company_telephone'] . '</p>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_address'] . '</p>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_city'] . $settings['company_state'] . '</p>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_country'] . '</p>' .
                '<p class="m-0 h6 font-weight-normal">' . $settings['company_zipcode'] . '</p>';

            $details['customer']['details'] = $customerdetails;

            $details['user']['details'] = $userdetails;

            $sales = $sell->itemsArray();

            return view('sales.sale_invoice', compact('sales', 'details', 'user','sell'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
