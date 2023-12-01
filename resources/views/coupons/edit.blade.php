{{Form::model($coupon, array('route' => array('coupons.update', $coupon->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">
    <div class="form-group col-md-12">
        {{Form::label('name',__('Name') ,['class' => 'col-form-label']) }}
        {{Form::text('name',null,array('class'=>'form-control font-style','required'=>'required'))}}
    </div>
    <div class="form-group col-md-6">
        {{Form::label('discount',__('Discount') ,['class' => 'col-form-label']) }}
        {{Form::number('discount',null,array('class'=>'form-control coupon-discount','required'=>'required','step'=>'0.01'))}}
        <span class="small">{{__('Note: Discount in Percentage')}}</span>
    </div>
    <div class="form-group col-md-6">
        {{Form::label('limit',__('Limit') ,['class' => 'col-form-label']) }}
        {{Form::number('limit',null,array('class'=>'form-control coupon-limit','required'=>'required'))}}
    </div>
  
    <div class="form-group col-md-12" id="auto">
        {{Form::label('code',__('Code') ,array('class'=>'col-form-label'))}}
        <div class="input-group">
            {{Form::text('code',null,array('class'=>'form-control','id'=>'auto-code','required'=>'required'))}}
            <button class="btn btn-outline-primary" type="button" id="code-generate"><i class="fa fa-history pr-1"></i>{{__(' Generate')}}</button>
        </div>
    </div>
    
</div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <input class="btn btn-primary" type="submit" value="{{ __('Edit') }}">
</div>

{{ Form::close() }}

