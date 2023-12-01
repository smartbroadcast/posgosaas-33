<?php

namespace App\Http\Controllers;

use App\Models\LandingPageSection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Utility;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\Branch;
use App\Models\BranchSalesTarget;
use App\Models\CashRegister;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Todo;
use App\Models\Vendor;
use App\Models\Calendar;

class HomeController extends Controller
{
    function index()
    {
        if (\Auth::check()) {
            $authuser = Auth::user();

            $user_id = $authuser->getCreatedBy();

            $low_stock = (int)Utility::settings()['low_product_stock_threshold'];

            $branches = Branch::select('id')->where('created_by', '=', $user_id)->count();

            $cashregisters = CashRegister::select('cash_registers.id')
                ->leftjoin('branches', 'branches.id', '=', 'cash_registers.branch_id')
                ->where('branches.created_by', '=', $user_id)
                ->count();

            $productObj = Product::getallproducts();

            $productscount = $productObj->count();

            $lowstockproducts = [];

            if ($productscount > 0) {

                foreach ($productObj->get() as $key => $product) {

                    $productquantity = $product->getTotalProductQuantity();

                    if ($productquantity <= $low_stock) {
                        $lowstockproducts[] = [
                            'name' => $product->name,
                            'quantity' => $productquantity
                        ];
                    }
                }
            }

            //   Dashboard calendar 
            $events    = Calendar::where('created_by', '=', \Auth::user()->getCreatedBy())->get();
            $now = date('m');
            $current_month_event = Calendar::select('id', 'start', 'end', 'title', 'created_at', 'className')->whereRaw('MONTH(start)=' . $now)->get();

            $arrEvents = [];
            foreach ($events as $event) {

                $arr['id']    = $event['id'];
                $arr['title'] = $event['title'];
                $arr['start'] = $event['start'];
                $arr['end']   = $event['end'];
                $arr['className'] = $event['className'];
                $arr['url']             = route('calendars.show', $event['id']);

                $arrEvents[] = $arr;
            }
            $arrEvents =  json_encode($arrEvents);
            //   Dashboard calendar 

            $notifications = Notification::getAllNotifications();

            $customers = Customer::select('id')->where('created_by', '=', $user_id)->count();

            $vendors = Vendor::select('id')->where('created_by', '=', $user_id)->count();

            $monthlySelledAmount = Sale::totalSelledAmount(true);
            $totalSelledAmount   = Sale::totalSelledAmount();

            $monthlyPurchasedAmount = Purchase::totalPurchasedAmount(true);
            $totalPurchasedAmount   = Purchase::totalPurchasedAmount();

            $purchasesArray = Purchase::getPurchaseReportChart();

            $salesArray = Sale::getSalesReportChart();

            $todos = Todo::where('created_by', '=', Auth::user()->id)->orderBy('id', 'DESC')->get();

            $saletarget = BranchSalesTarget::getBranchTargets(true);

            $homes = [
                'branches',
                'cashregisters',
                'productscount',
                'lowstockproducts',
                'notifications',
                'customers',
                'vendors',
                'monthlySelledAmount',
                'totalSelledAmount',
                'monthlyPurchasedAmount',
                'totalPurchasedAmount',
                'purchasesArray',
                'salesArray',
                'todos',
                'saletarget',
            ];

            $getOrderChart     = $this->getOrderChart(['duration' => 'week']);
            $ownersCount       = User::totalOwners();
            $paidOwnersCount   = User::countPaidOwners();
            $ordersCount       = Order::totalOrders();
            $ordersPrice       = Order::totalOrdersPrice();
            $plansCount        = Plan::totalPlan();
            $mostPurchasedPlan = Plan::most_purchased_plan();

            $sa = [
                'getOrderChart',
                'ownersCount',
                'ordersCount',
                'ordersPrice',
                'plansCount',
                'paidOwnersCount',
                'mostPurchasedPlan',
            ];

            if (Auth::user()->isSuperAdmin()) {
                return view('sa-dashboard', compact($sa));
            }

            return view('dashboard', compact($homes, 'arrEvents'));
        } else {
            if (!file_exists(storage_path() . "/installed")) {
                header('location:install');
                die;
            } else {
                if (env('DISPLAY_LANDING') == 'on') {
                    $plans = Plan::get();
                    $get_section = LandingPageSection::orderBy('section_order', 'ASC')->get();
                    return view('layouts.landing', compact('plans', 'get_section'));
                } else {
                    return redirect('login');
                }
            }
        }
    }

    public function getOrderChart(array $arrParam)
    {
        $arrDuration = [];
        if ($arrParam['duration']) {
            if ($arrParam['duration'] == 'week') {
                $previous_week = strtotime("-2 week +1 day");

                for ($i = 0; $i < 14; $i++) {
                    $arrDuration[date('Y-m-d', $previous_week)] = date('d-M', $previous_week);

                    $previous_week = strtotime(date('Y-m-d', $previous_week) . " +1 day");
                }
            }
        }
        $arrTask = [];
        foreach ($arrDuration as $date => $label) {
            $data               = Order::select(DB::raw('count(*) as total'))->whereDate('created_at', '=', $date)->first();
            $arrTask['label'][] = $label;
            $arrTask['data'][]  = $data->total;
        }

        return $arrTask;
    }

    public function changeMode()
    {
        $usr = Auth::user();
        $usr->mode  = $usr->mode == 'light' ? 'dark' : 'light';
        $usr->save();

        return redirect()->back();
    }
}
