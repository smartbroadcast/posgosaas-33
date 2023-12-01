@extends('layouts.app')

@section('page-title', __('Categories List'))

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Categories List') }}</h5>
    </div>
@endsection

@section('action-btn')
    @can('Create Expense Category')
        <a class="btn btn-sm btn-primary btn-icon " data-bs-toggle="tooltip" data-ajax-popup="true"
            data-title="{{ __('Add New Category') }}" data-url="{{ route('expensecategories.create') }}"
            title="{{ __('Add Category') }}"><span class="btn-inner--icon"><i class="ti ti-plus text-white"></i></span></a>
    @endcan
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Categories List') }}</li>
@endsection

@section('content')
    @can('Manage Expense Category')
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header card-body table-border-style">

                        <div class="table-responsive">
                            <table class="table" id="pc-dt-simple" role="grid">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Category Name') }}</th>
                                        <th width="200px">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expensecategories as $key => $expensecategory)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $expensecategory->name }}</td>
                                            <td class="Action">
                                                @can('Edit Expense Category')
                                                    <div class="action-btn btn-info ms-2">
                                                        <a href="#" data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            data-title="{{ __('Edit Category') }}" title="{{ __('Edit') }}"
                                                            data-url="{{ route('expensecategories.edit', $expensecategory->id) }}"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Delete Expense Category')
                                                    <div class="action-btn bg-danger ms-2">
                                                        <a href="#"
                                                            class="bs-pass-para mx-3 btn btn-sm d-inline-flex align-items-center"
                                                            data-toggle="sweet-alert" data-bs-toggle="tooltip"
                                                            title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $expensecategory->id }}">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>

                                                    </div>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['expensecategories.destroy', $expensecategory->id], 'id' => 'delete-form-' . $expensecategory->id]) !!}
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
