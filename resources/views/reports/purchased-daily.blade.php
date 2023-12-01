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
<li class="breadcrumb-item">{{ __('Purchase Daily') }}</li>
@endsection

@section('action-btn')
    <a class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="collapse"
    data-bs-target=".multi-collapse-daily-purchase" title="{{ __('Filter') }}"><i
        class="ti ti-filter text-white"></i></a>
@endsection

@can('Manage Purchases')

    @section('content')
        
        <ul class="nav nav-pills my-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" href="#daily-chart" role="tab"
                    aria-controls="pills-home" aria-selected="true">{{ __('Daily') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-profile-tab" data-bs-toggle="pill"
                    href="{{ route('purchased.monthly.analysis') }}"
                    onclick="window.location.href = '{{ route('purchased.monthly.analysis') }}'" role="tab"
                    aria-controls="pills-profile" aria-selected="false">{{ __('Monthly') }}</a>
            </li>

        </ul>

        <div class="w-100">
            <div class="card collapse multi-collapse-daily-purchase">
                <div class="card-body py-3">
                    <div class="row input-daterange analysis-datepicker align-items-center">
                        <div class="form-group col-md-3 mb-0">
                            {{ Form::label('start-date', __('Start date'), ['class' => 'col-form-label']) }}
                            <div class="input-group">
                                {{ Form::text('start-date', $start_date, ['class' => 'form-control','placeholder' => __('Select Start date'),'id' => 'start-date']) }}
                            </div>
                        </div>
                        <div class="form-group col-md-3 mb-0">
                            {{ Form::label('end-date', __('End date'), ['class' => 'col-form-label']) }}
                            <div class="input-group">
                                {{ Form::hidden('end_date_status', 0, ['id' => 'end_date_status']) }}

                                {{ Form::text('end-date', $end_date, ['class' => 'form-control','placeholder' => __('Select End date'),'id' => 'end-date']) }}
                            </div>
                        </div>
                        <div class="form-group col-md-3 mb-0 {{ $display_status }}">
                            {{ Form::label('daily_branch_id', __('Branch'), ['class' => 'col-form-label']) }}
                            <div class="input-group">
                                {{ Form::select('daily_branch_id', $branches, null, ['class' => 'form-control','id' => 'daily_branch_id','data-toggle' => 'select']) }}
                            </div>
                        </div>
                        <div class="form-group col-md-3 mb-0 {{ $display_status }}">
                            {{ Form::label('daily_cash_register_id', __('Cash Register'), ['class' => 'col-form-label']) }}
                            <div class="input-group">
                                {{ Form::select('daily_cash_register_id', $cash_registers, null, ['class' => 'form-control','id' => 'daily_cash_register_id','data-toggle' => 'select']) }}
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
                                <div class="tab-pane fade show active" id="daily-chart" role="tabpanel">

                                    <div class="col-lg-12">
                                        <div class="card-header">
                                            <div class="row ">
                                                <div class="col-6">
                                                    <h6>{{ __('Daily Report') }}</h6>
                                                </div>
                                                <div class="col-6 text-end">
                                                    <h6>{{ __('Last 30 Days') }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div id="purchase-daily-report"></div>
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
            var purchaseDailyReport;

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
                            text: '{{ __('Days') }}'
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
                purchaseDailyReport = new ApexCharts($this[0], options);
                purchaseDailyReport.render();

            };

            function ajax_purchase_daily_chart_filter() {

                var data = {
                    'start_date': $('#start-date').val(),
                    'end_date': $('#end-date').val(),
                    'branch_id': $('#daily_branch_id').val(),
                    'cash_register_id': $('#daily_cash_register_id').val(),
                };

                $.ajax({
                    url: '{{ route('purchased.daily.chart.filter') }}',
                    dataType: 'json',
                    data: data,
                    success: function(data) {

                        var $chart = $('#purchase-daily-report');

                        if ($chart.length) {
                            if (typeof purchaseDailyReport == 'undefined') {
                                init($chart, data);
                            } else {
                                purchaseDailyReport.updateOptions({
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

                $("#start-date").datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true
                });
                // $('[data-toggle="select"]').select2({});

                setEndDate($('#start-date').val());

            });

            $(document).on('change', '#daily_cash_register_id, #vendor_id, #end-date', function(e) {

                if ($(this).attr('id') == 'end-date') {
                    if ($('#end_date_status').val() == 1) {
                        return;
                    }
                }
                ajax_purchase_daily_chart_filter();
            });

            $(document).on('change', '#start-date', function(e) {

                $('#end_date_status').val(1);

                setEndDate($(this).val());

                ajax_purchase_daily_chart_filter();

                $('#end_date_status').val(0);

            });

            $(document).on('change', '#daily_branch_id', function(e) {

                $.ajax({
                    url: '{{ route('get.cash.registers') }}',
                    dataType: 'json',
                    async: false,
                    data: {
                        'branch_id': $(this).val()
                    },
                    success: function(data) {
                        $('#daily_cash_register_id').find('option').not(':first').remove();
                        $.each(data, function(key, value) {
                            $('#daily_cash_register_id')
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

                ajax_purchase_daily_chart_filter();
            });
        </script>
    @endpush
@endcan
