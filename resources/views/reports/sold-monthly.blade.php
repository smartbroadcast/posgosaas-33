@extends('layouts.app')

@section('page-title', __('Sale Daily/Monthly Report'))

@section('title')
    <div class="d-inline-block">
        <h4 class="title">{{ __('Sale Monthly') }}</h4>
    </div>
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
<li class="breadcrumb-item">{{ __('Reports') }}</li>
<li class="breadcrumb-item">{{ __('Sale Monthly') }}</li>
@endsection

@section('action-btn')
<a class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="collapse"
                data-bs-target=".multi-collapse-monthly-sale" title="{{ __('Filter') }}"> <i
                    class="ti ti-filter text-white"></i> </a>
@endsection


@can('Manage Sales')

    @section('content')
    

        <ul class="nav nav-pills my-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link" id="pills-home-tab" data-bs-toggle="pill" href="{{ route('sold.daily.analysis') }}"
                    onclick="window.location.href = '{{ route('sold.daily.analysis') }}'" role="tab"
                    aria-controls="pills-home" aria-selected="true">{{ __('Daily') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" id="pills-profile-tab" data-bs-toggle="pill" href="#monthly-chart" role="tab"
                    aria-controls="pills-profile" aria-selected="false">{{ __('Monthly') }}</a>
            </li>
        </ul>


        <div class=w-100>
            <div class="card collapse multi-collapse-monthly-sale {{ $display_status }}">
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
                                <div class="tab-pane fade" id="daily-chart" role="tabpanel">
                                </div>

                                <div class="tab-pane fade show active" id="monthly-chart" role="tabpanel">
                                    <div class="row {{ $display_status ? 'mt-59' : '' }}">
                                        <div class="col-lg-12">
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
                                                <div id="sale-monthly-report"></div>
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
        <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>

        <script type="text/javascript">
            var saleMonthlyReport;

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
                        name: '{{ __('Sale') }}',
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
                    }
                };
                saleMonthlyReport = new ApexCharts($this[0], options);
                saleMonthlyReport.render();
            };

            function ajax_sale_monthly_chart_filter() {

                var data = {
                    'start_date': $('#start-date').val(),
                    'end_date': $('#end-date').val(),
                    'branch_id': $('#monthly_branch_id').val(),
                    'cash_register_id': $('#monthly_cash_register_id').val(),
                };

                $.ajax({
                    url: '{{ route('sold.monthly.chart.filter') }}',
                    dataType: 'json',
                    data: data,
                    success: function(data) {

                        var $chart = $('#sale-monthly-report');

                        if ($chart.length) {
                            if (typeof saleMonthlyReport == 'undefined') {
                                init($chart, data);
                            } else {
                                saleMonthlyReport.updateOptions({
                                    series: [{
                                        data: data.value
                                    }, ],
                                    xaxis: {
                                        categories: data.label,
                                    },
                                });
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
                ajax_sale_monthly_chart_filter();
            });

            $(document).on('change', '#monthly_cash_register_id', function(e) {

                ajax_sale_monthly_chart_filter();
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

                ajax_sale_monthly_chart_filter();
            });
        </script>
    @endpush
@endcan
