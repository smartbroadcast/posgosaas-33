@extends('layouts.app')

@section('page-title')
    {{__('Email Templates')}}
@endsection

@section('title')
<div class="d-inline-block">
    <h5 class="h4 d-inline-block font-weight-400 mb-0">{{__('Email Template')}}</h5>
</div>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Email Template') }}</li>
@endsection

@push('old-datatable-css')
<link rel="stylesheet" href="{{asset('custom/css/summernote/summernote-bs4.css')}}">
{{-- <link rel="stylesheet" href="{{ asset('custom/css/jquery.dataTables.min.css') }}"> --}}
@endpush

@push('scripts')
<script src="{{asset('custom/css/summernote/summernote-bs4.js')}}"></script> 
<script src="{{asset('custom/js/tinymce/tinymce.min.js')}}"></script>
<script>
    if ($(".pc-tinymce-2").length) {
        tinymce.init({
            selector: '.pc-tinymce-2',
            height: "400",
            content_style: 'body { font-family: "Inter", sans-serif; }'
        });
    }
</script>
@endpush

@section('action-btn')
<div class="text-end mb-3">
    <div class="d-flex justify-content-end drp-languages">
        <ul class="list-unstyled mb-0 m-2">
            <li class="dropdown dash-h-item drp-language" style="list-style-type: none;">
                <a
                class="dash-head-link dropdown-toggle arrow-none me-0"
                data-bs-toggle="dropdown"
                href="#"
                role="button"
                aria-haspopup="false"
                aria-expanded="false"
                >
                <span class="drp-text hide-mob text-primary">{{Str::upper($currEmailTempLang->lang )}}</span>
                <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                </a>
                <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                    @foreach ($languages as $lang)

                    <a href="{{ route('manage.email.language', [$emailTemplate->id, $lang]) }}"
                    class="dropdown-item {{ $currEmailTempLang->lang == $lang ? 'text-primary' : '' }}">{{ Str::upper($lang) }}</a>
                    @endforeach
            
                </div>
            </li>
        </ul>    
        <ul class="list-unstyled mb-0 m-2">
            <li class="dropdown dash-h-item drp-language" style="list-style-type: none;">
                <a class="dash-head-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <span class="drp-text hide-mob text-primary">{{ __('Template: ') }} {{ $emailTemplate->name }}</span>
                <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                </a>
                <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                    @foreach ($EmailTemplates as $EmailTemplate)
                    <a href="{{ route('manage.email.language', [$EmailTemplate->id,(Request::segment(3)?Request::segment(3):\Auth::user()->lang)]) }}"
                    class="dropdown-item {{$emailTemplate->name == $EmailTemplate->name ? 'text-primary' : '' }}">{{ $EmailTemplate->name }}
                    </a>
                    @endforeach
            
                </div>
            </li>
        </ul>
    </div>
</div>
@endsection
@section('content')

    <div class="row">
        
        <div class="col-12">
            <div class="row">
                
            </div>
            <div class="card">
                <div class="card-body">
    
                    <div class="language-wrap">
                        <div class="row"> 
                            <h6>{{ __('Place Holders') }}</h6>
                            <div class="col-lg-12 col-md-9 col-sm-12 language-form-wrap">
    
                                <div class="card">
                                    <div class="card-header card-body">
                                        <div class="row text-xs">
                                            @if($emailTemplate->slug=='new_user')
                                                <div class="row">         
                                                    <p class="col-6">{{__('App Name')}} : <span class="pull-end text-primary">{app_name}</span></p>
                                                    <p class="col-6">{{__('App Url')}} : <span class="pull-right text-primary">{app_url}</span></p>
                                                    <p class="col-6">{{__('User Name')}} : <span class="pull-right text-primary">{user_name}</span></p>
                                                    <p class="col-6">{{__('User Email')}} : <span class="pull-right text-primary">{user_email}</span></p>
                                                    <p class="col-6">{{__('User Password')}} : <span class="pull-right text-primary">{user_password}</span></p>
                                                </div>
                                                @elseif($emailTemplate->slug=='new_owner')
                                                <div class="row">                           
                                                    <p class="col-6">{{__('App Name')}} : <span class="pull-end text-primary">{app_name}</span></p>
                                                    <p class="col-6">{{__('App Url')}} : <span class="pull-right text-primary">{app_url}</span></p>
                                                    <p class="col-6">{{__('Owner Name')}} : <span class="pull-right text-primary">{owner_name}</span></p>
                                                    <p class="col-6">{{__('Owner Email')}} : <span class="pull-right text-primary">{owner_email}</span></p>
                                                    <p class="col-6">{{__('Password')}} : <span class="pull-right text-primary">{owner_password}</span></p>
                                                    
                                                </div>
                                            @elseif($emailTemplate->slug=='new_customer')
                                                <div class="row">
                                                    <p class="col-6">{{__('App Name')}} : <span class="pull-end text-primary">{app_name}</span></p>
                                                    <p class="col-6">{{__('App Url')}} : <span class="pull-right text-primary">{app_url}</span></p>
                                                    <p class="col-6">{{__('Customer Name')}} : <span class="pull-right text-primary">{customer_name}</span></p>
                                                    <p class="col-6">{{__('Customer Email')}} : <span class="pull-right text-primary">{customer_email}</span></p>
                                                    <p class="col-6">{{__('Customer Phone number')}} : <span class="pull-right text-primary">{customer_phone_number}</span></p>
                                                    <p class="col-6">{{__('Customer Address')}} : <span class="pull-right text-primary">{customer_address}</span></p>
                                                    <p class="col-6">{{__('Customer Country')}} : <span class="pull-right text-primary">{customer_country}</span></p>
                                                    <p class="col-6">{{__('Customer Zipcode')}} : <span class="pull-right text-primary">{customer_zipcode}</span></p>
                                                </div>
                                            @elseif($emailTemplate->slug=='new_vendor')
                                                <div class="row">
                                                    <p class="col-6">{{__('App Name')}} : <span class="pull-end text-primary">{app_name}</span></p>
                                                    <p class="col-6">{{__('App Url')}} : <span class="pull-right text-primary">{app_url}</span></p>
                                                    <p class="col-6">{{__('Vendor Name')}} : <span class="pull-right text-primary">{vendor_name}</span></p>
                                                    <p class="col-6">{{__('Vendor Email')}} : <span class="pull-right text-primary">{vendor_email}</span></p>
                                                    <p class="col-6">{{__('Vendor Phone number')}} : <span class="pull-right text-primary">{vendor_phone_number}</span></p>
                                                    <p class="col-6">{{__('Vendor Address')}} : <span class="pull-right text-primary">{vendor_address}</span></p>
                                                    <p class="col-6">{{__('Vendor Country')}} : <span class="pull-right text-primary">{vendor_country}</span></p>
                                                    <p class="col-6">{{__('Vendor Zipcode')}} : <span class="pull-right text-primary">{vendor_zipcode}</span></p>
                                                </div>
                                            @elseif($emailTemplate->slug=='new_quote')
                                                <div class="row">
                                                    <p class="col-6">{{__('App Name')}} : <span class="pull-end text-primary">{app_name}</span></p>
                                                    <p class="col-6">{{__('App Url')}} : <span class="pull-right text-primary">{app_url}</span></p>
                                                    <p class="col-6">{{__('Quotation Date')}} : <span class="pull-right text-primary">{quotation_date}</span></p>
                                                    <p class="col-6">{{__('Quotation Reference No')}} : <span class="pull-right text-primary">{quotation_reference_no}</span></p>
                                                    <p class="col-6">{{__('Quotation Customers')}} : <span class="pull-right text-primary">{quotation_customers}</span></p>
                                                    <p class="col-6">{{__('Customer Email')}} : <span class="pull-right text-primary">{customer_email}</span></p>
                                                    
                                                </div>
                                           
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-9 col-sm-12 language-form-wrap">
                                
                                {{Form::model($currEmailTempLang, array('route' => array('email_template.update', $currEmailTempLang->parent_id), 'method' => 'PUT')) }}
                                <div class="row">
                                    <div class="form-group col-12">
                                        {{Form::label('subject',__('Subject'),['class'=>'form-control-label text-dark'])}}
                                        {{Form::text('subject',null,array('class'=>'form-control font-style','required'=>'required'))}}
                                    </div>
                                    
                                    <div class="form-group col-md-6">
                                        {{Form::label('name',__('Name'),['class'=>'form-control-label text-dark'])}}
                                        {{Form::text('name',$emailTemplate->name,['class'=>'form-control font-style','disabled'=>'disabled'])}}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('from',__('From'),['class'=>'form-control-label text-dark'])}}
                                        {{ Form::text('from', $emailTemplate->from, ['class' => 'form-control font-style', 'required' => 'required']) }}
                                    </div>
                                    <div class="form-group col-12">
                                        {{Form::label('content',__('Email Message'),['class'=>'form-control-label text-dark'])}}
                                        {{Form::textarea('content',$currEmailTempLang->content,array('class'=>'pc-tinymce-2','required'=>'required'))}}
    
                                    </div>
                                   
                                   
                                    <div class="col-md-12 text-end">
                                        {{Form::hidden('lang',null)}}
                                        <input type="submit" value="{{__('Save')}}" class="btn btn-print-invoice  btn-primary">
                                    </div>
                                  
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