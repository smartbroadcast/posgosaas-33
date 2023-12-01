@extends('layouts.app')

@section('page-title')
    {{ __('Calendar') }}
@endsection

@section('title')
    {{ __('Calendar') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Calendar') }}</li>
@endsection

@section('action-btn')
    @can('Create Calendar Event')
        <a href="#" data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" data-title="{{ __('Create New Event') }}"
            title="{{ __(' Create New Event') }}" data-url="{{ route('calendars.create') }}"
            class="btn btn-sm btn-primary btn-icon m-1">
            <span class=""><i class="ti ti-plus text-white"></i></span>
        </a>
    @endcan
@endsection


@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Calendar') }}</h5>
                </div>
                <div class="card-body">
                    <div id='calendar' class='calendar'></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">

            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">{{ __('Current Month Events') }}</h4>
                    <ul class="event-cards list-group list-group-flush mt-3 w-100">
                        {{-- @dd($current_month_event) --}}
                        @foreach ($current_month_event as $event)
                            <li class="list-group-item card mb-3">
                                <div class="row align-items-center justify-content-between">
                                    <div class="col-auto mb-3 mb-sm-0">
                                        <div class="d-flex align-items-center">
                                            <div class="theme-avtar  @if ($event->className == 'event-danger') bg-danger @elseif($event->className == 'event-info') bg-info @elseif($event->className == 'event-warning') bg-warning @elseif($event->className == 'event-success') bg-success @endif"
                                                style="@if ($event->className == 'event-primary') background-color: #51459d !important @endif">
                                                <i class="ti ti-calendar-event"></i>
                                            </div>
                                            <div class="ms-3">
                                                <h6 class="m-0">{{ $event->title }}</h6>
                                                <small
                                                    class="text-muted">{{ date('d F Y', strtotime($event->start)) }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <div class="d-inline-flex mb-4">

                                            @can('Edit Calendar Event')
                                                <div class="action-btn btn-info ms-2">
                                                    <a href="#" data-ajax-popup="true" data-bs-toggle="tooltip"
                                                        data-title="{{ __('Edit Event') }}" title="{{ __('Edit Event') }}"
                                                        data-size="md" data-url="{{ route('calendars.edit', $event->id) }}"
                                                        class="mx-3 btn btn-sm d-inline-flex align-items-center">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan

                                            @can('Delete Calendar Event')
                                                <div class="action-btn bg-danger ms-2">
                                                    <a href="#"
                                                        class="bs-pass-para mx-3 btn btn-sm d-inline-flex align-items-center"
                                                        data-toggle="sweet-alert" data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-bs-toggle="tooltip"
                                                        data-confirm-yes="delete-form-{{ $event->id }}"
                                                        title="{{ __('Delete') }}">
                                                        <i class="ti ti-trash text-white"></i>
                                                    </a>
                                                </div>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['calendars.destroy', $event->id], 'id' => 'delete-form-' . $event->id]) !!}
                                                {!! Form::close() !!}
                                            @endcan
                                        </div>
                                    </div>

                                </div>
                            </li>
                        @endforeach

                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="{{ asset('assets/js/plugins/main.min.js') }}"></script>


    <script type="text/javascript">
        (function() {
            var etitle;
            var etype;
            var etypeclass;
            var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    timeGridDay: "{{__('Day')}}",
                    timeGridWeek: "{{__('Week')}}",
                    dayGridMonth: "{{__('Month')}}"
                    },
                themeSystem: 'bootstrap',

                slotDuration: '00:10:00',
                navLinks: true,
                droppable: true,
                selectable: true,
                selectMirror: true,
                editable: true,
                dayMaxEvents: true,
                handleWindowResize: true,
                events: {!! $arrEvents !!},



                eventClick: function(e) {
                    e.jsEvent.preventDefault();
                    var title = e.title;
                    var url = e.el.href;

                    if (typeof url != 'undefined') {
                        $("#commonModal .modal-title").html(e.event.title);
                        $("#commonModal .modal-dialog").addClass('modal-md');
                        $("#commonModal").modal('show');

                        $.get(url, {}, function(data) {
                            console.log(data);
                            $('#commonModal .body ').html(data);

                            if ($(".d_week").length > 0) {
                                $($(".d_week")).each(function(index, element) {
                                    var id = $(element).attr('id');

                                    (function() {
                                        const d_week = new Datepicker(document
                                            .querySelector('#' + id), {
                                                buttonClass: 'btn',
                                                format: 'yyyy-mm-dd',
                                            });
                                    })();

                                });
                            }


                        });
                        return false;
                    }
                }

            });

            calendar.render();
        })();
    </script>
@endpush
