@extends('layouts.app')

@section('page-title', __('Cash Registers List'))

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Cash Registers List') }}</h5>
    </div>
@endsection

@section('action-btn')
    @can('Create Cash Register')
        <a data-ajax-popup="true" data-title="{{ __('Add New Cash Register') }}"
            data-url="{{ route('cashregisters.create') }}" data-bs-toggle="tooltip" title="{{ __('Cash Register') }}"
            class="btn btn-sm btn-primary btn-icon ">
            <span class=""><i class="ti ti-plus text-white"></i></span>
        </a>
    @endcan
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Settings') }}</li>
    <li class="breadcrumb-item">{{ __('Cash Registers') }}</li>
@endsection

@section('content')
    @can('Manage Cash Register')
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header card-body table-border-style">

                        <div class="table-responsive">
                            <table class="table" id="pc-dt-simple" role="grid">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Cash Register Name') }}</th>
                                        <th>{{ __('Branch Name') }}</th>
                                        <th width="200px">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cashregisters as $key => $cashregister)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $cashregister->name }}</td>
                                            <td>{{ $cashregister->branchname }}</td>
                                            <td class="Action">
                                                @can('Edit Cash Register')
                                                    <div class="action-btn btn-info ms-2">
                                                        <a href="#" data-ajax-popup="true"
                                                            data-title="{{ __('Edit Cash Register') }}"
                                                            title="{{ __('Edit') }}" data-bs-toggle="tooltip"
                                                            data-url="{{ route('cashregisters.edit', $cashregister->id) }}"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Delete Cash Register')
                                                    <div class="action-btn bg-danger ms-2">
                                                        <a href="#"
                                                            class="bs-pass-para mx-3 btn btn-sm d-inline-flex align-items-center"
                                                            data-toggle="sweet-alert" data-bs-toggle="tooltip"
                                                            title="{{ __('Delete') }}"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $cashregister->id }}">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                    </div>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['cashregisters.destroy', $cashregister->id], 'id' => 'delete-form-' . $cashregister->id]) !!}
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
