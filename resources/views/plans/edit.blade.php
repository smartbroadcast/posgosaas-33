{{Form::model($plan, ['route' => ['plans.update', $plan->id], 'method' => 'PUT', 'enctype' => "multipart/form-data"]) }}
<div class="modal-body">
<div class="row">
    <div class="form-group col-md-6">
        {{Form::label('name',__('Name'),['class' => 'col-form-label']) }}
        {{Form::text('name',null,['class'=>'form-control font-style','placeholder'=>__('Enter Plan Name'),'required'=>'required'])}}
    </div>
    <div class="form-group col-md-6">
        @if($plan->id != 1)
            {{Form::label('price',__('Price'),['class' => 'col-form-label']) }}
            {{Form::number('price',null,['class'=>'form-control','placeholder'=>__('Enter Plan Price'),'required'=>'required'])}}
        @endif
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('duration', __('Duration'),['class' => 'col-form-label']) }}
        {!! Form::select('duration', $arrDuration, null,['class' => 'form-control','required'=>'required', 'data-toggle' => 'select']) !!}
    </div>
    <div class="form-group col-md-6">
        {{Form::label('max_users',__('Maximum Users'),['class' => 'col-form-label']) }}
        {{Form::number('max_users',null,['class'=>'form-control max-users','required'=>'required'])}}
        <span class="small">{{__('Note: "-1" for Unlimited')}}</span>
    </div>
    <div class="form-group col-md-6">
        {{Form::label('max_customers',__('Maximum Customers'),['class' => 'col-form-label']) }}
        {{Form::number('max_customers',null,['class'=>'form-control max-customers','required'=>'required'])}}
        <span class="small">{{__('Note: "-1" for Unlimited')}}</span>
    </div>
    <div class="form-group col-md-6">
        {{Form::label('max_vendors',__('Maximum Vendors'),['class' => 'col-form-label']) }}
        {{Form::number('max_vendors',null,['class'=>'form-control max-vendors','required'=>'required'])}}
        <span class="small">{{__('Note: "-1" for Unlimited')}}</span>
    </div>
    <div class="form-group col-md-12">

        {{-- <div class="choose-files mt-3">
            <label for="image">
                <div class=" bg-primary edit-plan-image"> <i
                        class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                </div>
                <input type="file" class="form-control file d-none" name="image" id="inputGroupFile"
                    data-filename="edit-plan-image" accept="image/png">
            </label>
        </div> --}}


        {{-- <div class="input-group">
            <div class="custom-file">
                {{ Form::file('image', [ 'class' => 'custom-file-input d-none',
                                         'accept'=>"image/png",
                                         'id' => "inputGroupFile",
                                        ] ) }}
                {{ Form::label('inputGroupFile', __('Choose file'), ['class' => 'custom-file-label1']) }}
                <span class="small mx-2">{{__('Please upload a valid image file. Size of image should not be more than 2MB.')}}</span>
            </div>
        </div> --}}
    </div>
    <div class="form-group col-md-12">
        {{ Form::label('description', __('Description'),['class' => 'col-form-label']) }}
        {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'2']) !!}
    </div>
</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <input class="btn btn-primary" type="submit" value="{{ __('Update') }}">
</div>

{{ Form::close() }}


