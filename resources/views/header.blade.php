@php
$user = Auth::user();
if ($user) {
    $currantLang = $user->lang;
    $languages = \App\Models\Utility::languages();
}
$cust_theme_bg = App\Models\Utility::getValByName('cust_theme_bg');


@endphp

 
@if ((isset($cust_theme_bg) && $cust_theme_bg == 'on'))
    <header class="dash-header transprent-bg">
@else
       <header class="dash-header">
@endif

    <div class="header-wrapper">
        <div class="me-auto dash-mob-drp">
            <ul class="list-unstyled">
                <li class="dash-h-item mob-hamburger">
                    <a href="#!" class="dash-head-link" id="mobile-collapse">
                        <div class="hamburger hamburger--arrowturn">
                            <div class="hamburger-box">
                                <div class="hamburger-inner"></div>
                            </div>
                        </div>
                    </a>
                </li>
                <li class="dropdown dash-h-item drp-company">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                            href="#"
                            role="button"
                            aria-haspopup="false"
                            aria-expanded="false"
            >
                    <span class="theme-avtar">
                        <img src="{{(!empty(\Auth::user()->avatar))?  \App\Models\Utility::get_file(\Auth::user()->avatar): asset(Storage::url("uploads/avatar/avatar.png"))}}" class="img-fluid rounded-circle">
                    </span>
                    {{-- <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <img src="{{(!empty(\Auth::user()->avatar))?  \App\Models\Utility::get_file(\Auth::user()->avatar): asset(Storage::url("uploads/avatar/avatar.png"))}}" class="wid-35 rounded-circle"> --}}

                        {{-- @php
                            $image_url = !empty($user->avatar) && asset(Storage::exists($user->avatar)) ? $user->avatar : 'logo/avatar.png';
                        @endphp --}}
                        {{-- <span class="theme-avtar rounded-circle"> <img src="{{ asset(Storage::url($image_url)) }}"
                                class="wid-35 rounded-circle"
                                onerror="this.onerror=null;this.src='{{ asset('public/img/theme/avatar.png') }}';"></span> --}}
                        <span class="hide-mob ms-2">{{ ucfirst(Auth::user()->name) }}</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                    </a>



                    <div class="dropdown-menu dash-h-dropdown">

                        <a href="{{ route('profile.display') }}" class="dropdown-item">
                            <i class="ti ti-user"></i>
                            <span>{{ __('Profile') }}</span>
                        </a>


                        <a href="{{ route('logout') }}" class="dropdown-item"
                            onclick="event.preventDefault(); document.getElementById('logout-form1').submit();">
                            <i class="ti ti-power"></i>
                            <span>{{ __('Logout') }}</span>
                            {!! Form::open(['method' => 'POST', 'id' => 'logout-form1', 'route' => ['logout'], 'style' => 'display:none']) !!}
                            {!! Form::close() !!}
                        </a>
                    </div>
                </li>

            </ul>
        </div>
        <div class="ms-auto">
            <ul class="list-unstyled">

                <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-world nocolor"></i>
                        <span class="drp-text hide-mob">{{ Str::upper($currantLang) }}</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">

                        @foreach ($languages as $language)  

                    
                            {{-- <a href="{{ route('change.language', $language) }}"
                                class="dropdown-item @if ($language == $currantLang) active-language @endif">
                                <span> {{ Str::upper($language) }}</span>
                            </a>   --}}

                            <a href="{{ route('change.language', $language) }}"
                            class="dropdown-item {{ basename(App::getLocale()) == $language ? 'text-primary' : '' }}">{{ Str::upper($language) }}</a>
    

                        @endforeach
                        @if (\Auth::user()->type == 'Super Admin') 
                            <hr class="dropdown-divider">
                            @can('Manage Language')
                                <a class="dropdown-item text-primary"
                                    href="{{ route('manage.language', [isset($currantLang) ? $currantLang : 'en']) }}">{{ __('Manage language') }}</a>
                            @endcan
                        @endif

                    </div>
                </li>

            </ul>
        </div>
    </div>
</header>
