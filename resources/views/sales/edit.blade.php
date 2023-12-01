@extends('layouts.app')


@section('page-title', __('Edit Sale'))

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Edit Sale') }}</h5>
    </div>
@endsection


@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.sales') }}">{{ __('Sale List') }}</a></li>
    <li class="breadcrumb-item">{{ __('Edit Sale') }}</li>
@endsection

@push('old-datatable-css')
    <link rel="stylesheet" href="{{ asset('custom/css/jquery.dataTables.min.css') }}">
@endpush

@section('content')
    <div class="row min-vh-100">
        <div class="col-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Sale Update') }}</h5>
                </div>
                <div class="card-body">
                    {{ Form::model($sale, ['route' => ['sales.update', $sale->id], 'method' => 'PUT']) }}

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('customer_id', __('Customers'), ['class' => 'col-form-label']) }}
                                {{ Form::select('customer_id', $customers, null, ['class' => 'form-control', 'data-toggle' => 'select']) }}
                            </div>
                        </div>
                        @if (Auth::user()->isOwner())
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('branch_id', __('Branches'), ['class' => 'col-form-label']) }}
                                    {{ Form::select('branch_id', [], null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('cash_register_id', __('Cash Registers'), ['class' => 'col-form-label']) }}
                                    {{ Form::select('cash_register_id', [], null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                                    </div>
                                    {{ Form::text('searchproducts', null, ['class' => 'form-control','placeholder' => __('Please add products to order list')]) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-md-12">
                            <table class="table carttable">
                                <thead class="thead-light">
                                    <tr role="row">
                                        <th style="width: 25%;">{{ __('Product') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Tax') }}</th>
                                        <th style="width: 12%;">{{ __('Subtotal') }}</th>
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
                        <div class="col-lg-12 text-right">
                            {{ Form::submit(__('Save Change'), ['class' => 'btn btn-primary float-end']) }}
                        </div>  
                    </div>

                    {{ Form::close() }}
                </div>
            </div>

        </div>
    </div>
@endsection


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


@push('stylesheets')
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            var items = [];
            var total = 0;
            $.ajax({
                url: '{{ route('sales.items') }}',
                dataType: 'json',
                data: {
                    'id': '{{ $sale->id }}'
                },
                success: function(data) {

                    if (data.length > 0) {
                        for (var i = 0; i < data.length; i++) {
                            items.push(data[i].product_id);
                            $('<tr id=' + data[i].product_id + '>')
                                .append(
                                    '<td>' + data[i].productname + '</td>' +
                                    '<td>' + addCommas(data[i].price) + '</td>' +
                                    '<td><div class="quantity buttons_added">' +
                                    '<input type="button" value="-" class="minus">' +
                                    '<input type="hidden" name="product[]" value="' + data[i]
                                    .product_id + '">' +
                                    '<input type="number" step="1" min="1" style="width: 50px" name="quantity[]" title="{{ __('Quantity') }}" class="input-number" size="4" data-id="' +
                                    data[i].product_id + '" data-price="' + data[i].price +
                                    '" data-tax="' + data[i].tax + '" value="' + data[i].quantity +
                                    '">' +
                                    '<input type="button" value="+" class="plus"></div></td>' +
                                    '<td>' + data[i].tax + '%</td>' +
                                    '<td><span>' + addCommas(data[i].subtotal) + '</span></td>' +
                                    '<td class="btn btn-sm d-inline-flex align-items-center">' +
                                     '<a class="action-btn bg-danger"><i class="ti ti-trash text-white remove-items"></i></a>' +
                                    '</td>'
                                )
                                .appendTo($('tbody'));
                            total += data[i].subtotal;
                        }
                        $('#total').text(addCommas(total));
                    }
                },
                error: function(data) {
                    data = data.responseJSON;
                    show_toastr('{{ __('Error') }}', data.error, 'error');
                }
            });

            $('input[name="searchproducts"]').autocomplete({
                    minLength: 0,
                    source: function(request, response) {
                        $.getJSON("{{ route('name.search.products') }}", {
                            search: request.term
                        }, response);
                    },
                    search: function() {
                        var term = $.trim(this.value);
                    },
                    select: function(event, ui) {
                        if ($.inArray(ui.item.id, items) == -1) {
                            items.push(ui.item.id);
                            $('<tr id=' + ui.item.id + '>')
                                .append(
                                    '<td>' + ui.item.name + '</td>' +
                                    '<td>' + addCommas(ui.item.price) + '</td>' +
                                    '<td><div class="quantity buttons_added">' +
                                    '<input type="button" value="-" class="minus">' +
                                    '<input type="hidden" name="product[]" value="' + ui.item.id + '">' +
                                    '<input type="number" step="1" min="1" max="' + ui.item.maxquantity +
                                    '" name="quantity[]" title="{{ __('Quantity') }}" class="input-number" size="4" data-id="' +
                                    ui.item.id + '" data-price="' + ui.item.price + '" data-tax="' + ui.item
                                    .tax + '" value="' + ui.item.quantity + '">' +
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
                .autocomplete("instance")._renderItem = function(ul, item) {
                    var ele = ($.inArray(item.id, items) == -1) ? $('<li>') : $(
                        '<li class="bg-primary text-white">');

                    return ele.append("<div>" + item.name + "</div>").appendTo(ul);
                };

            $(document).on('change', 'input[name="quantity[]"]', function(e) {
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
                $(rows).each(function(index, value) {
                    total += getSubTotal($('tr#' + this.id + ' .quantity input[type="number"]'));
                });
                $('#total').text(addCommas(total))
            }

            $(document).on('click', '.remove-items', function(e) {
                e.preventDefault();

                var ele = $(this).closest('tr');

                if (confirm('{{ __('Are you sure you want to remove item?') }}')) {
                    ele.hide(250, function() {
                        ele.remove();
                        manageTotals();
                    });
                    items.remove(parseInt(ele.attr('id')));
                }
            });

            $.ajax({
                url: '{{ route('user.type') }}',
                dataType: 'json',
                success: function(data) {
                    if (data) {
                        if (data[0].isOwner) {
                            $.ajax({
                                url: '{{ route('get.branches') }}',
                                dataType: 'json',
                                success: function(data) {

                                    if (data.length > 0) {

                                        $.each(data, function(key, value) {
                                            $('#branch_id')
                                                .append($("<option></option>")
                                                    .attr("value", value.id)
                                                    .text(value.name));
                                        });

                                        $('#branch_id').trigger('change');
                                    }
                                },
                                error: function(data) {
                                    data = data.responseJSON;
                                    show_toastr('{{ __('Error') }}', data.error,
                                        'error');
                                }
                            });
                        }
                    }
                },
                error: function(data) {
                    data = data.responseJSON;
                    show_toastr('{{ __('Error') }}', data.error, 'error');
                }
            });

            $(document).on('change', '#branch_id', function(e) {
                $.ajax({
                    url: '{{ route('get.cash.registers') }}',
                    dataType: 'json',
                    data: {
                        'branch_id': $(this).val()
                    },
                    success: function(data) {
                        $('#cash_register_id').find('option').remove();
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
            });

        });
    </script>
@endpush
