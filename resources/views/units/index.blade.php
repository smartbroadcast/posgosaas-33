@extends('layouts.app')

@section('page-title', __('Units List'))

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Units List') }}</h5>
    </div>
@endsection


@section('action-btn')
    @can('Create Unit')
        <a href="#" data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title="{{ __('Add New Unit') }}"
            data-title="{{ __('Add New Unit') }}" data-url="{{ route('units.create') }}"
            class="btn btn-sm btn-primary btn-icon ">
            <span class=""><i class="ti ti-plus text-white"></i></span>
        </a>
    @endcan
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Unit') }}</li>
@endsection

@section('content')
    @can('Manage Unit')
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table" id="pc-dt-simple">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Unit Name') }}</th>
                                        <th>{{ __('Short Name') }}</th>
                                        <th width="200px">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($units as $key => $unit)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $unit->name }}</td>
                                            <td>{{ $unit->shortname }}</td>
                                            <td class="Action">
                                                @can('Edit Unit')
                                                    <div class="action-btn btn-info ms-2">
                                                        <a href="#" data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            data-title="{{ __('Edit Unit') }}" title="{{ __('Edit Unit') }}"
                                                            data-size="md" data-url="{{ route('units.edit', $unit->id) }}"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Delete Unit')
                                                    <div class="action-btn bg-danger ms-2">
                                                        <a href="#"
                                                            class="bs-pass-para mx-3 btn btn-sm d-inline-flex align-items-center"
                                                            data-toggle="sweet-alert" data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-bs-toggle="tooltip"
                                                            data-confirm-yes="delete-form-{{ $unit->id }}"
                                                            title="{{ __('Delete') }}">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                    </div>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['units.destroy', $unit->id], 'id' => 'delete-form-' . $unit->id]) !!}
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
