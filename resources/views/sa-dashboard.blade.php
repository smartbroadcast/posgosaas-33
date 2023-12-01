@extends('layouts.app')

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Dashboard') }}</h5>
    </div>
@endsection

@section('page-title', __('Dashboard'))

@section('header-content')
    <div class="row">
        <div class="col-xxl-6">
            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="card" style="min-height: 225px;">
                        <div class="card-body">
                            <div class="theme-avtar bg-primary">
                                <i class="ti ti-users"></i>
                            </div>
                            <p class="text-muted text-sm mt-4 mb-2">{{ __('Total Users') }} : {{ $ownersCount }}</p>
                            <h6 class="mb-3">{{ __('Paid Users') }}</h6>
                            <h3 class="mb-0">{{ $paidOwnersCount }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="card" style="min-height: 225px;">
                        <div class="card-body">
                            <div class="theme-avtar bg-info">
                                <i class="ti ti-shopping-cart"></i>
                            </div>
                            <p class="text-muted text-sm mt-4 mb-2">{{ __('Total Orders') }}:{{ $ordersCount }}</p>
                            <h6 class="mb-3">{{ __('Total Order Amount') }}</h6>
                            <h3 class="mb-0">{{ $ordersPrice }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="card" style="min-height: 225px;">
                        <div class="card-body">
                            <div class="theme-avtar bg-warning">
                                <i class="ti ti-trophy"></i>
                            </div>
                            <p class="text-muted text-sm mt-4 mb-2">{{ __('Total Plan') }}: {{ $plansCount }}</p>
                            <h6 class="mb-3">{{ __('Most Purchase Plan') }}</h6>
                            <h3 class="mb-0">{{ $mostPurchasedPlan }}</h3>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header">
                    <div class="row ">
                        <div class="col-6">
                            <h5>{{ __('Order Report') }}</h5>
                        </div>
                        <div class="col-6 text-end">
                            <h6>{{ __('Last 14 Days') }}</h6>
                        </div>
                    </div>

                </div>
                <div class="card-body">

                    <div id="order-chart" height="200" class="p-3"></div>
                </div>
            </div>


        </div>

    </div>
@endsection



@push('scripts')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>

    <script>
        (function() {
            var options = {

                series: [{
                    name: '{{ __('Order') }}',
                    data: {!! json_encode($getOrderChart['data']) !!}
                }, ],

                chart: {
                    height: 300,
                    type: 'area',
                    dropShadow: {
                        enabled: true,
                        color: '#000',
                        top: 18,
                        left: 7,
                        blur: 10,
                        opacity: 0.2
                    },
                    toolbar: {
                        show: false,
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },

                title: {
                    text: '',
                    align: 'left'
                },

                xaxis: {
                    categories: {!! json_encode($getOrderChart['label']) !!},
                    title: {
                        text: 'Days'
                    }
                },

                colors: ['#6fd943', '#ff3a6e'],

                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: false,
                },
                // markers: {
                //     size: 4,
                //     colors: ['#ffa21d', '#FF3A6E'],
                //     opacity: 0.9,
                //     strokeWidth: 2,
                //     hover: {
                //         size: 7,
                //     }
                // },
                yaxis: {
                    title: {
                        text: 'Amount'
                    },
                }
            };
            var chart = new ApexCharts(document.querySelector("#order-chart"), options);
            chart.render();
        })();
    </script>
@endpush
