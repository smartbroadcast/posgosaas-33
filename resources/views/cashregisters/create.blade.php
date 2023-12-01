{{ Form::open(['url' => 'cashregisters']) }}
<div class="modal-body">

<div class="form-group">
    {{ Form::label('name', __('Cash Register Name'), ['class' => 'col-form-label']) }}
    {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter new Cash Register Name')]) }}
</div>

<div class="form-group">
    {{ Form::label('branch_id', __('Branch'), ['class' => 'col-form-label']) }}
    <div class="input-group">
        {{ Form::select('branch_id', $branches, null, ['class' => 'form-control', 'data-toggle' => 'select']) }}
    </div>
</div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <input class="btn btn-primary" type="submit" value="{{ __('Create') }}">
</div>

{{ Form::close() }}
