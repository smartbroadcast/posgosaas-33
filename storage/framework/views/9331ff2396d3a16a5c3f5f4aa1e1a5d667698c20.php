<?php
$user = Auth::user();
if ($user) {
    $currantLang = $user->lang;
    $languages = \App\Models\Utility::languages();
}

$emailTemplate     = App\Models\EmailTemplate::first();

// $logo = asset(Storage::url('logo'));
if (\Auth::user()->type == 'Super Admin'){

    $logo=\App\Models\Utility::get_file('uploads/logo/');
}
else {
    $logo=\App\Models\Utility::get_file('/');
    
}

if (\Auth::user()->type == 'Super Admin') {
    $company_logo = Utility::get_superadmin_logo();
} else {
    $company_logo = Utility::get_company_logo();
}

$cust_theme_bg = App\Models\Utility::getValByName('cust_theme_bg');
?>

<?php if((isset($cust_theme_bg) && $cust_theme_bg == 'on')): ?>
    <nav class="dash-sidebar light-sidebar transprent-bg">
<?php else: ?>
        <nav class="dash-sidebar light-sidebar">
<?php endif; ?>


<div class="navbar-wrapper">
    <div class="m-header main-logo">
        <a href="<?php echo e(route('home')); ?>" class="b-brand">
            <!-- ========   change your logo hear   ============ -->
            
            <img src="<?php echo e($logo . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png')); ?>"
                alt="<?php echo e(config('app.name', 'Posgo')); ?>" class="logo logo-lg">
           
        </a>
    </div>
    <div class="navbar-content">
        <ul class="dash-navbar">
            <li class="dash-item  <?php echo e(Request::segment(1) == '' ? 'active' : ''); ?>">
                <a href="<?php echo e(route('home')); ?>" class="dash-link"><span class="dash-micon"><i
                            class="ti ti-home"></i></span><span class="dash-mtext"><?php echo e(__('Dashboard')); ?></span></a>
            </li>

            <?php if(Auth::user() && Auth::user()->parent_id == 0): ?>
                <li class="dash-item dash-hasmenu">
                    <a href="<?php echo e(route('users.index')); ?>" class="dash-link"><span class="dash-micon"><i
                                class="ti ti-users"></i></span><span class="dash-mtext"><?php echo e(__('Owners')); ?></span></a>
                </li>
            <?php else: ?>
                <?php if(Gate::check('Manage User') || Gate::check('Manage Role') || Gate::check('Manage Permission')): ?>
                    <li class="dash-item dash-hasmenu">
                        <a href="#!" class="dash-link"><span class="dash-micon"><i
                                    class="ti ti-users"></i></span><span
                                class="dash-mtext"><?php echo e(__('Staff')); ?></span><span class="dash-arrow"><i
                                    data-feather="chevron-right"></i></span></a>
                        <ul class="dash-submenu">

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage User')): ?>
                                <li class="dash-item dash-hasmenu">
                                    <a class="dash-link" href="<?php echo e(route('users.index')); ?>">Users</a>
                                </li>
                            <?php endif; ?>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Role')): ?>
                                <li class="dash-item dash-hasmenu">
                                    <a class="dash-link" href="<?php echo e(route('roles.index')); ?>">Roles</a>
                                </li>
                            <?php endif; ?>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Permission')): ?>
                                <li class="dash-item dash-hasmenu">
                                    <a class="dash-link" href="<?php echo e(route('permissions.index')); ?>">permissions</a>
                                </li>
                            <?php endif; ?>

                        </ul>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Customer')): ?>
                <li class="dash-item ">
                    <a href="<?php echo e(route('customers.index')); ?>"
                        class="dash-link <?php echo e(Request::segment(1) == 'customers' ? 'active' : ''); ?>"><span
                            class="dash-micon"><i class="ti ti-user"></i></span><span
                            class="dash-mtext"><?php echo e(__('Customers')); ?></span>
                    </a>
                </li>
            <?php endif; ?>


            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Vendor')): ?>
                <li class="dash-item ">
                    <a href="<?php echo e(route('vendors.index')); ?>"
                        class="dash-link <?php echo e(Request::segment(1) == 'customers' ? 'active' : ''); ?>"><span
                            class="dash-micon"><i class="ti ti-user-plus"></i></span><span
                            class="dash-mtext"><?php echo e(__('Vendors')); ?></span>
                    </a>
                </li>
            <?php endif; ?>


            <?php if(Gate::check('Manage Product') || Gate::check('Manage Category') || Gate::check('Manage Brand') || Gate::check('Manage Tax') || Gate::check('Manage Unit')): ?>
                <li class="dash-item dash-hasmenu">
                    <a href="#" class="dash-link"><span class="dash-micon"><i
                                class="ti ti-brand-producthunt"></i></span><span
                            class="dash-mtext"><?php echo e(__('Products')); ?></span><span class="dash-arrow"><i
                                data-feather="chevron-right"></i></span></a>



                    <ul class="dash-submenu">

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Product')): ?>
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link" href="<?php echo e(route('products.index')); ?>">Products</a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Category')): ?>
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link" href="<?php echo e(route('categories.index')); ?>">Categories</a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Brand')): ?>
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link" href="<?php echo e(route('brands.index')); ?>">Brands</a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Tax')): ?>
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link" href="<?php echo e(route('taxes.index')); ?>">Tax</a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Unit')): ?>
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link" href="<?php echo e(route('units.index')); ?>">Unit</a>
                            </li>
                        <?php endif; ?>

                    </ul>
                </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Purchases')): ?>
                <li class="dash-item dash-hasmenu">
                    <a href="#navbar-purchases"
                        class="dash-link <?php echo e(Request::segment(1) == 'purchases' || Request::segment(1) . '/' . Request::segment(2) == 'reports/purchases' ? 'active' : ''); ?>"><span
                            class="dash-micon"><i class="ti ti-shopping-cart"></i></span><span
                            class="dash-mtext"><?php echo e(__('Purchases')); ?></span><span class="dash-arrow"><i
                                data-feather="chevron-right"></i></span></a>



                    <ul class="dash-submenu">

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Product')): ?>
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link" href="<?php echo e(route('purchases.index')); ?>"><?php echo e(__('Add Purchase')); ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Category')): ?>
                     
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link" href="<?php echo e(route('reports.purchases')); ?>"><?php echo e(__('Purchases')); ?></a>
                            </li>
                        <?php endif; ?>

                    </ul>
                </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Sales')): ?>
                <li class="dash-item dash-hasmenu">
                    <a href="#" class="dash-link"><span class="dash-micon"><i class="ti ti-book"></i></span><span
                            class="dash-mtext"><?php echo e(__('Sales')); ?></span><span class="dash-arrow"><i
                                data-feather="chevron-right"></i></span></a>


                    <ul class="dash-submenu">

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Product')): ?>
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link" href="<?php echo e(route('sales.index')); ?>"><?php echo e(__('Add Sale')); ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Category')): ?>
                        
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link" href="<?php echo e(route('reports.sales')); ?>"><?php echo e(__('Sales')); ?></a>
                            </li>
                        <?php endif; ?>

                    </ul>
                </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Returns')): ?>
                

                <li class="dash-item <?php echo e(\Request::route()->getName() == 'productsreturn.index' || \Request::route()->getName() == 'productsreturn.edit' || \Request::route()->getName() == 'productsreturn.create' ? ' active' : ''); ?>">
                    <a href="<?php echo e(!empty(\Auth::user()->getDefualtViewRouteByModule('productsreturn')) ? route(\Auth::user()->getDefualtViewRouteByModule('productsreturn')) : route('productsreturn.index')); ?>" class="dash-link ">
                        <span class="dash-micon"><i class="ti ti-receipt-refund"></i></span><span class="dash-mtext"><?php echo e(__('Returns')); ?></span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Quotations')): ?>
            <li class="dash-item <?php echo e(\Request::route()->getName() == 'quotations.index' || \Request::route()->getName() == 'quotations.edit' || \Request::route()->getName() == 'quotations.create' ? ' active' : ''); ?>">
                <a href="<?php echo e(!empty(\Auth::user()->getDefualtViewRouteByModule('quotations')) ? route(\Auth::user()->getDefualtViewRouteByModule('quotations')) : route('quotations.index')); ?>" class="dash-link ">
                    <span class="dash-micon"><i class="ti ti-currency-pound"></i></span><span class="dash-mtext"><?php echo e(__('Quotations')); ?></span>
                </a>
            </li>

                
            <?php endif; ?>

            <?php if(Gate::check('Manage Expense') || Gate::check('Manage Expense Category')): ?>
                <li class="dash-item dash-hasmenu">
                    <a href="#"
                        class="dash-link <?php echo e(Request::segment(1) == 'expenses' || Request::segment(1) == 'expensecategories' ? 'active' : ''); ?>"><span
                            class="dash-micon"><i class="ti ti-report-money"></i></span><span
                            class="dash-mtext"><?php echo e(__('Expenses')); ?></span><span class="dash-arrow"><i
                                data-feather="chevron-right"></i></span></a>


                    <ul class="dash-submenu">

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Expense')): ?>
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link" href="<?php echo e(route('expenses.index')); ?>"><?php echo e(__('Expense List')); ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Expense Category')): ?>
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link" href="<?php echo e(route('expensecategories.index')); ?>"><?php echo e(__('Expense
                                    Category')); ?></a>
                            </li>
                        <?php endif; ?>

                    </ul>
                </li>
            <?php endif; ?>


            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Calendar Event')): ?>
                <li class="dash-item ">
                    <a href="<?php echo e(route('calendars.index')); ?>"
                        class="dash-link <?php echo e(Request::segment(1) == 'calendars' ? 'active' : ''); ?>"><span
                            class="dash-micon"><i class="ti ti-calendar"></i></span><span
                            class="dash-mtext"><?php echo e(__('Calendar')); ?></span>
                    </a>
                </li>
            <?php endif; ?>


            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Notification')): ?>
                <li class="dash-item ">
                    <a href="<?php echo e(route('notifications.index')); ?>"
                        class="dash-link <?php echo e(Request::segment(1) == 'notifications' ? 'active' : ''); ?>"><span
                            class="dash-micon"><i class="ti ti-notification"></i></span><span
                            class="dash-mtext"><?php echo e(__('Notifications')); ?></span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Plan')): ?>
            <li class="dash-item <?php echo e(\Request::route()->getName() == 'plans'  || \Request::route()->getName() == 'stripe' || \Request::route()->getName() == 'plans.show' || \Request::route()->getName() == 'plans.edit' ? ' active' : ''); ?>">
                <a href="<?php echo e(route('plans.index')); ?>" class="dash-link">
                    <span class="dash-micon"><i class="ti ti-award"></i></span><span class="dash-mtext"><?php echo e(__('Plans')); ?></span>
                </a>
            </li>
            
                
            <?php endif; ?>


            <?php if(Auth::user()->isSuperAdmin()): ?>
                <li class="dash-item ">
                    <a href="<?php echo e(route('plan_request.index')); ?>"
                        class="dash-link <?php echo e(request()->is('plan_request*') ? 'active' : ''); ?>"><span
                            class="dash-micon"><i class="ti ti-arrow-down-right-circle"></i></span><span
                            class="dash-mtext"><?php echo e(__('Plan Request')); ?></span>
                    </a>
                </li>
            <?php endif; ?>  

            

                <?php if(Auth::user()->isSuperAdmin()): ?>
                    <li class="dash-item <?php echo e((Request::route()->getName() == 'email_template.index' || Request::segment(1) == 'email_template_lang' || Request::route()->getName() == 'manageemail.lang') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('manage.email.language',[$emailTemplate ->id,\Auth::user()->lang])); ?>" class="dash-link"><span
                                class="dash-micon"><i class="ti ti-template"></i></span><span
                                class="dash-mtext"><?php echo e(__('Email Template')); ?></span></a>
                    </li>
                <?php endif; ?>

                



            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Coupon')): ?>
                <li class="dash-item dash-hasmenu">
                    <li class="dash-item <?php echo e(\Request::route()->getName() == 'coupons.index' || \Request::route()->getName() == 'coupons.show' ? ' active' : ''); ?>">
                        <a href="<?php echo e(!empty(\Auth::user()->getDefualtViewRouteByModule('coupons')) ? route(\Auth::user()->getDefualtViewRouteByModule('coupons')) : route('coupons.index')); ?>" class="dash-link ">
                            <span class="dash-micon"><i class="ti ti-gift"></i></span><span class="dash-mtext"><?php echo e(__('Coupons')); ?></span>
                        </a>
                    </li>
                    
                </li>
            <?php endif; ?>


            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Order')): ?>
                <li class="dash-item dash-hasmenu">
                    <a href="<?php echo e(route('order.index')); ?>"
                        class="dash-link <?php echo e(Request::segment(1) == 'orders' ? 'active' : ''); ?>"><span
                            class="dash-micon"><i class="ti ti-calendar-plus"></i></span><span
                            class="dash-mtext"><?php echo e(__('Orders')); ?></span>
                    </a>
                </li>
            <?php endif; ?>


            
            

            <?php if(Gate::check('Manage Product') || Gate::check('Manage Category') || Gate::check('Manage Brand') || Gate::check('Manage Tax') || Gate::check('Manage Expense') || Gate::check('Manage Customer') || Gate::check('Manage Vendor') || Gate::check('Manage Purchases') || Gate::check('Manage Sales')): ?>
                <li
                    class="dash-item dash-hasmenu 
                <?php echo e(Request::segment(1) == 'product-stock-analysis' ||
                Request::segment(1) == 'product-category-analysis' ||
                Request::segment(1) == 'product-brand-analysis' ||
                Request::segment(1) == 'product-tax-analysis' ||
                Request::segment(1) == 'expense-analysis' ||
                Request::segment(1) == 'customer-sales-analysis' ||
                Request::segment(1) == 'vendor-purchased-analysis' ||
                Request::segment(1) == 'purchased-daily-analysis' ||
                Request::segment(1) == 'purchased-monthly-analysis' ||
                Request::segment(1) == 'sold-daily-analysis' ||
                Request::segment(1) == 'sold-monthly-analysis'
                    ? 'active dash-trigger'
                    : ''); ?>">
                    <a href="#" class="dash-link "><span class="dash-micon"><i
                                class="ti ti-report"></i></span><span
                            class="dash-mtext"><?php echo e(__('Reports')); ?></span><span class="dash-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="dash-submenu">


                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Product')): ?>
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link"
                                    href="<?php echo e(route('product.stock.analysis')); ?>"><?php echo e(__('Stock Analysis')); ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Category')): ?>
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link"
                                    href="<?php echo e(route('product.category.analysis')); ?>"><?php echo e(__('Category Report')); ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Brand')): ?>
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link"
                                    href="<?php echo e(route('product.brand.analysis')); ?>"><?php echo e(__('Brand Report')); ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Tax')): ?>
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link"
                                    href="<?php echo e(route('product.tax.analysis')); ?>"><?php echo e(__('Tax Report')); ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Expense')): ?>
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link"
                                    href="<?php echo e(route('expense.analysis')); ?>"><?php echo e(__('Expense Report')); ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Customer')): ?>
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link"
                                    href="<?php echo e(route('customer.sales.analysis')); ?>"><?php echo e(__('Customer Report')); ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Vendor')): ?>
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link"
                                    href="<?php echo e(route('vendor.purchased.analysis')); ?>"><?php echo e(__('Vendor Report')); ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Purchases')): ?>
                            <li
                                class="dash-item dash-hasmenu <?php echo e(Request::segment(1) == 'purchased-daily-analysis' || Request::segment(1) == 'purchased-monthly-analysis' ? 'active' : ''); ?>">
                                <a class="dash-link "
                                    href="<?php echo e(route('purchased.daily.analysis')); ?>"><?php echo e(__('Purchase Daily/Monthly Report')); ?></a>
                            </li>
                        <?php endif; ?>


                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Sales')): ?>
                            <li
                                class="dash-item dash-hasmenu <?php echo e(Request::segment(1) == 'sold-daily-analysis' || Request::segment(1) == 'sold-monthly-analysis' ? 'active' : ''); ?>">
                                <a class="dash-link "
                                    href="<?php echo e(route('sold.daily.analysis')); ?>"><?php echo e(__('Sale Daily/Monthly Report')); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Setting -->
            <?php if(Auth::user() && Auth::user()->parent_id == 0): ?>
                <li class="dash-item dash-hasmenu">
                    <a href="<?php echo e(route('settings.index')); ?>" class="dash-link"><span class="dash-micon"><i
                                class="ti ti-settings"></i></span><span
                            class="dash-mtext"><?php echo e(__('Settings')); ?></span></a>
                </li>
            <?php else: ?>

                <?php if(Gate::check('Store Settings') || Gate::check('Manage Branch') || Gate::check('Manage Cash Register') || Gate::check('Manage Branch Sales Target')): ?>
                    <li class="dash-item dash-hasmenu">
                        <a href="#!" class="dash-link"><span class="dash-micon"><i
                                    class="ti ti-settings"></i></span><span
                                class="dash-mtext"><?php echo e(__('Settings')); ?></span><span class="dash-arrow"><i
                                    data-feather="chevron-right"></i></span></a>
                        <ul class="dash-submenu">

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Store Settings')): ?>
                                <li class="dash-item dash-hasmenu">
                                    <a class="dash-link"
                                        href="<?php echo e(route('settings.index')); ?>"><?php echo e(__('Store Settings')); ?></a>
                                </li>
                            <?php endif; ?>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Branch')): ?>
                                <li class="dash-item dash-hasmenu">
                                    <a class="dash-link"
                                        href="<?php echo e(route('branches.index')); ?>"><?php echo e(__('Branches')); ?></a>
                                </li>
                            <?php endif; ?>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Cash Register')): ?>
                                <li class="dash-item dash-hasmenu">
                                    <a class="dash-link"
                                        href="<?php echo e(route('cashregisters.index')); ?>"><?php echo e(__('Cash Registers')); ?></a>
                                </li>
                            <?php endif; ?>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Branch Sales Target')): ?>
                                <li class="dash-item dash-hasmenu">
                                    <a class="dash-link"
                                        href="<?php echo e(route('branchsalestargets.index')); ?>"><?php echo e(__('Branch Sales Target')); ?></a>
                                </li>
                            <?php endif; ?>

                        </ul>
                    </li>
                <?php endif; ?>
            <?php endif; ?>


        </ul>
    </div>
    </ul>

</div>
</div>
</nav>
<?php /**PATH /Users/imac/Sites/localhost/posgosaas-33/resources/views/sidenav.blade.php ENDPATH**/ ?>