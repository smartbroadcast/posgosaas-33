{{ Form::model($category, ['route' => ['categories.update', $category->id], 'method' => 'PUT']) }}
<div class="modal-body">

<div class="form-group">
    {{ Form::label('name', __('Category Name'), ['class' => 'col-form-label']) }}
    {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter new Category Name')]) }}
</div>
</div>

 <div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <input class="btn btn-primary" type="submit" value="{{ __('Edit') }}">
</div>
{{ Form::close() }}
