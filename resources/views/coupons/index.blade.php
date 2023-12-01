@extends('layouts.app')

@section('page-title', __('Coupons') )

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Coupons') }}</h5>
    </div>
@endsection

@section('action-btn')
    @can('Create Coupon')
        <a class="btn btn-sm btn-primary btn-icon m-1"  data-bs-toggle="tooltip"
            data-size="md" data-ajax-popup="true" data-title="{{__('Add New Coupon')}}" data-url="{{route('coupons.create')}}" title="{{ __('Add Coupon') }}"> <i class="ti ti-plus text-white"></i></a>
        </a>
    @endcan
@endsection

@section('breadcrumb')
         <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item">{{ __('Coupons') }}</li>
@endsection

@section('content')
    @can('Manage Coupon')
        <div class="row">
            <div class="col-xl-12">
                
                    <div class="card">
                        <div class="card-header card-body table-border-style">
                       
                        <div class="table-responsive">
                            <table class="table" id="pc-dt-simple" role="grid">
                                <thead>
                                <tr>
                                    <th> {{__('Name')}}</th>
                                    <th> {{__('Code')}}</th>
                                    <th> {{__('Discount (%)')}}</th>
                                    <th> {{__('Limit')}}</th>
                                    <th> {{__('Used')}}</th>
                                    <th width="200px">{{ __('Action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($coupons as $key => $coupon)
                                    <tr>
                                        <td>{{ $coupon->name }}</td>
                                        <td>{{ $coupon->code }}</td>
                                        <td>{{ $coupon->discount }}</td>
                                        <td>{{ $coupon->limit }}</td>
                                        <td>{{ $coupon->used_coupon() }}</td>
                                        <td class="Action">

                                            <div class="action-btn btn-warning ms-2">
                                            <a href="{{ route('coupons.show',$coupon->id) }}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" title='show'>
                                                <i class="ti ti-eye text-white"></i>
                                            </a>
                                           </div>  


                                            @can('Edit Coupon')
                                            <div class="action-btn btn-info ms-2">
                                                <a href="#" data-ajax-popup="true" data-bs-toggle="tooltip" title='edit' data-title="{{__('Edit Coupon')}}" title="{{__('Edit')}}" data-size="md" data-url="{{route('coupons.edit', $coupon->id)}}"
                                                   class="mx-3 btn btn-sm d-inline-flex align-items-center"><i class="ti ti-pencil text-white"> </i></a>
                                               </div>
                                            @endcan

                                            @can('Delete Coupon')
                                             <div class="action-btn bg-danger ms-2">
                                                <a href="#" class="bs-pass-para mx-3 btn btn-sm d-inline-flex align-items-center bs-pass-para" data-bs-toggle="tooltip" title="Delete"  data-toggle="sweet-alert"
                                                   data-confirm="{{ __('Are You Sure?') }}" data-text="{{__('This action can not be undone. Do you want to continue?')}}"
                                                   data-confirm-yes="delete-form-{{$coupon->id}}" title="{{__('Delete')}}">
                                                    <i class="ti ti-trash text-white"></i>
                                                </a>


                                            </div>  
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['coupons.destroy', $coupon->id],'id' => 'delete-form-'.$coupon->id]) !!}
                                                {!! Form::close() !!}
                                            @endcan
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            $(document).on('keypress keydown keyup', '.coupon-discount, .coupon-limit', function () {
                if ($(this).val() > 100 && $(this).hasClass('coupon-discount')) {
                    $(this).val('100');
                } else if ($(this).val() < 0 || $(this).val() == '') {
                    $(this).val('0');
                } else {
                }
            });

            $(document).on('click', '.code', function () {
                var type = $(this).val();
                if (type == 'manual') {
                    $('#manual').removeClass('d-none').addClass('d-block');
                    $('#auto').removeClass('d-block').addClass('d-none');
                } else {
                    $('#auto').removeClass('d-none').addClass('d-block');
                    $('#manual').removeClass('d-block').addClass('d-none');
                }
            });

            $(document).on('click', '#code-generate', function () {
                var length = 10;
                var result = '';
                var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                var charactersLength = characters.length;
                for (var i = 0; i < length; i++) {
                    result += characters.charAt(Math.floor(Math.random() * charactersLength));
                }
                $('#auto-code').val(result);
            });
        });
    </script>
@endpush
