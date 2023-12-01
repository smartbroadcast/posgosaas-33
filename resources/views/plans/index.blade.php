@extends('layouts.app')

@section('page-title', __('Plans'))

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Manage Plans') }}</h5>
    </div>
@endsection

@section('action-btn')
    @can('Create Plan')
        <a href="#" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" data-bs-placement="top"
            title="{{ __('Add Plan') }}" data-ajax-popup="true" data-size="lg" data-title="{{ __('Add Plan') }}"
            data-url="{{ route('plans.create') }}"><i class="ti ti-plus text-white"></i></a>
    @endcan
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Plans') }}</li>
@endsection

@section('content')

    <div class="row">
        @foreach ($plans as $plan)
            <div class="col-lg-3 col-md-4">
                <div class="card price-card price-1 wow animate__fadeInUp" data-wow-delay="0.2s"
                    style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                    <div class="card-body">
                        <span class="price-badge bg-primary">{{ $plan->name }}</span>

                        <div class="d-flex flex-row-reverse m-0 p-0 ">
                            @can('Edit Plan')
                                <div class="action-btn bg-primary ms-2">
                                    <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-ajax-popup="true"
                                        data-title="{{ __('Edit Plan') }}" data-url="{{ route('plans.edit', $plan->id) }}"
                                        data-size="lg" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Edit') }}"
                                        data-bs-placement="top"><span class="text-white"><i
                                                class="ti ti-pencil"></i></span></a>
                                </div>
                            @endcan
                            @can('Buy Plan')
                                @if (\Auth::user()->isOwner() && \Auth::user()->plan_id == $plan->id)
                                    <span class="d-flex align-items-center ms-2">
                                        <i class="f-10 lh-1 fas fa-circle text-success"></i>
                                        <span class="ms-2">{{ __('Active') }}</span>
                                    </span>
                                @endif
                            @endcan
                        </div>


                        <span
                            class="mb-4 f-w-600 p-price">{{ env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$' }}{{ number_format($plan->price) }}<small
                                class="text-sm">/ {{ $plan->duration }}</small></span>
                        {{-- <p class="mb-0">
                            {{ $plan->name }} {{ __('Plan') }}
                        </p> --}}
                        <p class="mb-0 text-sm">
                            {{ $plan->description }}
                        </p>

                        <ul class="list-unstyled my-4">
                            <li>
                                <span class="theme-avtar">
                                    <i class="text-primary ti ti-circle-plus"></i></span>
                                {{ $plan->max_users == -1 ? __('Unlimited') : $plan->max_users }} {{ __('Users') }}
                            </li>
                            <li>
                                <span class="theme-avtar">
                                    <i class="text-primary ti ti-circle-plus"></i></span>
                                {{ $plan->max_customers == -1 ? __('Unlimited') : $plan->max_customers }}
                                {{ __('Customers') }}
                            </li>
                            <li>
                                <span class="theme-avtar">
                                    <i class="text-primary ti ti-circle-plus"></i></span>
                                {{ $plan->max_vendors == -1 ? __('Unlimited') : $plan->max_vendors }}
                                {{ __('Vendors') }}
                            </li>
                        </ul>
                        <div class="row">

                            @can('Buy Plan')
                                @if ($plan->id != \Auth::user()->plan_id && \Auth::user()->isOwner())
                                    @if ($plan->price > 0)
                                        <div class="col-8">
                                           
                                                <a href="{{ route('stripe', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                                    class="btn btn-primary d-flex justify-content-center align-items-center btn-sm"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-original-title="{{ __('Subscribe') }}"
                                                    title="{{ __('Subscribe') }}">{{ __('Subscribe') }}
                                                    <i class="ti ti-arrow-narrow-right ms-1"></i></a>
                                            
                                            
                                        </div>
                                    @endif
                                @endif

                                @if (\Auth::user()->plan_id != $plan->id)
                                    @if ($plan->id != 1)
                                       
                                        <div class="col-4">
                                            @if (\Auth::user()->plan_requests != $plan->id)
                                               
                                                <a href="{{ route('send.request', [\Illuminate\Support\Facades\Crypt::encrypt($plan->id)]) }}"
                                                    class="btn btn-primary btn-icon btn-sm"
                                                    data-title="{{ __('Send Request') }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-original-title="{{ __('Send Request') }}"
                                                    title="{{ __('Send Request') }}">
                                                    <span class="btn-inner--icon"><i class="ti ti-arrow-forward-up"></i></span>
                                                </a>
                                            @else
                                                
                                                <a href="{{ route('request.cancel', \Auth::user()->id) }}"
                                                    class="btn btn-danger btn-icon btn-sm"
                                                    data-title="{{ __('Cancel Request') }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    data-bs-original-title="{{ __('Cancel Request') }}"
                                                    title="{{ __('Cancel Request') }}">
                                                    <span class="btn-inner--icon"><i class="ti ti-shield-x"></i></span>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                @endif



                                @if (Auth::user()->isOwner() && Auth::user()->plan_id == $plan->id && date('Y-m-d') < \Auth::user()->plan_expire_date)
                                    <p class="mb-0">
                                        {{ __('Plan Expired : ') }}
                                        {{ \Auth::user()->plan_expire_date ? \Auth::user()->dateFormat(\Auth::user()->plan_expire_date) : __('Unlimited') }}
                                    </p>
                                @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('public/vendor/unisharp/laravel-ckeditor/ckeditor.js') }}"></script>

    <script>
        $(document).ready(function() {
            $(document).on('keypress keydown keyup', '.max-users, .max-customers, .max-vendors', function() {
                if ($(this).val() == '' || $(this).val() < -1) {
                    $(this).val('0');
                }
            });
        });
    </script>
@endpush
