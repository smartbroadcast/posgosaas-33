

@extends('layouts.master')

@php
$lang = (!empty(\Cookie::get('language'))) ? \Cookie::get('language') : 'en';
@endphp
@section('content')
    <div class="col-xl-6">
        <div class="card">


            <div class="card-body">

                <div class="">
                    <h2 class="mb-3 f-w-600">{{ 'Reset Password' }}</h2>
                </div>


                @if (Session::has('message'))
                    <div class="alert {{ Session::get('alert-class', 'alert-info') }} alert-dismissible fade show ">
                        {{ Session::get('message') }}
                        <button type="button" class="btn-close mt-3" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif


                <form method="POST" action="{{ route('password.update') }}" id="form_data" class="needs-validation" novalidate="">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <div class="">
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input id="email" type="email"  placeholder="{{ __('Email') }}"
                                class="form-control @error('email') is-invalid @enderror" name="email"
                                value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <label class="form-label">{{ __('Password') }}</label>
                                </div>

                            </div>

                            <input id="password" type="password" placeholder="{{ __('Password') }}"
                               class="form-control @error('password') is-invalid @enderror" name="password"
                               required autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                  

                        <div class="form-group mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <label class="form-label">{{ __('Confirm Password') }}</label>
                                </div>

                            </div>

                            <input id="password_confirmation" type="password" placeholder="{{ __('Confirm Password') }}"
                               class="form-control" name="password_confirmation" required autocomplete="current-password">
                       
                        </div>



                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-block mt-2"
                               >{{ __('Reset Password') }}</button>
                        </div>
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


