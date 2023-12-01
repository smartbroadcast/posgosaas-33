@extends('layouts.master')


@section('content')
    <div class="col-xl-6">
        <div class="card-body">
            <div class="">
                <h2 class="mb-3 f-w-600">{{__('Reset Password')}}</h2>
            </div>
             @if(session('status'))
                <div class="alert alert-primary">
                    {{ session('status') }}
                </div>
            @endif
            <p class="mb-4 text-muted">{{__('We will send a link to reset your password')}}</p>
            <form method="POST" action="{{ route('password.email') }}">
            @csrf
                <div class="">
                    
                    <div class="form-group mb-3">
                        <label class="form-label">{{__('Email')}}</label>
                        <input id="email" type="email" placeholder="{{ __('Email') }}"
                        class="form-control @error('email') is-invalid @enderror" name="email"
                        value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <small>{{ $message }}</small>
                        </span>
                        @enderror
                    </div>
                    @if(env('RECAPTCHA_MODULE') == 'yes')
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
                        <button type="submit" class="btn btn-primary btn-block mt-2" id="login_button">Send Password Reset Link</button>
                    </div>
                    @if(Utility::getValByName('disable_signup_button')=='on')
                   
                    <div class="my-4 text-xs text-muted text-center">
                        <p>{{__("Back To")}} <a href="{{route('login',$lang)}}">{{__('Login')}}</a></p>
                    </div>
                    @endif 
                </div>
            </form>
        </div>
    </div>
@endsection
@push('custom-scripts')
@if(env('RECAPTCHA_MODULE') == 'yes')
        {!! NoCaptcha::renderJs() !!}
@endif
@endpush