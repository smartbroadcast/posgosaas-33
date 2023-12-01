{{ Form::open(['url' => 'roles']) }}

<div class="modal-body">
    <div class="form-group">
        {{ Form::label('name', __('Role Name'), ['class' => 'col-form-label']) }}
        {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter new role name')]) }}
    </div>

    @if (!empty($permissions) && count($permissions) > 0)

        <div class="d-flex align-items-center justify-content-between">
            {{ Form::label('permission', __('Assign Permission'), ['class' => 'form-label col-form-label']) }}

            <div class="custom-control form-check float-right">
                {{ Form::checkbox('select-all', false, null, ['class' => 'form-check-input', 'id' => 'select-all']) }}
                {{ Form::label('select-all', 'Select All', ['class' => 'form-check-label ']) }}
            </div>
        </div>

        <div class="">
            <div class="">
                <div class="table-responsive shadow-none">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="200px">{{ __('Module') }}</th>
                                <th class="text-center">{{ __('Permissions') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $modules = ['User', 'Customer', 'Vendor', 'Role', 'Branch', 'Branch Sales Target', 'Tax', 'Unit', 'Product', 'Quotations', 'Expense', 'Expense Category', 'Returns', 'Category', 'Brand', 'Cash Register', 'Calendar Event', 'Notification', 'Profile'];
                            ?>
                            @foreach ($modules as $module)
                                <tr>
                                    <td class="form-control-label">{{ __($module) }}</td>

                                    <td>
                                        <div class="row">
                                            <?php
                                            $internalPermission = ['Manage', 'Create', 'Edit', 'Delete'];
                                            ?>
                                            @foreach ($internalPermission as $ip)
                                                @if (in_array($ip . ' ' . $module, $permissions))
                                                    @php($key = array_search($ip . ' ' . $module, $permissions))
                                                    <div class="col-3 custom-control form-check">
                                                        {{ Form::checkbox('permissions[]', $key, false, ['class' => 'form-check-input', 'id' => 'permission_' . $key]) }}
                                                        {{ Form::label('permission_' . $key, $ip, ['class' => 'custom-control-label ']) }}

                                                    </div>
                                                @endif
                                            @endforeach

                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            <tr>
                                <td class="form-control-label">{{ __('Account') }}</td>
                                <td>
                                    <div class="row">
                                        <?php
                                        $customPermission = ['Email Settings', 'Manage Logos', 'Create Language', 'Change Language', 'Manage Language', 'Change Password', 'System Settings', 'Billing Settings', 'Store Settings', 'Manage Purchases', 'Manage Sales'];
                                        ?>
                                        @foreach ($customPermission as $p)
                                            @if (in_array($p, $permissions))
                                                @php($key = array_search($p, $permissions))
                                                <div class="col-4 custom-control form-check">
                                                    {{ Form::checkbox('permissions[]', $key, false, ['class' => 'form-check-input', 'id' => 'permission_' . $key]) }}
                                                    {{ Form::label('permission_' . $key, $p, ['class' => 'custom-control-label']) }}
                                                </div>
                                            @endif
                                        @endforeach

                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <input class="btn btn-primary" type="submit" value="{{ __('Create') }}">
    </div>

    {{ Form::close() }}
