<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Email Templates')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('title'); ?>
<div class="d-inline-block">
    <h5 class="h4 d-inline-block font-weight-400 mb-0"><?php echo e(__('Email Template')); ?></h5>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item"><?php echo e(__('Email Template')); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('old-datatable-css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('custom/css/summernote/summernote-bs4.css')); ?>">

<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('custom/css/summernote/summernote-bs4.js')); ?>"></script> 
<script src="<?php echo e(asset('custom/js/tinymce/tinymce.min.js')); ?>"></script>
<script>
    if ($(".pc-tinymce-2").length) {
        tinymce.init({
            selector: '.pc-tinymce-2',
            height: "400",
            content_style: 'body { font-family: "Inter", sans-serif; }'
        });
    }
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('action-btn'); ?>
<div class="text-end mb-3">
    <div class="d-flex justify-content-end drp-languages">
        <ul class="list-unstyled mb-0 m-2">
            <li class="dropdown dash-h-item drp-language" style="list-style-type: none;">
                <a
                class="dash-head-link dropdown-toggle arrow-none me-0"
                data-bs-toggle="dropdown"
                href="#"
                role="button"
                aria-haspopup="false"
                aria-expanded="false"
                >
                <span class="drp-text hide-mob text-primary"><?php echo e(Str::upper($currEmailTempLang->lang )); ?></span>
                <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                </a>
                <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                    <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <a href="<?php echo e(route('manage.email.language', [$emailTemplate->id, $lang])); ?>"
                    class="dropdown-item <?php echo e($currEmailTempLang->lang == $lang ? 'text-primary' : ''); ?>"><?php echo e(Str::upper($lang)); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
                </div>
            </li>
        </ul>    
        <ul class="list-unstyled mb-0 m-2">
            <li class="dropdown dash-h-item drp-language" style="list-style-type: none;">
                <a class="dash-head-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <span class="drp-text hide-mob text-primary"><?php echo e(__('Template: ')); ?> <?php echo e($emailTemplate->name); ?></span>
                <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                </a>
                <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                    <?php $__currentLoopData = $EmailTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $EmailTemplate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('manage.email.language', [$EmailTemplate->id,(Request::segment(3)?Request::segment(3):\Auth::user()->lang)])); ?>"
                    class="dropdown-item <?php echo e($emailTemplate->name == $EmailTemplate->name ? 'text-primary' : ''); ?>"><?php echo e($EmailTemplate->name); ?>

                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
                </div>
            </li>
        </ul>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <div class="row">
        
        <div class="col-12">
            <div class="row">
                
            </div>
            <div class="card">
                <div class="card-body">
    
                    <div class="language-wrap">
                        <div class="row"> 
                            <h6><?php echo e(__('Place Holders')); ?></h6>
                            <div class="col-lg-12 col-md-9 col-sm-12 language-form-wrap">
    
                                <div class="card">
                                    <div class="card-header card-body">
                                        <div class="row text-xs">
                                            <?php if($emailTemplate->slug=='new_user'): ?>
                                                <div class="row">         
                                                    <p class="col-6"><?php echo e(__('App Name')); ?> : <span class="pull-end text-primary">{app_name}</span></p>
                                                    <p class="col-6"><?php echo e(__('App Url')); ?> : <span class="pull-right text-primary">{app_url}</span></p>
                                                    <p class="col-6"><?php echo e(__('User Name')); ?> : <span class="pull-right text-primary">{user_name}</span></p>
                                                    <p class="col-6"><?php echo e(__('User Email')); ?> : <span class="pull-right text-primary">{user_email}</span></p>
                                                    <p class="col-6"><?php echo e(__('User Password')); ?> : <span class="pull-right text-primary">{user_password}</span></p>
                                                </div>
                                                <?php elseif($emailTemplate->slug=='new_owner'): ?>
                                                <div class="row">                           
                                                    <p class="col-6"><?php echo e(__('App Name')); ?> : <span class="pull-end text-primary">{app_name}</span></p>
                                                    <p class="col-6"><?php echo e(__('App Url')); ?> : <span class="pull-right text-primary">{app_url}</span></p>
                                                    <p class="col-6"><?php echo e(__('Owner Name')); ?> : <span class="pull-right text-primary">{owner_name}</span></p>
                                                    <p class="col-6"><?php echo e(__('Owner Email')); ?> : <span class="pull-right text-primary">{owner_email}</span></p>
                                                    <p class="col-6"><?php echo e(__('Password')); ?> : <span class="pull-right text-primary">{owner_password}</span></p>
                                                    
                                                </div>
                                            <?php elseif($emailTemplate->slug=='new_customer'): ?>
                                                <div class="row">
                                                    <p class="col-6"><?php echo e(__('App Name')); ?> : <span class="pull-end text-primary">{app_name}</span></p>
                                                    <p class="col-6"><?php echo e(__('App Url')); ?> : <span class="pull-right text-primary">{app_url}</span></p>
                                                    <p class="col-6"><?php echo e(__('Customer Name')); ?> : <span class="pull-right text-primary">{customer_name}</span></p>
                                                    <p class="col-6"><?php echo e(__('Customer Email')); ?> : <span class="pull-right text-primary">{customer_email}</span></p>
                                                    <p class="col-6"><?php echo e(__('Customer Phone number')); ?> : <span class="pull-right text-primary">{customer_phone_number}</span></p>
                                                    <p class="col-6"><?php echo e(__('Customer Address')); ?> : <span class="pull-right text-primary">{customer_address}</span></p>
                                                    <p class="col-6"><?php echo e(__('Customer Country')); ?> : <span class="pull-right text-primary">{customer_country}</span></p>
                                                    <p class="col-6"><?php echo e(__('Customer Zipcode')); ?> : <span class="pull-right text-primary">{customer_zipcode}</span></p>
                                                </div>
                                            <?php elseif($emailTemplate->slug=='new_vendor'): ?>
                                                <div class="row">
                                                    <p class="col-6"><?php echo e(__('App Name')); ?> : <span class="pull-end text-primary">{app_name}</span></p>
                                                    <p class="col-6"><?php echo e(__('App Url')); ?> : <span class="pull-right text-primary">{app_url}</span></p>
                                                    <p class="col-6"><?php echo e(__('Vendor Name')); ?> : <span class="pull-right text-primary">{vendor_name}</span></p>
                                                    <p class="col-6"><?php echo e(__('Vendor Email')); ?> : <span class="pull-right text-primary">{vendor_email}</span></p>
                                                    <p class="col-6"><?php echo e(__('Vendor Phone number')); ?> : <span class="pull-right text-primary">{vendor_phone_number}</span></p>
                                                    <p class="col-6"><?php echo e(__('Vendor Address')); ?> : <span class="pull-right text-primary">{vendor_address}</span></p>
                                                    <p class="col-6"><?php echo e(__('Vendor Country')); ?> : <span class="pull-right text-primary">{vendor_country}</span></p>
                                                    <p class="col-6"><?php echo e(__('Vendor Zipcode')); ?> : <span class="pull-right text-primary">{vendor_zipcode}</span></p>
                                                </div>
                                            <?php elseif($emailTemplate->slug=='new_quote'): ?>
                                                <div class="row">
                                                    <p class="col-6"><?php echo e(__('App Name')); ?> : <span class="pull-end text-primary">{app_name}</span></p>
                                                    <p class="col-6"><?php echo e(__('App Url')); ?> : <span class="pull-right text-primary">{app_url}</span></p>
                                                    <p class="col-6"><?php echo e(__('Quotation Date')); ?> : <span class="pull-right text-primary">{quotation_date}</span></p>
                                                    <p class="col-6"><?php echo e(__('Quotation Reference No')); ?> : <span class="pull-right text-primary">{quotation_reference_no}</span></p>
                                                    <p class="col-6"><?php echo e(__('Quotation Customers')); ?> : <span class="pull-right text-primary">{quotation_customers}</span></p>
                                                    <p class="col-6"><?php echo e(__('Customer Email')); ?> : <span class="pull-right text-primary">{customer_email}</span></p>
                                                    
                                                </div>
                                           
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-9 col-sm-12 language-form-wrap">
                                
                                <?php echo e(Form::model($currEmailTempLang, array('route' => array('email_template.update', $currEmailTempLang->parent_id), 'method' => 'PUT'))); ?>

                                <div class="row">
                                    <div class="form-group col-12">
                                        <?php echo e(Form::label('subject',__('Subject'),['class'=>'form-control-label text-dark'])); ?>

                                        <?php echo e(Form::text('subject',null,array('class'=>'form-control font-style','required'=>'required'))); ?>

                                    </div>
                                    
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('name',__('Name'),['class'=>'form-control-label text-dark'])); ?>

                                        <?php echo e(Form::text('name',$emailTemplate->name,['class'=>'form-control font-style','disabled'=>'disabled'])); ?>

                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('from',__('From'),['class'=>'form-control-label text-dark'])); ?>

                                        <?php echo e(Form::text('from', $emailTemplate->from, ['class' => 'form-control font-style', 'required' => 'required'])); ?>

                                    </div>
                                    <div class="form-group col-12">
                                        <?php echo e(Form::label('content',__('Email Message'),['class'=>'form-control-label text-dark'])); ?>

                                        <?php echo e(Form::textarea('content',$currEmailTempLang->content,array('class'=>'pc-tinymce-2','required'=>'required'))); ?>

    
                                    </div>
                                   
                                   
                                    <div class="col-md-12 text-end">
                                        <?php echo e(Form::hidden('lang',null)); ?>

                                        <input type="submit" value="<?php echo e(__('Save')); ?>" class="btn btn-print-invoice  btn-primary">
                                    </div>
                                  
                                </div>
                                <?php echo e(Form::close()); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/imac/Sites/localhost/posgosaas-33/resources/views/email_templates/show.blade.php ENDPATH**/ ?>