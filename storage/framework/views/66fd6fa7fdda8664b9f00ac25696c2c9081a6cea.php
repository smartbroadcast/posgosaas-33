<?php $__env->startSection('page-title', __('Customers')); ?>

<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0"><?php echo e(__('Customers')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-btn'); ?>

    <a href="<?php echo e(route('customer.export')); ?>" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip"
        title="<?php echo e(__('Export')); ?>">
        <i class="ti ti-file-export text-white"></i>
    </a>

    <a href="#" class="btn btn-sm btn-primary btn-icon" data-url="<?php echo e(route('customer.file.import')); ?>"
        data-bs-toggle="tooltip" data-bs-toggle="tooltip" title="<?php echo e(__('Import')); ?>" data-ajax-popup="true"
        data-title="<?php echo e(__('Import customer CSV file')); ?>">
        <i class="ti ti-file-import text-white"></i>
    </a>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Customer')): ?>
        <a href="#" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" data-title="<?php echo e(__('Add New Customer')); ?>"
            title="<?php echo e(__(' New Customer')); ?>" data-url="<?php echo e(route('customers.create')); ?>"
            class="btn btn-sm btn-primary btn-icon m-1">
            <span class=""><i class="ti ti-plus text-white"></i></span>
        </a>
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item"><?php echo e(__('Customer')); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Customer')): ?>
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header card-body table-border-style">

                        <div class="table-responsive">
                            <table class="table" id="pc-dt-simple">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo e(__('Name')); ?></th>
                                        <th><?php echo e(__('Email')); ?></th>
                                        <th><?php echo e(__('Date/Time Added')); ?> </th>
                                        <th width="200px"><?php echo e(__('Action')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($key + 1); ?></td>
                                            <td><?php echo e($customer->name); ?></td>
                                            <td><?php echo e($customer->email); ?></td>
                                            <td><?php echo e(Auth::user()->datetimeFormat($customer->created_at)); ?></td>
                                            <td class="Action">
                                                <?php if($customer->is_active == 1): ?>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Customer')): ?>
                                                        <div class="action-btn btn-info ms-2">
                                                            <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                                data-ajax-popup="true" title="<?php echo e(__('Edit Customer')); ?>"
                                                                data-title="<?php echo e(__('Edit Customer')); ?>" data-size="lg"
                                                                data-url="<?php echo e(route('customers.edit', $customer->id)); ?>"
                                                                data-bs-toggle="tooltip" title="<?php echo e(__('Edit Customer')); ?>">
                                                                <i class="ti ti-pencil text-white"></i>

                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Customer')): ?>
                                                        <div class="action-btn bg-danger ms-2">
                                                            <a href="#"
                                                                class="bs-pass-para mx-3 btn btn-sm d-inline-flex align-items-center"
                                                                data-toggle="sweet-alert" data-bs-toggle="tooltip"
                                                                data-confirm="<?php echo e(__('Are You Sure?')); ?>"
                                                                data-text="<?php echo e(__('This action can not be undone. Do you want to continue?')); ?>"
                                                                data-confirm-yes="delete-form-<?php echo e($customer->id); ?>"
                                                                title="<?php echo e(__('Delete')); ?>">
                                                                <i class="ti ti-trash text-white"></i>
                                                            </a>
                                                        </div>
                                                        <?php echo Form::open(['method' => 'DELETE', 'route' => ['customers.destroy', $customer->id], 'id' => 'delete-form-' . $customer->id]); ?>

                                                        <?php echo Form::close(); ?>

                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <a href="#" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-lock"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/imac/Sites/localhost/posgosaas-33/resources/views/customers/index.blade.php ENDPATH**/ ?>