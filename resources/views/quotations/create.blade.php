@extends('layouts.app')

@section('page-title')
{{ __('Create Quotations') }}
@endsection

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Create Quotations') }}</h5>
    </div>
@endsection

@section('breadcrumb')
         <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
         <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">{{ __('Quotations') }}</a></li>
        <li class="breadcrumb-item">{{ __('Create Quotations') }}</li>
@endsection

@push('old-datatable-css')
<link rel="stylesheet" href="{{ asset('custom/css/jquery.dataTables.min.css') }}">
@endpush


@section('content')
    <div class="row min-vh-100">
        <div class="col-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5>Quotation Create</h5>
                </div>
                <div class="card-body">
                    {{ Form::open(['url' => 'quotations']) }}

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('date', __('Date'), ['class' => 'col-form-label']) }}
                                {{ Form::text('date', null, ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('reference_no', __('Reference No'), ['class' => 'col-form-label']) }}
                                {{ Form::text('reference_no', null, ['class' => 'form-control' ,'placeholder'=>'Enter reference no']) }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('customer_id', __('Customers'), ['class' => 'col-form-label']) }}
                                {{ Form::select('customer_id', $customers, null, ['class' => 'form-control', 'id' => 'customer_id', 'data-toggle' => 'select']) }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('customer_email', __('Customer Email'), ['class' => 'col-form-label']) }}
                                {{ Form::text('customer_email', null, ['class' => 'form-control', 'id' => 'customer_email' ,'placeholder'=>'Enter customer email']) }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                                    </div>
                                    {{ Form::text('searchproducts', null, ['class' => 'form-control', 'placeholder' => __('Please add products to quotation list')]) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-md-12">
                            <table class="table carttable">
                                <thead class="thead-light">
                                <tr role="row">
                                    <th class="w-25">{{ __('Product') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Tax') }}</th>
                                    <th>{{ __('Subtotal') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td><strong>{{ __('Total') }}</strong></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><strong><span id="total"></span></strong></td>
                                    <td></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {{ Form::label('quotation_note', __('Quotation Note'), ['class' => 'col-form-label']) }}
                                {{ Form::textarea('quotation_note', null, ['class' => 'form-control', 'rows' => 3, 'style' => 'resize: none' ,'placeholder'=>'Enter quotation note']) }}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 text-right">
                            {{ Form::submit(__(' Submit'), ['class' => 'btn btn-primary float-end']) }}
                        </div>  
                    </div>

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('stylesheets')
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script type="text/javascript">
        $('#date').datepicker({
            "format": 'yyyy-mm-dd',
            "startDate": new Date(),
            "autoclose": true
        });

        $("select option[value='']").prop('disabled', !$("select option[value='']").prop('disabled'));

        $("#date").datepicker("setDate", new Date());

        $(function () {
            var items = [];

            $('input[name="searchproducts"]').autocomplete({
                minLength: 0,
                source: function (request, response) {
                    $.getJSON("{{ route('name.search.products') }}", {
                        search: request.term
                    }, response);
                },
                search: function () {
                    var term = $.trim(this.value);
                },
                select: function (event, ui) {
                    if ($.inArray(ui.item.id, items) == -1) {
                        items.push(ui.item.id);
                        $('<tr id=' + ui.item.id + '>')
                            .append(
                                '<td>' + ui.item.name + '</td>' +
                                '<td>' + addCommas(ui.item.price) + '</td>' +
                                '<td><div class="quantity buttons_added">' +
                                '<input type="button" value="-" class="minus">' +
                                '<input type="hidden" name="product[]" value="' + ui.item.id + '">' +
                                '<input type="number" step="1" style="width: 50px" min="1" max="' + ui.item.maxquantity + '" name="quantity[]" title="{{ __('Quantity') }}" class="input-number" size="4" data-id="' + ui.item.id + '" data-price="' + ui.item.price + '" data-tax="' + ui.item.tax + '" value="' + ui.item.quantity + '">' +
                                '<input type="button" value="+" class="plus"></div></td>' +
                                '<td>' + ui.item.tax + '%</td>' +
                                '<td><span>' + addCommas(ui.item.subtotal) + '</span></td>' +
                               '<td class="btn btn-sm d-inline-flex align-items-center">' +
                                    '<a class="action-btn bg-danger"><i class="ti ti-trash text-white remove-items"></i></a>' +
                                    '</td>'
                            )
                            .appendTo($('tbody'));
                        manageTotals();
                    }
                    return true;
                }
            })
                .autocomplete("instance")._renderItem = function (ul, item) {
                var ele = ($.inArray(item.id, items) == -1) ? $('<li>') : $('<li class="bg-primary text-white">')

                return ele.append("<div>" + item.name + "</div>").appendTo(ul);
            };

            $(document).on('change', '#customer_id', function (e) {
                e.preventDefault();

                $.ajax({
                    type: 'GET',
                    url: '{{ route('get.customer.email') }}',
                    dataType: 'json',
                    data: {
                        'id': $(this).val(),
                    },
                    success: function (data) {
                        $('#customer_email').val('');
                        if (data.length > 0) {
                            $('#customer_email').val(data[0]['email']);
                        }
                    }
                });
            });

            $(document).on('change keyup', 'input[name="quantity[]"]', function (e) {
                e.preventDefault();

                var ele = $(this);
                var id = ele.data('id');

                $('tr#' + id + ' td span').text(addCommas(getSubTotal(ele)));

                manageTotals();
            });

            function getSubTotal(ele) {
                var price = ele.data('price');
                var tax = ele.data('tax');
                var quantity = ele.val();

                var subtotal = price * quantity;
                var tax = (subtotal * tax) / 100;

                return subtotal + tax;
            }

            function manageTotals() {
                var total = 0;
                var rows = $("table.carttable tbody > tr:visible");
                $(rows).each(function (index, value) {
                    total += getSubTotal($('tr#' + this.id + ' .quantity input[type="number"]'));
                });

                $('#total').text(addCommas(total))
            }

            $(document).on('click', '.remove-items', function (e) {
                e.preventDefault();

                var ele = $(this).closest('tr');

                if (confirm('{{ __('Are you sure you want to remove item?') }}')) {
                    ele.hide(250, function () {
                        ele.remove();
                        manageTotals();
                    });
                    items.remove(parseInt(ele.attr('id')));
                }
            });
        });
    </script>
@endpush


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