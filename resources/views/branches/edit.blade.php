{{ Form::model($branch, ['route' => ['branches.update', $branch->id], 'method' => 'PUT']) }}
<div class="modal-body">
<div class="form-group">
    {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
    {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter new Branch Name')]) }}
</div>

<div class="form-group">
    {{ Form::label('branch_type', __('Branch type'), ['class' => 'col-form-label']) }}
    {{ Form::select('branch_type', ['' => __('Select Branch Type'), 'retail' => 'Retail', 'restaurant' => 'Restaurant'], null, ['class' => 'form-control', 'data-toggle' => 'select']) }}
</div>

<div class="form-group">
    {{ Form::label('branch_manager', __('Branch Manager'), ['class' => 'col-form-label']) }}
    {{ Form::select('branch_manager', $users, null, ['class' => 'form-control', 'data-toggle' => 'select']) }}
</div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <input class="btn btn-primary" type="submit" value="{{ __('Edit') }}">
</div>

{{ Form::close() }}

