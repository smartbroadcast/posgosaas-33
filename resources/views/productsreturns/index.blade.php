@extends('layouts.app')

@section('page-title', __('Returns'))

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Returns') }}</h5>
    </div>
@endsection

@section('action-btn')

    <a href="{{ route('productsreturns.export') }}" data-bs-toggle="tooltip" class="btn btn-sm btn-primary btn-icon"
        title="{{ __('Export') }}">
        <i class="ti ti-file-export text-white"></i>
    </a>
    @can('Create Returns')
        <a href="{{ route('productsreturn.create') }}" data-bs-toggle="tooltip" title="{{ __('Add Return') }}"
            class="btn btn-sm btn-primary btn-icon m-1">
            <span class=""><i class="ti ti-plus text-white"></i></span>
        </a>
    @endcan

@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Product Returns') }}</li>
@endsection

@push('old-datatable-css')
    <link rel="stylesheet" href="{{ asset('custom/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('custom/css/customdatatable.css') }}">
@endpush

@section('content')
    @can('Manage Returns')
        <div class="row">
            <div class="col-xl-12">
                <div class="card ">
                    <div class="card-header card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table" id="pc-dt-simple" role="grid">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Reference No') }}</th>
                                        <th>{{ __('Vendor') }}</th>
                                        <th>{{ __('Customer') }}</th>
                                        <th>{{ __('Grand Total') }}</th>
                                        <th  width="200px">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($returns as $key => $return)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $return->date }}</td>
                                            <td>{{ $return->reference_no }}</td>
                                            <td>{{ $return->vendor != null ? ucfirst($return->vendor->name) : __('Walk-in Vendor') }}
                                            </td>
                                            <td>{{ $return->customer != null ? ucfirst($return->customer->name) : __('Walk-in Customer') }}
                                            </td>
                                            <td>{{ Auth::user()->priceFormat($return->getTotal()) }}</td>
                                            <td class="Action">
                                                @can('Edit Returns')
                                                    <div class="action-btn btn-info ms-2">
                                                        <a href="{{ route('productsreturn.edit', $return->id) }}"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                            data-bs-toggle="tooltip" data-title="{{ __('Edit return') }}"
                                                            title="{{ __('Edit') }}"><i class="ti ti-pencil text-white"></i></a>
                                                    </div>
                                                @endcan
                                                @can('Delete Returns')
                                                    <div class="action-btn bg-danger ms-2">
                                                        <a href="#"
                                                            class="bs-pass-para mx-3 btn btn-sm d-inline-flex align-items-center"
                                                            data-toggle="sweet-alert" data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $return->id }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}"><i
                                                                class="ti ti-trash text-white"></i>
                                                        </a>
                                                        </a>


                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['productsreturn.destroy', $return->id], 'id' => 'delete-form-' . $return->id]) !!}
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
