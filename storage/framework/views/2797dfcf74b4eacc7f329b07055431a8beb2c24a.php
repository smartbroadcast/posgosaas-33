<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0"><?php echo e(__('Dashboard')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-title', __('Dashboard')); ?>

<?php $__env->startSection('header-content'); ?>
    <div class="row">
        <div class="col-xxl-6">
            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="card" style="min-height: 225px;">
                        <div class="card-body">
                            <div class="theme-avtar bg-primary">
                                <i class="ti ti-users"></i>
                            </div>
                            <p class="text-muted text-sm mt-4 mb-2"><?php echo e(__('Total Users')); ?> : <?php echo e($ownersCount); ?></p>
                            <h6 class="mb-3"><?php echo e(__('Paid Users')); ?></h6>
                            <h3 class="mb-0"><?php echo e($paidOwnersCount); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="card" style="min-height: 225px;">
                        <div class="card-body">
                            <div class="theme-avtar bg-info">
                                <i class="ti ti-shopping-cart"></i>
                            </div>
                            <p class="text-muted text-sm mt-4 mb-2"><?php echo e(__('Total Orders')); ?>:<?php echo e($ordersCount); ?></p>
                            <h6 class="mb-3"><?php echo e(__('Total Order Amount')); ?></h6>
                            <h3 class="mb-0"><?php echo e($ordersPrice); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="card" style="min-height: 225px;">
                        <div class="card-body">
                            <div class="theme-avtar bg-warning">
                                <i class="ti ti-trophy"></i>
                            </div>
                            <p class="text-muted text-sm mt-4 mb-2"><?php echo e(__('Total Plan')); ?>: <?php echo e($plansCount); ?></p>
                            <h6 class="mb-3"><?php echo e(__('Most Purchase Plan')); ?></h6>
                            <h3 class="mb-0"><?php echo e($mostPurchasedPlan); ?></h3>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header">
                    <div class="row ">
                        <div class="col-6">
                            <h5><?php echo e(__('Order Report')); ?></h5>
                        </div>
                        <div class="col-6 text-end">
                            <h6><?php echo e(__('Last 14 Days')); ?></h6>
                        </div>
                    </div>

                </div>
                <div class="card-body">

                    <div id="order-chart" height="200" class="p-3"></div>
                </div>
            </div>


        </div>

    </div>
<?php $__env->stopSection(); ?>



<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/plugins/apexcharts.min.js')); ?>"></script>

    <script>
        (function() {
            var options = {

                series: [{
                    name: '<?php echo e(__('Order')); ?>',
                    data: <?php echo json_encode($getOrderChart['data']); ?>

                }, ],

                chart: {
                    height: 300,
                    type: 'area',
                    dropShadow: {
                        enabled: true,
                        color: '#000',
                        top: 18,
                        left: 7,
                        blur: 10,
                        opacity: 0.2
                    },
                    toolbar: {
                        show: false,
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },

                title: {
                    text: '',
                    align: 'left'
                },

                xaxis: {
                    categories: <?php echo json_encode($getOrderChart['label']); ?>,
                    title: {
                        text: 'Days'
                    }
                },

                colors: ['#6fd943', '#ff3a6e'],

                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: false,
                },
                // markers: {
                //     size: 4,
                //     colors: ['#ffa21d', '#FF3A6E'],
                //     opacity: 0.9,
                //     strokeWidth: 2,
                //     hover: {
                //         size: 7,
                //     }
                // },
                yaxis: {
                    title: {
                        text: 'Amount'
                    },
                }
            };
            var chart = new ApexCharts(document.querySelector("#order-chart"), options);
            chart.render();
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/imac/Sites/localhost/posgosaas-33/resources/views/sa-dashboard.blade.php ENDPATH**/ ?>