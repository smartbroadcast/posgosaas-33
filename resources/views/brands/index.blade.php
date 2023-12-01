@extends('layouts.app')

@section('page-title', __('Brands List'))

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Brands List') }}</h5>
    </div>
@endsection

@section('action-btn')
    @can('Create Brand')
        <a href="#" data-ajax-popup="true" data-size="md" data-title="{{ __('Add New Brand') }}"
            data-url="{{ route('brands.create') }}" data-bs-toggle="tooltip" title="{{ __('Add Brand') }}"
            class="btn btn-sm btn-primary btn-icon">
            <span class=""><i class="ti ti-plus text-white"></i></span>
        </a>
    @endcan
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Brands') }}</li>
@endsection

@section('content')
    @can('Manage Brand')
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header card-body table-border-style">
                        
                        <div class="table-responsive">
                            <table class="table" id="pc-dt-simple">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Brand Name') }}</th>
                                        <th width="200px">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($brands as $key => $brand)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $brand->name }}</td>
                                            <td class="Action">
                                                @can('Edit Brand')
                                                    <div class="action-btn btn-info ms-2">
                                                        <a href="#" data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            data-title="{{ __('Edit Brand') }}" title="{{ __('Edit Brand') }}"
                                                            data-size="md" data-url="{{ route('brands.edit', $brand->id) }}"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Delete Brand')
                                                    <div class="action-btn bg-danger ms-2">
                                                        <a href="#"
                                                            class="bs-pass-para mx-3 btn btn-sm d-inline-flex align-items-center"
                                                            data-toggle="sweet-alert" data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-bs-toggle="tooltip"
                                                            data-confirm-yes="delete-form-{{ $brand->id }}"
                                                            title="{{ __('Delete') }}">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>



                                                    </div>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['brands.destroy', $brand->id], 'id' => 'delete-form-' . $brand->id]) !!}
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
