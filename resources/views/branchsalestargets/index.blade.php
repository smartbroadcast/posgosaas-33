@extends('layouts.app')

@section('page-title', __('Branch Sales Target'))

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Branch Sales Target') }}</h5>  
    </div>
@endsection

@section('action-btn')
    @can('Create Branch Sales Target')
        <a data-ajax-popup="true" data-title="{{ __('Create New Sales Target') }}"
            data-url="{{ route('branchsalestargets.create') }}" data-bs-toggle="tooltip" title="{{ __('Sales Target') }}"
            data-title="{{ __('Sales Target') }}" class="btn btn-sm btn-primary btn-icon m-1">
            <span class=""><i class="ti ti-plus text-white"></i></span>
        </a>
    @endcan
@endsection

@push('stylesheets')

@endpush


@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Settings') }}</li>
    <li class="breadcrumb-item">{{ __('Branch Sales Target') }}</li>
@endsection

@section('content')
    @can('Manage Branch Sales Target')

        @if (!empty($saletarget) && count($saletarget) > 0)
            <div class="min-vh-78 mt-3">
                @foreach ($saletarget as $target)
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h3 class="mb-0 h6">{{ $target['month'] }}</h3>
                                        </div>
                                        <div class="col text-end">
                                            @can('Edit Branch Sales Target')
                                                <div class="action-btn btn-info ms-2">
                                                    <a href="#" data-ajax-popup="true" data-bs-toggle="tooltip"
                                                        data-title="{{ __('Edit Sales Target') }}" title="{{ __('Edit') }}"
                                                        data-url="{{ route('branchsalestargets.edit', $target['id']) }}"
                                                        class="mx-3 btn btn-sm d-inline-flex align-items-center">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('Delete Branch Sales Target')
                                                <div class="action-btn bg-danger ms-2">
                                                    <a href="#"
                                                        class="bs-pass-para mx-3 btn btn-sm d-inline-flex align-items-center"
                                                        data-toggle="sweet-alert" data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $target['id'] }}">
                                                        <i class="ti ti-trash text-white" title="{{ __('Delete') }}"></i>
                                                    </a>
                                                </div>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['branchsalestargets.destroy', $target['id']], 'id' => 'delete-form-' . $target['id']]) !!}
                                                {!! Form::close() !!}
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <table class="table align-items-center mb-0 ">
                                        <thead class="thead-light">
                                            <tr class="border-top-0">
                                                <th class="w-25">{{ __('Branch Name') }}</th>
                                                <th class="w-25">{{ __('Target') }}</th>
                                                <th class="w-25">{{ __('Sales') }}</th>
                                                <th class="w-25">{{ __('Progress') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list">
                                            @if (isset($target['branch']) && !empty($target['branch'] && count($target['branch']) > 0))
                                                @for ($i = 0; $i < count($target['branch']); $i++)
                                                    <tr>
                                                        <th scope="row">
                                                            <div class="media align-items-center">
                                                                <div class="media-body">
                                                                    <span
                                                                        class="name mb-0 text-sm">{{ $target['branch'][$i] }}</span>
                                                                </div>
                                                            </div>
                                                        </th>
                                                        <td class="budget">
                                                            {{ $target['totaltarget'][$i] }}
                                                        </td>
                                                        <td>
                                                            {{ $target['totalselledprice'][$i] }}
                                                        </td>
                                                        <td class="circular-progressbar p-0">
                                                            <?php
                                                            $percentage = $target['percentage'][$i];
                                                            
                                                            $status = $percentage > 0 && $percentage <= 25 ? 'red' : ($percentage > 25 && $percentage <= 50 ? 'orange' : ($percentage > 50 && $percentage <= 75 ? 'blue' : ($percentage > 75 && $percentage <= 100 ? 'green' : '')));
                                                            ?>
                                                            <div class="flex-wrapper">
                                                                <div class="single-chart">
                                                                    <svg viewBox="0 0 36 36"
                                                                        class="circular-chart {{ $status }}">
                                                                        <path class="circle-bg" d="M18 2.0845
                                                                                  a 15.9155 15.9155 0 0 1 0 31.831
                                                                                  a 15.9155 15.9155 0 0 1 0 -31.831" />
                                                                        <path class="circle"
                                                                            stroke-dasharray="{{ $percentage }}, 100" d="M18 2.0845
                                                                                  a 15.9155 15.9155 0 0 1 0 31.831
                                                                                  a 15.9155 15.9155 0 0 1 0 -31.831" />
                                                                        <text x="18" y="20.35"
                                                                            class="percentage">{{ $percentage }}%</text>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endfor
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="row min-vh-78">
                <div class="col">
                    <div class="card">
                        <div class="card-header text-center">
                            <h3 class="mb-0">{{ __('No Target Records Found.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan
@endsection

@push('scripts')
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
@endpush
