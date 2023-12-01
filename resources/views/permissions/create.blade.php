{{ Form::open(['url' => 'permissions', 'class' => 'repeater']) }}

<div class="form-group">
    {{ Form::label('name', __('Name')) }}
    <div class="input-group col-xs-3">
        {{ Form::text('permissions[]', null, ['class' => 'form-control', 'placeholder' => __('Enter new Permission Name')]) }}
    </div>
</div>

@if(!$roles->isEmpty())
    <h5>{{ __('Assign Permission to Roles') }}</h5>
    <div class="form-group">
        @foreach ($roles as $key => $role)
            <div class="custom-control custom-checkbox">
                {{ Form::checkbox('roles[]', $role->id, null, ['class' => 'custom-control-input', 'id' => $key]) }}
                {{ Form::label($key, ucfirst($role->name), ['class' => 'custom-control-label']) }}
            </div>
        @endforeach
    </div>
@endif

<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-md" data-dismiss="modal">{{ __('Cancel') }}</button>
    <input class="btn btn-success btn-md" type="submit" value="{{ __('Create') }}">
</div>
{{ Form::close() }}
