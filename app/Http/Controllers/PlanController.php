<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('Manage Plan'))
        {
            $plans = Plan::get();
            $admin_payment_setting = Utility::getAdminPaymentSetting();            

            return view('plans.index', compact('plans', 'admin_payment_setting'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->can('Create Plan'))
        {
            $arrDuration = Plan::$arrDuration;

            return view('plans.create', compact('arrDuration'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->can('Create Plan'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'name' => 'required|unique:plans',
                                   'price' => 'required|numeric|min:0',
                                   'duration' => 'required',
                                   'max_users' => 'required|numeric',
                                   'max_customers' => 'required|numeric',
                                   'max_vendors' => 'required|numeric',
                               ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $plan       = new Plan();
            $plan->name = $request->name;
            if($request->price > 0)
            {
                $admin_payment_setting = Utility::getAdminPaymentSetting();
                if(!empty($admin_payment_setting)  &&  ($admin_payment_setting['is_stripe_enabled'] == 'on' || $admin_payment_setting['is_paypal_enabled'] == 'on' || $admin_payment_setting['is_paystack_enabled'] == 'on' || $admin_payment_setting['is_flutterwave_enabled'] == 'on' || $admin_payment_setting['is_razorpay_enabled'] == 'on' || $admin_payment_setting['is_mercado_enabled'] == 'on' || $admin_payment_setting['is_paytm_enabled'] == 'on' || $admin_payment_setting['is_mollie_enabled'] == 'on' || $admin_payment_setting['is_skrill_enabled'] == 'on' || $admin_payment_setting['is_coingate_enabled'] == 'on'))
                {


                    $plan->price = $request->price;
                }
                else
                {
                    return redirect()->back()->with('error', __('Please set stripe/paypal api key & secret key for add new plan'));
                }
            }

            $plan->duration      = $request->duration;
            $plan->max_users     = $request->max_users;
            $plan->max_customers = $request->max_customers;
            $plan->max_vendors   = $request->max_vendors;
            $plan->description   = $request->description;

            if($request->hasFile('image'))
            {
                $validator = Validator::make(
                    $request->all(), [
                                       'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20480',
                                   ]
                );

                if($validator->fails())
                {
                    return redirect()->back()->with('error', $validator->errors()->first());
                }

                $filenameWithExt = $request->file('image')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('image')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $filepath        = $request->file('image')->storeAs('plans', $fileNameToStore);
                $plan->image     = $filepath;
            }
            $plan->save();
                     
            return redirect()->route('plans.index')->with('success', __('Plan added successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($plan_id)
    {
        if(Auth::user()->can('Edit Plan'))
        {
            $arrDuration = Plan::$arrDuration;
            $plan        = Plan::find($plan_id);

            return view('plans.edit', compact('plan', 'arrDuration'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, Plan $plan)
    {
        if(Auth::user()->can('Edit Plan'))
        {
            if(!empty($plan))
            {
                $validator = Validator::make(
                    $request->all(), [
                                       'name' => 'required|unique:plans,name,' . $plan->id,
                                       'duration' => 'required',
                                       'max_users' => 'required|numeric',
                                       'max_customers' => 'required|numeric',
                                       'max_vendors' => 'required|numeric',
                                   ]
                );

                if($validator->fails())
                {
                    return redirect()->back()->with('error', $validator->errors()->first());
                }

                $plan->name = $request->name;
                if($request->price > 0)
                {
                    $admin_payment_setting = Utility::getAdminPaymentSetting();
                    if($admin_payment_setting['is_stripe_enabled'] == 'on' || $admin_payment_setting['is_paypal_enabled'] == 'on' || $admin_payment_setting['is_paystack_enabled'] == 'on' || $admin_payment_setting['is_flutterwave_enabled'] == 'on' || $admin_payment_setting['is_razorpay_enabled'] == 'on' || $admin_payment_setting['is_mercado_enabled'] == 'on' || $admin_payment_setting['is_paytm_enabled'] == 'on' || $admin_payment_setting['is_mollie_enabled'] == 'on' || $admin_payment_setting['is_skrill_enabled'] == 'on' || $admin_payment_setting['is_coingate_enabled'] == 'on')
                    {

                        $plan->price = $request->price;
                    }
                    else
                    {
                        return redirect()->back()->with('error', __('Please set stripe/paypal api key & secret key for add new plan'));
                    }
                }
                $plan->duration      = $request->duration;
                $plan->max_users     = $request->max_users;
                $plan->max_customers = $request->max_customers;
                $plan->max_vendors   = $request->max_vendors;
                $plan->description   = $request->description;

                if($request->hasFile('image'))
                {
                    $validator = Validator::make(
                        $request->all(), [
                                           'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20480',
                                       ]
                    );

                    if($validator->fails())
                    {
                        return redirect()->back()->with('error', $validator->errors()->first());
                    }

                    $oldfilepath = $plan->image;
                    if(asset(Storage::exists($oldfilepath)))
                    {
                        asset(Storage::delete($oldfilepath));
                    }

                    $filenameWithExt = $request->file('image')->getClientOriginalName();
                    $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension       = $request->file('image')->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                    $filepath        = $request->file('image')->storeAs('plans', $fileNameToStore);
                    $plan->image     = $filepath;
                }

                $plan->save();

                return redirect()->route('plans.index')->with('success', __('Plan updated successfully.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Plan not found.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function userPlan(Request $request)
    {
        $user   = Auth::user();
        $planID = Crypt::decrypt($request->code);
        $plan   = Plan::find($planID);
        if($plan)
        {
            if($plan->price <= 0)
            {
                $user->assignPlan($plan->id);

                return redirect()->route('plans.index')->with('success', __('Plan Successfully activated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Something is wrong.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Plan not found.'));
        }
    }
}
