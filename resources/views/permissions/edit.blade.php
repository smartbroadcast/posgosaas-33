{{ Form::model($permission, ['route' => ['permissions.update', $permission->id], 'method' => 'PUT']) }}

<div class="form-group">
    {{ Form::label('name', __('Permission Name')) }}
    {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Edit Permission Name')]) }}
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-md" data-dismiss="modal">{{ __('Cancel') }}</button>
    <input class="btn btn-success btn-md" type="submit" value="{{ __('Edit') }}">
</div>

{{ Form::close() }}
