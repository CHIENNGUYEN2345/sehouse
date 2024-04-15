<?php


//  Auth
//Route::post('save-file','BaseController@saveFile')->name('save-file');
Route::group(['prefix' => 'admin', 'middleware' => 'no_auth:admin'], function () {
    Route::get('login', 'Admin\AuthController@login')->name('admin.login');
    Route::post('authenticate', 'Admin\AuthController@authenticate');

    Route::match(['GET', 'POST'], 'register', 'Admin\AuthController@register');
    Route::get('resent-mail-active', 'Admin\AuthController@resentMailActive');

    //  Đăng nhập bằng FB - GG
    Route::get('/login/{param}/redirect/', 'Admin\AuthController@redirect');
    Route::get('/login/{param}/callback/', 'Admin\AuthController@callback');
});

Route::match(array('GET', 'POST'), 'forgot-password', 'Admin\AuthController@forgotPassword');
Route::match(array('GET', 'POST'), 'email-forgot-password', 'Admin\AuthController@getEmailForgotPassword');
Route::match(array('GET', 'POST'), 'email-change', 'Admin\AuthController@changeEmail');
Route::get('confirm-email-change', 'Admin\AuthController@confirmChangeEmail');
Route::group(['prefix' => '', 'middleware' => 'no_auth:admin'], function () {
    Route::get('active-account', 'Admin\AuthController@activeAccount');
});
Route::group(['prefix' => 'admin', 'middleware' => ['guest:admin']], function () {
    Route::get('logout', 'Admin\AuthController@logout');
});

//  Ajax
Route::group(['prefix' => 'ajax'], function () {
    Route::get('log-ip', 'Admin\DashboardController@logIp');
});

//  Filemanager
Route::group(['prefix' => '', 'middleware' => ['guest:admin']], function () {
    Route::get('admin/browser', 'CkFinderController@browser')->name('browser');
    Route::get('admin/filemanager/show', 'CkFinderController@browser');
});


//  Cache
Route::group(['prefix' => 'cache'], function () {
    Route::group(['prefix' => 'clear'], function () {
        Route::get('all', 'CacheController@clearAll');
        Route::get('view', 'CacheController@clearView');
        Route::get('setting', 'CacheController@clearSetting');
        Route::get('route', 'CacheController@clearRoute');
        Route::get('error', 'CacheController@clearError')->middleware('permission:super_admin');
    });
});
Route::get('cache-flush', 'Frontend\HomeController@cache_flush');


Route::group(['prefix' => 'admin', 'middleware' => ['guest:admin', 'get_permissions', 'locale']], function () {


    Route::get('/', function () {
        return Redirect::to(Request()->route()->getPrefix() . '/dashboard');
    });
    Route::group(['prefix' => 'admin_logs'], function () {
        Route::get('', 'Admin\AdminLogController@getIndex')->middleware('permission:super_admin');
        Route::get('delete/{id}', 'Admin\AdminLogController@delete')->middleware('permission:super_admin');
        Route::get('all-delete', 'Admin\AdminLogController@allDelete')->middleware('permission:super_admin');
        Route::post('multi-delete', 'Admin\AdminLogController@multiDelete')->middleware('permission:super_admin');
        Route::get('delete-all', 'Admin\AdminLogController@allDelete');
    });
    Route::group(['prefix' => 'user'], function () {
        Route::get('', 'Admin\UserController@getIndex')->middleware('permission:user_view');
        Route::get('delete/{id}', 'Admin\UserController@delete');
        Route::post('multi-delete', 'Admin\UserController@multiDelete');
        Route::match(array('GET', 'POST'), 'add', 'Admin\UserController@add')->middleware('permission:user_add');
        Route::match(array('GET', 'POST'), 'edit/{id}', 'Admin\UserController@update')->middleware('permission:user_edit');
    });
    Route::get('dashboard', 'Admin\DashboardController@getIndex');
    Route::get('seed/{action}', 'Admin\SeedController@getIndex');
    Route::get('theme/change', 'Admin\DashboardController@changeTheme');


    //  Admin
    Route::group(['prefix' => 'profile'], function () {
        Route::match(array('GET', 'POST'), '', 'Admin\AdminController@profile')->name('admin.profile');
        Route::match(array('GET', 'POST'), 'change-password', 'Admin\AdminController@changePassword');
        Route::match(array('GET', 'POST'), 'reset-password/{id}', 'Admin\AdminController@resetPassword')->middleware('permission:super_admin');
        Route::match(array('GET', 'POST'), '{id}', 'Admin\AdminController@profileAdmin')->name('admin.profile_admin');
        Route::get('{id}/login', 'Admin\AdminController@loginToAccount')->middleware('permission:super_admin');
    });

    Route::group(['prefix' => 'province'], function () {
        Route::get('', 'Admin\ProvinceController@getIndex')->name('province')->middleware('permission:location');
        Route::get('publish', 'Admin\ProvinceController@getPublish')->name('province.publish')->middleware('permission:location');
        Route::match(array('GET', 'POST'), 'add', 'Admin\ProvinceController@add')->middleware('permission:location');
        Route::get('delete/{id}', 'Admin\ProvinceController@delete')->middleware('permission:location');
        Route::post('multi-delete', 'Admin\ProvinceController@multiDelete')->middleware('permission:location');
        Route::get('search-for-select2', 'Admin\ProvinceController@searchForSelect2')->name('province.search_for_select2');
        Route::get('edit/{id}', 'Admin\ProvinceController@update')->middleware('permission:location');
        Route::post('edit/{id}', 'Admin\ProvinceController@update')->middleware('permission:location');
    });


    Route::group(['prefix' => 'district'], function () {
        Route::get('', 'Admin\DistrictController@getIndex')->name('district')->middleware('permission:location');
        Route::get('publish', 'Admin\DistrictController@getPublish')->name('district.publish')->middleware('permission:location');
        Route::match(array('GET', 'POST'), 'add', 'Admin\DistrictController@add')->middleware('permission:location');
        Route::get('delete/{id}', 'Admin\DistrictController@delete')->middleware('permission:location');
        Route::post('multi-delete', 'Admin\DistrictController@multiDelete')->middleware('permission:location');
        Route::get('search-for-select2', 'Admin\DistrictController@searchForSelect2')->name('district.search_for_select2');
        Route::get('edit/{id}', 'Admin\DistrictController@update')->middleware('permission:location');
        Route::post('edit/{id}', 'Admin\DistrictController@update')->middleware('permission:location');
    });

    Route::group(['prefix' => 'ward'], function () {
        Route::get('', 'Admin\WardController@getIndex')->name('ward')->middleware('permission:location');
        Route::get('publish', 'Admin\WardController@getPublish')->name('ward.publish')->middleware('permission:location');
        Route::match(array('GET', 'POST'), 'add', 'Admin\WardController@add')->middleware('permission:location');
        Route::get('delete/{id}', 'Admin\WardController@delete')->middleware('permission:location');
        Route::post('multi-delete', 'Admin\WardController@multiDelete')->middleware('permission:location');
        Route::get('search-for-select2', 'Admin\WardController@searchForSelect2')->name('ward.search_for_select2');
        Route::get('edit/{id}', 'Admin\WardController@update')->middleware('permission:location');
        Route::post('edit/{id}', 'Admin\WardController@update')->middleware('permission:location');
    });

    //  Setting
    Route::group(['prefix' => 'setting'], function () {
        Route::post('ajax-update', 'Admin\SettingController@ajaxUpdate')->middleware('permission:setting');
    });
    Route::match(array('GET', 'POST'), 'setting', 'Admin\SettingController@getIndex')->middleware('permission:setting');
    Route::match(array('GET', 'POST'), 'setting/mail-header', 'Admin\SettingController@configMailHeader')->name('admin.mail_header')->middleware('permission:setting');
    Route::match(array('GET', 'POST'), 'setting/mail-footer', 'Admin\SettingController@configMailFooter')->name('admin.mail_footer')->middleware('permission:setting');
    Route::match(array('GET', 'POST'), 'backup', 'Admin\BackupController@getIndex');
    Route::group(['prefix' => 'backup'], function () {
        Route::get('backup_database', 'Admin\BackupController@backupDatabase');
        Route::get('backup_database/download/{file_name}', 'Admin\BackupController@downloadDB')->name('downloadDB');
        Route::get('backup_database/delete/{file_name}', 'Admin\BackupController@deleteDB')->name('deleteDB');
    });

    //  Cache
    Route::group(['prefix' => 'cache', 'middleware' => ['permission:setting']], function () {
        Route::get('', 'Admin\CacheController@getIndex')->name('cache');
    });

    //  Error
    Route::group(['prefix' => 'error', 'middleware' => ['permission:super_admin']], function () {
        Route::get('', 'Admin\ErrorController@getIndex')->name('error');
        Route::get('delete/{id}', 'Admin\ErrorController@delete');
        Route::post('multi-delete', 'Admin\ErrorController@multiDelete');
    });

    //  queue
    Route::group(['prefix' => 'queue', 'middleware' => ['permission:super_admin']], function () {
        Route::get('', 'Admin\QueueController@getIndex')->name('queue');
        Route::get('delete/{id}', 'Admin\QueueController@delete');
        Route::post('multi-delete', 'Admin\QueueController@multiDelete');
    });

    //  Import
    Route::group(['prefix' => 'import'], function () {
        Route::get('', 'Admin\ImportController@getIndex');
        Route::get('delete/{id}', 'Admin\ImportController@delete')->middleware('permission:super_admin');
        Route::post('multi-delete', 'Admin\ImportController@multiDelete')->middleware('permission:super_admin');
        Route::get('download-excel-demo', 'Admin\ImportController@downloadExcelDemo');
        Route::match(array('GET', 'POST'), 'add', 'Admin\ImportController@add');
        Route::match(array('GET', 'POST'), '{id}', 'Admin\ImportController@update')->middleware('permission:super_admin');
    });


    //  Tool
    Route::get('tooltip-info', 'Admin\DashboardController@tooltipInfo');
    Route::post('ajax-up-file', 'Admin\DashboardController@ajax_up_file')->name('ajax-up-file');
    Route::post('ajax-up-file2', 'Admin\DashboardController@ajax_up_file2')->name('ajax-up-file2');

    //  Admin
    Route::group(['prefix' => 'admin'], function () {
        Route::get('login-other-account', 'Admin\AdminController@loginOtherAccount')->middleware('permission:super_admin');

        Route::get('', 'Admin\AdminController@getIndex')->name('admin')->middleware('permission:admin_view');
        Route::get('publish', 'Admin\AdminController@getPublish')->name('admin.admin_publish')->middleware('permission:admin_edit');
        Route::match(array('GET', 'POST'), 'add', 'Admin\AdminController@add')->middleware('permission:admin_add');
        Route::get('delete/{id}', 'Admin\AdminController@delete')->middleware('permission:admin_delete');
        Route::post('multi-delete', 'Admin\AdminController@multiDelete')->middleware('permission:admin_delete');

        Route::get('search-for-select2', 'Admin\AdminController@searchForSelect2')->name('admin.search_for_select2')->middleware('permission:admin_view');
        Route::get('search-for-select2-all', 'Admin\AdminController@searchForSelect2All')->middleware('permission:admin_view');

        Route::post('import-excel', 'Admin\AdminController@importExcel')->middleware('permission:admin_add');

        Route::get( 'edit/{id}', 'Admin\AdminController@update')->middleware('permission:admin_view');
        Route::post( 'edit/{id}', 'Admin\AdminController@update')->middleware('permission:admin_edit');
    });

    //  Role
    Route::group(['prefix' => 'role'], function () {
        Route::get('', 'Admin\RoleController@getIndex')->name('role')->middleware('permission:role_view');
        Route::match(array('GET', 'POST'), 'add', 'Admin\RoleController@add')->middleware('permission:role_add');
        Route::get('delete/{id}', 'Admin\RoleController@delete')->middleware('permission:role_delete');
        Route::post('multi-delete', 'Admin\RoleController@multiDelete')->middleware('permission:role_delete');
        Route::get('search-for-select2', 'Admin\RoleController@searchForSelect2')->name('role.search_for_select2')->middleware('permission:role_view');
        Route::match(array('GET', 'POST'), 'edit/{id}', 'Admin\RoleController@update')->middleware('permission:role_edit');
    });

    //  change_data_history
    Route::group(['prefix' => 'change_data_history'], function () {
        Route::get('', 'Admin\ChangeDataHistoryController@getIndex')->name('change_data_history')->middleware('permission:super_admin');
        Route::match(array('GET', 'POST'), 'add', 'Admin\ChangeDataHistoryController@add')->middleware('permission:super_admin');
        Route::get('delete/{id}', 'Admin\ChangeDataHistoryController@delete')->middleware('permission:super_admin');
        Route::post('multi-delete', 'Admin\ChangeDataHistoryController@multiDelete')->middleware('permission:super_admin');
    });
});

Route::group(['prefix' => 'admin'], function () {
    //  Location
    Route::group(['prefix' => 'location'], function () {
        Route::get('{table}/get-data', 'Admin\DashboardController@getDataLocation');
    });
});

require base_path('app/CoreERP/routes/web.php');
require base_path('app/'.env('CRM_CORE').'/routes/web.php');
require base_path('app/Custom/routes/web.php');
//require base_path('Modules/EduMarketing/Routes/web.php');
