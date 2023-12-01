@extends('layouts.master')


@section('content')
    <div class="col-xl-6">
        <div class="card">


            <div class="card-body">

                <div class="">
                    <h2 class="mb-3 f-w-600">{{ 'Login' }}</h2>
                </div>


                @if (Session::has('message'))
                    <div class="alert {{ Session::get('alert-class', 'alert-info') }} alert-dismissible fade show ">
                        {{ Session::get('message') }}
                        <button type="button" class="btn-close mt-3" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif


                <form method="POST" action="{{ route('login') }}" id="form_data" class="needs-validation" novalidate="">
                    @csrf
                    <div class="">
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input id="email" type="email" placeholder="{{ __('Email') }}"
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

                            <input id="input-password" type="password" placeholder="{{ __('Password') }}"
                                class="form-control @error('password') is-invalid @enderror" name="password" required
                                autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-4">
                            <div class="mb-3">
                                <div class="text-left">
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request', $lang) }}"
                                            class="small text-muted text-underline--dashed border-primary">
                                            {{ __('Forgot your password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                        </div>

                        @if (env('RECAPTCHA_MODULE') == 'yes')
                            <div class="form-group mb-3">
                                {!! NoCaptcha::display() !!}
                                @error('g-recaptcha-response')
                                    <span class="small text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        @endif


                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-block mt-2"
                                id="login_button">{{ __('Login') }}</button>
                        </div>
                        @if (Utility::getValByName('disable_signup_button') == 'on')
                            <div class="my-4 text-center">
                                <p>{{ __("Don't have an account?") }} <a
                                        href="{{ route('register', $lang) }}">{{ __('Register') }}</a></p>

                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('custom-scripts')
    <script src="{{ asset('custom/libs/jquery/dist/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#form_data").submit(function(e) {
                $("#login_button").attr("disabled", true);
                return true;
            });
        });
    </script>

    @if (env('RECAPTCHA_MODULE') == 'yes')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endpush
