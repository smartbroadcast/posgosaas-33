{{ Form::model($brand, ['route' => ['brands.update', $brand->id], 'method' => 'PUT']) }}
<div class="modal-body">
<div class="form-group">
    {{ Form::label('name', __('Brand Name'), ['class' => 'col-form-label']) }}
    {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter new Brand Name')]) }}
</div>
</div>

 <div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <input class="btn btn-primary" type="submit" value="{{ __('Edit') }}">
</div>

{{ Form::close() }}
