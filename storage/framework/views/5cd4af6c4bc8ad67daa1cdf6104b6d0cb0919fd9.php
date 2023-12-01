<?php $__env->startSection('page-title', __('Orders')); ?>

<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0"><?php echo e(__('Orders')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item"><?php echo e(__('Orders')); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">

                    <div class="table-responsive">
                        <table class="table" id="pc-dt-simple" role="grid">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('Order Id')); ?></th>
                                    <th><?php echo e(__('Name')); ?></th>
                                    <th><?php echo e(__('Plan Name')); ?></th>
                                    <th><?php echo e(__('Price')); ?></th>
                                    <th><?php echo e(__('Type')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Coupon')); ?></th>
                                    <th><?php echo e(__('Date')); ?></th>
                                    <th class="text-right"><?php echo e(__('Invoice')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                               
                                <tr> 
                                        
                                        <td><?php echo e($order->order_id); ?></td>
                                        <td><?php echo e(ucfirst($order->user_name)); ?></td>
                                        <td><?php echo e($order->plan_name); ?></td>
                                        <td>$<?php echo e(number_format($order->price)); ?></td>
                                        <td><?php echo e($order->payment_type); ?></td>
                                        <td>
                                            <span
                                                class="badge p-2 px-3 rounded <?php if($order->payment_status == 'succeeded' || $order->payment_status == 'approved'): ?> bg-success <?php else: ?> bg-success <?php endif; ?>" style="width:83px">
                                                
                                                <?php echo e(ucfirst($order->payment_status)); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e(!empty($order->appliedCoupon->coupon_detail) ? (!empty($order->appliedCoupon->coupon_detail->code) ? $order->appliedCoupon->coupon_detail->code : '') : ''); ?>

                                        </td>
                                        <td><?php echo e($order->created_at->format('d M Y H:i:s')); ?></td>
                                        <td class="text-right">
                                            <?php if(!empty($order->receipt)): ?>
                                                <a href="<?php echo e($order->receipt); ?>" title="Invoice" target="_blank"
                                                    class="view-icon"><i class="ti ti-file-invoice text-primary"></i>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/imac/Sites/localhost/posgosaas-33/resources/views/orders/index.blade.php ENDPATH**/ ?>