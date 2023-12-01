@extends('layouts.app')

@section('page-title', __('Branches List'))

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Branches List') }}</h5>
    </div>
@endsection

@section('action-btn')
    @can('Create Branch')
        <button type="button" class="btn btn-sm btn-primary btn-icon " data-bs-toggle="tooltip" data-ajax-popup="true"
            data-title="{{ __('Add New Branch') }}" data-url="{{ route('branches.create') }}" title="{{ __('Add Branch') }}">
            <span class=""><i class="ti ti-plus text-white"></i></span>
        </button>
    @endcan
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Settings') }}</li>
    <li class="breadcrumb-item">{{ __('Branches') }}</li>
@endsection

@section('content')
    @can('Manage Branch')
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table" id="pc-dt-simple" role="grid">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Branch Type') }}</th>
                                        <th>{{ __('Branch Manager') }} </th>
                                        <th>{{ __('Cash Register') }}</th>
                                        <th width="200px">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($branches as $key => $branch)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $branch->name }}</td>
                                            <td>{{ $branch->branch_type }}</td>
                                            <td>{{ ucfirst($branch->username) }}</td>
                                            <td>{{ $branch->branchtotal > 0 ? 'Yes (' . $branch->branchtotal . ')' : 'No' }}
                                            </td>
                                            <td class="Action">
                                                @can('Edit Branch')
                                                    <div class="action-btn btn-info ms-2">
                                                        <a href="#" data-ajax-popup="true" data-title="{{ __('Edit Branch') }}"
                                                            title="{{ __('Edit') }}" data-bs-toggle="tooltip"
                                                            data-url="{{ route('branches.edit', $branch->id) }}"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Delete Branch')
                                                    <div class="action-btn bg-danger ms-2">
                                                        <a href="#"
                                                            class="bs-pass-para mx-3 btn btn-sm d-inline-flex align-items-center"
                                                            data-toggle="sweet-alert" data-bs-toggle="tooltip"
                                                            data-confirm="{{ __('Are You Sure?') }}" title="{{ __('Delete') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $branch->id }}">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                    </div>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['branches.destroy', $branch->id], 'id' => 'delete-form-' . $branch->id]) !!}
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
