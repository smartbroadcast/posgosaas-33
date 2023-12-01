{{ Form::open(['url' => 'taxes']) }}
 <div class="modal-body">
<div class="form-group">
    {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
    {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Tax Name'), 'required' => '']) }}
</div>

<div class="form-group">
    {{ Form::label('percentage', __('Percentage'), ['class' => 'col-form-label']) }}
    {{ Form::number('percentage', null, ['class' => 'form-control', 'placeholder' => __('Enter Tax Percentage'), 'step' => '0.01', 'required' => '']) }}
</div>

<div class="form-group">
    {{ Form::label('is_default', __('Is Default'), ['class' => 'col-form-label']) }}
    <div class="form-check form-check-inline">
        {{ Form::radio('is_default', '1', false, ['class' => 'form-check-input', 'id' => 'is_default_yes']) }}
        {{ Form::label('is_default_yes', __('Yes'), ['class' => 'form-check-label']) }}
    </div>
    <div class="form-check form-check-inline">
        {{ Form::radio('is_default', '0', true, ['class' => 'form-check-input', 'id' => 'is_default_no']) }}
        {{ Form::label('is_default_no', __('No'), ['class' => 'form-check-label']) }}
    </div>
</div>

</div>
<div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <input class="btn btn-primary" type="submit" value="{{ __('Create') }}">
    </div>

{{ Form::close() }}




