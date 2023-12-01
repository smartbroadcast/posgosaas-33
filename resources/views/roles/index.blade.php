@extends('layouts.app')

@section('page-title', __('Roles List'))

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Roles List') }}</h5>
    </div>
@endsection

@section('action-btn')
    @can('Create Role')
        <a href="#" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="{{ __('Add Role') }}"
            data-title="{{ __('Add Role') }}" data-url="{{ route('roles.create') }}" class="btn btn-sm btn-primary btn-icon">
            <i class="ti ti-plus text-white"></i></a>
        </a>
    @endcan

@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Role') }}</li>
@endsection

@section('content')

    @can('Manage Role')
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header card-body table-border-style">

                        <div class="table-responsive">
                            <table class="table" id="pc-dt-simple">

                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Role') }}</th>
                                        <th>{{ __('Permissions') }}</th>
                                        <th class="text-right">{{ __('Operation') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $key => $role)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $role->name }}</td>
                                            <td style="white-space: inherit !important;">
                                                @foreach ($role->permissions()->pluck('name') as $pername)
                                                    <span class="badge rounded p-2 m-1 px-3 bg-primary ">
                                                        <a href="#" class="absent-btn text-white">{{ $pername }}</a>
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td class="pull-right">
                                                @can('Edit Role')
                                                    <div class="action-btn btn-info ms-2">
                                                        <a href="#" data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            data-title="{{ __('Edit Role') }}" data-size="lg"
                                                            data-url="{{ route('roles.edit', $role->id) }}"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                            title="{{ __('Edit') }}">
                                                            <i class="ti ti-pencil text-white"> </i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Delete Role')
                                                    <div class="action-btn bg-danger ms-2">
                                                        <a href="#"
                                                            class="bs-pass-para mx-3 btn btn-sm d-inline-flex align-items-center"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                            data-title="{{ __('Delete') }}"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $role->id }}">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                    </div>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id], 'id' => 'delete-form-' . $role->id]) !!}
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
    <script>
        $(document).on('click', '#select-all', function(e) {
            if (this.checked) {
                $(':checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $(':checkbox').each(function() {
                    this.checked = false;
                });
            }
        });
    </script>
@endpush
