@extends('layouts.app')


@section('page-title', __('Tax Report'))

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Tax Report') }}</h5>
    </div>
@endsection


@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Reports') }}</li>
    <li class="breadcrumb-item">{{ __('Tax Report') }}</li>
@endsection

@push('old-datatable-css')
    <link rel="stylesheet" href="{{ asset('custom/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('custom/css/customdatatable.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/flatpickr.min.css') }}">
@endpush

@can('Manage Tax')




    @section('content')
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="setting-tab">
                            {{-- <div class="row">
                                <ul class="nav nav-tabs my-4 ml-4">
                                    @can('Manage Purchases')
                                        <li>
                                            <a data-bs-toggle="tab" href="#manage-purchases"
                                                class="active">{{ __('Purchases') }}</a>
                                        </li>
                                    @endcan

                                    @can('Manage Sales')
                                        <li class="annual-billing">
                                            <a data-bs-toggle="tab" href="#manage-sales"
                                                class="">{{ __('Sales') }}</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div> --}}

                            <div class="p-3 card">
                                <ul class="nav nav-pills nav-fill" id="pills-tab" role="tablist">
                                    @can('Manage Purchases')
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="pills-user-tab-1" href="#manage-purchases" data-bs-toggle="pill"
                                            data-bs-toggle="#pills-user-1">{{ __('Purchases') }}</a>
                                    </li>
                                    @endcan
                                    @can('Manage Sales')
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="pills-user-tab-2" href="#manage-sales" data-bs-toggle="pill"
                                            data-bs-toggle="#pills-user-2">{{ __('Sales') }}</a>
                                    </li>
                                    @endcan
                                  
                                </ul>
                            </div>


                            <div class="tab-content">
                                @can('Manage Purchases')
                                    <div class="tab-pane fade show active" id="manage-purchases" role="tabpanel">
                                        <div class="row justify-content-md-center">
                                            <div class="col-xl-2 col-md-6">
                                                <div class="card text-white btn-gradient-info">
                                                    <div class="card-header total-purchased-amount"></div>
                                                    <div class="card-body">
                                                        <h5 class="card-title text-white">{{ __('Purchased Amount') }}</h5>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-2 col-md-6">
                                                <div class="card text-white btn-gradient-secondary">
                                                    <div class="card-header total-purchased-product-tax-amount"></div>
                                                    <div class="card-body">
                                                        <h5 class="card-title text-white">{{ __('Product Tax Amount') }}</h5>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="card collapse multi-collapse-purchase">
                                                    <div class="card-body p-3 shadow-sm">
                                                        <div
                                                            class="row input-daterange purchase-analysis-datepicker align-items-center">
                                                            <div class="form-group col-md-3 mb-0">
                                                                {{ Form::label('duration1', __('Date Duration'), ['class' => 'col-form-label']) }}
                                                                <div class="input-group"  style="width: 751px;">
                                                                    {{-- {{ Form::text('duration', __('Select Date Range'), ['class' => 'form-control', 'id' => 'duration1', 'placeholder' => __('Select Date Range')]) }}
                                                                {{ Form::hidden('start_date1', $start_date, ['class' => 'form-control', 'id' => 'purchased-start-date']) }}
                                                                {{ Form::hidden('due_date1', $end_date, ['class' => 'form-control', 'id' => 'purchased-end-date']) }} --}}


                                                                    <div class="col-lg-4 col-md-9 col-sm-12">
                                                                        <input type='text' class="form-control"
                                                                            id="pc-daterangepicker-1" placeholder="Select time"
                                                                            type="text" />
                                                                        {{ Form::hidden('start_date1', $start_date, ['class' => 'form-control', 'id' => 'start_date1']) }}
                                                                        {{ Form::hidden('due_date1', $end_date, ['class' => 'form-control', 'id' => 'end_date1']) }}
                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <div class="form-group col-md-3 mb-0">
                                                                {{ Form::label('vendor_id', __('Vendor'), ['class' => 'col-form-label']) }}
                                                                <div class="input-group">
                                                                    {{ Form::select('vendor_id', $vendors, null, ['class' => 'form-control','id' => 'vendor_id','data-toggle' => 'select']) }}
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-md-3 mb-0 {{ $display_status }}">
                                                                {{ Form::label('purchase_branch_id', __('Branch'), ['class' => 'col-form-label']) }}
                                                                <div class="input-group">
                                                                    {{ Form::select('purchase_branch_id', $branches, null, ['class' => 'form-control','id' => 'purchase_branch_id','data-toggle' => 'select']) }}
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-md-3 mb-0 {{ $display_status }}">
                                                                {{ Form::label('purchase_cash_register_id', __('Cash Register'), ['class' => 'col-form-label']) }}
                                                                <div class="input-group">
                                                                    {{ Form::select('purchase_cash_register_id', $cash_registers, null, ['class' => 'form-control','id' => 'purchase_cash_register_id','data-toggle' => 'select']) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h3 class="mb-0 h5 float-left">{{ __('Purchase Tax Report') }}</h3>
                                                        <button type="button"
                                                            class="float-right btn btn-sm btn-primary btn-icon m-1"
                                                            data-bs-toggle="collapse" data-bs-target=".multi-collapse-purchase"
                                                            title="{{ __('Filter') }}">
                                                            <i class="ti ti-filter text-white"></i>
                                                        </button>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row mt-3">
                                                            <div class="col-sm-12 table-responsive" id="myTable">
                                                                <table class="table dataTable purchase_myTable " role="grid">
                                                                    <thead class="thead-light">
                                                                        <tr role="row">
                                                                            <th>{{ __('Reference No.') }}</th>
                                                                            <th>{{ __('Date') }}</th>
                                                                            <th>{{ __('Vendor') }}</th>
                                                                            <th>{{ __('Product Tax') }}</th>
                                                                            <th>{{ __('Grand Total') }}</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <tfoot>
                                                                        <tr>
                                                                            <td rowspan="1" colspan="1"></td>
                                                                            <td rowspan="1" colspan="1"></td>
                                                                            <td rowspan="1" colspan="1"></td>
                                                                            <td rowspan="1" colspan="1">
                                                                                <h5 class="h6"
                                                                                    id="totalpurchasetaxamount"></h5>
                                                                            </td>
                                                                            <td rowspan="1" colspan="1">
                                                                                <h5 class="h6"
                                                                                    id="totalpurchasesubtotal"></h5>
                                                                            </td>
                                                                        </tr>
                                                                    </tfoot>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                                @can('Manage Sales')
                                    <div class="nav-link fade" id="manage-sales" role="tabpanel">
                                        <div class="row justify-content-md-center">
                                            <div class="col-xl-2 col-md-6">
                                                {{-- <div class="card bg-gradient-success border-0 m-0">
                                                    <div class="card-body py-3">
                                                        <div class="row text-center">
                                                            <div class="col justify-content-center">
                                                                <span
                                                                    class="h5 font-weight-bold mb-0 text-white total-saled-amount"></span>
                                                                <h5 class="fs-14 card-title text-uppercase mb-0 text-white">
                                                                    {{ __('Sales Amount') }}</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                                <div class="card text-white btn-gradient-info">
                                                    <div class="card-header total-saled-amount"></div>
                                                    <div class="card-body">
                                                        <h5 class="card-title text-white">{{ __('Sales Amount') }}</h5>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-2 col-md-6">
                                               <div class="card text-white btn-gradient-secondary">
                                                    <div class="card-header total-saled-product-tax-amount"></div>
                                                    <div class="card-body">
                                                        <h5 class="card-title text-white">{{ __('Product Tax Amount') }}</h5>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col">
                                                <div class="card collapse multi-collapse-sale">
                                                    <div class="card-body p-3 shadow-sm">
                                                        <div
                                                            class="row input-daterange sale-analysis-datepicker align-items-center">
                                                            <div class="form-group col-md-3 mb-0">
                                                                {{ Form::label('duration1', __('Date Duration'), ['class' => 'col-form-label']) }}
                                                                <div class="input-group"  style="width: 751px;">
                                                                    {{-- {{ Form::text('duration', __('Select Date Range'), ['class' => 'form-control', 'id' => 'sale-duration', 'placeholder' => __('Select Date Range')]) }}
                                                                {{ Form::hidden('start_date1', $start_date, ['class' => 'form-control', 'id' => 'sale-start-date']) }}
                                                                {{ Form::hidden('due_date1', $end_date, ['class' => 'form-control', 'id' => 'sale-end-date']) }} --}}

                                                                    <div class="col-lg-4 col-md-9 col-sm-12">
                                                                        <input type='text' class="form-control"
                                                                            id="pc-daterangepicker-2" placeholder="Select time"
                                                                            type="text" />
                                                                        {{ Form::hidden('start_date1', $start_date, ['class' => 'form-control', 'id' => 'start_date1']) }}
                                                                        {{ Form::hidden('due_date1', $end_date, ['class' => 'form-control', 'id' => 'end_date1']) }}
                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <div class="form-group col-md-3 mb-0">
                                                                {{ Form::label('customer_id', __('Customer'), ['class' => 'col-form-label']) }}
                                                                <div class="input-group">
                                                                    {{ Form::select('customer_id', $customers, null, ['class' => 'form-control','id' => 'customer_id','data-toggle' => 'select']) }}
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-md-3 mb-0 {{ $display_status }}">
                                                                {{ Form::label('sale_branch_id', __('Branch'), ['class' => 'col-form-label']) }}
                                                                <div class="input-group">
                                                                    {{ Form::select('sale_branch_id', $branches, null, ['class' => 'form-control','id' => 'sale_branch_id','data-toggle' => 'select']) }}
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-md-3 mb-0 {{ $display_status }}">
                                                                {{ Form::label('sale_cash_register_id', __('Cash Register'), ['class' => 'col-form-label']) }}
                                                                <div class="input-group">
                                                                    {{ Form::select('sale_cash_register_id', $cash_registers, null, ['class' => 'form-control','id' => 'sale_cash_register_id','data-toggle' => 'select']) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h3 class="mb-0  h5 float-left">{{ __('Sale Tax Report') }}</h3>
                                                        <a class="float-right btn btn-sm btn-primary btn-icon m-1"
                                                            data-bs-toggle="collapse" data-bs-target=".multi-collapse-sale"
                                                            title="{{ __('Filter') }}">
                                                            <i class="ti ti-filter text-white"></i>
                                                        </a>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row mt-3">
                                                            <div class="col-sm-12 table-responsive" id="myTable2">
                                                                <table class="table dataTable sale_myTable2" role="grid">
                                                                    <thead class="thead-light">
                                                                        <tr role="row">
                                                                            <th>{{ __('Reference No.') }}</th>
                                                                            <th>{{ __('Date') }}</th>
                                                                            <th>{{ __('Customer') }}</th>
                                                                            <th>{{ __('Product Tax') }}</th>
                                                                            <th>{{ __('Grand Total') }}</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <tfoot>
                                                                        <tr>
                                                                            <td rowspan="1" colspan="1"></td>
                                                                            <td rowspan="1" colspan="1"></td>
                                                                            <td rowspan="1" colspan="1"></td>
                                                                            <td rowspan="1" colspan="1">
                                                                                <h5 class="h6" id="totalsaletaxamount">
                                                                                </h5>
                                                                            </td>
                                                                            <td rowspan="1" colspan="1">
                                                                                <h5 class="h6" id="totalsalesubtotal">
                                                                                </h5>
                                                                            </td>
                                                                        </tr>
                                                                    </tfoot>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection


    @push('scripts')
        <script src="{{ asset('assets/js/plugins/flatpickr.min.js') }}"></script>
        <script>
            document.querySelector("#pc-daterangepicker-1").flatpickr({
                mode: "range",
                onChange: function(selectedDates, dateStr, instance) {
                    var dates = dateStr.split(" to ");
                    var start = moment(dates[0]).format('YYYY-MM-DD');
                    var end = moment(dates[0]).format('YYYY-MM-DD');
                    $('#start_date1').val(start);
                    $('end_date1').val(end);
                    if (dates.length == 1) {
                        var end = moment(dates[1]).format('YYYY-MM-DD');
                        $('end_date1').val(end);
                        if (typeof ajax_product_purchase_tax_analysis_filter == 'function') {
                            ajax_product_purchase_tax_analysis_filter();
                        }
                    }
                }
            });
        </script>
    @endpush



    @push('scripts')
        <script src="{{ asset('assets/js/plugins/flatpickr.min.js') }}"></script>
        <script>
            document.querySelector("#pc-daterangepicker-2").flatpickr({
                mode: "range",
                onChange: function(selectedDates, dateStr, instance) {
                    var dates = dateStr.split(" to ");
                    var start = moment(dates[0]).format('YYYY-MM-DD');
                    var end = moment(dates[0]).format('YYYY-MM-DD');
                    $('#start_date1').val(start);
                    $('end_date1').val(end);
                    if (dates.length == 1) {
                        var end = moment(dates[1]).format('YYYY-MM-DD');
                        $('end_date1').val(end);
                        if (typeof ajax_product_sale_tax_analysis_filter == 'function') {
                            ajax_product_sale_tax_analysis_filter();
                        }
                    }
                }
            });
        </script>
    @endpush

    @push('scripts')
        <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
        <script type="text/javascript">
            function ajax_product_purchase_tax_analysis_filter() {

                var data = {
                    'start_date': $('#purchased-start-date').val(),
                    'end_date': $('#purchased-end-date').val(),
                    'branch_id': $('#purchase_branch_id').val(),
                    'cash_register_id': $('#purchase_cash_register_id').val(),
                }

                $('#myTable .purchase_myTable').DataTable({
                        "destroy": true,
                        "paging": true,
                        "ordering": false,
                        "processing": true,
                        "pageLength": 10,
                        "language": dataTabelLang,
                        "ajax": {
                            "type": "GET",
                            "url": '{{ route('product.purchase.tax.analysis.filter') }}',
                            "data": data,
                        },
                        "columns": [{
                                "data": "invoice_id"
                            },
                            {
                                "data": "created_at"
                            },
                            {
                                "data": "vendorname"
                            },
                            {
                                "data": "tax_amount"
                            },
                            {
                                "data": "sub_total"
                            },
                        ],
                    })
                    .on("xhr.dt", function(e, settings, json, xhr) {
                        $('#totalpurchasetaxamount').text(json.totalPurchasedTaxAmount);
                        $('#totalpurchasesubtotal, .total-purchased-amount').text(json.totalPurchasedSubTotal);
                        $('.total-purchased-product-tax-amount').text(json.totalPurchasedTaxAmount);
                    });
            }

            // $(function () {
            //     function cb(start, end) {
            //         $("#duration1").val(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            //         $('#purchased-start-date').val(start.format('YYYY-MM-DD'));
            //         $('#purchased-end-date').val(end.format('YYYY-MM-DD'));
            //         ajax_product_purchase_tax_analysis_filter();
            //     }

            //     $('#duration1').daterangepicker({
            //         // timePicker: true,
            //         autoApply: true,
            //         autoclose: true,
            //         autoUpdateInput: false,
            //         // startDate: start,
            //         // endDate: end,
            //         locale: {
            //             format: 'MMM D, YY hh:mm A',
            //             applyLabel: "Apply",
            //             cancelLabel: "Cancel",
            //             fromLabel: "From",
            //             toLabel: "To",
            //             daysOfWeek: [
            //                 '{{ __('Sun') }}',
            //                 '{{ __('Mon') }}',
            //                 '{{ __('Tue') }}',
            //                 '{{ __('Wed') }}',
            //                 '{{ __('Thu') }}',
            //                 '{{ __('Fri') }}',
            //                 '{{ __('Sat') }}'
            //             ],
            //             monthNames: [
            //                 '{{ __('January') }}',
            //                 '{{ __('February') }}',
            //                 '{{ __('March') }}',
            //                 '{{ __('April') }}',
            //                 '{{ __('May') }}',
            //                 '{{ __('June') }}',
            //                 '{{ __('July') }}',
            //                 '{{ __('August') }}',
            //                 '{{ __('September') }}',
            //                 '{{ __('October') }}',
            //                 '{{ __('November') }}',
            //                 '{{ __('December') }}'
            //             ],
            //         }
            //     }, cb);
            // });


            function ajax_product_sale_tax_analysis_filter() {

                var data = {
                    'start_date': $('#sale-start-date').val(),
                    'end_date': $('#sale-end-date').val(),
                    'branch_id': $('#sale_branch_id').val(),
                    'cash_register_id': $('#sale_cash_register_id').val(),
                }

                $('#myTable2 .sale_myTable2').DataTable({
                        "destroy": true,
                        "paging": true,
                        "ordering": false,
                        "processing": true,
                        "pageLength": 10,
                        "language": dataTabelLang,
                        "ajax": {
                            "type": "GET",
                            "url": '{{ route('product.sale.tax.analysis.filter') }}',
                            "data": data,
                        },
                        "columns": [{
                                "data": "invoice_id"
                            },
                            {
                                "data": "created_at"
                            },
                            {
                                "data": "customername"
                            },
                            {
                                "data": "tax_amount"
                            },
                            {
                                "data": "sub_total"
                            },
                        ],
                    })
                    .on("xhr.dt", function(e, settings, json, xhr) {
                        $('#totalsaletaxamount').text(json.totalSaledTaxAmount);
                        $('#totalsalesubtotal, .total-saled-amount').text(json.totalSaledSubTotal);
                        $('.total-saled-product-tax-amount').text(json.totalSaledTaxAmount);
                    });
            }

            // $(function () {
            //     function cb(start, end) {
            //         $("#sale-duration").val(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            //         $('#sale-start-date').val(start.format('YYYY-MM-DD'));
            //         $('#sale-end-date').val(end.format('YYYY-MM-DD'));
            //         ajax_product_sale_tax_analysis_filter();
            //     }

            //     $('#sale-duration').daterangepicker({
            //         // timePicker: true,
            //         autoApply: true,
            //         autoclose: true,
            //         autoUpdateInput: false,
            //         // startDate: start,
            //         // endDate: end,
            //         locale: {
            //             format: 'MMM D, YY hh:mm A',
            //             applyLabel: "Apply",
            //             cancelLabel: "Cancel",
            //             fromLabel: "From",
            //             toLabel: "To",
            //             daysOfWeek: [
            //                 '{{ __('Sun') }}',
            //                 '{{ __('Mon') }}',
            //                 '{{ __('Tue') }}',
            //                 '{{ __('Wed') }}',
            //                 '{{ __('Thu') }}',
            //                 '{{ __('Fri') }}',
            //                 '{{ __('Sat') }}'
            //             ],
            //             monthNames: [
            //                 '{{ __('January') }}',
            //                 '{{ __('February') }}',
            //                 '{{ __('March') }}',
            //                 '{{ __('April') }}',
            //                 '{{ __('May') }}',
            //                 '{{ __('June') }}',
            //                 '{{ __('July') }}',
            //                 '{{ __('August') }}',
            //                 '{{ __('September') }}',
            //                 '{{ __('October') }}',
            //                 '{{ __('November') }}',
            //                 '{{ __('December') }}'
            //             ],
            //         }
            //     }, cb);
            // });

            $(document).ready(function() {

                // $('[data-toggle="select"]').select2({});
                $('#purchase_cash_register_id, #vendor_id').trigger('change');
                ajax_product_purchase_tax_analysis_filter();

            });

            $(document).on('change', '#purchase_cash_register_id, #vendor_id', function(e) {

                ajax_product_purchase_tax_analysis_filter();
            });

            $(document).on('change', '#purchase_branch_id', function(e) {

                $.ajax({
                    url: '{{ route('get.cash.registers') }}',
                    dataType: 'json',
                    async: false,
                    data: {
                        'branch_id': $(this).val()
                    },
                    success: function(data) {
                        $('#purchase_cash_register_id').find('option').not(':first').remove();
                        $.each(data, function(key, value) {
                            $('#purchase_cash_register_id')
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

                ajax_product_purchase_tax_analysis_filter();
            });

            $(document).on('change', '#sale_cash_register_id, #customer_id', function(e) {

                ajax_product_sale_tax_analysis_filter();
            });

            $(document).on('change', '#sale_branch_id', function(e) {

                $.ajax({
                    url: '{{ route('get.cash.registers') }}',
                    dataType: 'json',
                    async: false,
                    data: {
                        'branch_id': $(this).val()
                    },
                    success: function(data) {
                        $('#sale_cash_register_id').find('option').not(':first').remove();
                        $.each(data, function(key, value) {
                            $('#sale_cash_register_id')
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

                ajax_product_sale_tax_analysis_filter();
            });

            $(document).on('click', '[href="#manage-sales"]', function(e) {

                if (!$(this).hasClass('sale-active')) {
                    ajax_product_sale_tax_analysis_filter();
                    $(this).addClass('sale-active');
                }
            });
        </script>
    @endpush
@endcan


@push('old-datatable-js')
    <script src="{{ asset('custom/js/jquery.dataTables.min.js') }}"></script>
    <script>
        var dataTabelLang = {
            paginate: {
                previous: "<i class='fas fa-angle-left'>",
                next: "<i class='fas fa-angle-right'>"
            },
            lengthMenu: "{{ __('Show') }} _MENU_ {{ __('entries') }}",
            zeroRecords: "{{ __('No data available in table.') }}",
            info: "{{ __('Showing') }} _START_ {{ __('to') }} _END_ {{ __('of') }} _TOTAL_ {{ __('entries') }}",
            infoEmpty: "{{ __('Showing 0 to 0 of 0 entries') }}",
            infoFiltered: "{{ __('(filtered from _MAX_ total entries)') }}",
            search: "{{ __('Search:') }}",
            thousands: ",",
            loadingRecords: "{{ __('Loading...') }}",
            processing: "{{ __('Processing...') }}"
        };

        var site_currency_symbol_position = '{{ \App\Models\Utility::getValByName('site_currency_symbol_position') }}';
        var site_currency_symbol = '{{ \App\Models\Utility::getValByName('site_currency_symbol') }}';
    </script>
@endpush
