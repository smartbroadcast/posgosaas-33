<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0"><?php echo e(__('Dashboard')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-title', __('Dashboard')); ?>


<?php $__env->startSection('header-content'); ?>
    <div class="row">
        <?php if(count($lowstockproducts) > 0): ?>
            <div class="col-md-12">
                <?php $__currentLoopData = $lowstockproducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <span class="alert-icon"><i class="ti ti-alert-triangle"></i></span>
                        <strong><?php echo e($product['name']); ?></strong><small><?php echo e(__(' (Only ') . $product['quantity'] . __(' items left)')); ?></small>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>


        <?php if(isset($notifications) && !empty($notifications) && count($notifications) > 0): ?>
            <div class="col-md-12">
                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="alert alert-<?php echo e($notification->color); ?> alert-dismissible fade show" role="alert">
                        <strong><?php echo $notification->description; ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if($branches == 0 || $cashregisters == 0 || $productscount == 0 || $customers == 0 || $vendors == 0): ?>
        <div class="row mt-4">
            <div class="col-md-12">
                <?php
                $alerts = [];
                
                $alerts[] = $branches == 0 ? __('Please add some Branches!') : '';
                
                $alerts[] = $cashregisters == 0 ? __('Please add some Cash Registers!') : '';
                
                $alerts[] = $productscount == 0 ? __('Please add some Products!') : '';
                
                $alerts[] = $customers == 0 ? __('Please add some Customers!') : '';
                
                $alerts[] = $vendors == 0 ? __('Please add some Vendors!') : '';
                
                $result = array_filter($alerts);
                ?>
                <?php if(isset($result) && !empty($result) && count($result) > 0): ?>
                    <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="alert alert-warning alert-dismissible fade show  mt-1" role="alert">
                            <span class="alert-icon"><i class="ti ti-alert-triangle"></i></span>
                            <strong><?php echo e($alert); ?></strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>


    <div class="row">

        <div class="col-sm-12">
            <div class="row">
                <div class="col-xxl-7">
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="card" style="min-height: 225px;">
                                <div class="card-body">
                                    <div class="theme-avtar bg-primary">
                                        <i class="ti ti-hand-finger"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2"><?php echo e(__('Sales Of This Month')); ?></p>
                                    <h6 class="mb-3"></h6>
                                    <h3 class="mb-0"><?php echo e($monthlySelledAmount); ?><span
                                            class="text-success text-sm"><i class=""></i> </span></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="card" style="min-height: 225px;">
                                <div class="card-body">
                                    <div class="theme-avtar bg-info">
                                        <i class="ti ti-chart-pie"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2"><?php echo e(__('Total Sales Amount')); ?></p>
                                    <h6 class="mb-3"></h6>
                                    <h3 class="mb-0"><?php echo e($totalSelledAmount); ?><span
                                            class="text-danger text-sm"><i class=""></i></span></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="card" style="min-height: 225px;">
                                <div class="card-body">
                                    <div class="theme-avtar bg-warning">
                                        <i class="ti ti-report-money"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2"><?php echo e(__('Purchase Of This Month')); ?></p>
                                    <h6 class="mb-3"></h6>
                                    <h3 class="mb-0"><?php echo e($monthlyPurchasedAmount); ?><span
                                            class="text-success text-sm"><i class=""></i> </span></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="card" style="min-height: 225px;">
                                <div class="card-body">
                                    <div class="theme-avtar bg-danger">
                                        <i class="ti ti-chart-bar"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2"><?php echo e(__('Total Purchase Amount')); ?></p>
                                    <h6 class="mb-3"></h6>
                                    <h3 class="mb-0"><?php echo e($totalPurchasedAmount); ?><span
                                            class="text-danger text-sm"><i class=""></i> </span></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-12">

                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between ">
                                    <h5><?php echo e(__('To do list')); ?></h5>
                                    <div type="button" class="btn btn-sm btn-primary btn-icon m-1">
                                        <a href="#" class="" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="<?php echo e(__('Add Todo Task')); ?>" data-ajax-popup="true"
                                            data-title="<?php echo e(__('Add Todo Task')); ?>"
                                            data-url="<?php echo e(route('todos.create')); ?>">
                                            <i class="ti ti-plus text-white"></i></a>
                                    </div>
                                </div>
                            </div>

                            <?php if(isset($todos) && !empty($todos) && count($todos) > 0): ?>
                                <ul class="list-group list-group-flush todo-scrollbar" data-toggle="checklist">
                                    <?php $__currentLoopData = $todos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $todo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="checklist-entry list-group-item flex-column align-items-start">
                                            <div
                                                class="d-flex align-items-center justify-content-between checklist-item checklist-item-<?php echo e($todo->color); ?> <?php echo e($todo->status == 1 ? 'checklist-item-checked' : ''); ?>">
                                                <div class="checklist-info">
                                                    <a href="#!" class="fs-14 mb-0"><b><?php echo e($todo->title); ?></b></a>
                                                    <small
                                                        class="d-block"><?php echo e(Auth::user()->datetimeFormat($todo->created_at)); ?></small>
                                                </div>
                                                <div>
                                                    <div class="form-check  custom-checkbox ">
                                                        <input class="custom-control-input form-check-input"
                                                            id="chk-todo-task-<?php echo e($todo->id); ?>"
                                                            data-url="<?php echo e(route('todo.status', $todo->id)); ?>"
                                                            type="checkbox"
                                                            <?php echo e($todo->status == 1 ? ' checked=""' : ''); ?>>
                                                        <label class="custom-control-label"
                                                            for="chk-todo-task-<?php echo e($todo->id); ?>"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-5">
                    <div class="card">
                        <div class="card-header">

                            <div class="row ">
                                <div class="col-6">
                                    <h5><?php echo e(__('Purchase Sale Report')); ?></h5>
                                </div>
                                <div class="col-6 text-end">
                                    <h6><?php echo e(__('Last 10 Days')); ?></h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="traffic-chart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-7">
                    <div class="card">
                        <div class="card-header">
                            <h5><?php echo e(__('Calendar')); ?></h5>
                        </div>
                        <div class="card-body">
                            <div id='calendar' class='calendar'></div>
                        </div>
                    </div>
                </div>

                <?php if(isset($saletarget) && !empty($saletarget) && count($saletarget) > 0): ?>

                    <?php $__currentLoopData = $saletarget; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $target): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-xxl-5">
                            <div class="card">
                                <div class="card-header">
                                    <h5><?php echo e(__('Branches Target')); ?> (<small><?php echo e(__('This Month')); ?></small>)</h5>
                                    <div class="row align-items-center">
                                        <div class="col">
                                        </div>

                                    </div>
                                </div>
                                <div class="">
                                    <table class="table align-items-center mb-0 ">
                                        <thead class="thead-light">
                                            <tr class="border-top-0">
                                                <th class="w-25"><?php echo e(__('Branch Name')); ?></th>
                                                <th class="w-25"><?php echo e(__('Target')); ?></th>
                                                <th class="w-25"><?php echo e(__('Sales')); ?></th>
                                                <th class="w-25"><?php echo e(__('Progress')); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="list">
                                            <?php if(isset($target['branch']) && count($target['branch']) > 0): ?>
                                                <?php for($i = 0; $i < count($target['branch']); $i++): ?>
                                                    <tr>
                                                        <th scope="row">
                                                            <div class="media align-items-center">
                                                                <div class="media-body">
                                                                    <span
                                                                        class="name mb-0 text-sm"><?php echo e($target['branch'][$i]); ?></span>
                                                                </div>
                                                            </div>
                                                        </th>
                                                        <td class="budget">
                                                            <?php echo e($target['totaltarget'][$i]); ?>

                                                        </td>
                                                        <td>
                                                            <?php echo e($target['totalselledprice'][$i]); ?>

                                                        </td>
                                                        <td class="circular-progressbar p-0">
                                                            <?php
                                                            $percentage = $target['percentage'][$i];
                                                            
                                                            $status = $percentage > 0 && $percentage <= 25 ? 'red' : ($percentage > 25 && $percentage <= 50 ? 'orange' : ($percentage > 50 && $percentage <= 75 ? 'blue' : ($percentage > 75 && $percentage <= 100 ? 'green' : '')));
                                                            ?>
                                                            <div class="flex-wrapper">
                                                                <div class="single-chart">
                                                                    <svg viewBox="0 0 36 36"
                                                                        class="circular-chart <?php echo e($status); ?>">
                                                                        <path class="circle-bg"
                                                                            d="M18 2.0845
                                                                                                      a 15.9155 15.9155 0 0 1 0 31.831
                                                                                                      a 15.9155 15.9155 0 0 1 0 -31.831" />
                                                                        <path class="circle"
                                                                            stroke-dasharray="<?php echo e($percentage); ?>, 100"
                                                                            d="M18 2.0845
                                                                                                      a 15.9155 15.9155 0 0 1 0 31.831
                                                                                                      a 15.9155 15.9155 0 0 1 0 -31.831" />
                                                                        <text x="18" y="20.35"
                                                                            class="percentage"><?php echo e($percentage); ?>%</text>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endfor; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                <?php endif; ?>

            </div>
        </div>





    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/plugins/apexcharts.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/moment.min.js')); ?>"></script>

    <script>
        (function() {
            var options = {
                chart: {
                    height: 350,
                    type: 'area', 
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
                series: [{
                        name: '<?php echo e(__('Purchase')); ?>',
                        data: <?php echo json_encode($purchasesArray['value']); ?>

                        // data: [200,300,400,500,600,700,800,500,400,600,500,700,700,300,500]

                    },
                    {
                        name: '<?php echo e(__('Sales')); ?>',
                        data: <?php echo json_encode($salesArray['value']); ?>

                        // data: [300,400,450,500,600,700,600,400,450,500,600,700,750,550,600]

                    },
                ],
                xaxis: {
                    categories: <?php echo json_encode($purchasesArray['label']); ?>,
                    title: {
                        text: '<?php echo e(__('Days')); ?>'
                    }
                },
                colors: ['#FF3A6E', '#6fd943'],

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
                        text: '<?php echo e(__('Amount')); ?>'
                    },
                }
            };
            var chart = new ApexCharts(document.querySelector("#traffic-chart"), options);
            chart.render();
        })();


        $(document).on('click', '.custom-checkbox .custom-control-input', function(e) {
            $.ajax({
                url: $(this).data('url'),
                method: 'PATCH',
                success: function(response) {},
                error: function(data) {
                    data = data.responseJSON;
                    show_toastr('<?php echo e(__('Error')); ?>', data.error, 'error')
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>


<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/plugins/main.min.js')); ?>"></script>


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
                    timeGridDay: "<?php echo e(__('Day')); ?>",
                    timeGridWeek: "<?php echo e(__('Week')); ?>",
                    dayGridMonth: "<?php echo e(__('Month')); ?>"
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
                events: <?php echo $arrEvents; ?>,



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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/imac/Sites/localhost/posgosaas-33/resources/views/dashboard.blade.php ENDPATH**/ ?>