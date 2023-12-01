{{ Form::open(['url' => 'store-language']) }}
<div class="modal-body">
<div class="form-group">
    {{ Form::label('code', __('Language Code') , ['class' => 'col-form-label']) }}
    {{ Form::text('code', null, ['class' => 'form-control', 'placeholder' => __('Enter new Language Code'), 'required' => 'required']) }}
</div>
</div>

 <div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Cancel')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>  
</div>
{{ Form::close() }}

