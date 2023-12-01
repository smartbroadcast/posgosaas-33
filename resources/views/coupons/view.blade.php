@extends('layouts.app')

@section('page-title', __('Coupon Detail') )


@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Coupon Detail') }}</h5>
    </div>
@endsection

@section('breadcrumb')
         <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
         <li class="breadcrumb-item"><a href="{{ route('coupons.index') }}">{{ __('Coupons') }}</a></li>
         <li class="breadcrumb-item">{{ $coupon->name }}</li>
@endsection

@section('content')
    @can('Manage Coupon')
    {{-- @dd(Request::route()->getName()) --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="card">
                        <div class="card-header">
                            {{ $coupon->name }}
                        </div>
                        <div class="card-body table-border-style">
                            <div class="table-responsive">
                                <table id="pc-dt-simple" class="table dataTable">
                                    <thead class="thead-light">
                                        <tr>                      
                                            <th scope="col" class="sort" data-sort="name"> {{__('User')}}</th>
                                        <th scope="col" class="sort" data-sort="name"> {{__('Date')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($userCoupons as $userCoupon)
                                        <tr>
                                            <td>{{ !empty($userCoupon->user_detail) ? $userCoupon->user_detail->name : '' }}</td>
                                            <td>{{ $userCoupon->created_at }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    @endcan
@endsection