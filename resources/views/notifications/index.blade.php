@extends('layouts.app')

@section('page-title', __('Notification List'))

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Notification List') }}</h5>
    </div>
@endsection

@section('action-btn')
    @can('Create Notification')
        <a class="btn btn-sm btn-primary btn-icon " data-size="lg" data-bs-toggle="tooltip" data-ajax-popup="true"
            data-title="{{ __('Add New Notification') }}" data-url="{{ route('notifications.create') }}"
            title="{{ __('Add Notification') }}">
            <span class=""><i class="ti ti-plus text-white"></i></span>
        </a>
    @endcan
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Notification') }}</li>
@endsection


@section('content')
    @can('Manage Notification')
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table" id="pc-dt-simple" role="grid">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Notifications') }}</th>
                                        <th>{{ __('Date/Time Added') }}</th>
                                        <th>{{ __('From') }}</th>
                                        <th>{{ __('To') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th width="200px">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notifications as $key => $notification)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td class="td-white-space">
                                                {!! $notification->description !!}
                                            </td>
                                            <td>{{ Auth::user()->datetimeFormat($notification->created_at) }}</td>
                                            <td>{{ Auth::user()->dateFormat($notification->from) }}</td>
                                            <td>{{ Auth::user()->dateFormat($notification->to) }}</td>
                                            <td>

                                                @can('Manage Notification')
                                                    <div class="nav-item dropdown display-notification"
                                                        data-li-id="{{ $notification->id }}">
                                                        <span data-toggle="dropdown"
                                                            class="badge badge-bg rounded notification-label py-2 px-3 notification-{{ $notification->status == 0 ? 'open' : 'close' }} bg-{{ $notification->status == 0 ? 'success' : 'danger' }}  ">
                                                            {{ $notification->status == 0 ? __('Open') : __('Close') }} </span>
                                                        <div
                                                            class="dropdown-menu dropdown-list notification-status dropdown-menu-right">
                                                            <div class="dropdown-list-content notification-actions"
                                                                data-id="{{ $notification->id }}"
                                                                data-url="{{ route('update.notification.status', $notification->id) }}">
                                                                <a href="#" data-status="0" data-class="notification-open"
                                                                    class="dropdown-item notification-action {{ $notification->status == 0 ? 'selected' : '' }}">{{ __('Open') }}
                                                                </a>
                                                                <a href="#" data-status="1" data-class="notification-close"
                                                                    class="dropdown-item notification-action {{ $notification->status == 1 ? 'selected' : '' }}">{{ __('Close') }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endcan
                                            </td>
                                            <td class="Action">
                                                @can('Edit Notification')
                                                    <div class="action-btn btn-info ms-2">
                                                        <a href="#" data-ajax-popup="true"
                                                            data-title="{{ __('Edit Notification') }}"
                                                            title="{{ __('Edit') }}" data-size="lg" data-bs-toggle="tooltip"
                                                            data-url="{{ route('notifications.edit', $notification->id) }}"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Delete Notification')
                                                    <div class="action-btn bg-danger ms-2">
                                                        <a href="#"
                                                            class="bs-pass-para mx-3 btn btn-sm d-inline-flex align-items-center"
                                                            data-toggle="sweet-alert" title="{{ __('Delete') }}"
                                                            data-bs-toggle="tooltip" data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $notification->id }}">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                    </div>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['notifications.destroy', $notification->id], 'id' => 'delete-form-' . $notification->id]) !!}
                                                    {!! Form::close() !!}
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('scripts')
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('public/vendor/unisharp/laravel-ckeditor/ckeditor.js') }}"></script>
    <script>
        $(document).on('change', '#from, #to', function(e) {
            if ((Date.parse($('#from').val()) > Date.parse($('#to').val()))) {
                $('#to').val('');
                alert("End date should be greater than Start date");
            }
        });

        $(document).on('click', '.notification-action', function(e) {
            e.stopPropagation();
            e.preventDefault();

            var ele = $(this);

            var id = ele.parent().attr('data-id');
            var url = ele.parent().attr('data-url');
            var status = ele.attr('data-status');

            $.ajax({
                url: url,
                method: 'PATCH',
                data: {
                    status: status
                },
                success: function(response) {

                    if (response) {

                        $('[data-li-id="' + id + '"] .notification-action').removeClass('selected');

                        if (ele.hasClass('selected')) {

                            ele.removeClass('selected');

                        } else {
                            ele.addClass('selected');
                        }

                        var notification = $('[data-li-id="' + id + '"] .notification-actions').find(
                            '.selected').text().trim();

                        var notification_class = $('[data-li-id="' + id + '"] .notification-actions')
                            .find('.selected').attr('data-class');
                        $('[data-li-id="' + id + '"] .notification-label').removeClass(
                                'notification-open notification-close').addClass(notification_class)
                            .text(notification);
                    }
                },
                error: function(data) {
                    data = data.responseJSON;
                    show_toastr('{{ __('Error') }}', data.error, 'error');
                }
            });
        });
    </script>
@endpush
