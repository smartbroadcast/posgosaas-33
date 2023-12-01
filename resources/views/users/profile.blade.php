@extends('layouts.app')
@php
$profile = asset(Storage::url('uploads/avatar/'));
@endphp
@push('css-page')
@endpush
@push('script-page')
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
@endpush
@section('page-title')
    {{ __('Profile') }}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0"> {{ __('Profile') }}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Profile') }}</li>
@endsection
@section('action-btn')
@endsection
@section('content')
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#useradd-1"
                                class="list-group-item list-group-item-action border-0">{{ __('Personal Information') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#useradd-2"
                                class="list-group-item list-group-item-action border-0">{{ __('Change Password') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                        </div>
                    </div>
                </div>


                <div class="col-xl-9">
                    <div id="useradd-1">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('Personal Information') }}</h5>
                                <small> {{ __('Details about your personal information') }}</small>
                            </div>
                            <div class="card-body">
                                {{ Form::model($user, ['route' => ['profile.upload'], 'enctype' => 'multipart/form-data']) }}
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label class="col-form-label text-dark">{{ __('Name') }}</label>
                                            <input class="form-control @error('name') is-invalid @enderror" name="name"
                                                type="text" id="fullname" placeholder="{{ __('Enter Your Name') }}"
                                                value="{{ $user->name }}" required autocomplete="name">
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="email" class="col-form-label text-dark">{{ __('Email') }}</label>
                                            <input class="form-control @error('email') is-invalid @enderror" name="email"
                                                type="text" id="email" placeholder="{{ __('Enter Your Email Address') }}"
                                                value="{{ $user->email }}" required autocomplete="email">
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <div class="choose-files ">
                                                <label for="avatar">
                                                    <div class="d-flex align-items-center">
                                                        <div class=" bg-primary profile_update nowrap">
                                                            <i class="ti ti-upload px-1"></i>
                                                            {{ __('Choose file here') }}
                                                        </div>
                                                        <div class="action-btn bg-danger ms-2">
                                                            <a class="btn btn-sm d-inline-flex align-items-center"
                                                                onclick="document.getElementById('delete_avatar').submit();">
                                                                <i class="ti ti-trash text-white"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <input type="file" class="form-control file d-none" name="profile"
                                                        id="avatar" data-filename="profile_update">
                                                </label>
                                            </div>
                                            <span
                                                class="text-xs text-muted">{{ __('Please upload a valid image file. Size of image should not be more than 2MB.') }}</span>
                                            @error('avatar')
                                                <span class="invalid-feedback text-danger text-xs"
                                                    role="alert">{{ $message }}</span>
                                            @enderror

                                        </div>

                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <input type="submit" value="{{ __('Save Changes') }}"
                                            class="btn btn-print-invoice  btn-primary m-r-10">
                                    </div>
                                </div>
                                </form>

                                {{ Form::close() }}
                            </div>
                            {!! Form::open(['method' => 'DELETE', 'id' => 'delete_avatar', 'route' => ['profile.delete']]) !!}
                            {!! Form::close() !!}
                        </div>
                    </div>

                    <div id="useradd-2">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('Change Password') }}</h5>
                                <small> {{ __('Details about your account password change') }}</small>
                            </div>
                            <div class="card-body">
                                {{ Form::open(['route' => 'update.password', 'method' => 'POST']) }}

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="current_password"
                                                class="col-form-label text-dark">{{ __('Current Password') }}</label>
                                            <input class="form-control" name="current_password" type="password"
                                                id="current_password" required autocomplete="current_password"
                                                placeholder="{{ __('Enter Current Password') }}">
                                            @error('current_password')
                                                <span class="invalid-current_password" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="password"
                                                class="form-label col-form-label text-dark">{{ __('New Password') }}</label>
                                            <input class="form-control" name="password" type="password" id="password"
                                                required autocomplete="password"
                                                placeholder="{{ __('Enter New Password') }}">
                                            @error('password')
                                                <span class="invalid-password" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="confirm_password"
                                                class="form-label col-form-label text-dark">{{ __('Confirm Password') }}</label>
                                            <input class="form-control" name="confirm_password" type="password"
                                                id="confirm_password" required autocomplete="confirm_password"
                                                placeholder="{{ __('Confirm New Password') }}">
                                            @error('confirm_password')
                                                <span class="invalid-confirm_password" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer pr-0">
                                    {{ Form::submit(__('Update'), ['class' => 'btn  btn-primary']) }}
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>


                </div>

            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300,

        })
        $(".list-group-item").click(function() {
            $('.list-group-item').filter(function() {
                return this.href == id;
            }).parent().removeClass('text-primary');
        });
    </script>
@endpush
