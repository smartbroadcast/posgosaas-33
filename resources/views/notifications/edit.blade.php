{{ Form::model($notification, ['route' => ['notifications.update', $notification->id], 'method' => 'PUT']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('from', __('From'), ['class' => 'col-form-label']) }}
                {{ Form::text('from', null, ['class' => 'form-control', 'id' => 'from', 'placeholder' => __('Select from date')]) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('to', __('To'), ['class' => 'col-form-label']) }}
                {{ Form::text('to', null, ['class' => 'form-control', 'id' => 'to', 'placeholder' => __('Select to date')]) }}
            </div>
        </div>
        <div class="col-md-12">



            {{ Form::label('color', __('Notification Color'), ['class' => 'd-block col-form-label']) }}
            <div class=" btn-group-toggle btn-group-colors event-tag mb-0" data-toggle="buttons">
                <label class="btn bg-info p-3 {{ $notification->color == 'info' ? 'custom_color_radio_button' :'' }}">
                    <input type="radio" name="color" value="info" autocomplete="off"  
                    {{ $notification->color == 'info' ? 'checked' :'' }}
                        class="d-none">
                </label>

                <label class="btn bg-warning  p-3 {{ $notification->color == 'warning' ? 'custom_color_radio_button' :'' }}">
                    <input type="radio" name="color" value="warning" 
                    {{ $notification->color == 'warning' ? 'checked' :'' }}
                        autocomplete="off" class="d-none">
                    </label>


                <label class="btn bg-danger  p-3  {{ $notification->color == 'danger' ? 'custom_color_radio_button' :'' }}">
                    <input type="radio" name="color" value="danger" autocomplete="off"
                    {{ $notification->color == 'danger' ? 'checked' :'' }}
                        class="d-none">
                    </label>

                <label class="btn bg-primary  p-3 {{ $notification->color == 'primary' ? 'custom_color_radio_button' :'' }}">
                    <input type="radio" name="color" value="primary"
                    {{ $notification->color == 'primary' ? 'checked' :'' }}
                        autocomplete="off" class="d-none">
                    </label>
            </div>


        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
                {{ Form::textarea('description', null, ['class' => 'form-control', 'id' => 'description', 'placeholder' => __('Enter Address'), 'rows' => 3, 'style' => 'resize: none']) }}
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <input class="btn btn-primary" type="submit" value="{{ __('Edit') }}">
</div>


{{ Form::close() }}
