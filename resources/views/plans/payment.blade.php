@extends('layouts.app')

@section('page-title')
    {{ __('Order Summary') }}
@endsection

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Order Summary') }}</h5>
    </div>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('plans.index') }}">{{ __('plan') }}</a></li>
    <li class="breadcrumb-item">{{ __('Order Summary') }}</li>
@endsection

@section('content')
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="sticky-top" style="top:30px">
                        <div class="card ">
                            <div class="list-group list-group-flush" id="useradd-sidenav">

                                @if ($admin_payment_setting['is_stripe_enabled'] == 'on' && !empty($admin_payment_setting['stripe_key']) && !empty($admin_payment_setting['stripe_secret']))
                                    <a href="#stripe_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Stripe') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                @endif

                                @if ($admin_payment_setting['is_paypal_enabled'] == 'on' && !empty($admin_payment_setting['paypal_client_id']) && !empty($admin_payment_setting['paypal_secret_key']))
                                    <a href="#paypal_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Paypal') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                @endif

                                @if ($admin_payment_setting['is_paystack_enabled'] == 'on' && !empty($admin_payment_setting['paystack_public_key']) && !empty($admin_payment_setting['paystack_secret_key']))
                                    <a href="#paystack_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Paystack') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif


                                @if (isset($admin_payment_setting['is_flutterwave_enabled']) && $admin_payment_setting['is_flutterwave_enabled'] == 'on')
                                    <a href="#flutterwave_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Flutterwave') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if (isset($admin_payment_setting['is_razorpay_enabled']) && $admin_payment_setting['is_razorpay_enabled'] == 'on')
                                    <a href="#razorpay_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Razorpay') }} <div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if (isset($admin_payment_setting['is_mercado_enabled']) && $admin_payment_setting['is_mercado_enabled'] == 'on')
                                    <a href="#mercado_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Mercado Pago') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if (isset($admin_payment_setting['is_paytm_enabled']) && $admin_payment_setting['is_paytm_enabled'] == 'on')
                                    <a href="#paytm_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Paytm') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                @endif

                                @if (isset($admin_payment_setting['is_mollie_enabled']) && $admin_payment_setting['is_mollie_enabled'] == 'on')
                                    <a href="#mollie_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Mollie') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if (isset($admin_payment_setting['is_skrill_enabled']) && $admin_payment_setting['is_skrill_enabled'] == 'on')
                                    <a href="#skrill_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Skrill') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if (isset($admin_payment_setting['is_coingate_enabled']) && $admin_payment_setting['is_coingate_enabled'] == 'on')
                                    <a href="#coingate_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Coingate') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if (isset($admin_payment_setting['is_paymentwall_enabled']) && $admin_payment_setting['is_paymentwall_enabled'] == 'on')
                                    <a href="#paymentwall_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Paymentwall') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif


                            </div>
                        </div>

                        <div class="mt-5">
                            <div class="card price-card price-1 wow animate__fadeInUp" data-wow-delay="0.2s" style="
                                                                                                        visibility: visible;
                                                                                                        animation-delay: 0.2s;
                                                                                                        animation-name: fadeInUp;
                                                                                                      ">
                                <div class="card-body">
                                    <span class="price-badge bg-primary">{{ $plan->name }}</span>

                                    <span
                                        class="mb-2 f-w-600 p-price custom-p-price">{{ (!empty(env('CURRENCY_SYMBOL')) ? env('CURRENCY_SYMBOL') : '$') . $plan->price }}<small
                                            class="text-sm">{{ __('Duration : ') . __(\App\Models\Plan::$arrDuration[$plan->duration]) }}</small></span>

                                    <ul class="list-unstyled my-5">
                                        <li>
                                            <span class="theme-avtar">
                                                <i class="text-primary ti ti-users"></i></span>
                                            {{ $plan->max_users == -1 ? __('Unlimited') : $plan->max_users }}
                                            {{ __('Users') }}
                                        </li>
                                        <li>
                                            <span class="theme-avtar">
                                                <i class="text-primary ti ti-users"></i></span>
                                            {{ $plan->max_customers == -1 ? __('Unlimited') : $plan->max_customers }}
                                            {{ __('Customers') }}
                                        </li>
                                        <li>
                                            <span class="theme-avtar">
                                                <i class="text-primary ti ti-users"></i></span>
                                            {{ $plan->max_vendors == -1 ? __('Unlimited') : $plan->max_vendors }}
                                            {{ __('Vendors') }}
                                        </li>
                                    </ul>

                                </div>

                            </div>
                        </div>
                    </div>


                </div>
                <div class="col-xl-9">

                    <div id="stripe_payment" class="card">
                        <div class="card-header">
                            <h5>{{ __('Stripe') }}</h5>

                        </div>
                        {{-- <div class="card-body"> --}}
                        @if ($admin_payment_setting['is_stripe_enabled'] == 'on' && !empty($admin_payment_setting['stripe_key']) && !empty($admin_payment_setting['stripe_secret']))
                            <div class="tab-pane {{ ($admin_payment_setting['is_stripe_enabled'] == 'on' && !empty($admin_payment_setting['stripe_key']) && !empty($admin_payment_setting['stripe_secret'])) == 'on' ? 'active' : '' }}"
                                id="stripe_payment">
                                <form role="form" action="{{ route('stripe.post') }}" method="post"
                                    class="require-validation" id="payment-form">
                                    @csrf
                                    <div class="border p-3 mb-3 rounded stripe-payment-div">
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <div class="custom-radio">
                                                    <label
                                                        class="font-16 font-weight-bold">{{ __('Credit / Debit Card') }}</label>    
                                                </div>
                                                <p class="mb-0 pt-1 text-sm">
                                                    {{ __('Safe money transfer using your bank account. We support Mastercard, Visa, Discover and American express.') }}
                                                </p>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="card-name-on"
                                                        class="col-form-label text-dark">{{ __('Name on card') }}</label>
                                                    <input type="text" name="name" id="card-name-on"
                                                        class="form-control required"
                                                        placeholder="{{ \Auth::user()->name }}">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div id="card-element">
                                                    <!-- A Stripe Element will be inserted here. -->
                                                </div>
                                                <div id="card-errors" role="alert"></div>
                                            </div>
                                            <div class="col-md-12 mt-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="form-group w-100">
                                                        <input type="text" id="stripe_coupon" name="coupon"
                                                            class="form-control coupon"
                                                            placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>
                                                    <div class="form-group ms-3 mt-2">
                                                        <a href="#"
                                                            class="btn btn-print-invoice btn-lg  btn-primary m-b-10 m-r-10 apply-coupon"
                                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"><i
                                                                class="" data-feather="save"> </i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="error" style="display: none;">
                                                    <div class='alert-danger alert'>
                                                        {{ __('Please correct the errors and try again.') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-sm-right text-end">
                                            <input type="hidden" name="plan_id"
                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                            <input type="submit" value="{{ __('Pay Now') }}"
                                                class="btn btn-xs btn-primary ">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                        {{-- </div> --}}
                    </div>

                    <div id="paypal_payment" class="card">
                        <div class="card-header">
                            <h5>{{ __('Paypal') }}</h5>
                        </div>
                        {{-- <div class="card-body"> --}}
                        @if ($admin_payment_setting['is_paypal_enabled'] == 'on' && !empty($admin_payment_setting['paypal_client_id']) && !empty($admin_payment_setting['paypal_secret_key']))
                            <div class="tab-pane {{ ($admin_payment_setting['is_stripe_enabled'] != 'on' && $admin_payment_setting['is_paypal_enabled'] == 'on' && !empty($admin_payment_setting['paypal_client_id']) && !empty($admin_payment_setting['paypal_secret_key'])) == 'on' ? 'active' : '' }}"
                                id="paypal_payment">
                                {{-- <div class="card"> --}}
                                <form class="w3-container w3-display-middle w3-card-4" method="POST" id="payment-form"
                                    action="{{ route('plan.pay.with.paypal') }}">
                                    @csrf
                                    <input type="hidden" name="plan_id"
                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                    <div class="border p-3 mb-3 rounded">
                                        <div class="row">
                                            <div class="col-md-12 mt-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="form-group w-100">
                                                        <label for="paypal_coupon"
                                                            class="form-control-label col-form-label">{{ __('Coupon') }}</label>
                                                        <input type="text" id="paypal_coupon" name="coupon"
                                                            class="form-control coupon"
                                                            placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>

                                                    <div class="form-group ms-3 mt-3">
                                                        <a href="#"
                                                            class="btn btn-print-invoice  mt-4 btn-primary  m-r-10 apply-coupon"
                                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"><i
                                                                class="" data-feather="save"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>




                                    <div class="form-group mt-3">
                                        <div class="col-sm-12 my-2 px-2">
                                            <div class="text-sm-right text-end">
                                                <input type="submit" value="{{ __('Pay Now') }}"
                                                    class="btn btn-xs btn-primary">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                {{-- </div> --}}
                            </div>
                        @endif
                        {{-- </div> --}}
                    </div>


                    <div id="paystack_payment" class="card">
                        <div class="card-header">
                            <h5>{{ __('Paystack') }}</h5>

                        </div>
                        {{-- <div class="card-body"> --}}
                        @if ($admin_payment_setting['is_paystack_enabled'] == 'on' && !empty($admin_payment_setting['paystack_public_key']) && !empty($admin_payment_setting['paystack_secret_key']))
                            <div class="tab-pane " id="paystack_payment">

                                <form class="w3-container w3-display-middle w3-card-4" method="POST"
                                    id="paystack-payment-form" action="{{ route('plan.pay.with.paystack') }}">
                                    @csrf
                                    <input type="hidden" name="plan_id"
                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                    <div class="border p-3 mb-3 rounded">
                                        <div class="row">
                                            <div class="col-md-12 mt-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="form-group w-100">
                                                        <label for="paypal_coupon"
                                                            class="col-form-label text-dark">{{ __('Coupon') }}</label>
                                                        <input type="text" id="paystack_coupon" name="coupon"
                                                            class="form-control coupon" data-from="paystack"
                                                            placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>

                                                    <div class="form-group ms-3 mt-3">
                                                        <a href="#"
                                                            class="btn btn-print-invoice  mt-4 btn-primary  m-r-10 apply-coupon"
                                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"><i
                                                                class="" data-feather="save"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mt-3">
                                        <div class="col-sm-12 my-2 px-2">
                                            <div class="text-sm-right text-end">
                                                <input type="button" id="pay_with_paystack" value="{{ __('Pay Now') }}"
                                                    class="btn btn-xs btn-primary">
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        @endif
                        {{-- </div> --}}
                    </div>


                    <div id="flutterwave_payment" class="card">
                        <div class="card-header">
                            <h5>{{ __('Flutterwave') }}</h5>

                        </div>
                        {{-- <div class="card-body"> --}}
                        @if (isset($admin_payment_setting['is_flutterwave_enabled']) && $admin_payment_setting['is_flutterwave_enabled'] == 'on')
                            <div class="tab-pane " id="flutterwave_payment">

                                <form role="form" action="{{ route('plan.pay.with.flaterwave') }}" method="post"
                                    class="require-validation" id="flaterwave-payment-form">
                                    @csrf
                                    <input type="hidden" name="plan_id"
                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                    <div class="border p-3 mb-3 rounded">
                                        <div class="row">
                                            <div class="col-md-12 mt-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="form-group w-100">
                                                        <label for="paypal_coupon"
                                                            class="col-form-label text-dark">{{ __('Coupon') }}</label>
                                                        <input type="text" id="flaterwave_coupon" name="coupon"
                                                            class="form-control coupon"
                                                            placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>

                                                    <div class="form-group ms-3 mt-3">
                                                        <a href="#"
                                                            class="btn btn-print-invoice  mt-4 btn-primary  m-r-10 apply-coupon"
                                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"><i
                                                                class="" data-feather="save"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-group mt-3">
                                        <div class="col-sm-12 my-2 px-2">
                                            <div class="text-sm-right text-end">
                                                <input type="button" id="pay_with_flaterwave" value="{{ __('Pay Now') }}"
                                                    class="btn btn-xs btn-primary">
                                            </div>
                                        </div>
                                    </div>
                                </form>



                            </div>
                        @endif
                        {{-- </div> --}}
                    </div>

                    <div id="razorpay_payment" class="card">
                        <div class="card-header">
                            <h5>{{ __('Razorpay') }} </h5>

                        </div>
                        {{-- <div class="card-body"> --}}
                        @if (isset($admin_payment_setting['is_razorpay_enabled']) && $admin_payment_setting['is_razorpay_enabled'] == 'on')
                            <div class="tab-pane " id="razorpay_payment">

                                <form role="form" action="{{ route('plan.pay.with.razorpay') }}" method="post"
                                    class="require-validation" id="razorpay-payment-form">
                                    @csrf
                                    <input type="hidden" name="plan_id"
                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                    <div class="border p-3 mb-3 rounded">
                                        <div class="row">
                                            <div class="col-md-12 mt-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="form-group w-100">
                                                        <label for="paypal_coupon"
                                                            class="col-form-label text-dark">{{ __('Coupon') }}</label>
                                                        <input type="text" id="razorpay_coupon" name="coupon"
                                                            class="form-control coupon" data-from="razorpay"
                                                            placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>

                                                    <div class="form-group ms-3 mt-3">
                                                        <a href="#"
                                                            class="btn btn-print-invoice  mt-4 btn-primary  m-r-10 apply-coupon"
                                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"><i
                                                                class="" data-feather="save"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mt-3">
                                        <div class="col-sm-12 my-2 px-2">
                                            <div class="text-sm-right text-end">
                                                <input type="button" id="pay_with_razorpay" value="{{ __('Pay Now') }}"
                                                    class="btn btn-xs btn-primary">
                                            </div>
                                        </div>
                                    </div>
                                </form>



                            </div>
                        @endif
                        {{-- </div> --}}
                    </div>


                    <div id="mercado_payment" class="card">
                        <div class="card-header">
                            <h5>{{ __('Mercado Pago') }}</h5>

                        </div>
                        {{-- <div class="card-body"> --}}
                        @if (isset($admin_payment_setting['is_mercado_enabled']) && $admin_payment_setting['is_mercado_enabled'] == 'on')
                            <div class="tab-pane " id="mercado_payment">

                                <form role="form" action="{{ route('plan.pay.with.mercado') }}" method="post"
                                    class="require-validation" id="mercado-payment-form">
                                    @csrf
                                    <input type="hidden" name="plan_id"
                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                    <div class="border p-3 mb-3 rounded">
                                        <div class="row">
                                            <div class="col-md-12 mt-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="form-group w-100">
                                                        <label for="paypal_coupon"
                                                            class="col-form-label text-dark">{{ __('Coupon') }}</label>
                                                        <input type="text" id="mercado_coupon" name="coupon" col-form-label
                                                            text-dark class="form-control coupon" data-from="mercado"
                                                            placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>

                                                    <div class="form-group ms-3 mt-3">
                                                        <a href="#"
                                                            class="btn btn-print-invoice  mt-4 btn-primary  m-r-10 apply-coupon"
                                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"><i
                                                                class="" data-feather="save"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mt-3">
                                        <div class="col-sm-12 my-2 px-2">
                                            <div class="text-sm-right text-end">
                                                <input type="submit" id="pay_with_mercado" value="{{ __('Pay Now') }}"
                                                    class="btn btn-xs btn-primary">
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        @endif
                        {{-- </div> --}}
                    </div>



                    <div id="paytm_payment" class="card">
                        <div class="card-header">
                            <h5>{{ __('Paytm') }}</h5>

                        </div>
                        {{-- <div class="card-body"> --}}
                        @if (isset($admin_payment_setting['is_paytm_enabled']) && $admin_payment_setting['is_paytm_enabled'] == 'on')
                            <div class="tab-pane " id="paytm_payment">

                                <form role="form" action="{{ route('plan.pay.with.paytm') }}" method="post"
                                    class="require-validation" id="paytm-payment-form">
                                    @csrf
                                    <input type="hidden" name="plan_id"
                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                    <div class="border p-3 mb-3 rounded">
                                        <div class="row">
                                            <div class="col-md-12 mt-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="form-group w-100">
                                                        <label for="paypal_coupon"
                                                            class="col-form-label text-dark">{{ __('Coupon') }}</label>
                                                        <input type="text" id="paytm_coupon" name="coupon"
                                                            class="form-control coupon" data-from="paytm"
                                                            placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>

                                                    <div class="form-group ms-3 mt-3">
                                                        <a href="#"
                                                            class="btn btn-print-invoice  mt-4 btn-primary  m-r-10 apply-coupon"
                                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"><i
                                                                class="" data-feather="save"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="flaterwave_coupon"
                                                        class="col-form-label text-dark">{{ __('Mobile Number') }}</label>
                                                    <input type="text" id="mobile" name="mobile"
                                                        class="form-control mobile" data-from="mobile"
                                                        placeholder="{{ __('Enter Mobile Number') }}" required>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="form-group mt-3">
                                        <div class="col-sm-12 my-2 px-2">
                                            <div class="text-sm-right text-end">
                                                <input type="submit" id="pay_with_paytm" value="{{ __('Pay Now') }}"
                                                    class="btn btn-xs btn-primary">
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        @endif
                        {{-- </div> --}}
                    </div>

                    <div id="mollie_payment" class="card">
                        <div class="card-header">
                            <h5>{{ __('Mollie') }}</h5>

                        </div>
                        {{-- <div class="card-body"> --}}
                        @if (isset($admin_payment_setting['is_mollie_enabled']) && $admin_payment_setting['is_mollie_enabled'] == 'on')
                            <div class="tab-pane " id="mollie_payment">

                                <form role="form" action="{{ route('plan.pay.with.mollie') }}" method="post"
                                    class="require-validation" id="mollie-payment-form">
                                    @csrf
                                    <input type="hidden" name="plan_id"
                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                    <div class="border p-3 mb-3 rounded">
                                        <div class="row">
                                            <div class="d-flex align-items-center">
                                                <div class="form-group w-100">
                                                    <label for="paypal_coupon"
                                                        class="col-form-label text-dark">{{ __('Coupon') }}</label>
                                                    <input type="text" id="mollie_coupon" name="coupon"
                                                        class="form-control coupon" data-from="mollie"
                                                        placeholder="{{ __('Enter Coupon Code') }}">
                                                </div>

                                                <div class="form-group ms-3 mt-3">
                                                    <a href="#"
                                                        class="btn btn-print-invoice  mt-4 btn-primary  m-r-10 apply-coupon"
                                                        data-bs-toggle="tooltip" title="{{ __('Apply') }}"><i
                                                            class="" data-feather="save"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>

                            <div class="form-group mt-3">
                                <div class="col-sm-12 my-2 px-2">
                                    <div class="text-sm-right text-end">
                                        <input type="submit" id="pay_with_mollie" value="{{ __('Pay Now') }}"
                                            class="btn btn-xs btn-primary">
                                    </div>
                                </div>
                            </div>
                            </form>

                    </div>
                    @endif
                    {{-- </div> --}}

                    <div id="skrill_payment" class="card">
                        <div class="card-header">
                            <h5>{{ __('Skrill') }}</h5>

                        </div>
                        {{-- <div class="card-body"> --}}
                        @if (isset($admin_payment_setting['is_skrill_enabled']) && $admin_payment_setting['is_skrill_enabled'] == 'on')
                            <div class="tab-pane " id="skrill_payment">

                                <form role="form" action="{{ route('plan.pay.with.skrill') }}" method="post"
                                    class="require-validation" id="skrill-payment-form">
                                    @csrf
                                    <input type="hidden" name="plan_id"
                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                    <div class="border p-3 mb-3 rounded">
                                        <div class="row">
                                            <div class="col-md-12 mt-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="form-group w-100">
                                                        <label for="paypal_coupon"
                                                            class="col-form-label text-dark">{{ __('Coupon') }}</label>
                                                        <input type="text" id="skrill_coupon" name="coupon"
                                                            class="form-control coupon" data-from="skrill"
                                                            placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>

                                                    <div class="form-group ms-3 mt-3">
                                                        <a href="#"
                                                            class="btn btn-print-invoice  mt-4 btn-primary  m-r-10 apply-coupon"
                                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"><i
                                                                class="" data-feather="save"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $skrill_data = [
                                            'transaction_id' => md5(date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id'),
                                            'user_id' => 'user_id',
                                            'amount' => 'amount',
                                            'currency' => 'currency',
                                        ];
                                        session()->put('skrill_data', $skrill_data);
                                        
                                    @endphp
                                    <div class="form-group mt-3">
                                        <div class="col-sm-12 my-2 px-2">
                                            <div class="text-sm-right text-end">
                                                <input type="submit" id="pay_with_skrill" value="{{ __('Pay Now') }}"
                                                    class="btn btn-xs btn-primary">
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        @endif
                        {{-- </div> --}}
                    </div>


                    <div id="coingate_payment" class="card">
                        <div class="card-header">
                            <h5>{{ __('Coingate') }}</h5>

                        </div>
                        {{-- <div class="card-body"> --}}
                        @if (isset($admin_payment_setting['is_coingate_enabled']) && $admin_payment_setting['is_coingate_enabled'] == 'on')
                            <div class="tab-pane " id="coingate_payment">

                                <form role="form" action="{{ route('plan.pay.with.coingate') }}" method="post"
                                    class="require-validation" id="coingate-payment-form">
                                    @csrf
                                    <input type="hidden" name="plan_id"
                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                    <div class="border p-3 mb-3 rounded">
                                        <div class="row">
                                            <div class="col-md-12 mt-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="form-group w-100">
                                                        <label for="paypal_coupon"
                                                            class="col-form-label text-dark">{{ __('Coupon') }}</label>
                                                        <input type="text" id="coingate_coupon" name="coupon"
                                                            class="form-control coupon" data-from="coingate"
                                                            placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>

                                                    <div class="form-group ms-3 mt-3">
                                                        <a href="#"
                                                            class="btn btn-print-invoice  mt-4 btn-primary  m-r-10 apply-coupon"
                                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"><i
                                                                class="" data-feather="save"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mt-3">
                                        <div class="col-sm-12 my-2 px-2">
                                            <div class="text-sm-right text-end">
                                                <input type="submit" id="pay_with_coingate" value="{{ __('Pay Now') }}"
                                                    class="btn btn-xs btn-primary">
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        @endif
                        {{-- </div> --}}
                    </div>


                    <div id="paymentwall_payment" class="card">
                        <div class="card-header">
                            <h5>{{ __('Paymentwall') }}</h5>

                        </div>
                        {{-- <div class="card-body"> --}}
                        @if (isset($admin_payment_setting['is_paymentwall_enabled']) && $admin_payment_setting['is_paymentwall_enabled'] == 'on')
                            <div class="tab-pane " id="paymentwall_payment">

                                <form role="form" action="{{ route('paymentwall') }}" method="post"
                                    class="require-validation" id="coingate-payment-form">
                                    @csrf
                                    <input type="hidden" name="coupon" id="paymentwall_coupon" value="">
                                    <input type="hidden" name="plan_id"
                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                    <input type="hidden" name="total_price" id="paymentwall_total_price"
                                        value="{{ $plan->price }}" class="form-control">

                                    <div class="border p-3 mb-3 rounded">
                                        <div class="row">
                                            <div class="col-md-12 mt-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="form-group w-100">
                                                        <label for="paymentwall_coupon"
                                                            class="col-form-label text-dark">{{ __('Coupon') }}</label>
                                                        <input type="text" id="paymentwall_coupon" name="coupon"
                                                            class="form-control coupon" data-from="paymentwall"
                                                            placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>

                                                    <div class="form-group ms-3 mt-3">
                                                        <a href="#"
                                                            class="btn btn-print-invoice  mt-4 btn-primary  m-r-10 apply-coupon"
                                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"><i
                                                                class="" data-feather="save"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="form-group mt-3">
                                        <div class="col-sm-12 my-2 px-2">
                                            <div class="text-sm-right text-end">
                                                <input type="submit" id="pay_with_paymentwall"
                                                    value="{{ __('Pay Now') }}" class="btn btn-xs btn-primary">
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        @endif
                        {{-- </div> --}}
                    </div>

                </div>





            </div>
        </div>



    </div>

    </div>
@endsection


@push('scripts')
    console.log('fdfd');
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('click', '.apply-coupon', function(e) {
                e.preventDefault();

                var ele = $(this);
                var coupon = ele.closest('.row').find('.coupon');

                if (coupon.val() == '') {
                    coupon.focus();
                    return;
                }

                $.ajax({
                    url: '{{ route('apply.coupon') }}',
                    datType: 'json',
                    data: {
                        plan_id: '{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}',
                        coupon: coupon.val()
                    },
                    success: function(data) {
                        $('.final-price').text(data.final_price);
                        $('#stripe_coupon, #paypal_coupon').val(coupon.val());
                        if (data.is_success) {
                            show_toastr('Success', data.message, 'success');
                        } else {
                            show_toastr('{{ __('Error') }}', data.message, 'error');
                        }
                    }
                })
            });
        });

        @if ($plan->price > 0.0 && $admin_payment_setting['is_stripe_enabled'] == 'on' && !empty($admin_payment_setting['stripe_key']) && !empty($admin_payment_setting['stripe_secret']))
            var stripe = Stripe('{{ $admin_payment_setting['stripe_key'] }}');
            var elements = stripe.elements();

            // Custom styling can be passed to options when creating an Element.
            var style = {
                base: {
                    // Add your base input styles here. For example:
                    fontSize: '14px',
                    color: '#32325d',
                },
            };

            // Create an instance of the card Element.
            var card = elements.create('card', {
                style: style
            });

            // Add an instance of the card Element into the `card-element` <div>.
            card.mount('#card-element');

            // Create a token or display an error when the form is submitted.
            var form = document.getElementById('payment-form');
            form.addEventListener('submit', function(event) {
                event.preventDefault();

                stripe.createToken(card).then(function(result) {
                    if (result.error) {
                        // $("#card-errors").html(result.error.message);
                        show_toastr('Error', result.error.message, 'error');

                    } else {
                        // Send the token to your server.
                        stripeTokenHandler(result.token);
                    }
                });
            });

            function stripeTokenHandler(token) {
                // Insert the token ID into the form so it gets submitted to the server
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);

                // Submit the form
                form.submit();
            }
        @endif

        @if (!empty($admin_payment_setting['is_paystack_enabled']) && isset($admin_payment_setting['is_paystack_enabled']) && $admin_payment_setting['is_paystack_enabled'] == 'on')
            $(document).on("click", "#pay_with_paystack", function() {
                $('#paystack-payment-form').ajaxForm(function(res) {
                    if (res.flag == 1) {
                        var paystack_callback = "{{ url('/plan/paystack') }}";
                        var order_id = '{{ time() }}';
                        var coupon_id = res.coupon;
                        var handler = PaystackPop.setup({
                            key: '{{ $admin_payment_setting['paystack_public_key'] }}',
                            email: res.email,
                            amount: res.total_price * 100,
                            currency: res.currency,
                            ref: 'pay_ref_id' + Math.floor((Math.random() * 1000000000) +
                                1
                            ), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line
                            // entirely so our API will generate one for you
                            metadata: {
                                custom_fields: [{
                                    display_name: "Email",
                                    variable_name: "email",
                                    value: res.email,
                                }]
                            },

                            callback: function(response) {

                                window.location.href = paystack_callback + '/' + response
                                    .reference + '/' + '{{ encrypt($plan->id) }}' +
                                    '?coupon_id=' + coupon_id
                            },
                            onClose: function() {
                                alert('window closed');
                            }
                        });
                        handler.openIframe();
                    } else if (res.flag == 2) {

                    } else {
                        show_toastr('Error', data.message, 'msg');
                    }

                }).submit();
            });
        @endif
        //    Flaterwave Payment
        @if (!empty($admin_payment_setting['is_flutterwave_enabled']) && isset($admin_payment_setting['is_flutterwave_enabled']) && $admin_payment_setting['is_flutterwave_enabled'] == 'on')
            $(document).on("click", "#pay_with_flaterwave", function() {
                $('#flaterwave-payment-form').ajaxForm(function(res) {
                    if (res.flag == 1) {
                        var coupon_id = res.coupon;
                        var API_publicKey = '{{ $admin_payment_setting['flutterwave_public_key'] }}';
                        var nowTim = "{{ date('d-m-Y-h-i-a') }}";
                        var flutter_callback = "{{ url('/plan/flaterwave') }}";
                        var x = getpaidSetup({
                            PBFPubKey: API_publicKey,
                            customer_email: '{{ Auth::user()->email }}',
                            amount: res.total_price,
                            currency: '{{ env('CURRENCY') }}',
                            txref: nowTim + '__' + Math.floor((Math.random() * 1000000000)) +
                                'fluttpay_online-' + {{ date('Y-m-d') }},
                            meta: [{
                                metaname: "payment_id",
                                metavalue: "id"
                            }],
                            onclose: function() {},
                            callback: function(response) {
                                var txref = response.tx.txRef;
                                if (
                                    response.tx.chargeResponseCode == "00" ||
                                    response.tx.chargeResponseCode == "0"
                                ) {
                                    window.location.href = flutter_callback + '/' + txref +
                                        '/' +
                                        '{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}?coupon_id=' +
                                        coupon_id;
                                } else {
                                    // redirect to a failure page.
                                }
                                x
                            .close(); // use this to close the modal immediately after payment.
                            }
                        });
                    } else if (res.flag == 2) {

                    } else {
                        show_toastr('Error', data.message, 'msg');
                    }

                }).submit();
            });
        @endif
        // Razorpay Payment
        @if (!empty($admin_payment_setting['is_razorpay_enabled']) && isset($admin_payment_setting['is_razorpay_enabled']) && $admin_payment_setting['is_razorpay_enabled'] == 'on')
            $(document).on("click", "#pay_with_razorpay", function() {
                $('#razorpay-payment-form').ajaxForm(function(res) {
                    if (res.flag == 1) {

                        var razorPay_callback = '{{ url('/plan/razorpay') }}';
                        var totalAmount = res.total_price * 100;
                        var coupon_id = res.coupon;
                        var options = {
                            "key": "{{ $admin_payment_setting['razorpay_public_key'] }}", // your Razorpay Key Id
                            "amount": totalAmount,
                            "name": 'Plan',
                            "currency": '{{ env('CURRENCY') }}',
                            "description": "",
                            "handler": function(response) {
                                window.location.href = razorPay_callback + '/' + response
                                    .razorpay_payment_id + '/' +
                                    '{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}?coupon_id=' +
                                    coupon_id;
                            },
                            "theme": {
                                "color": "#528FF0"
                            }
                        };
                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                    } else if (res.flag == 2) {

                    } else {
                        show_toastr('Error', data.message, 'msg');
                    }

                }).submit();
            });
        @endif
    </script>


    <script>
        var type = window.location.hash.substr(1);
        $('.list-group-item').removeClass('active');
        $('.list-group-item').removeClass('text-primary');
        if (type != '') {
            $('a[href="#' + type + '"]').addClass('active').removeClass('text-primary');
        } else {
            $('.list-group-item:eq(0)').addClass('active').removeClass('text-primary');
        }

        $(document).on('click', '.list-group-item', function() {
            $('.list-group-item').removeClass('active');
            $('.list-group-item').removeClass('text-primary');
            setTimeout(() => {
                $(this).addClass('active').removeClass('text-primary');
            }, 10);
        });

        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>

@endpush
