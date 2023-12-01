<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UsersCoupons;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;

// use PayPal\Api\Amount;
// use PayPal\Api\Item;
// use PayPal\Api\ItemList;
// use PayPal\Api\Payer;
// use PayPal\Api\Payment;
// use PayPal\Api\PaymentExecution;
// use PayPal\Api\RedirectUrls;
// use PayPal\Api\Transaction;
// use PayPal\Auth\OAuthTokenCredential;
// use PayPal\Rest\ApiContext;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller
{
    private $_api_context;

    public function paymentConfig()
    {
        $user = \Auth::user();
        if (\Auth::user()->isOwner()) {
            $payment_setting = Utility::getAdminPaymentSetting();
        } else {
            $payment_setting = Utility::getCompanyPaymentSetting();
        }
        config(
            [
                'paypal.sandbox.client_id' => isset($payment_setting['paypal_client_id']) ? $payment_setting['paypal_client_id'] : '',
                'paypal.sandbox.client_secret' => isset($payment_setting['paypal_secret_key']) ? $payment_setting['paypal_secret_key'] : '',
                'paypal.mode' => isset($payment_setting['paypal_mode']) ? $payment_setting['paypal_mode'] : '',
            ]
        );
        // dd($payment_setting);
    }

      
    public function planPayWithPaypal(Request $request)
    {   
        // dd($request->all());
        $this->paymentConfig();
        // $setting
        

        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan   = Plan::find($planID);

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $get_amount = $plan->price;

        if ($plan) {
            try {
                $coupon_id = 0;
                $price     = $plan->price;
                if (!empty($request->coupon)) {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    // dd($coupons);
                    if (!empty($coupons)) {
                        $usedCoupun = $coupons->used_coupon();
                        $discount_value = ($plan->price / 100) * $coupons->discount;
                        $price = $plan->price - $discount_value;
                        if ($coupons->limit == $usedCoupun) {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
                        $coupon_id = $coupons->id;

                        if( $price<1){
                            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                            $user = Auth::user();
                            $order                 = new Order();
                            $order->order_id       = $orderID;
                            $order->name           = $user->name;
                            $order->card_number    = '';
                            $order->card_exp_month = '';
                            $order->card_exp_year  = '';
                            $order->plan_name      = $plan->name;
                            $order->plan_id        = $plan->id;
                            $order->price          =  $price;
                            $order->price_currency = env('CURRENCY');
                            $order->txn_id         = 'null';
                            $order->payment_type   = __('PAYPAL');
                            $order->payment_status = 'success';
                            $order->receipt        = '';
                            $order->user_id        = $user->id;

                            $order->save();

                            $assignPlan = $user->assignPlan($plan->id);

                            if($assignPlan['is_success'])
                            {
                                return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                            }
                            else
                            {
                                return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                            }

                        }
                    } else {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }
                

                $paypalToken = $provider->getAccessToken();
                $response = $provider->createOrder([
                    "intent" => "CAPTURE",
                    "application_context" => [
                        "return_url" => route('plan.get.payment.status', [$plan->id, $price, $coupon_id]),
                        "cancel_url" => route('plan.get.payment.status', [$plan->id, $price, $coupon_id]),
                    ],
                    "purchase_units" => [
                        0 => [
                            "amount" => [
                                "currency_code" => 'USD',
                                "value" => $get_amount,
                            ],
                        ],
                    ],
                    ]);
                    // dd($response);
                if (isset($response['id']) && $response['id'] != null) {
                    // redirect to approve href
                    foreach ($response['links'] as $links) {
                        if ($links['rel'] == 'approve') {
                            return redirect()->away($links['href']);
                        }
                    }
                    return redirect()
                        ->route('plans.index')
                        ->with('error', 'Something went wrong.');
                } else {
                    return redirect()
                        ->route('plans.index')
                        ->with('error', $response['message'] ?? 'Something went wrong.');
                }
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', __($e->getMessage()));
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function planGetPaymentStatus(Request $request, $plan_id,$amount,$coupon_id)
    {
        // dd($coupon_id);
        $this->paymentconfig();
        $user = Auth::user();
        $plan = Plan::find($plan_id);
        if ($plan) {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($request['token']);
            $payment_id = Session::get('paypal_payment_id');
            $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

                if ($coupon_id != '') {
                    $coupons = Coupon::find($coupon_id);
// dd($coupons);
                    if (!empty($coupons)) {

                        $userCoupon         = new UsersCoupons();
                        $userCoupon->user_id   = $user->id;
                        $userCoupon->coupon_id = $coupons->id;
                        $userCoupon->order_id  = $order_id;
                        $userCoupon->save();
                        $usedCoupun = $coupons->used_coupon();
                        if ($coupons->limit <= $usedCoupun) {
                            $coupons->is_active = 0;
                            $coupons->save();
                        }
                    }
                }
                // if ($result['state'] == 'approved') {
                    if (isset($response['status']) && $response['status'] == 'COMPLETED') 
                    {
                        if ($response['status'] == 'COMPLETED') {
                            $statuses = __('successfull');
                        }
                        $order                 = new Order();
                        $order->order_id       = $order_id;
                        $order->name           = $user->name;
                        $order->card_number    = '';
                        $order->card_exp_month = '';
                        $order->card_exp_year  = '';
                        $order->plan_name      = $plan->name;
                        $order->plan_id        = $plan->id;
                        $order->price          = $amount;
                        $order->price_currency = env('CURRENCY');
                        // $order->txn_id         = $payment_id;
                        $order->txn_id         = isset($request->TXNID) ? $request->TXNID : '';
                        $order->payment_type   = __('PAYPAL');
                        $order->payment_status = $statuses;
                        $order->receipt        = '';
                        $order->user_id        = $user->id;
                        $order->save();
                        $assignPlan = $user->assignPlan($plan->id);
                        if ($assignPlan['is_success']) {
                            return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                        } else {
                            return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                        }
                    }
      
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function customerPayWithPaypal(Request $request, $invoice_id)
    {
        $settings = DB::table('settings')->where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('value', 'name');
        $user     = \Auth::user();

        $get_amount = $request->amount;

        $request->validate(['amount' => 'required|numeric|min:0']);


        $invoice = Invoice::find($invoice_id);

        if ($invoice) {
            if ($get_amount > $invoice->getDue()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {
                $this->setApiContext();

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                $name = Utility::invoiceNumberFormat($settings, $invoice->invoice_id);

                $payer = new Payer();
                $payer->setPaymentMethod('paypal');

                $item_1 = new Item();
                $item_1->setName($name)->setCurrency(Utility::getValByName('site_currency'))->setQuantity(1)->setPrice($get_amount);

                $item_list = new ItemList();
                $item_list->setItems([$item_1]);

                $amount = new Amount();
                $amount->setCurrency(Utility::getValByName('site_currency'))->setTotal($get_amount);

                $transaction = new Transaction();
                $transaction->setAmount($amount)->setItemList($item_list)->setDescription($name)->setInvoiceNumber($orderID);

                $redirect_urls = new RedirectUrls();
                $redirect_urls->setReturnUrl(
                    route(
                        'customer.get.payment.status',
                        $invoice->id
                    )
                )->setCancelUrl(
                    route(
                        'customer.get.payment.status',
                        $invoice->id
                    )
                );

                $payment = new Payment();
                $payment->setIntent('Sale')->setPayer($payer)->setRedirectUrls($redirect_urls)->setTransactions([$transaction]);

                try {

                    $payment->create($this->_api_context);
                } catch (\PayPal\Exception\PayPalConnectionException $ex) //PPConnectionException
                {
                    if (\Config::get('app.debug')) {
                        return redirect()->back()->with('error', __('Connection timeout'));
                    } else {
                        return redirect()->back()->with('error', __('Some error occur, sorry for inconvenient'));
                    }
                }
                foreach ($payment->getLinks() as $link) {
                    if ($link->getRel() == 'approval_url') {
                        $redirect_url = $link->getHref();
                        break;
                    }
                }
                Session::put('paypal_payment_id', $payment->getId());
                if (isset($redirect_url)) {
                    return Redirect::away($redirect_url);
                }

                return redirect()->back()->with('error', __('Unknown error occurred'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customerGetPaymentStatus(Request $request, $invoice_id)
    {
        $settings = DB::table('settings')->where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('value', 'name');
        $user     = \Auth::user();

        $invoice = Invoice::find($invoice_id);

        $this->setApiContext();

        $payment_id = Session::get('paypal_payment_id');

        Session::forget('paypal_payment_id');

        if (empty($request->PayerID || empty($request->token))) {
            return redirect()->back()->with('error', __('Payment failed'));
        }

        $payment = Payment::get($payment_id, $this->_api_context);

        $execution = new PaymentExecution();
        $execution->setPayerId($request->PayerID);

        try {
            $result   = $payment->execute($execution, $this->_api_context)->toArray();
            $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
            $status   = ucwords(str_replace('_', ' ', $result['state']));
            if ($result['state'] == 'approved') {
                $amount = $result['transactions'][0]['amount']['total'];
            } else {
                $amount = isset($result['transactions'][0]['amount']['total']) ? $result['transactions'][0]['amount']['total'] : '0.00';
            }


            if ($result['state'] == 'approved') {
                $payments = InvoicePayment::create(
                    [

                        'invoice_id' => $invoice->id,
                        'date' => date('Y-m-d'),
                        'amount' => $amount,
                        'account_id' => 0,
                        'payment_method' => 0,
                        'order_id' => $order_id,
                        'currency' => Utility::getValByName('site_currency'),
                        'txn_id' => $payment_id,
                        'payment_type' => __('PAYPAL'),
                        'receipt' => '',
                        'reference' => '',
                        'description' => 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id),
                    ]
                );

                if ($invoice->getDue() <= 0) {
                    $invoice->status = 4;
                    $invoice->save();
                } elseif (($invoice->getDue() - $payments->amount) == 0) {
                    $invoice->status = 4;
                    $invoice->save();
                } else {
                    $invoice->status = 3;
                    $invoice->save();
                }

                $invoicePayment              = new Transaction();
                $invoicePayment->user_id     = $invoice->customer_id;
                $invoicePayment->user_type   = 'Customer';
                $invoicePayment->type        = 'PAYPAL';
                $invoicePayment->created_by  = \Auth::user()->id;
                $invoicePayment->payment_id  = $invoicePayment->id;
                $invoicePayment->category    = 'Invoice';
                $invoicePayment->amount      = $amount;
                $invoicePayment->date        = date('Y-m-d');
                $invoicePayment->created_by  = \Auth::user()->creatorId();
                $invoicePayment->payment_id  = $payments->id;
                $invoicePayment->description = 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id);
                $invoicePayment->account     = 0;

                \App\Transaction::addTransaction($invoicePayment);

                Utility::userBalance('customer', $invoice->customer_id, $request->amount, 'debit');

                Utility::bankAccountBalance($request->account_id, $request->amount, 'credit');

                return redirect()->back()->with('success', __('Payment successfully added'));
            } else {
                return redirect()->back()->with('error', __('Transaction has been ' . $status));
            }
        } catch (\Exception $e) {

            return redirect()->back()->with('error', __('Transaction has been failed.'));
        }
    }
}
