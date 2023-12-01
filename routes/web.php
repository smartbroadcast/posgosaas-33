<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\VendorController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__ . '/auth.php';

Route::get('/', [HomeController::class,'index'])->name('home')->middleware(['XSS']);
Route::get('/change/mode', ['as' => 'change.mode', 'uses' => 'HomeController@changeMode']);



Route::resource('roles', RoleController::class)->middleware(['auth','XSS']);


// Route::resource('permissions', 'PermissionController')->middleware(['auth', 'XSS']);
Route::resource('permissions', PermissionController::class)->middleware(['auth','XSS']);


Route::get('checkusertype', [UserController::class,'checkUserType'])->name('user.type')->middleware(['auth', 'XSS']);
Route::get('profile', [UserController::class,'displayProfile'])->name('profile.display')->middleware(['auth', 'XSS']);
Route::post('upload', [UserController::class,'uploadProfile'])->name('profile.upload')->middleware(['auth', 'XSS']);
Route::post('update-password', [UserController::class,'updatePassword'])->name('update.password')->middleware(['auth', 'XSS']);
Route::delete('deleteprofile', [UserController::class,'deleteProfile'])->name('profile.delete')->middleware(['auth', 'XSS']);
Route::patch('changeuserstatus/{id}', [UserController::class,'changeUserStatus'])->name('user.status')->middleware(['auth', 'XSS']);


Route::get('user/{id}/plan', [UserController::class,'upgradePlan'])->name('plan.upgrade')->middleware(['auth', 'XSS']);
Route::get('user/{id}/plan/{pid}', [UserController::class,'activePlan'])->name('plan.active')->middleware(['auth', 'XSS']);




Route::resource('users', UserController::class)->middleware(['auth', 'XSS']);

Route::get('search-customers/{search?}', [CustomerController::class,'searchCustomers'])->name('search.customers')->middleware(['auth', 'XSS']);
Route::get('get-customer-email/{search?}', [CustomerController::class,'getCustomerEmail'])->name('get.customer.email')->middleware(['auth', 'XSS']);

// Route::get('search-customers/{search?}', 'CustomerController@searchCustomers')->name('search.customers')->middleware(['auth', 'XSS']);
// Route::get('get-customer-email/{search?}', 'CustomerController@getCustomerEmail')->name('get.customer.email')->middleware(['auth', 'XSS']);

Route::resource('customers', CustomerController::class)->middleware(['auth','XSS']);

Route::get('search-vendors/{search?}', [VendorController::class,'searchVendors'])->name('search.vendors')->middleware(['auth', 'XSS']);
// Route::get('search-vendors/{search?}', 'VendorController@searchVendors')->name('search.vendors')->middleware(['auth', 'XSS']);
Route::resource('vendors', VendorController::class)->middleware(['auth','XSS']);

Route::get('get-branches', [BranchController::class,'getBranches'])->name('get.branches')->middleware(['auth', 'XSS']);
// Route::get('get-branches', 'BranchController@getBranches')->name('get.branches')->middleware(['auth', 'XSS']);
Route::resource('branches', BranchController::class)->middleware(['auth', 'XSS']);

Route::resource('branchsalestargets', BranchSalesTargetController::class)->middleware(['auth', 'XSS']);

Route::resource('taxes', TaxController::class)->middleware(['auth','XSS']);

Route::resource('units', UnitController::class)->middleware(['auth','XSS']);

Route::get('add-to-cart/{id}/{session}', [ProductController::class,'addToCart'])->middleware(['XSS']);
Route::patch('update-cart', [ProductController::class,'updateCart'])->middleware(['XSS']);
Route::delete('remove-from-cart', [ProductController::class,'removeFromCart'])->middleware(['XSS']);
Route::post('empty-cart', [ProductController::class,'emptyCart'])->middleware(['XSS']);
Route::get('name-search-products', [ProductController::class,'searchProductsByName'])->name('name.search.products')->middleware(['XSS']);
Route::get('search-products', [ProductController::class,'searchProducts'])->name('search.products')->middleware(['XSS']);


// Route::get('add-to-cart/{id}/{session}', 'ProductController@addToCart')->middleware(['auth', 'XSS']);
// Route::patch('update-cart', 'ProductController@updateCart')->middleware(['auth', 'XSS']);
// Route::delete('remove-from-cart', 'ProductController@removeFromCart')->middleware(['auth', 'XSS']);
// Route::post('empty-cart', 'ProductController@emptyCart')->middleware(['auth', 'XSS']);
// Route::get('name-search-products', 'ProductController@searchProductsByName')->name('name.search.products')->middleware(['auth', 'XSS']);
// Route::get('search-products', 'ProductController@searchProducts')->name('search.products')->middleware(['auth', 'XSS']);

Route::get('product-categories', [CategoryController::class,'getProductCategories'])->name('product.categories')->middleware(['XSS']);
// Route::get('product-categories', 'CategoryController@getProductCategories')->name('product.categories')->middleware(['auth', 'XSS']);

Route::resource('products', ProductController::class)->middleware(['auth','XSS']);


Route::resource('categories', CategoryController::class)->middleware(['auth','XSS']);

Route::resource('brands', BrandController::class)->middleware(['auth','XSS']);


Route::get('purchased-invoice/{id}', [PurchaseController::class,'purchasedInvoice'])->name('purchased.invoice');
Route::get('purchased-invoices/preview/{template}/{color}', [PurchaseController::class,'previewPurchasedInvoice'])->name('purchased.invoice.preview');
Route::get('purchased-invoices/{id}/get_invoice', [PurchaseController::class,'printPurchaseInvoice'])->name('get.purchased.invoice');

Route::get('purchased-items', [PurchaseController::class,'purchasedItems'])->name('purchased.items')->middleware(['auth', 'XSS']);


Route::resource('purchases', PurchaseController::class)->middleware(['auth','XSS']);





Route::get('selled-invoice/{id}', [SaleController::class,'selledInvoice'])->name('selled.invoice');
Route::get('selled-invoices/preview/{template}/{color}', [SaleController::class,'previewSelledInvoice'])->name('selled.invoice.preview');

Route::get('sales-invoices/{id}/get_invoice', [SaleController::class,'printSaleInvoice'])->name('get.sales.invoice')->middleware(['XSS']);
Route::get('sales-items', [SaleController::class,'salesItems'])->name('sales.items')->middleware(['auth', 'XSS']);

Route::resource('sales', SaleController::class)->middleware(['auth','XSS']);

Route::get('returned-items', [ProductsReturnController::class,'returnedItems'])->name('returned.items')->middleware(['auth','XSS']);
// Route::get('returned-items', 'ProductsReturnController@returnedItems')->name('returned.items')->middleware(['auth', 'XSS']);
Route::resource('productsreturn', ProductsReturnController::class)->middleware(['auth','XSS']);


Route::get('quotation-items', [QuotationController::class,'quotationItems'])->name('quotation.items')->middleware(['auth', 'XSS']);
Route::get('quotation-invoice/{id}', [QuotationController::class,'quotationInvoice'])->name('quotation.invoice');
Route::get('quotation-invoices/{id}/get_invoice', [QuotationController::class,'printQuotationInvoice'])->name('get.quotation.invoice')->middleware(['auth', 'XSS']);
Route::patch('changequotationstatus/{id}', [QuotationController::class,'changeQuotationStatus'])->name('update.quotation.status')->middleware(['auth', 'XSS']);
Route::patch('resendquotation', [QuotationController::class,'resendQuotation'])->name('resend.quotation')->middleware(['auth', 'XSS']);
Route::get('quotation-invoices/preview/{template}/{color}', [QuotationController::class,'previewQuotationInvoice'])->name('quotation.invoice.preview')->middleware(['auth', 'XSS']);
Route::resource('quotations', QuotationController::class)->middleware(['auth','XSS']);



Route::get(
    '/invoice/pay/{invoice}',
    [
        'as' => 'pay.invoice',
        'uses' => 'PurchaseController@payinvoice',
    ]
);

Route::get('invoice-filter', [ReportController::class,'invoiceFilter'])->name('invoice.filter')->middleware(['auth','XSS']);
// Route::get('invoice-filter', 'ReportController@invoiceFilter')->name('invoice.filter')->middleware(['auth', 'XSS']);

Route::get('show-purchase-invoice/{id}', [ReportController::class,'showPurchaseInvoice'])->name('show.purchase.invoice')->middleware(['auth','XSS']);
// Route::get('show-purchase-invoice/{id}', 'ReportController@showPurchaseInvoice')->name('show.purchase.invoice')->middleware(['auth', 'XSS']);
Route::get('purchase-invoice/{id}/edit', [ReportController::class,'editPurchaseInvoice'])->name('edit.purchase.invoice')->middleware(['auth','XSS']);
// Route::get('purchase-invoice/{id}/edit', 'ReportController@editPurchaseInvoice')->name('edit.purchase.invoice')->middleware(['auth', 'XSS']);
Route::get('reports/purchases', [ReportController::class,'reportsPurchases'])->name('reports.purchases')->middleware(['auth','XSS']);


Route::get('show-sell-invoice/{id}', [ReportController::class,'showSellInvoice'])->name('show.sell.invoice')->middleware(['auth','XSS']);
// Route::get('show-sell-invoice/{id}', 'ReportController@showSellInvoice')->name('show.sell.invoice')->middleware(['auth', 'XSS']);
Route::get('sale-invoice/{id}/edit', [ReportController::class,'editSaleInvoice'])->name('edit.sale.invoice')->middleware(['auth','XSS']);
// Route::get('sale-invoice/{id}/edit', 'ReportController@editSaleInvoice')->name('edit.sale.invoice')->middleware(['auth', 'XSS']);
Route::get('reports/sales', [ReportController::class,'reportsSales'])->name('reports.sales')->middleware(['auth','XSS']);
// Route::get('reports/sales', [ReportController::class,'reportsSales'])->name('reports.sales')->middleware(['auth','XSS']);

Route::get('product-stock-analysis', [ReportController::class,'productStockAnalysisView'])->name('product.stock.analysis')->middleware(['auth','XSS']);
Route::get('filter-stock-analysis', [ReportController::class,'productStockAnalysisFilter'])->name('product.stock.analysis.filter')->middleware(['auth','XSS']);

Route::get('product-category-analysis', [ReportController::class,'productCategoryAnalysisView'])->name('product.category.analysis')->middleware(['auth','XSS']);
Route::get('filter-category-analysis', [ReportController::class,'productCategoryAnalysisFilter'])->name('product.category.analysis.filter')->middleware(['auth','XSS']);

Route::get('product-brand-analysis', [ReportController::class,'productBrandAnalysisView'])->name('product.brand.analysis')->middleware(['auth','XSS']);
Route::get('filter-brand-analysis', [ReportController::class,'productBrandAnalysisFilter'])->name('product.brand.analysis.filter')->middleware(['auth','XSS']);

Route::get('product-tax-analysis', [ReportController::class,'productTaxAnalysisView'])->name('product.tax.analysis')->middleware(['auth','XSS']);
Route::get('filter-purchase-tax-analysis', [ReportController::class,'productPurchaseTaxAnalysisFilter'])->name('product.purchase.tax.analysis.filter')->middleware(['auth','XSS']);

Route::get('ilter-sale-tax-analysis', [ReportController::class,'productSaleTaxAnalysisFilter'])->name('product.sale.tax.analysis.filter')->middleware(['auth','XSS']);

Route::get('expense-analysis', [ReportController::class,'expenseAnalysisView'])->name('expense.analysis')->middleware(['auth','XSS']);
Route::get('filter-expense-analysis', [ReportController::class,'expenseAnalysisFilter'])->name('expense.analysis.filter')->middleware(['auth','XSS']);


Route::get('customer-sales-analysis', [ReportController::class,'customerSalesAnalysisView'])->name('customer.sales.analysis')->middleware(['auth','XSS']);
Route::get('filter-customer-sales-analysis', [ReportController::class,'customerSalesAnalysisFilter'])->name('customer.sales.analysis.filter')->middleware(['auth','XSS']);

Route::get('vendor-purchased-analysis', [ReportController::class,'vendorPurchasedAnalysisView'])->name('vendor.purchased.analysis')->middleware(['auth','XSS']);
Route::get('filter-vendor-purchased-analysis', [ReportController::class,'vendorPurchasedAnalysisFilter'])->name('vendor.purchased.analysis.filter')->middleware(['auth','XSS']);


Route::get('purchased-daily-analysis', [ReportController::class,'purchasedDailyAnalysisView'])->name('purchased.daily.analysis')->middleware(['auth','XSS']);
Route::get('purchased-monthly-analysis', [ReportController::class,'purchasedMonthlyAnalysisView'])->name('purchased.monthly.analysis')->middleware(['auth','XSS']);
Route::get('filter-purchased-daily-chart', [ReportController::class,'purchasedDailyChartFilter'])->name('purchased.daily.chart.filter')->middleware(['auth','XSS']);
Route::get('filter-purchased-monthly-chart', [ReportController::class,'purchasedMonthlyChartFilter'])->name('purchased.monthly.chart.filter')->middleware(['auth','XSS']);




Route::get('sold-daily-analysis', [ReportController::class,'soldDailyAnalysisView'])->name('sold.daily.analysis')->middleware(['auth','XSS']);
Route::get('sold-monthly-analysis', [ReportController::class,'soldMonthlyAnalysisView'])->name('sold.monthly.analysis')->middleware(['auth','XSS']);
Route::get('filter-sold-daily-chart', [ReportController::class,'soldDailyChartFilter'])->name('sold.daily.chart.filter')->middleware(['auth','XSS']);
Route::get('filter-sold-monthly-chart', [ReportController::class,'soldMonthlyChartFilter'])->name('sold.monthly.chart.filter')->middleware(['auth','XSS']);




Route::patch('update-payment-status/{slug}/{id}', [ReportController::class,'updatePaymentStatus'])->name('update.payment.status')->middleware(['auth','XSS']);
// Route::patch('update-payment-status/{slug}/{id}', 'ReportController@updatePaymentStatus')->name('update.payment.status')->middleware(['auth', 'XSS']);

Route::resource('reports', ReportController::class)->middleware(['auth','XSS']);


Route::get('get-cash-registers', [CashRegisterController::class,'getCashRegisters'])->name('get.cash.registers')->middleware(['auth', 'XSS']);
Route::resource('cashregisters', CashRegisterController::class)->middleware(['auth', 'XSS']);

Route::resource('expenses', ExpenseController::class)->middleware(['auth', 'XSS']);
Route::resource('expensecategories', ExpenseCategoryController::class)->middleware(['auth', 'XSS']);

Route::resource('calendars', CalendarController::class)->middleware(['auth', 'XSS']);
Route::get('calendars/show/event/{id}', 'CalendarController@show')->name('Calendar.show')->middleware(['auth', 'XSS']);

Route::patch('change-notificationstatus/{id}', 'NotificationController@changeNotificationStatus')->name('update.notification.status')->middleware(['auth']);
Route::resource('notifications', NotificationController::class)->middleware(['auth', 'XSS']);

Route::patch('changetodotatus/{id}', [TodoController::class,'changeTodoStatus'])->name('todo.status')->middleware(['auth']);

// Route::patch('changetodotatus/{id}', 'TodoController@changeTodoStatus')->name('todo.status')->middleware(['auth']);
Route::resource('todos', TodoController::class)->middleware(['auth', 'XSS']);

Route::get('/apply-coupon', [CouponController::class,'applyCoupon'])->name('apply.coupon')->middleware(['auth','XSS']);

Route::resource('coupons', CouponController::class)->middleware(['auth', 'XSS']);


Route::resource('plans', PlanController::class)->middleware(['auth', 'XSS']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::resource('plan_request', PlanRequestController::class);
    }
);




Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        Route::get('change-language/{lang}', [LanguageController::class,'changeLanguage'])->name('change.language')->middleware(['auth','XSS']);
        Route::get('manage-language/{lang}', [LanguageController::class,'manageLanguage'])->name('manage.language')->middleware(['auth','XSS']);
        Route::post('store-language-data/{lang}', [LanguageController::class,'storeLanguageData'])->name('store.language.data')->middleware(['auth','XSS']);
        Route::get('create-language', [LanguageController::class,'createLanguage'])->name('create.language')->middleware(['auth','XSS']);
        Route::post('store-language', [LanguageController::class,'storeLanguage'])->name('store.language')->middleware(['auth','XSS']);
        Route::delete('lang/{lang}', [LanguageController::class,'destroyLang'])->name('lang.destroy')->middleware(['auth','XSS']);


    }
);





Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        Route::post('/test', [SystemController::class,'testEmail'])->name('test.email');
        Route::post('/test/send', [SystemController::class,'testEmailSend'])->name('test.email.send');

        // Route::post('/test', ['as' => 'test.email', 'uses' => 'SystemController@testEmail']);
        // Route::post('/test/send', ['as' => 'test.email.send', 'uses' => 'SystemController@testEmailSend']);
        
        Route::resource('settings', SystemController::class);

        Route::post('system-settings', [SystemController::class,'saveSystemSettings'])->name('system.settings');

        Route::post('general-settings', [SystemController::class,'saveGeneralSettings'])->name('general.settings');
        Route::post('payment-settings', [SystemController::class,'savePaymentSettings'])->name('payment.settings');
        Route::post('invoice-footer-settings', [SystemController::class,'saveInvoiceFooterSettings'])->name('invoice.footer.settings');

        Route::post('template-settings', [SystemController::class,'saveTemplateSettings'])->name('template.settings');

        Route::post('storage-settings', [SystemController::class,'storageSettingStore'])->name('storage.setting.store')->middleware(['auth','XSS']);
    }
);



//================================= Custom Landing Page ====================================//

Route::get('/landingpage', 'LandingPageSectionController@index')->name('custom_landing_page.index')->middleware(['auth', 'XSS']);
Route::get('/LandingPage/show/{id}', 'LandingPageSectionController@show');
Route::post('/LandingPage/setConetent', 'LandingPageSectionController@setConetent')->middleware(['auth', 'XSS']);
Route::get('/get_landing_page_section/{name}', function ($name) {
    $plans = \DB::table('plans')->get();
    return view('custom_landing_page.' . $name, compact('plans'));
});
Route::post('/LandingPage/removeSection/{id}', 'LandingPageSectionController@removeSection')->middleware(['auth', 'XSS']);
Route::post('/LandingPage/setOrder', 'LandingPageSectionController@setOrder')->middleware(['auth', 'XSS']);
Route::post('/LandingPage/copySection', 'LandingPageSectionController@copySection')->middleware(['auth', 'XSS']);



//================================= Plan Payment Gateways  ====================================//
Route::post('/plan-pay-with-paystack', [PaystackPaymentController::class,'planPayWithPaystack'])->name('plan.pay.with.paystack')->middleware(['auth','XSS']);
Route::get('/plan/paystack/{pay_id}/{plan_id}', [PaystackPaymentController::class,'getPaymentStatus'])->name('plan.paystack');

Route::post('/plan-pay-with-flaterwave', [FlutterwavePaymentController::class,'planPayWithFlutterwave'])->name('plan.pay.with.flaterwave')->middleware(['auth','XSS']);
Route::get('/plan/flaterwave/{txref}/{plan_id}', [FlutterwavePaymentController::class,'getPaymentStatus'])->name('plan.flaterwave');

Route::post('/plan-pay-with-razorpay', [RazorpayPaymentController::class,'planPayWithRazorpay'])->name('plan.pay.with.razorpay')->middleware(['auth','XSS']);
Route::get('/plan/razorpay/{txref}/{plan_id}', [RazorpayPaymentController::class,'getPaymentStatus'])->name('plan.razorpay');

Route::post('/plan-pay-with-paytm', [PaytmPaymentController::class,'planPayWithPaytm'])->name('plan.pay.with.paytm')->middleware(['auth','XSS']);
Route::post('/plan/paytm/{plan}/{coupon?}', [PaytmPaymentController::class,'getPaymentStatus'])->name('plan.paytm');

Route::post('/plan-pay-with-mercado', [MercadoPaymentController::class,'planPayWithMercado'])->name('plan.pay.with.mercado')->middleware(['auth','XSS']);
Route::get('/plan/mercado/{plan}', [MercadoPaymentController::class,'getPaymentStatus'])->name('plan.mercado');

Route::post('/plan-pay-with-mollie', [MolliePaymentController::class,'planPayWithMollie'])->name('plan.pay.with.mollie')->middleware(['auth','XSS']);
Route::get('/plan/mollie/{plan}', [MolliePaymentController::class,'getPaymentStatus'])->name('plan.mollie');

Route::post('/plan-pay-with-skril', [SkrillPaymentController::class,'planPayWithSkrill'])->name('plan.pay.with.skrill')->middleware(['auth','XSS']);
Route::get('/plan/skrill/{plan}', [SkrillPaymentController::class,'getPaymentStatus'])->name('plan.skrill');

Route::post('/plan-pay-with-coingate', [CoingatePaymentController::class,'planPayWithCoingate'])->name('plan.pay.with.coingate')->middleware(['auth','XSS']);
Route::get('/plan/coingate/{plan}/{coupons}', [CoingatePaymentController::class,'getPaymentStatus'])->name('plan.coingate');

Route::post('paymentwall', [PaymentWallPaymentController::class,'paymentwall'])->name('paymentwall');
Route::post('plan-pay-with-paymentwall/{plan}', [PaymentWallPaymentController::class,'planPayWithPaymentwall'])->name('plan.pay.with.paymentwall');
Route::any('/plan/{flag}', [PaymentWallPaymentController::class,'paymenterror'])->name('callback.error');

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('orders', [StripePaymentController::class,'index'])->name('order.index');
        Route::get('/stripe/{code}', [StripePaymentController::class,'stripe'])->name('stripe');
        Route::post('/stripe', [StripePaymentController::class,'stripePost'])->name('stripe.post');

    }
);

Route::post('plan-pay-with-paypal', [PaypalController::class,'planPayWithPaypal'])->name('plan.pay.with.paypal')->middleware(['auth', 'XSS']);


Route::get('{id}/plan-get-payment-status/{amount}/{coupon}', [PaypalController::class,'planGetPaymentStatus'])->name('plan.get.payment.status')->middleware(['auth', 'XSS']);



Route::get('plan_request', [PlanRequestController::class,'index'])->name('plan_request.index')->middleware(['auth', 'XSS',]);
Route::get('request_frequency/{id}', [PlanRequestController::class,'requestView'])->name('request.view')->middleware(['auth', 'XSS',]);
Route::get('request_send/{id}', [PlanRequestController::class,'userRequest'])->name('send.request')->middleware(['auth', 'XSS',]);
Route::get('request_response/{id}/{response}', [PlanRequestController::class,'acceptRequest'])->name('response.request')->middleware(['auth', 'XSS',]);
Route::get('request_cancel/{id}', [PlanRequestController::class,'cancelRequest'])->name('request.cancel')->middleware(['auth', 'XSS',]);



// -------------------------------------import export------------------------------

Route::get('export/customer', [CustomerController::class,'export'])->name('customer.export');
Route::get('import/customer/file', [CustomerController::class,'importFile'])->name('customer.file.import');
Route::post('import/customer', [CustomerController::class,'import'])->name('customers.import');


// Route::get('export/customer', 'CustomerController@export')->name('customer.export');
// Route::get('import/customer/file', 'CustomerController@importFile')->name('customer.file.import');
// Route::post('import/customer', 'CustomerController@import')->name('customers.import');

Route::get('export/vender', [VendorController::class,'export'])->name('vendors.export');
Route::get('import/vender/file', [VendorController::class,'importFile'])->name('vendors.file.import');
Route::get('import/vender', [VendorController::class,'import'])->name('vendors.import');

// Route::get('export/vender', 'VendorController@export')->name('vendors.export');
// Route::get('import/vender/file', 'VendorController@importFile')->name('vendors.file.import');
// Route::post('import/vender', 'VendorController@import')->name('vendors.import');

Route::get('export/Quotation', [QuotationController::class,'export'])->name('Quotation.export');
Route::get('export/ProductsReturn', [ProductsReturnController::class,'export'])->name('productsreturns.export');

// Route::get('export/Quotation', 'QuotationController@export')->name('Quotation.export');
// Route::get('export/ProductsReturn', 'ProductsReturnController@export')->name('productsreturns.export');

Route::get('export/Sale', [SaleController::class,'export'])->name('Sale.export');
Route::get('export/Purchase', [PurchaseController::class,'export'])->name('Purchase.export');
Route::get('export/Expense', [ExpenseController::class,'export'])->name('Expense.export');
Route::get('export/Product', [ProductController::class,'export'])->name('Product.export');

// Route::get('export/Sale', 'SaleController@export')->name('Sale.export');
// Route::get('export/Purchase', 'PurchaseController@export')->name('Purchase.export');
// Route::get('export/Expense', 'ExpenseController@export')->name('Expense.export');
// Route::get('export/Product', 'ProductController@export')->name('Product.export');

// recaptcha
Route::post('/recaptcha-settings', [SystemController::class,'recaptchaSettingStore'])->name('recaptcha.settings.store')->middleware(['auth', 'XSS',]);

// Route::post('/recaptcha-settings', ['as' => 'recaptcha.settings.store', 'uses' => 'SystemController@recaptchaSettingStore'])->middleware(['auth', 'XSS']);

// user reset password
Route::any('user-reset-password/{id}', [UserController::class,'userPassword'])->name('user.reset');
Route::post('export/Product', [UserController::class,'userPasswordReset'])->name('user.password.update');

// Route::any('user-reset-password/{id}', 'UserController@userPassword')->name('user.reset');
// Route::post('user-reset-password/{id}', 'UserController@userPasswordReset')->name('user.password.update');

// copy link for purchase/sale
Route::get('/purchase/invoice/{id}/', [ReportController::class,'purchaseLink'])->name('purchase.link.copy');
Route::get('/sale/invoice/{id}/', [ReportController::class,'saleLink'])->name('sale.link.copy');

// Route::get('/purchase/invoice/{id}/', 'ReportController@purchaseLink')->name('purchase.link.copy');
// Route::get('/sale/invoice/{id}/', 'ReportController@saleLink')->name('sale.link.copy');



// Email Templates
Route::get('email_template_lang/{id}/{lang?}', [EmailTemplateController::class,'manageEmailLang'])->name('manage.email.language')->middleware(['auth','XSS']);

Route::post('email_template_store/{pid}', [EmailTemplateController::class,'storeEmailLang'])->name('store.email.language')->middleware(['auth']);

Route::post('email_template_status/{id}', [EmailTemplateController::class,'updateStatus'])->name('status.email.language')->middleware(['auth']);

Route::resource('email_template', EmailTemplateController::class)->middleware(
    [
        'auth',
        // 'XSS',
        // 'revalidate',    
    ]
);

Route::resource('email_template_lang', EmailTemplateLangController::class)->middleware( ['auth','XSS','revalidate',]);
// Route::resource('email_template_lang', 'EmailTemplateLangController')->middleware(
//     [
//         'auth',
//         'XSS',
//         'revalidate',
//     ]
// );