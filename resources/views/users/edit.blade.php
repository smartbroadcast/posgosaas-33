{{ Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'PUT']) }}
<div class="modal-body">
    <div class="row mb-3">
        <div class="form-group col-md-6">
            {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Edit User Name')]) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('email', __('Email'), ['class' => 'col-form-label']) }}
            {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => __('Edit Email Address')]) }}
        </div>

        @if (Auth::user()->parent_id != 0)
            <div class='form-group col-md-12'>
                <h5>{{ __('Assign Role') }}</h5>
                {{ Form::select('roles[]', $roles, null, ['class' => 'form-control', 'required' => '']) }}
            </div>

            <div class="form-group col-md-6">
                {{ Form::label('branch_id', __('Branch'), ['class' => 'col-form-label']) }}
                <div class="input-group">
                    {{ Form::select('branch_id', $branches, null, ['class' => 'form-control', 'data-toggle' => 'select']) }}
                </div>
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('cash_register_id', __('Cash Register'), ['class' => 'col-form-label']) }}
                <div class="input-group">
                    {{ Form::select('cash_register_id', $cash_registers, null, ['class' => 'form-control','data-toggle' => 'select']) }}
                </div>
            </div>
        @endif
        <div class="form-group col-md-6">
            {{ Form::label('password', __('Password'), ['class' => 'col-form-label']) }}<br>
            {{ Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter new Password')]) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('password_confirmation', __('Confirm Password'), ['class' => 'col-form-label']) }}<br>
            {{ Form::password('password_confirmation', ['class' => 'form-control','placeholder' => __('Confirm Password')]) }}
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <input class="btn btn-primary" type="submit" value="{{ __('Edit') }}">
</div>

{{ Form::close() }}
