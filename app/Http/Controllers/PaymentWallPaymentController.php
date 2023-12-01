<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\UsersCoupons;
use App\Models\client;
use Illuminate\Support\Facades\Auth;

class PaymentWallPaymentController extends Controller
{

    public $secret_key;
    public $public_key;
    public $is_enabled;

    public function paymentwall(Request $request)
    {
        $data = $request->all();
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        return view('plans.paymentwall', compact('data', 'admin_payment_setting'));
    }


    public function paymentConfig($user)
    {
        if (Auth::check()) {
            $user = Auth::user();
        }
        if ($user->type == 'isOwner') {
            $payment_setting = Utility::getAdminPaymentSetting();
        } else {
            $payment_setting = Utility::getCompanyPaymentSetting();
        }

        $this->secret_key = isset($payment_setting['paymentwall_private_key ']) ? $payment_setting['paymentwall_private_key  '] : '';
        $this->public_key = isset($payment_setting['paymentwall_public_key']) ? $payment_setting['paymentwall_public_key'] : '';
        $this->is_enabled = isset($payment_setting['is_paymentwall_enabled']) ? $payment_setting['is_paymentwall_enabled'] : 'off';

        return $this;
    }

    public function paymenterror($flag, Request $request)
    {
        if ($flag == 1) {
            return redirect()->route("plans.index")->with('error', __('Transaction has been Successfull! '));
        } else {
            return redirect()->route("plans.index")->with('error', __('Transaction has been failed!'));
        }
    }

    public function planPayWithPaymentwall(Request $request, $plan_id)
    {
        $planID    = \Illuminate\Support\Facades\Crypt::decrypt($plan_id);
     
        $plan      = Plan::find($planID);
        $authuser  = Auth::user();
        $coupon_id = '';

        if ($plan) {
            $price = $plan->price;
            if ($price <= 0) {
                $authuser->plan = $plan->id;
                $authuser->save();

                $assignPlan = $authuser->assignPlan($plan->id);
                if ($assignPlan['is_success'] == true && !empty($plan)) {
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                    Order::create(
                        [
                            'order_id' => $orderID,
                            'name' => null,
                            'email' => null,
                            'card_number' => null,
                            'card_exp_month' => null,
                            'card_exp_year' => null,
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'price' => $price == null ? 0 : $price,
                            'price_currency' => !empty(env('CURRENCY')) ? env('CURRENCY') : 'usd',
                            'txn_id' => '',
                            'payment_type' => __('Flutterwave'),
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );
                    $res['msg']  = __("Plan successfully upgraded.");
                    $res['flag'] = 2;

                    return $res;
                }
            } else {
                $orderID = time();
                \Paymentwall_Config::getInstance()->set(array(
                    'private_key' => 'sdrsefrszdef'
                ));
                $parameters = $request->all();
                $chargeInfo = array(
                    'email' => $parameters['email'],
                    'history[registration_date]' => '1489655092',
                    'amount' => $price,
                    'currency' => !empty($this->currancy) ? $this->currancy : 'USD',
                    'token' => $parameters['brick_token'],
                    'fingerprint' => $parameters['brick_fingerprint'],
                    'description' => 'Order #123'
                );
                $charge = new \Paymentwall_Charge();
                $charge->create($chargeInfo);
                $responseData = json_decode($charge->getRawResponseData(), true);
                $response = $charge->getPublicData();

                if ($charge->isSuccessful() and empty($responseData['secure'])) {
                    if ($charge->isCaptured()) {
                        if ($request->has('coupon') && $request->coupon != '') {
                            $coupons = Coupon::find($request->coupon);
                            if (!empty($coupons)) {
                                $userCoupon            = new UsersCoupons();
                                $userCoupon->user_id   = $authuser->id;
                                $userCoupon->coupon_id = $coupons->id;
                                $userCoupon->order_id  = $orderID;
                                $userCoupon->save();
                                $usedCoupun = $coupons->used_coupon();
                                if ($coupons->limit <= $usedCoupun) {
                                    $coupons->is_active = 0;
                                    $coupons->save();
                                }
                            }
                        }
                        $orderID = time();
                        $order                 = new Order();
                        $order->order_id       = $orderID;
                        $order->name           = $authuser->name;
                        $order->card_number    = '';
                        $order->card_exp_month = '';
                        $order->card_exp_year  = '';
                        $order->plan_name      = $plan->name;
                        $order->plan_id        = $plan->id;
                        $order->price          = isset($paydata['amount']) ? $paydata['amount'] : $price;
                        $order->price_currency = $this->currancy;
                        $order->txn_id         = isset($paydata['txid']) ? $paydata['txid'] : 0;
                        $order->payment_type   = __('PaymentWall');
                        $order->payment_status = 'success';
                        $order->receipt        = '';
                        $order->user_id        = $authuser->id;
                        $order->save();
                        $assignPlan = $authuser->assignPlan($plan->id);
                        if ($assignPlan['is_success']) {
                            $res['msg'] = __("Plan successfully upgraded.");
                            $res['flag'] = 1;
                            return $res;
                        }
                    } elseif ($charge->isUnderReview()) {
                        // decide on risk charge
                    }
                } elseif (!empty($responseData['secure'])) {
                    $response = json_encode(array('secure' => $responseData['secure']));
                } else {
                    $errors = json_decode($response, true);
                    $res['flag'] = 2;
                    return $res;
                }
                echo $response;
            }
        }
    }
}
