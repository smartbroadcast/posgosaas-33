@extends('layouts.app')

@section('page-title', __('Permissions List') )

@section('action-btn')
    <button type="button" class="btn btn-sm btn-neutral" data-ajax-popup="true" data-title="{{__('Create Permission')}}"
            data-url="{{route('permissions.create')}}">{{ __('Create Permission') }}</button>
@endsection

@section('content')
    <div class="row min-vh-78">
        <div class="col">
            <div class="card">
                <!-- Card header -->
                <div class="card-header">
                    <h3 class="mb-0">{{ __('Permissions List') }}</h3>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-12 table-responsive">
                        <table class="table dataTable" id="datatable-basic" role="grid">
                            <thead class="thead-light">
                            <tr role="row">
                                <th>#</th>
                                <th>{{ __('Permissions') }}</th>
                                <th style="width: 180px;">{{ __('Operation') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($permissions as $key => $permission)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $permission->name }}</td>
                                    <td>
                                        <a href="#" data-ajax-popup="true" data-title="{{__('Edit Permission')}}"
                                           data-url="{{route('permissions.edit', $permission->id)}}"
                                           class="btn btn-info btn-sm">{{ __('Edit') }}</a>

                                        <a href="#" class="btn btn-danger btn-sm" data-toggle="sweet-alert"
                                           data-confirm="{{ __('Are You Sure?') }}|{{ __('This action can not be undone. Do you want to continue?') }}"
                                           data-confirm-yes="document.getElementById('delete-form-{{$permission->id}}').submit();">
                                            {{__('Delete')}}
                                        </a>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['permissions.destroy', $permission->id],'id' => 'delete-form-'.$permission->id]) !!}
                                        {!! Form::close() !!}
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
@endsection
