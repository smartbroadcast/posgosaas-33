<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Plan;
use App\Models\UsersCoupons;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->can('Manage Coupon'))
        {
            $coupons = Coupon::all();
            return view('coupons.index', compact('coupons'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->can('Create Coupon'))
        {
            return view('coupons.create');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::user()->can('Create Coupon'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                                   'discount' => 'required|numeric',
                                   'limit' => 'required|numeric',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            if(empty($request->manualCode) && empty($request->autoCode))
            {
                return redirect()->back()->with('error', __('Coupon code is required'));
            }

            $coupon           = new Coupon();
            $coupon->name     = $request->name;
            $coupon->discount = $request->discount;
            $coupon->limit    = $request->limit;

            if(!empty($request->manualCode))
            {
                $coupon->code = strtoupper($request->manualCode);
            }

            if(!empty($request->autoCode))
            {
                $coupon->code = $request->autoCode;
            }

            $coupon->save();

            return redirect()->route('coupons.index')->with('success', __('Coupon created successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Coupon $coupon
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Coupon $coupon)
    {
        $userCoupons = UsersCoupons::where('coupon_id', $coupon->id)->get();

        return view('coupons.view', compact('userCoupons', 'coupon'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Coupon $coupon
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Coupon $coupon)
    {
        if(Auth::user()->can('Edit Coupon'))
        {
            return view('coupons.edit', compact('coupon'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Coupon $coupon
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coupon $coupon)
    {
        if(Auth::user()->can('Edit Coupon'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                                   'discount' => 'required|numeric',
                                   'limit' => 'required|numeric',
                                   'code' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $coupon           = Coupon::find($coupon->id);
            $coupon->name     = $request->name;
            $coupon->discount = $request->discount;
            $coupon->limit    = $request->limit;
            $coupon->code     = $request->code;

            $coupon->save();

            return redirect()->route('coupons.index')->with('success', __('Coupon updated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Coupon $coupon
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coupon $coupon)
    {
        if(Auth::user()->can('Delete Coupon'))
        {
            $coupon->delete();

            return redirect()->route('coupons.index')->with('success', __('Coupon deleted successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function applyCoupon(Request $request)
    {
        $plan_id = Crypt::decrypt($request->plan_id);
        $plan    = Plan::find($plan_id);
        if($plan && $request->coupon != '')
        {
            $original_price = self::formatPrice($plan->price);
            $coupons        = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
            if(!empty($coupons))
            {
                $usedCoupun = $coupons->used_coupon();

                if($coupons->limit == $usedCoupun)
                {
                    return response()->json(
                        [
                            'is_success' => false,
                            'final_price' => $original_price,
                            'price' => number_format($plan->price, 2),
                            'message' => __('This coupon code has expired.'),
                        ]
                    );
                }
                else
                {
                    $discount_value = ($plan->price / 100) * $coupons->discount;
                    $plan_price     = $plan->price - $discount_value;
                    $price          = self::formatPrice($plan->price - $discount_value);
                    $discount_value = '-' . self::formatPrice($discount_value);

                    return response()->json(
                        [
                            'is_success' => true,
                            'discount_price' => $discount_value,
                            'final_price' => $price,
                            'price' => number_format($plan_price, 2),
                            'message' => __('Coupon code has applied successfully.'),
                        ]
                    );
                }
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'final_price' => $original_price,
                        'price' => number_format($plan->price, 2),
                        'message' => __('This coupon code is invalid or has expired.'),
                    ]
                );
            }
        }
    }

    public function formatPrice($price)
    {
        return env('CURRENCY_SYMBOL') . number_format($price, 2);
    }
}
