{{ Form::open(['url' => 'calendars', 'method' => 'post']) }}
<div class="modal-body">
    <div class="row">

        <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
            <div class="form-group">
                {{ Form::label('title', __('Event Title'), ['class' => 'col-form-label']) }}
                {{ Form::text('title', null, ['class' => 'form-control ', 'placeholder' => __('Enter Event Title')]) }}
            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-lg-6 col-xl-6">
            <div class="form-group">
                {{ Form::label('start', __('Event start Date'), ['class' => 'col-form-label']) }}
                {{ Form::text('start', null, ['class' => 'form-control d_week', 'autocomplete' => 'off']) }}
            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-lg-6 col-xl-6">
            <div class="form-group">
                {{ Form::label('end', __('Event End Date'), ['class' => 'col-form-label']) }}
                {{ Form::text('end', null, ['class' => 'form-control d_week', 'autocomplete' => 'off']) }}
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
            <div class="form-group">
                {{ Form::label('className', __('Event Select Color'), ['class' => 'col-form-label d-block mb-3']) }}
                <div class="btn-group-toggle btn-group-colors event-tag" data-toggle="buttons">
                    <label class="btn bg-info active p-3"><input type="radio" name="className" value="event-info"
                            checked class="d-none"></label>
                    <label class="btn bg-warning p-3"><input type="radio" name="className" value="event-warning"
                            class="d-none"></label>
                    <label class="btn bg-danger p-3"><input type="radio" name="className" value="event-danger"
                            class="d-none"></label>
                    <label class="btn bg-success p-3"><input type="radio" name="className" value="event-success"
                            class="d-none"></label>
                    <label class="btn p-3" style="background-color: #51459d !important"><input type="radio"
                            name="className" class="d-none" value="event-primary"></label>
                </div>
            </div>
        </div>

        <div class="form-group">
            {{ Form::label('description', __('Event Description'), ['class' => 'col-form-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __('Enter Event Description'), 'rows' => '5']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="Cancel" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">

</div>
{{ Form::close() }}
