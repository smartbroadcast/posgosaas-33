@extends('layouts.app')

@section('page-title', __('Products List'))

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Products List') }}</h5>
    </div>
@endsection

@section('action-btn')

    <a href="{{ route('Product.export') }}" class="btn btn-sm btn-primary btn-icon " data-bs-toggle="tooltip"
        title="{{ __('Export') }}">
        <i class="ti ti-file-export text-white"></i>
    </a>
    @can('Create Product')
        <a href="#" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="{{ __('Add New Product') }}"
            data-title="{{ __('Add New Product') }}" data-url="{{ route('products.create') }}"
            class="btn btn-sm btn-primary btn-icon m-1">
            <span class=""><i class="ti ti-plus text-white"></i></span>
        </a>
    @endcan
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Product') }}</li>
@endsection

@section('content')
    @can('Manage Product')
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header card-body table-border-style">
                        {{-- <h5></h5> --}}
                        <div class="table-responsive">
                            <table class="table" id="pc-dt-simple">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="w-25">{{ __('Name') }}</th>
                                        <th>{{ __('Brand') }}</th>
                                        <th>{{ __('Category') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Barcode') }}</th>
                                        <th class="text-right">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $key => $product)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td><span class="break-all">{{ $product->name }}</span></td>
                                            <td>{{ $product->brandname }}</td>
                                            <td>{{ $product->categoryname }}</td>
                                            <td>
                                                @if ($product->getTotalProductQuantity() > \App\Models\Utility::settings()['low_product_stock_threshold'])
                                                    <span
                                                        class="badge bg-success p-2 px-3 rounded">{{ $product->quantity }}</span>
                                                @else
                                                    <span
                                                        class="badge bg-danger p-2 px-3 rounded">{{ $product->quantity }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div id="{{ $product->id }}"
                                                    class="product_barcode product_barcode_hight_de"
                                                    data-skucode="{{ $product->sku }}"></div>
                                            </td>
                                            <td class="text-right">
                                                @can('Edit Product')
                                                    <div class="action-btn btn-info ms-2">

                                                        <a href="#" data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            data-title="{{ __('Edit Product') }}"
                                                            title="{{ __('Edit Product') }}" data-size="lg"
                                                            data-url="{{ route('products.edit', $product->id) }}"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center edit-product">
                                                            <i class="ti ti-pencil text-white" title="{{ __('Edit') }}"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Delete Product')
                                                    <div class="action-btn bg-danger ms-2">
                                                        <a href="#"
                                                            class="bs-pass-para mx-3 btn btn-sm d-inline-flex align-items-center"
                                                            data-toggle="sweet-alert" data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $product->id }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>

                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['products.destroy', $product->id], 'id' => 'delete-form-' . $product->id]) !!}
                                                        {!! Form::close() !!}
                                                    @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('scripts')
    <script src="{{ asset('public/vendor/unisharp/laravel-ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/js/jquery-barcode.min.js') }}"></script>
    <script src="{{ asset('public/js/jquery-barcode.js') }}"></script>
    <script>
        $(document).on('click', '.add-poduct, .edit-product', function() {
            setTimeout(function() {
                img_display();
            }, 1000);
        });

        $(document).on('click', '.product-img-btn', function() {
            $('input[name="imgstatus"]').val(1);
            $(this).closest('#product-image').find('img').attr("src", "");
            $(this).closest('#product-image').find('button').addClass('d-none');
        });
        
        $(document).ready(function() {
            $(".product_barcode").each(function() {
                var id = $(this).attr("id");
                var sku = $(this).data('skucode');
                generateBarcode(sku, id);
            });
        });

        function generateBarcode(val, id) {
            var value = val;
            var btype = '{{ $barcode['barcodeType'] }}';
            var renderer = '{{ $barcode['barcodeFormat'] }}';
            var settings = {
                output: renderer,
                bgColor: '#FFFFFF',
                color: '#000000',
                barWidth: '1',
                barHeight: '50',
                moduleSize: '5',
                posX: '10',
                posY: '20',
                addQuietZone: '1'
            };
            $('#' + id).html("").show().barcode(value, btype, settings);

        }

        $(document).on('change', 'input[name="sku"]', function() {
            var str = $(this).val();
            if (str !== '') {
                var val = is_Dash(str);
                if (val == false) {
                    show_toastr("{{ __('Error') }}", "Please enter a valid sku format.(use ' - ' in sku code)",
                        'error');
                }
            }
        });

        function img_display() {
            if ($('#product-image img.profile-image').attr('src') == undefined) {
                $("#product-image img.profile-image").addClass('d-none');
            } else {
                $("#product-image img.profile-image").removeClass('d-none');
            }

        }

        function is_Dash(str) {
            regexp = /[\-]+/i;

            if (regexp.test(str)) {
                return true;
            } else {
                return false;
            }
        }
    </script>
@endpush
