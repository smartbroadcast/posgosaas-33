@extends('layouts.master')

@section('content')
    <div class="col-xl-6">

        <div class="card-body">
            <div class="">
                <h2 class="mb-3 f-w-600">{{ __('Create an account') }}</h2>


               
            </div>
            <form method="POST" action="{{ route('register') }}" role="form">
                @csrf
                <div class="mt-3">
                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Enter Username') }}</label>
                        <input id="name" type="text" placeholder="{{ __('Name') }}"
                            class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}"
                            required autocomplete="name" autofocus>
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Enter Email address') }}</label>
                        <input id="email" type="email" placeholder="{{ __('E-Mail') }}"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" required autocomplete="email">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <small>{{ $message }}</small>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Enter Password') }}</label>
                        <input id="password" type="password" placeholder="{{ __('Password') }}"
                                    class="form-control @error('password') is-invalid @enderror" name="password" required
                                    autocomplete="new-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <small>{{ $message }}</small>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Enter Confirm Password') }}</label>
                        <input id="password-confirm" type="password" class="form-control"
                        placeholder="{{ __('Confirm Password') }}" name="password_confirmation" required
                        autocomplete="new-password">
                        @error('password_confirmation')
                        <span class="error invalid-password_confirmation text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    </div>

                    @if (env('RECAPTCHA_MODULE') == 'yes')
                                <div class="form-group ">
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
                            id="login_button">{{ __('Register') }}</button>
                    </div>
                    @if (Utility::getValByName('disable_signup_button') == 'on')
                        <div class="my-4 text-xs text-muted text-left">
                            <p>
                                {{ __('Already have an account?') }} <a
                                    href="{{ route('login', $lang) }}">{{ __('Sign in') }}</a>
                            </p>

                        </div>
                    @endif

                </div>
            </form>
        </div>
    </div>
@endsection
@push('custom-scripts')
    @if (env('RECAPTCHA_MODULE') == 'yes')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endpush
