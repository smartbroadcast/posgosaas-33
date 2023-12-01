{{ Form::model($event, ['route' => ['calendars.update', $event->id], 'method' => 'PUT']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group">
            {{ Form::label('title', __('Event Title'), ['class' => 'col-form-label']) }}
            {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => __('Enter Event Title')]) }}
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('start', __('Event start Date'), ['class' => 'col-form-label']) }}
                {{ Form::text('start', null, ['class' => 'form-control d_week', 'autocomplete' => 'off']) }}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('end', __('Event End Date'), ['class' => 'col-form-label']) }}
                {{ Form::text('end', null, ['class' => 'form-control d_week', 'autocomplete' => 'off']) }}
            </div>
        </div>
        {{-- {{ dd($event->color) }} --}}
        <div class="form-group">
            {{ Form::label('className', __('Event Select Color'), ['class' => 'col-form-label d-block mb-3']) }}
            <div class=" btn-group-toggle btn-group-colors event-tag" data-toggle="buttons">
                <label
                    class="btn bg-info p-3 {{ $event->className == 'event-info'
                        ? 'custom_color_radio_button
                                                                                                                                                                                                                                '
                        : '' }} "><input
                        type="radio" name="className" class="d-none" value="event-info"
                        {{ $event->className == 'event-info' ? 'checked' : '' }}></label>

                <label
                    class="btn bg-warning p-3 {{ $event->className == 'event-warning' ? 'custom_color_radio_button' : '' }}">
                    <input type="radio" class="d-none" name="className" value="event-warning"
                        {{ $event->className == 'event-warning' ? 'checked' : '' }}>
                </label>

                <label
                    class="btn bg-danger p-3 {{ $event->className == 'event-danger' ? 'custom_color_radio_button' : '' }}"><input
                        type="radio" name="className" class="d-none" value="event-danger"
                        {{ $event->className == 'event-danger' ? 'checked' : '' }}></label>


                <label
                    class="btn bg-success p-3 {{ $event->className == 'event-success' ? 'custom_color_radio_button' : '' }}"><input
                        type="radio" class="d-none" name="className" value="event-success"
                        {{ $event->className == 'event-success' ? 'checked' : '' }}></label>

                <label class="btn p-3 {{ $event->className == 'event-primary' ? 'custom_color_radio_button' : '' }}"
                    style="background-color: #51459d !important"><input type="radio" class="d-none" name="className"
                        value="event-primary" {{ $event->className == 'event-primary' ? 'checked' : '' }}></label>
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('description', __('Event Description'), ['class' => 'col-form-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => '5', 'placeholder' => __('Enter Event Description')]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">

</div>
{{ Form::close() }}
