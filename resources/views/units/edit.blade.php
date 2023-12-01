{{ Form::model($unit, ['route' => ['units.update', $unit->id], 'method' => 'PUT']) }}
<div class="modal-body">

<div class="form-group">
    {{ Form::label('name', __('Unit Name'), ['class' => 'col-form-label']) }}
    {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter new Unit Name')]) }}
</div>

<div class="form-group">
    {{ Form::label('shortname', __('Short Name'), ['class' => 'col-form-label']) }}
    {{ Form::text('shortname', null, ['class' => 'form-control', 'placeholder' => __('Enter Unit Short Name')]) }}
</div>
</div>

 <div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <input class="btn btn-primary" type="submit" value="{{ __('Edit') }}">
</div>

{{ Form::close() }}


