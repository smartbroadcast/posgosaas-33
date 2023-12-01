@extends('layouts.app')

@section('page-title', __('Expenses'))

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Expenses List') }}</h5>
    </div>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Expenses') }}</li>
@endsection

@section('action-btn')

    <a href="{{ route('Expense.export') }}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip"
        title="{{ __('Export') }}">
        <span class="text-white"><i class="ti ti-file-export"></i></span>
    </a>

    @can('Create Expense')
        <a class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" data-ajax-popup="true"
            data-title="{{ __('Add New Expense') }}" data-url="{{ route('expenses.create') }}"
            title="{{ __('Add Expense') }}">
            <span class="text-white"><i class="ti ti-plus"></i></span>
        </a>
    @endcan

@endsection

@section('content')
    @can('Manage Expense')
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header card-body table-border-style">
                        <div class="table-responsive" id="expense-table">
                            <table class="table expense_table" id="pc-dt-simple" role="grid">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Branch') }}</th>
                                        <th>{{ __('Expense Date') }}</th>
                                        <th>{{ __('Expense Category') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th width="200px">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expenses as $key => $expense)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $expense->branchname }}</td>
                                            <td>{{ Auth::user()->dateFormat($expense->date) }}</td>
                                            <td>{{ $expense->ecname }}</td>
                                            <td>{{ Auth::user()->priceFormat($expense->amount) }}</td>
                                            <td class="Action">
                                                @can('Edit Expense')
                                                    <div class="action-btn btn-info ms-2">
                                                        <a href="#" data-ajax-popup="true" data-title="{{ __('Edit Expense') }}"
                                                            data-bs-toggle="tooltip"
                                                            data-url="{{ route('expenses.edit', $expense->id) }}"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                            title="{{ __('Edit') }}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Delete Expense')
                                                    <div class="action-btn bg-danger ms-2">
                                                        <a href="#"
                                                            class="bs-pass-para mx-3 btn btn-sm d-inline-flex align-items-center"
                                                            data-toggle="sweet-alert" title="{{ __('Delete') }}"
                                                            data-bs-toggle="tooltip" data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $expense->id }}">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                    </div>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['expenses.destroy', $expense->id], 'id' => 'delete-form-' . $expense->id]) !!}
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
    <script>
        $(document).ready(function() {
            $('#expense-table .expense_table').DataTable().destroy();
            $('#expense-table .expense_table').DataTable({
                "language": dataTabelLang,
                dom: 'Bfrtip',
                buttons: [{
                        "extend": 'copy',
                        "text": 'Copy',
                        "className": 'btn btn-warning btn-sm badge badge-soft-warning badge-lg border-0',
                        exportOptions: {
                            columns: ':not(:last-child)',
                        }
                    },
                    {
                        "extend": 'csv',
                        "text": 'CSV',
                        "className": 'btn btn-info btn-sm badge badge-soft-info badge-lg border-0',
                        exportOptions: {
                            columns: ':not(:last-child)',
                        }
                    },
                    {
                        "extend": 'excel',
                        "text": 'Excel',
                        "className": 'btn btn-success btn-sm badge badge-soft-success badge-lg border-0',
                        exportOptions: {
                            columns: ':not(:last-child)',
                        }
                    },
                    {
                        "extend": 'pdf',
                        "text": 'PDF',
                        "className": 'btn btn-danger btn-sm badge badge-soft-danger badge-lg border-0',
                        exportOptions: {
                            columns: ':not(:last-child)',
                        }
                    },
                    {
                        "extend": 'print',
                        "text": 'Print',
                        "className": 'btn btn-primary btn-sm badge badge-soft-primary badge-lg border-0',
                        exportOptions: {
                            columns: ':not(:last-child)',
                        }
                    }
                ]
            });
        });
    </script>
    <script src="{{ asset('js/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/datatables/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('js/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('js/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/datatables/buttons.print.min.js') }}"></script>
@endpush
