<?php
use App\Http\Controllers\CommonController;
use App\Http\Controllers\CrudGeneratorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\ReturnController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileManagerController;


Route::controller(ReturnController::class)->group(function () {
        // Route::get('categories', 'index');
         Route::post('return_upload', 'upload1'); 
    });

Route::get('/product_image/{id}/{name}', [App\Http\Controllers\ImageResizeController::class, 'product_image_resize']);
Route::get('/category_image/{name}', [App\Http\Controllers\ImageResizeController::class, 'category_image_resize']);
Route::get('/collection_image/{name}', [App\Http\Controllers\ImageResizeController::class, 'collection_image_resize']);
Route::get('/slider_image/{name}', [App\Http\Controllers\ImageResizeController::class, 'slider_image_resize']);
Route::get('/banner_image/{name}', [App\Http\Controllers\ImageResizeController::class, 'banner_image_resize']);
Route::get('/home', function () { /*home is redirect route defined in fortservice provider after logi auth  from here divert route based on role,dont use separate admin rout files now  */
    if (auth()->user()->hasRole(['Admin'])) {
        return redirect(route('admin.dashboard'));
    } else {
        return redirect(route('user.dashboard'));
    }

});
/** ==============Email verification customisation =========== */
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

//resend mail
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
/**===================================End custom verification============================== */

 Route::controller(CommonController::class)->group(function () {
    Route::post('/fieldExist', 'field_exist');
    Route::post('/getDependentSelectData', 'getDependentSelectData');
    Route::post('/getCities', 'getCities');
    Route::post('/getDependentSelectDataMultipleVal',  'getDependentSelectDataMultipleVal');
    Route::match(['get', 'post'], '/search_table', 'search_table');
    Route::post('/search_products',  'search_products');
    Route::post('/fetchRowFromTable','fetchRowFromTable');
    Route::post('/deleteRecordFromTable', 'deleteRecordFromTable');

    Route::post('delete_file_from_table',  'deleteFileFromTable')->name('deleteTableFile');

    Route::post('deleteInJsonColumnData', 'deleteInJsonColumnData')->name('deleteInJsonColumnData');
    Route::post('assignUser', 'assignUser')->name('assignUser');
    Route::post('delete_file_self','deleteFileFromSelf')->name('deleteFileSelf');
    Route::post('table_field_update',  'table_field_update')->name('table_filed_update');
    Route::post('singleFieldUpdateFromTable',  'singleFieldUpdateFromTable')->name('singleFieldUpdateFromTable');
    Route::post('bulk_delete',  'bulkDelete')->name('bulkDelete');
    Route::post('getTableColumn', 'getColumnsFromTable');
    Route::post('getTableColumnCheckboxForm',  'getColumnsFromTableCheckbox');
    Route::post('getValidationHtml', 'getValidationHtml');
    Route::post('getRepeatableHtml',  'getRepeatableHtml');
    Route::post('getCreateInputOptionHtml', 'getCreateInputOptionHtml');
    Route::post('getSideColumnInputOptionHtml', 'getSideColumnInputOptionHtml');
    Route::post('getToggableGroupHtml',  'getToggableGroupHtml');

 });
 
//  Route::domain('vendor.colourindogo.test')->group(function () {
//             Route::group(['middleware' => ['guest']], function () {
//                 /**
//                  * Register Routes
//                  */

//                 Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'show'])->name('register.show');
//                 Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.perform');
            
//                 /**
//                  * Login Routes
//                  */
//                 Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'show'])->name('login');
//                 Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.perform');
//                 Route::post('/verify_otp', [App\Http\Controllers\Auth\LoginController::class, 'verify_otp']);
//                 Route::post('/resend_otp', [App\Http\Controllers\Auth\LoginController::class, 'resend_otp']);
            
//                 Route::get('forget-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'ForgetPassword'])->name('ForgetPasswordGet');
//                 Route::post('forget-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'ForgetPasswordStore'])->name('ForgetPasswordPost');
//                 Route::get('reset-password/{token}', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'ResetPassword'])->name('ResetPasswordGet');
//                 Route::post('reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'ResetPasswordStore'])->name('ResetPasswordPost');
//                 Route::get('verify_email/{_vX00}/{_tX00}', [App\Http\Controllers\Auth\RegisterController::class, 'verify_email'])->name('email_verify');
            
            
            
//             });
// });


Route::domain('admin.colourindogo.test')->group(function () {
        Route::middleware(['admin'])->group(function () {
          Route::controller(FrontendController::class)->group(function () {
            Route::get('/',  'index');
            Route::get('/clear_cache',  'clear_cache')->name('clear_cache');
            Route::get('/cache', 'cache')->name('cache');
            Route::get('/redirect','redirect');

          });
          Route::controller(DashboardController::class)->group(function () {
            Route::get('/dashboard', 'index')->name('admin.dashboard');
            Route::get('/dashboard_data',  'dashboard_data')->name('admin.dashboard_data');
           });
          
          Route::controller(CrudGeneratorController::class)->group(function () {
             Route::get('/crud', 'index')->name('admin.crud');
             Route::match(['get', 'post'], '/generateModule','generateModule')->name('admin.generateModule');
             Route::match(['get', 'post'], '/generateTable', 'generateTable')->name('admin.generateTable');
             Route::match(['get', 'post'], '/addTableRelationship', 'addTableRelationship')->name('admin.addTableRelationship');

           });
           // RoleController
              registerCrudRoutes('roles', \App\Controllers\RoleController::class, [
                    'export' => 'exportRole',
                ]);

                registerCrudRoutes('permissions', \App\Controllers\PermissionController::class, [
                    'custom' => [
                        ['method' => 'post', 'uri' => 'permission/load_form', 'action' => 'loadAjaxForm', 'name' => 'permission.loadAjaxForm']
                    ]
                ]);

                registerCrudRoutes('users', \App\Controllers\UserController::class, [
                    'export' => 'exportUser',
                    'custom' => [
                        ['method' => 'post', 'uri' => 'user/load_form', 'action' => 'loadAjaxForm', 'name' => 'user.loadAjaxForm'],
                        ['method' => 'get', 'uri' => 'users/{role}', 'action' => 'index1']
                    ]
                ]);

                registerCrudRoutes('products', \App\Controllers\ProductController::class, [
                    'export' => 'exportProduct',
                    'custom' => [
                        ['method' => 'get', 'uri' => 'product/excel_template', 'action' => 'exportProductExcelTempalte', 'name' => 'product.export_template'],
                        ['method' => 'post', 'uri' => 'products/import', 'action' => 'importProduct', 'name' => 'products.import'],
                        ['method' => 'post', 'uri' => 'products-import-discounts', 'action' => 'importDiscounts', 'name' => 'products.import-discounts'],
                        ['method' => 'get', 'uri' => 'products-export-basic', 'action' => 'exportProductBasic', 'name' => 'products.export-basic'],
                        ['method' => 'post', 'uri' => 'generateAccordian', 'action' => 'generateAccordian'],
                        ['method' => 'post', 'uri' => 'delete_prod_image', 'action' => 'deleteImage', 'name' => 'delete_product_image']
                    ]
                ]);

                registerCrudRoutes('attributes', \App\Controllers\AttributeController::class, [
                    'export' => 'exportAttribute'
                ]);

                registerCrudRoutes('categories', \App\Controllers\CategoryController::class, [
                    'export' => 'exportCategory',
                    'custom' => [
                        ['method' => 'post', 'uri' => 'get_category_based_product_features', 'action' => 'getCategoryProductFeature']
                    ]
                ]);

                registerCrudRoutes('brands', \App\Controllers\BrandController::class, [
                    'export' => 'exportBrand'
                ]);

                registerCrudRoutes('attribute_famlies', \App\Controllers\AttributeFamilyController::class, [
                    'export' => 'exportAttributeFamily',
                    'custom' => [
                        ['method' => 'post', 'uri' => 'getAttributesHtml', 'action' => 'getAttributesHtml']
                    ]
                ]);

                registerCrudRoutes('customer_groups', \App\Controllers\CustomerGroupController::class, [
                    'export' => 'exportCustomerGroup'
                ]);

                registerCrudRoutes('slider_banners', \App\Controllers\SliderBannerController::class, [
                    'export' => 'exportSliderBanner'
                ]);

                registerCrudRoutes('banners', \App\Controllers\BannerController::class, [
                    'export' => 'exportBanner'
                ]);

                registerCrudRoutes('content_sections', \App\Controllers\ContentSectionController::class, [
                    'export' => 'exportContentSection',
                    'custom' => [
                        ['method' => 'post', 'uri' => 'update_order_sequence', 'action' => 'updateSequence', 'name' => 'updateSequence']
                    ]
                ]);

                Route::controller(\App\Controllers\OrderController::class)->group(function () {
                    Route::get('driver_orders/{driver_id}', 'driver_orders')->name('driver.orders');
                    Route::resource('orders', \App\Controllers\OrderController::class);
                    Route::post('orders/view', 'view')->name('orders.view');
                    Route::get('order_item/{id}', 'show_order_related_to_item_id')->name('orders.view_item_id');
                    Route::get("export_orders/{type}", "exportOrders")->name("orders.export");
                });

                Route::resources([
                    'return_items' => \App\Controllers\ReturnItemsController::class,
                    'refunds' => \App\Controllers\RefundController::class,
                    'payments' => \App\Controllers\PaymentController::class,
                ]);

                registerCrudRoutes('coupons', \App\Controllers\CouponController::class, [
                    'export' => 'exportCoupon'
                ]);

                registerCrudRoutes('settings', \App\Controllers\SettingController::class);

                registerCrudRoutes('new_coupons', \App\Controllers\NewCouponController::class, [
                    'export' => 'exportNewCoupon'
                ]);

                registerCrudRoutes('collections', \App\Controllers\CollectionController::class, [
                    'export' => 'exportCollection'
                ]);

                registerCrudRoutes('videos', \App\Controllers\VideoController::class, [
                    'export' => 'exportVideo'
                ]);

                registerCrudRoutes('product_addons', \App\Controllers\ProductAddonController::class, [
                    'export' => 'exportProductAddons'
                ]);

                registerCrudRoutes('website_banners', \App\Controllers\WebsiteBannerController::class, [
                    'export' => 'exportWebsiteBanner'
                ]);

                registerCrudRoutes('website_sliders', \App\Controllers\WebsiteSliderController::class, [
                    'export' => 'exportWebsiteSlider'
                ]);

                registerCrudRoutes('website_content_sections', \App\Controllers\WebsiteContentSectionController::class, [
                    'export' => 'exportWebsiteContentSection'
                ]);

                registerCrudRoutes('sliders', \App\Controllers\SliderController::class, [
                    'export' => 'exportSlider'
                ]);

                Route::controller(\App\Controllers\FileManagerController::class)->group(function () {
                    Route::get('/file-manager', 'index');
                    Route::post('/file-manager/upload', 'upload1');
                    Route::post('/file-manager/create-folder', 'createFolder');
                    Route::post('/file-manager/delete', 'delete');
                });

          

           

            
      
      
      
      
        });
         Route::middleware('guest')->group(function () {
            Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'show'])->name('login');
            Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('admin.login.perform');
         });
       


});
