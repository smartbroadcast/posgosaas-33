@extends('layouts.app')

@section('page-title', __('Quotations'))

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Quotations') }}</h5>
    </div>
@endsection

@section('action-btn')

    <a href="{{ route('Quotation.export') }}" class="btn btn-sm btn-primary btn-icon " data-bs-toggle="tooltip"
        title="{{ __('Export') }}">
        <i class="ti ti-file-export text-white"></i>
    </a>

    @can('Create Quotations')
        <a href="{{ route('quotations.create') }}" data-bs-toggle="tooltip" title="{{ __('Add Quotation') }}"
            class="btn btn-sm btn-primary btn-icon m-1"><span class=""><i
                    class="ti ti-plus text-white"></i></span></a>
    @endcan

@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Quotations') }}</li>
@endsection

@section('content')
    @can('Manage Quotations')
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header card-body table-border-style">

                        <div class="table-responsive">
                            <table class="table" id="pc-dt-simple" role="grid">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Due Date') }}</th>
                                        <th>{{ __('Reference No') }}</th>
                                        <th>{{ __('Customer Name') }}</th>
                                        <th>{{ __('Customer Email') }}</th>
                                        <th>{{ __('Grand Total') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th width="200px">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($quotations as $key => $quotation)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ Auth::user()->dateFormat($quotation->date) }}</td>
                                            <td>{{ $quotation->reference_no }}</td>
                                            <td>{{ $quotation->customer != null ? ucfirst($quotation->customer->name) : __('Walk-in Customer') }}
                                            </td>
                                            <td>{{ $quotation->customer_email }}</td>
                                            <td>{{ Auth::user()->priceFormat($quotation->getTotal()) }}</td>
                                            <td>
                                                <a href="#" class="badge bg-info p-2 px-3" id="resend-quotation"
                                                    data-id="{{ $quotation->id }}"
                                                    data-mail="{{ $quotation->customer_email }}"
                                                    title="{{ __('To resend quotation to customer') }}">{{ __('Resend') }}</a>
                                            </td>
                                            <td>
                                                @can('Manage Quotations')
                                                    <div class="nav-item dropdown display-quotation"
                                                        data-li-id="{{ $quotation->id }}">
                                                        
                                                        <span data-bs-toggle="dropdown"
                                                            class="badge badge-lg  py-2 px-3  quotation-label  quotation-{{ $quotation->status == 0 ? 'open' : 'close' }} bg-{{ $quotation->status == 0 ? 'success' : 'danger' }} "
                                                            aria-expanded="false">{{ $quotation->status == 0 ? __('Open') : __('Close') }}</span>

                                                        <div
                                                            class="dropdown-menu dropdown-list quotation-status dropdown-menu-right">
                                                            <div class="dropdown-list-content quotation-actions"
                                                                data-id="{{ $quotation->id }}"
                                                                data-url="{{ route('update.quotation.status', $quotation->id) }}">
                                                                <a href="#" data-status="0" data-class="bg-success"
                                                                    class="dropdown-item quotation-action {{ $quotation->status == 0 ? 'selected' : '' }}">{{ __('Open') }}
                                                                </a>
                                                                <a href="#" data-status="1" data-class="bg-danger"
                                                                    class="dropdown-item quotation-action {{ $quotation->status == 1 ? 'selected' : '' }}">{{ __('Close') }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endcan
                                            </td>
                                            <td class="Action">
                                                <div class="action-btn bg-success ms-2">
                                                    <a href="{{ route('get.quotation.invoice', Crypt::encrypt($quotation->id)) }}"
                                                        data-bs-toggle="tooltip" target="_blank"
                                                        class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                        title="{{ __('Download') }}">
                                                        <i class="ti ti-download text-white"></i>
                                                    </a>
                                                </div>
                                                @can('Edit Quotations')
                                                    <div class="action-btn btn-info ms-2">
                                                        <a href="{{ route('quotations.edit', $quotation->id) }}"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                            data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Delete Quotations')
                                                    <div class="action-btn bg-danger ms-2">
                                                        <a href="#"
                                                            class="bs-pass-para mx-3 btn btn-sm d-inline-flex align-items-center"
                                                            data-toggle="sweet-alert" title="{{ __('Delete') }}"
                                                            data-bs-toggle="tooltip" data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $quotation->id }}">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                    </div>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['quotations.destroy', $quotation->id], 'id' => 'delete-form-' . $quotation->id]) !!}
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
    <script type="text/javascript">
        $(function() {
            $(document).on('click', '#resend-quotation', function(e) {
                e.preventDefault();
                var ele = $(this);

                $.ajax({
                    url: '{{ route('resend.quotation') }}',
                    method: "patch",
                    data: {
                        quotation_id: $(this).data('id'),
                        customer_email: $(this).data('mail'),
                    },
                    beforeSend: function() {
                        ele.prop("disabled", true);
                        ele.text('{{ __('Processing...') }}');
                    },
                    success: function(response) {
                        if (response.code == '200') {
                            show_toastr('Success', response.success, 'success')
                        }
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        show_toastr('{{ __('Error') }}', data.error, 'error');
                    },
                    complete: function() {
                        ele.prop("disabled", false);
                        ele.text('{{ __('Resend') }}');
                    }
                });
            });

            $(document).on('click', '.quotation-action', function(e) {
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

                            $('[data-li-id="' + id + '"] .quotation-action').removeClass(
                                'selected');

                            if (ele.hasClass('selected')) {

                                ele.removeClass('selected');

                            } else {
                                ele.addClass('selected');
                            }

                            var quotation = $('[data-li-id="' + id + '"] .quotation-actions')
                           
                                .find('.selected').text().trim();

                            var quotation_class = $('[data-li-id="' + id +
                                '"] .quotation-actions').find('.selected').attr(
                                'data-class');
                            $('[data-li-id="' + id + '"] .quotation-label').removeClass(
                                    'quotation-open quotation-close').addClass(quotation_class)
                                .text(quotation);
                        }
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        show_toastr('{{ __('Error') }}', data.error, 'error');
                    }
                });
            });
        });
    </script>
@endpush
