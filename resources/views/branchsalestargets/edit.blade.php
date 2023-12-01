{{ Form::model($branchsalestarget, ['route' => ['branchsalestargets.update', $branchsalestarget->id], 'method' => 'PUT']) }}
<div class="modal-body">
<div class="form-group">
    {{ Form::label('month', __('Month'),['class' => 'col-form-label']) }}
    {{ Form::text('month', null, ['class' => 'form-control', 'placeholder' => __('Select Month'), 'readonly' => '']) }}
</div>
@foreach($targetedbranches as $key => $branch)
    <div class="form-group">
        {{ Form::label('branch_'.$key, ucfirst($branch->name), ['class' => 'col-form-label']) }}
        {{ Form::hidden('branches[]', $branch->branch_id) }}
        {{ Form::number('amount[]', $branch->target_amount, ['class' => 'form-control', 'id'=>'branch_'.$key, 'placeholder' => __('Enter Target Amount'), 'step' => '0.01']) }}
    </div>
@endforeach
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <input class="btn btn-primary" type="submit" value="{{ __('Edit') }}">
</div>

{{ Form::close() }}
