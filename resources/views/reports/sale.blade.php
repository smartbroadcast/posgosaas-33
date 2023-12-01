@extends('layouts.app')

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Sales') }}</h5>
    </div>
@endsection


@section('action-btn')
        <a  class="btn btn-sm btn-primary btn-icon m-1 " data-bs-toggle="collapse"
                    data-bs-toggle="tooltip" 
                        data-bs-target=".multi-collapse" title="{{ __('Filter') }}"> <i class="ti ti-filter"></i> </a>

        <a href="{{ route('Sale.export') }}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" title="{{ __('Export') }}">
            <i class="ti ti-file-export"></i> 
        </a>

   
@endsection

@section('breadcrumb')
         <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item">{{ __('Sale list') }}</li>
@endsection

@push('old-datatable-css')
<link rel="stylesheet" href="{{ asset('custom/css/jquery.dataTables.min.css') }}">
<link rel="stylesheet" href="{{ asset('custom/css/customdatatable.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/plugins/flatpickr.min.css') }}">
@endpush


@push('scripts')
    <script src="{{ asset('assets/js/plugins/flatpickr.min.js') }}"></script>
    <script>
        // minimum setup
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
                    if (typeof ajax_invoice_filter == 'function') {
                        ajax_invoice_filter();
                    }
                }
            }
        });
    </script>
@endpush

@can('Manage Sales')

    @section('content')
        <div class="row ">
            <div class="col-12">
                <div class="card collapse multi-collapse">
                    <div class="card-body p-3">
                        <div class="row input-daterange analysis-datepicker align-items-center">
                            <div class="form-group col-md-4 mb-0">
                                {{ Form::label('duration1', __('Date Duration'), ['class' => 'form-control-label']) }}
                                <div class="input-group" style="width: 1066px;">
                                    {{-- {{ Form::text('duration', __('Select Date Range'), ['class' => 'form-control', 'id' => 'duration1', 'placeholder' => __('Select Date Range')]) }}
                                    {{ Form::hidden('start_date1', $start_date, ['class' => 'form-control', 'id' => 'start_date1']) }}
                                    {{ Form::hidden('due_date1', $end_date, ['class' => 'form-control', 'id' => 'end_date1']) }} --}}


                                    <div class="col-lg-4 col-md-9 col-sm-12">
                                        <input type='text' class="form-control" id="pc-daterangepicker-1"
                                            placeholder="Select time" type="text" />
                                        {{ Form::hidden('start_date1', $start_date, ['class' => 'form-control', 'id' => 'start_date1']) }}
                                        {{ Form::hidden('due_date1', $end_date, ['class' => 'form-control', 'id' => 'end_date1']) }}
                                    </div>

                                </div>
                            </div>
                            <div class="form-group col-md-4 mb-0">
                                {{ Form::label('sell_to', __('Sold To'), ['class' => 'form-control-label']) }}
                                <div class="input-group">
                                    {{ Form::select('sell_to', $customers, null, ['class' => 'form-control', 'id' => 'sell_to', 'data-toggle' => 'select']) }}
                                </div>
                            </div>
                            <div class="form-group col-md-4 mb-0">
                                {{ Form::label('sell_by', __('Sold By'), ['class' => 'form-control-label']) }}
                                <div class="input-group">
                                    {{ Form::select('sell_by', $users, null, ['class' => 'form-control', 'id' => 'sell_by', 'data-toggle' => 'select']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card table-card">
                    <div class="card-header card-body table-border-style">
                        {{-- <h5></h5> --}}
                    <div class="col-sm-12 table-responsive mt-3 table_over">
                        <table class="table dataTable" id="myTable" role="grid">
                            <thead class="thead-light">
                                <tr role="row">
                                    <th style="width: 277px;">{{ __('Invoice ID') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Sold By') }}</th>
                                    <th>{{ __('Sold To') }}</th>
                                    <th>{{ __('Items Sold') }}</th>
                                    <th>{{ __('Total') }}</th>
                                    <th>{{ __('Payment Status') }}</th>
                                    <th style="width: 180px;">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td rowspan="1" colspan="1">
                                        <h5 class="h6">{{ __('Grand Total') }}</h5>
                                    </td>
                                    <td rowspan="1" colspan="1"></td>
                                    <td rowspan="1" colspan="1"></td>
                                    <td rowspan="1" colspan="1"></td>
                                    <td rowspan="1" colspan="1">
                                        <h5 class="h6" id="totalitems"></h5>
                                    </td>
                                    <td rowspan="1" colspan="1">
                                        <h5 class="h6" id="totalcounts"></h5>
                                    </td>
                                    <td rowspan="1" colspan="1"></td>
                                    <td rowspan="1" colspan="1"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                </div>

            </div>
        </div>

    @endsection

    @push('old-datatable-js')
        
    <script src="{{ asset('custom/js/jquery.dataTables.min.js') }}"></script>
    <script>
        var dataTabelLang = {
            paginate: {previous: "<i class='fas fa-angle-left'>", next: "<i class='fas fa-angle-right'>"},
            lengthMenu: "{{__('Show')}} _MENU_ {{__('entries')}}",
            zeroRecords: "{{__('No data available in table.')}}",
            info: "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
            infoEmpty: "{{ __('Showing 0 to 0 of 0 entries') }}",
            infoFiltered: "{{ __('(filtered from _MAX_ total entries)') }}",
            search: "{{__('Search:')}}",
            thousands: ",",
            loadingRecords: "{{ __('Loading...') }}",
            processing: "{{ __('Processing...') }}"
        };

        var site_currency_symbol_position = '{{ \App\Models\Utility::getValByName('site_currency_symbol_position') }}';
        var site_currency_symbol = '{{ \App\Models\Utility::getValByName('site_currency_symbol') }}';
    </script>

    @endpush

    @push('scripts')
        <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>

        <script>
           
            $(document).on('click', '.copy_link_sale', function(e) {
               e.preventDefault();
               var copyText = $(this).attr('href');
       
               document.addEventListener('copy', function (e) {
                   e.clipboardData.setData('text/plain', copyText);
                   e.preventDefault();
               }, true);
       
               document.execCommand('copy');
               show_toastr('Success', 'Url copied to clipboard', 'success');
           });
       </script>

        <script type="text/javascript">
            

            $(document).ready(function() {
                ajax_invoice_filter();
            });

            $(document).on('change', '#sell_to, #sell_by', function(e) {
                ajax_invoice_filter();
            });

            function ajax_invoice_filter() {

                var data = {
                    'url': '{{ route('invoice.filter') }}',
                    'start_date': $('#start_date1').val(),
                    'end_date': $('#end_date1').val(),
                    'customer_id': $('#sell_to').val(),
                    'user_id': $('#sell_by').val(),
                    'customers': 1,
                }

                $('#myTable').DataTable({
                        "processing": true,
                        "destroy": true,
                        "paging": true,
                        "pageLength": 10,
                        "ordering": false,
                        "language": dataTabelLang,
                        "ajax": {
                            "type": "GET",
                            "url": data.url,
                            "data": data,
                        },
                        "columns": [{
                                "data": "invoice_id"
                            },
                            {
                                "data": "created_at"
                            },
                            {
                                "data": "username"
                            },
                            {
                                "data": "customername"
                            },
                            {
                                "data": "itemscount"
                            },
                            {
                                "data": "itemstotal"
                            },
                            {
                                "data": "paymentstatus"
                            },
                            {
                                "data": "action"
                            }
                        ],
                    })
                    .on("xhr.dt", function(e, settings, json, xhr) {
                        $('#totalitems').text(json.totalItemsCount);
                        $('#totalcounts').text(json.totalCount);
                        setTimeout(function() {
                            loadConfirm();
                        }, 1000);
                    });
            }

            $(document).on('click', '.payment-action', function(e) {
                e.stopPropagation();
                e.preventDefault();

                var ele = $(this);

                var id = ele.parent().attr('data-id');
                var url = ele.parent().attr('data-url');
                var status = ele.attr('data-status');

                $.ajax({
                    url: url,
                    method: 'PATCH',
                    data: {
                        status: status
                    },
                    success: function(response) {

                        if (response) {

                            $('[data-li-id="' + id + '"] .payment-action').removeClass('selected');

                            if (ele.hasClass('selected')) {

                                ele.removeClass('selected');

                            } else {
                                ele.addClass('selected');
                            }

                            var payment = $('[data-li-id="' + id + '"] .payment-actions').find('.selected')
                                .text().trim();

                            var payment_class = $('[data-li-id="' + id + '"] .payment-actions').find(
                                '.selected').attr('data-class');
                            $('[data-li-id="' + id + '"] .payment-label').removeClass(
                                'unpaid partially-paid paid').addClass(payment_class).text(payment);
                        }
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        show_toastr('{{ __('Error') }}', data.error, 'error');
                    }
                });
            });
        </script>
    @endpush
@endcan
