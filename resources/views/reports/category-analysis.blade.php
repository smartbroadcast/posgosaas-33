@extends('layouts.app')

@section('page-title', __('Category Analysis'))

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Category Analysis') }}</h5>
    </div>
@endsection

@section('action-btn')
    <a class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="collapse" data-bs-toggle="tooltip"
        title="{{ __('Filter') }}" data-title="{{ __('Filter') }}" data-bs-target=".multi-collapse">
        <i class="ti ti-filter text-white"></i>
    </a>
@endsection

@push('old-datatable-css')
    <link rel="stylesheet" href="{{ asset('custom/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('custom/css/customdatatable.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/flatpickr.min.css') }}">
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Reports') }}</li>
    <li class="breadcrumb-item">{{ __('Category Analysis') }}</li>
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
                    if (typeof ajax_product_category_analysis_filter == 'function') {
                        ajax_product_category_analysis_filter();
                    }
                }
            }
        });
    </script>
@endpush


@can('Manage Category')

    @section('content')
        <div class="row">
            <div class="col-12">
                <div class="card collapse multi-collapse">
                    <div class="card-body  p-3">
                        <div class="row input-daterange analysis-datepicker align-items-center">
                            <div class="form-group col-md-3  mb-0">
                                {{ Form::label('duration1', __('Date Duration'), ['class' => 'col-form-label']) }}
                                <div class="input-group" style="width: 787px;">
                                 
                        
                                    <div class="col-lg-4 col-md-9 col-sm-12">
                                        <input type='text' class="form-control" id="pc-daterangepicker-1"
                                            placeholder="Select time" type="text" />
                                        {{ Form::hidden('start_date1', $start_date, ['class' => 'form-control', 'id' => 'start_date1']) }}
                                        {{ Form::hidden('due_date1', $end_date, ['class' => 'form-control', 'id' => 'end_date1']) }}
                                    </div>


                                </div>
                            </div>
                            <div class="form-group col-md-3 mb-0">
                                {{ Form::label('category_id', __('Product Category'), ['class' => 'col-form-label']) }}
                                <div class="input-group">
                                    {{ Form::select('category_id', $product_categories, null, ['class' => 'form-control','id' => 'category_id','data-toggle' => 'select']) }}
                                </div>
                            </div>
                            <div class="form-group col-md-3 mb-0 {{ $display_status }}">
                                {{ Form::label('branch_id', __('Branch'), ['class' => 'col-form-label']) }}
                                <div class="input-group">
                                    {{ Form::select('branch_id', $branches, null, ['class' => 'form-control','id' => 'branch_id','data-toggle' => 'select']) }}
                                </div>
                            </div>
                            <div class="form-group col-md-3 mb-0 {{ $display_status }}">
                                {{ Form::label('cash_register_id', __('Cash Register'), ['class' => 'col-form-label']) }}
                                <div class="input-group">
                                    {{ Form::select('cash_register_id', $cash_registers, null, ['class' => 'form-control','id' => 'cash_register_id','data-toggle' => 'select']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 

                <div class="row">
                    <div class="col-xxl-12">
                        <div class="row">
                            <div class="col">
                                <div class="card" style="min-height: 92px;">
                                    <div class="card-body">
                                        <div class="row align-items-center justify-content-between">
                                            <div class="col-auto">
                                                <div class="theme-avtar bg-info">
                                                    <i class="ti ti-box"></i>
                                                </div>
                                            </div>
                                            <div class="col-auto mb-3 mb-sm-0">
                                                <small class="text-muted">{{ __('Total Purchased Quantity') }}</small>
                                                <h6 class="m-0" id="totalpurchasedquantity"></h6>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card" style="min-height: 92px;">
                                    <div class="card-body">
                                        <div class="row align-items-center justify-content-between">
                                            <div class="col-auto">
                                                <div class="theme-avtar bg-info">
                                                    <i class="ti ti-box"></i>
                                                </div>
                                            </div>
                                            <div class="col-auto mb-3 mb-sm-0">
                                                <small class="text-muted">{{ __('Total Sold Quantity') }}</small>
                                                <h6 class="m-0" id="totalsoldquantity"></h6>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card" style="min-height: 92px;">
                                    <div class="card-body">
                                        <div class="row align-items-center justify-content-between">
                                            <div class="col-auto">
                                                <div class="theme-avtar bg-primary">
                                                    <i class="ti ti-report-money"></i>
                                                </div>
                                            </div>
                                            <div class="col-auto mb-3 mb-sm-0">
                                                <small class="text-muted">{{ __('Total Purchased Price') }}</small>
                                                <h6 class="m-0" id="totalpurchasedprice"></h6>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card" style="min-height: 92px;">
                                    <div class="card-body">
                                        <div class="row align-items-center justify-content-between">
                                            <div class="col-auto">
                                                <div class="theme-avtar bg-primary">
                                                    <i class="ti ti-report-money"></i>
                                                </div>
                                            </div>
                                            <div class="col-auto mb-3 mb-sm-0">
                                                <small class="text-muted">{{ __('Total Sold Price') }}</small>
                                                <h6 class="m-0" id="totalsoldprice"></h6>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card" style="min-height: 92px;">
                                    <div class="card-body">
                                        <div class="row align-items-center justify-content-between">
                                            <div class="col-auto">
                                                <div class="theme-avtar bg-primary">
                                                    <i class="ti ti-report-money"></i>
                                                </div>
                                            </div>
                                            <div class="col-auto mb-3 mb-sm-0">
                                                <small class="text-muted">{{ __('Total Profit/Loss') }}</small>
                                                <h6 class="m-0" id="totalprofitorloss"></h6>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card table-card">
                    <div class="card-header card-body table-border-style">
                        <div class="col-sm-12 table-responsive table_over" id="myTable">
                            <table class="table dataTable category_myTable" role="grid">
                                <thead class="thead-light">
                                    <tr role="row">
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Purchased Quantity') }}</th>
                                        <th>{{ __('Sold Quantity') }}</th>
                                        <th>{{ __('Purchased Price') }}</th>
                                        <th>{{ __('Sold Price') }}</th>
                                        <th>{{ __('Profit/Loss') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <tfoot>
                                    {{-- <tr>
                                        <td rowspan="1" colspan="1">
                                            <h5 class="h6">{{ __('Grand Total') }}</h5>
                                        </td>
                                        <td rowspan="1" colspan="1"></td>
                                        <td rowspan="1" colspan="1">
                                            <h5 class="h6" id="totalpurchasedquantity"></h5>
                                        </td>
                                        <td rowspan="1" colspan="1">
                                            <h5 class="h6" id="totalsoldquantity"></h5>
                                        </td>
                                        <td rowspan="1" colspan="1">
                                            <h5 class="h6" id="totalpurchasedprice"></h5>
                                        </td>
                                        <td rowspan="1" colspan="1">
                                            <h5 class="h6" id="totalsoldprice"></h5>
                                        </td>
                                        <td rowspan="1" colspan="1">
                                            <h5 class="h6" id="totalprofitorloss"></h5>
                                        </td>
                                    </tr> --}}
                                </tfoot>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
        <script type="text/javascript">
            function ajax_product_category_analysis_filter() {

                var data = {
                    'start_date': $('#start-date').val(),
                    'end_date': $('#end-date').val(),
                    'category_id': $('#category_id').val(),
                    'branch_id': $('#branch_id').val(),
                    'cash_register_id': $('#cash_register_id').val(),
                }

                $('#myTable .category_myTable').DataTable({
                        "destroy": true,
                        "paging": true,
                        "ordering": false,
                        "processing": true,
                        "pageLength": 10,
                        "language": dataTabelLang,
                        "ajax": {
                            "type": "GET",
                            "url": '{{ route('product.category.analysis.filter') }}',
                            "data": data,
                        },
                        "columns": [{
                                "data": "id"
                            },
                            {
                                "data": "name"
                            },
                            {
                                "data": "purchased_quantity"
                            },
                            {
                                "data": "sold_quantity"
                            },
                            {
                                "data": "purchased_price"
                            },
                            {
                                "data": "sold_price"
                            },
                            {
                                "data": "profitorloss"
                            },
                        ],
                    })
                    .on("xhr.dt", function(e, settings, json, xhr) {
                        $('#totalpurchasedquantity').text(json.totalPurchasedQuantity);
                        $('#totalpurchasedprice').text(json.totalPurchasedPrice);
                        $('#totalsoldquantity').text(json.totalSoldQuantity);
                        $('#totalsoldprice').text(json.totalSoldPrice);
                        $('#totalprofitorloss').html(json.totalProfitOrLoss);
                    });
            }



            $(document).ready(function() {
                ajax_product_category_analysis_filter();
                $(document).on('change', '#category_id', function(e) {
                    ajax_product_category_analysis_filter();
                });
            });

            $(document).on('change', '#cash_register_id', function(e) {
                ajax_product_category_analysis_filter();
            });

            $(document).on('change', '#branch_id', function(e) {

                $.ajax({
                    url: '{{ route('get.cash.registers') }}',
                    dataType: 'json',
                    async: false,
                    data: {
                        'branch_id': $(this).val()
                    },
                    success: function(data) {
                        $('#cash_register_id').find('option').not(':first').remove();
                        $.each(data, function(key, value) {
                            $('#cash_register_id')
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

                ajax_product_category_analysis_filter();
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
