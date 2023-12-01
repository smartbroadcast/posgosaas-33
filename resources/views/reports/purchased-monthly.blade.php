@extends('layouts.app')

@section('page-title', __('Purchase Daily/Monthly Report'))

@section('title')
    <div class="d-inline-block">
        <h4 class="title">{{ __('Purchase Daily/Monthly Report') }}</h4>
    </div>
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
<li class="breadcrumb-item">{{ __('Reports') }}</li>
<li class="breadcrumb-item">{{ __('Purchase Monthly') }}</li>
@endsection

@section('action-btn')
<a class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="collapse"
data-bs-target=".multi-collapse-monthly-purchase" title="{{ __('Filter') }}"> <i
    class="ti ti-filter text-white"></i> </a>

@endsection


@can('Manage Purchases')

    @section('content')
       

        <ul class="nav nav-pills my-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link" id="pills-home-tab" data-bs-toggle="pill"
                    href="{{ route('purchased.daily.analysis') }}"
                    onclick="window.location.href = '{{ route('purchased.daily.analysis') }}'" role="tab"
                    aria-controls="pills-home" aria-selected="true">{{ __('Daily') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" id="pills-profile-tab" data-bs-toggle="pill" href="#monthly-chart" role="tab"
                    aria-controls="pills-profile" aria-selected="false">{{ __('Monthly') }}</a>
            </li>
        </ul>

        <div class="w-100">
            <div class="card collapse multi-collapse-monthly-purchase {{ $display_status }}">
                <div class="card-body py-3">
                    <div class="row input-daterange align-items-center">
                        <div class="form-group col-md-6 mb-0">
                            {{ Form::label('monthly_branch_id', __('Branch'), ['class' => 'col-form-label']) }}
                            <div class="input-group">
                                {{ Form::select('monthly_branch_id', $branches, null, ['class' => 'form-control','id' => 'monthly_branch_id','data-toggle' => 'select']) }}
                            </div>
                        </div>
                        <div class="form-group col-md-6 mb-0">
                            {{ Form::label('monthly_cash_register_id', __('Cash Register'), ['class' => 'col-form-label']) }}
                            <div class="input-group">
                                {{ Form::select('monthly_cash_register_id', $cash_registers, null, ['class' => 'form-control','id' => 'monthly_cash_register_id','data-toggle' => 'select']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row min-vh-74">
            <div class="col-12">
                <div class="card">
                    
                        <div class="setting-tab">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="monthly-chart" role="tabpanel">
                                    <div class="row {{ $display_status ? 'mt-59' : '' }}">
                                        <div class="col">

                                            <div class="card-header">
                                                <div class="row ">
                                                    <div class="col-6">
                                                        <h6>{{ __('Monthly Report') }}</h6>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <h6>{{ __('Last 12 Months') }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div id="purchase-monthly-report"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                   
                </div>
            </div>
        </div>
        </div>
    @endsection

    @push('scripts')
        <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>

        <script type="text/javascript">
            // function init($this, data) {

            //     var options = {
            //         series: [
            //             {
            //                 name: '{{ __('Purchase') }}',
            //                 data: data.value
            //             },
            //         ],
            //         chart: {
            //             height: 350,
            //             type: 'line',
            //             dropShadow: {
            //                 enabled: true,
            //                 color: '#000',
            //                 top: 18,
            //                 left: 7,
            //                 blur: 10,
            //                 opacity: 0.2
            //             },
            //             toolbar: {
            //                 show: false
            //             }
            //         },
            //         colors: ['#77B6EA'],
            //         dataLabels: {
            //             enabled: true,
            //         },
            //         stroke: {
            //             curve: 'smooth',
            //         },
            //         title: {
            //             text: '',
            //             align: 'left'
            //         },
            //         grid: {
            //             borderColor: '#e7e7e7',
            //             row: {
            //                 colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            //                 opacity: 0.5
            //             },
            //         },
            //         markers: {
            //             size: 1
            //         },
            //         xaxis: {
            //             categories: data.label,
            //             title: {
            //                 text: '{{ __('Months') }}'
            //             }
            //         },
            //         yaxis: {
            //             title: {
            //                 text: '{{ __('Amount') }}'
            //             },
            //         },
            //         legend: {
            //             position: 'top',
            //             horizontalAlign: 'right',
            //             floating: true,
            //             offsetY: -25,
            //             offsetX: -5
            //         }
            //     };

            //     purchaseMonthlyReport = new ApexCharts($this[0], options);
            //     purchaseMonthlyReport.render();
            // }

            var purchaseMonthlyReport;

            function init($this, data) {
                var options = {
                    chart: {
                        height: 400,
                        type: 'area',
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
                    series: [{
                        name: '{{ __('Purchase') }}',
                        data: data.value
                    }, ],
                    xaxis: {
                        categories: data.label,
                        title: {
                            text: '{{ __('Months') }}'
                        }
                    },
                    colors: ['#6fd943', '#FF3A6E'],

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
                            text: '{{ __('Amount') }}'
                        },
                    },
                };
                purchaseMonthlyReport = new ApexCharts($this[0], options);
                purchaseMonthlyReport.render();
            };

            function ajax_purchase_monthly_chart_filter() {

                var data = {
                    'start_date': $('#start-date').val(),
                    'end_date': $('#end-date').val(),
                    'branch_id': $('#monthly_branch_id').val(),
                    'cash_register_id': $('#monthly_cash_register_id').val(),
                };

                $.ajax({
                    url: '{{ route('purchased.monthly.chart.filter') }}',
                    dataType: 'json',
                    data: data,
                    success: function(data) {

                        var $chart = $('#purchase-monthly-report');

                        if ($chart.length) {
                            if (typeof purchaseMonthlyReport == 'undefined') {
                                init($chart, data);
                            } else {
                                purchaseMonthlyReport.updateOptions({
                                    series: [{
                                        data: data.value
                                    }, ],
                                    xaxis: {
                                        categories: data.label,
                                    },
                                })
                            }

                        }
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        show_toastr('{{ __('Error') }}', data.error, 'error');
                    }
                });
            }

            function addDays(s, days) {
                var b = s.split(/\D/);
                var d = new Date(b[0], b[1] - 1, b[2]);
                d.setDate(d.getDate() + Number(days));

                function z(n) {
                    return (n < 10 ? '0' : '') + n
                }

                if (isNaN(+d)) return d.toString();
                return d.getFullYear() + '-' + z(d.getMonth() + 1) + '-' + z(d.getDate());
            }

            function setEndDate(value) {

                var added30 = addDays(value, 30);

                var currentdateParts = value.split("-");
                var currentdays = new Date(currentdateParts[0], currentdateParts[1] - 1, currentdateParts[2]);

                var added30dateParts = added30.split("-");
                var added30days = new Date(added30dateParts[0], added30dateParts[1] - 1, added30dateParts[2]);

                $("#end-date").datepicker("destroy");
                $("#end-date").datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    startDate: currentdays,
                    endDate: added30days
                });
                $('#end-date').datepicker('setDate', added30days);
            }

            $(document).ready(function() {
                ajax_purchase_monthly_chart_filter();
            });

            $(document).on('change', '#monthly_cash_register_id', function(e) {

                ajax_purchase_monthly_chart_filter();
            });

            $(document).on('change', '#monthly_branch_id', function(e) {

                $.ajax({
                    url: '{{ route('get.cash.registers') }}',
                    dataType: 'json',
                    async: false,
                    data: {
                        'branch_id': $(this).val()
                    },
                    success: function(data) {
                        $('#monthly_cash_register_id').find('option').not(':first').remove();
                        $.each(data, function(key, value) {
                            $('#monthly_cash_register_id')
                                .append($("<option></option>")
                                    .attr("value", value.id)
                                    .text(value.name));
                        });
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        show_toastr('{{ __('Error') }}', data.error, 'error');
                    }
                });

                ajax_purchase_monthly_chart_filter();
            });
        </script>
    @endpush
@endcan
