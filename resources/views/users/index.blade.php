@extends('layouts.app')

@if (Auth::user()->parent_id == 0)
    @section('page-title', __('Manage Owners'))
@else
    @section('page-title', __('Users List'))
@endif

@php
$user = Auth::user();

$image_url = !empty($user->avatar) && asset(Storage::exists($user->avatar)) ? $user->avatar : 'logo/avatar.png';
// dd($image_url);
@endphp

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">
            @if (Auth::user()->parent_id == 0)
                {{ __('Manage Owners') }}
            @else
                {{ __('Users List') }}
            @endif
        </h5>
    </div>
@endsection

@section('action-btn')

    <a class="btn btn-sm btn-primary grid" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Grid View') }}">
        <i class="ti ti-layout-grid"></i>
    </a>

    <a class="btn btn-sm btn-primary list" data-bs-toggle="tooltip" data-bs-original-title="{{ __('List View') }}">
        <i class="ti ti-list-check"></i>
    </a>

    @can('Create User')
        <a href="#" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
            data-title="@if (Auth::user()->parent_id == 0) {{ __('Add Owner') }} @else {{ __('Add User') }} @endif"
            title="@if (Auth::user()->parent_id == 0) {{ __('Add Owner') }} @else {{ __('Add User') }} @endif"
            data-url="{{ route('users.create') }}" class="btn btn-sm btn-primary btn-icon m-1">
            <i class="ti ti-plus text-white"></i></a>
        </a>
    @endcan
@endsection

@section('breadcrumb')

    @if (Auth::user()->parent_id == 0)
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item">{{ __('Owners') }}</li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item">{{ __('Users') }}</li>
    @endif
@endsection

@section('content')
    <section class="section list_view" style="display:none">
        @can('Manage User')
            <div class="row mt-4">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header card-body table-border-style">

                            <div class="table-responsive">
                                <table class="table" id="pc-dt-simple">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Lastlogin') }}</th>
                                            @if (Auth::user()->isSuperAdmin())
                                                <th>{{ __('Total Users') }}</th>
                                                <th>{{ __('Plan') }}</th>
                                                <th>{{ __('Plan Expiry Date') }}</th>
                                            @else
                                                <th>{{ __('User Role') }}</th>
                                            @endif
                                            <th width="200px">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $key => $user)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ ucfirst($user->name) }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->last_login_at }}</td>
                                                @if (Auth::user()->isSuperAdmin())
                                                    <td>{{ $user->users }}</td>
                                                    <td>{{ !empty($user->getPlan) ? $user->getPlan->name : '' }}</td>
                                                    <td>
                                                        @if (!empty($user->plan_expire_date))
                                                            {{ Auth::user()->datetimeFormat($user->plan_expire_date) }}
                                                        @else
                                                            {{ __('Unlimited') }}
                                                        @endif
                                                    </td>
                                                @else
                                                    <td>
                                                        @foreach ($user->roles()->pluck('name') as $pername)
                                                            <span
                                                                class="badge bg-success p-2 px-3 rounded">{{ $pername }}</span>
                                                        @endforeach
                                                    </td>
                                                @endif
                                                <td class="Action">
                                                    @if (Auth::user()->isSuperAdmin())
                                                        <div class="action-btn btn-warning ms-2">
                                                            <a href="#" data-ajax-popup="true" data-bs-toggle="tooltip"
                                                                data-title="{{ __('Upgrade Plan') }}"
                                                                title="{{ __('Upgrade Plan') }}" data-size="lg"
                                                                data-url="{{ route('plan.upgrade', $user->id) }}"
                                                                class="mx-3 btn btn-sm d-inline-flex align-items-center">
                                                                <i class="ti ti-trophy text-white"></i>
                                                            </a>
                                                        </div>
                                                    @endif

                                                    @if ($user->is_active == 1)
                                                        @can('Edit User')
                                                            <div class="action-btn btn-info ms-2">
                                                                <a href="#" data-ajax-popup="true" data-bs-toggle="tooltip"
                                                                    data-title="{{ __('Edit User') }}" title="{{ __('Edit') }}"
                                                                    data-size="lg" data-url="{{ route('users.edit', $user->id) }}"
                                                                    class="mx-3 btn btn-sm d-inline-flex align-items-center">
                                                                    <i class="ti ti-pencil text-white"></i>
                                                                </a>
                                                            </div>
                                                        @endcan
                                                        @can('Delete User')
                                                            <div
                                                                class="action-btn  {{ $user->user_status == 1 ? 'btn-success' : 'btn-danger' }}  ms-2 sd">
                                                                <a href="#" data-confirm="{{ __('Are You Sure?') }}"
                                                                    data-text="Do you want to continue?" data-bs-toggle="tooltip"
                                                                    data-confirm-yes="status-form-{{ $user->id }}"
                                                                    class="bs-pass-para mx-3 btn btn-sm d-inline-flex align-items-center">
                                                                    @if ($user->user_status == 1)
                                                                        <i class="ti ti-user-check text-white"
                                                                            data-bs-toggle="tooltip" title="{{ __('Active') }}"
                                                                            data-title="{{ __('Active') }}"></i>
                                                                    @else
                                                                        <i class="ti ti-user-x text-white" data-bs-toggle="tooltip"
                                                                            title="{{ __('Deactive') }}"
                                                                            data-title="{{ __('Deactive') }}"></i>
                                                                    @endif
                                                                </a>
                                                            </div>
                                                        @endcan
                                                    @else
                                                        <a href="#" class="">
                                                            <i class="ti ti-lock text-white"></i>
                                                        </a>
                                                    @endif
                                                    @if (Auth::user()->isSuperAdmin() || Auth::user()->isOwner())
                                                        <div class="action-btn btn-secondary ms-2">
                                                            <a href="#"
                                                                class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                                data-url="{{ route('user.reset', \Crypt::encrypt($user->id)) }}"
                                                                data-ajax-popup="true" data-bs-toggle="tooltip"
                                                                title="{{ __('Reset Password') }}" data-toggle="tooltip"
                                                                data-title="{{ __('Reset Password') }}"><i
                                                                    class="ti ti-key text-white"></i> </a>
                                                        </div>
                                                    @endif
                                                    {!! Form::open(['method' => 'PATCH', 'route' => ['user.status', $user->id], 'id' => 'status-form-' . $user->id]) !!}
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
        @endcan
    </section>

    <section class="section grid_view">
        @can('Manage User')
            <div class="row mt-3">
                @foreach ($users as $user)
                    <div class="col-xl-3">

                        <div class="card  text-center">
                            <div class="card-header border-0 pb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        {{-- <div class="badge p-2 px-3 rounded bg-primary">{{$user->name }}</div> --}}

                                        @if (Auth::user()->isOwner())
                                            @foreach ($user->roles()->pluck('name') as $pername)
                                                <div class="badge p-2 px-3 rounded bg-primary">{{ $pername }}</div>
                                            @endforeach
                                        @endif  
                                          
                                        @can('Delete User')
                                        <div
                                            class="action-btn  {{ $user->user_status == 1 ? 'btn-success' : 'btn-danger' }}  ms-2 sd">
                                            <a href="#" data-confirm="{{ __('Are You Sure?') }}"
                                                data-text="Do you want to continue?" data-bs-toggle="tooltip"
                                                data-confirm-yes="status-form-{{ $user->id }}"
                                                class="bs-pass-para mx-3 btn btn-sm d-inline-flex align-items-center">
                                                @if ($user->user_status == 1)
                                                    <i class="ti ti-user-check text-white"
                                                        data-bs-toggle="tooltip" title="{{ __('Active') }}"
                                                        data-title="{{ __('Active') }}"></i>
                                                @else
                                                    <i class="ti ti-user-x text-white" data-bs-toggle="tooltip"
                                                        title="{{ __('Deactive') }}"
                                                        data-title="{{ __('Deactive') }}"></i>
                                                @endif
                                            </a>
                                        </div>
                                    @endcan


                                    </h6>
                                </div>
                                <div class="card-header-right">
                                    <div class="btn-group card-option">
                                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            @if ($user->is_active == 1)
                                                <a href="#" class="dropdown-item"
                                                    data-url="{{ route('users.edit', $user->id) }}" data-size="md"
                                                    data-ajax-popup="true" data-title="{{ __('Update User') }}"><i
                                                        class="ti ti-pencil"></i><span
                                                        class="ms-2">{{ __('Edit') }}</span></a>
                                            @endif

                                            @if (Auth::user()->isSuperAdmin() || Auth::user()->isOwner())
                                                <a href="#" class="dropdown-item" data-ajax-popup="true"
                                                    data-size="md" data-title="{{ __('Change Password') }}"
                                                    data-url="{{ route('user.reset', \Crypt::encrypt($user->id)) }}"><i
                                                        class="ti ti-key"></i>
                                                    <span class="ms-1">{{ __('Reset Password') }}</span></a>
                                            @endif
                                            {!! Form::open(['method' => 'PATCH', 'route' => ['user.status', $user->id], 'id' => 'status-form-' . $user->id]) !!}
                                            {!! Form::close() !!}

                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="card-body">
                                <div class="avtar">
                                    <a class="theme-avtar rounded-circle"href="{{ asset(Storage::url("avatar/")).'/'}}{{ !empty($user->avatar)?$user->avatar:'avatar.png' }}" target="_blank">
                                        <img alt="" alt="wid-75 rounded-circle grid-img" src="{{ asset(Storage::url("avatar/")).'/'}}{{ !empty($user->avatar)?$user->avatar:'avatar.png' }}"  class="img-fluid rounded-circle wid-75 rounded-circle grid-img">
                                    </a>
                                    {{-- <span class="theme-avtar rounded-circle"> <img
                                            src="{{ asset(Storage::url($user->avatar)) }}"
                                            class="wid-75 rounded-circle grid-img"
                                            onerror="this.onerror=null;this.src='{{ asset('public/img/theme/avatar.png') }}';"></span> --}}
                                </div>

                                <h4 class="mt-2">{{ $user->name }}</h4>

                                <small>{{ $user->email }}</small> 

                               
                                
                                @if (\Auth::user()->type == 'Super Admin')


                                <div class="mt-4">
                                    <div class="row justify-content-between align-items-center">
                                        <div class="col-6 text-center mb-2">
                                            <span class="d-block font-bold mb-0">{{ !empty($user->getPlan) ? $user->getPlan->name : '' }}</span>
                                        </div>
                                        <div class="col-6 text-center Id mb-2">
                                            <div class="col-6 text-center Id mb-2">
                                                <a href="#" data-url="{{ route('plan.upgrade', $user->id) }}"
                                                    data-size="lg" data-ajax-popup="true"
                                                    data-title="{{ __('Upgrade Plan') }}"
                                                    class="btn small--btn btn-outline-primary text-sm">{{ __('Upgrade Plan') }}</a>

                                                     {{-- <div class="col-7 text-end">
                                                        <a href="#" data-url="{{ route('plan.upgrade', $user->id) }}"
                                                            class="btn btn-sm btn-primary btn-icon" data-size="lg"
                                                            data-ajax-popup="true"
                                                            data-title="{{ __('Upgrade Plan') }}">{{ __('Upgrade Plan') }}</a>
                                                    </div> --}}
                                            </div>
                                        </div>
                                        {{-- <div class="col-12">
                                            <hr class="my-3">
                                        </div> --}}
                                        
                                        <div class="col-12 text-center pb-2">
                                            <a class="text-muted">{{ __('Users : ') }}
                                                {{ $user->users }}</a><br><br>
                                                <a>{{ __('Plan Expire : ') }}
                                                    {{ !empty($user->plan_expire_date) ? \Auth::user()->dateFormat($user->plan_expire_date) : 'Unlimited' }}</a>
                                        </div>
                                    </div>
                                </div>
                                   
                                @endif   
                            </div>
                        </div>
                    </div>
                @endforeach 
                @can('Create User')
                
                <div class="col-xl-3 col-lg-4 col-sm-4">
                    <a href="#" class="btn-addnew-project  " data-ajax-popup="true" data-url="{{ route('users.create') }}"
                        data-title="@if (Auth::user()->parent_id == 0) {{ __('Add Owner') }} @else {{ __('Add User') }} @endif" data-bs-toggle="tooltip" title=""
                        class="btn btn-primary" data-bs-original-title="{{ __('Create') }}">
                        <div class="badge bg-primary proj-add-icon">
                            <i class="ti ti-plus"></i>
                        </div>
                        <h6 class="mt-4 mb-2">@if (Auth::user()->parent_id == 0) {{ __('Add Owner') }} @else {{ __('Add User') }} @endif</h6>
                        
                        <p class="text-muted text-center">@if (Auth::user()->parent_id == 0) {{ __('Click here to add new Owner') }} @else {{ __('Click here to add new User') }} @endif</p>
                    </a>
                </div>  
                @endcan


            </div>
        @endcan
    </section>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $(".grid").hide();
            $(".grid").click(function() {
                $(".grid_view").show();
                $(".list_view").hide();
                $(".list").show();
                $(".grid").hide();
            });

            $(".list").click(function() {
                $(".grid").show();
                $(".list_view").show();
                $(".grid_view").hide();
                $(".list").hide();
                $(".grid").show();
            });
        });
    </script>

    <script type="text/javascript">
        $(document).on('change', '#branch_id', function(e) {
            $.ajax({
                url: '{{ route('get.cash.registers') }}',
                dataType: 'json',
                data: {
                    'branch_id': $(this).val()
                },
                success: function(data) {
                    $('#cash_register_id').find('option').not(':first').remove();
                    $.each(data, function(key, value) {
                        $('#cash_register_id')
                            .append($("<option></option>")
                                .attr("value", value.id)
                                .text(value.name));
                    });
                },
                error: function(data) {
                    data = data.responseJSON;
                    show_toastr('{{ __('Error') }}', data.message, 'error');
                }
            });
        });
    </script>
@endpush
