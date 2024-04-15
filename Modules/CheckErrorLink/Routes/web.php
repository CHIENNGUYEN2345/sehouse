<?php

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

Route::get('check_error_link', function() {
    die('fe');
    $list_domain = Modules\CheckErrorLink\Models\DomainCheck::where('status', 1)->pluck('id')->toArray();
    $linkchecks = Modules\CheckErrorLink\Models\LinkCheck::whereIn('domain_id', $list_domain)->where('status', 1)->get();
    \Modules\CheckErrorLink\Http\Helpers\CommonHelper::check_link_run($linkchecks);
    die('ok');
});

Route::group(['prefix' => 'admin', 'middleware' => ['guest:admin', 'get_permissions']], function () {
    Route::group(['prefix' => 'check_error_link'], function () {
        Route::get('', 'Admin\CheckErrorLinkController@getIndex')->middleware('permission:super_admin')->name('check_link');
        Route::get('run', 'Admin\DomainController@run')->name('check_error_link.run');
        Route::get('publish', 'Admin\CheckErrorLinkController@getPublish')->name('check_link.publish');
        Route::match(array('GET', 'POST'), 'add/{id?}', 'Admin\CheckErrorLinkController@add')->middleware('permission:super_admin');
        Route::get('delete/{id}', 'Admin\CheckErrorLinkController@delete')->middleware('permission:super_admin');
        Route::post('multi-delete', 'Admin\CheckErrorLinkController@multiDelete')->middleware('permission:super_admin');
        Route::get('all-delete', 'Admin\CheckErrorLinkController@allDelete')->middleware('permission:super_admin');
        Route::get('{id}', 'Admin\CheckErrorLinkController@update');
        Route::post('{id}', 'Admin\CheckErrorLinkController@update');
    });
    Route::group(['prefix' => 'domain'], function () {
        Route::get('', 'Admin\DomainController@getIndex')->middleware('permission:super_admin')->name('check_link');
        Route::get('list-links/{id}', 'Admin\CheckErrorLinkController@getIndex2')->name('list-link');
        Route::get('publish', 'Admin\DomainController@getPublish')->name('check_link.publish');
        Route::match(array('GET', 'POST'), 'add', 'Admin\DomainController@add')->middleware('permission:super_admin');
        Route::get('delete/{id}', 'Admin\DomainController@delete')->middleware('permission:super_admin');
        Route::post('multi-delete', 'Admin\DomainController@multiDelete')->middleware('permission:super_admin');
        Route::get('all-delete', 'Admin\DomainController@allDelete')->middleware('permission:super_admin');
        Route::get('run', 'Admin\DomainController@run');
        Route::get('{id}', 'Admin\DomainController@update');
        Route::post('{id}', 'Admin\DomainController@update');
    });
    Route::group(['prefix' => 'error_link_logs'], function () {
        Route::get('', 'Admin\ErrorLinkLogsController@getIndex')->middleware('permission:super_admin')->name('error_link_logs');
        Route::get('publish', 'Admin\ErrorLinkLogsController@getPublish')->name('check_link.publish');
        Route::match(array('GET', 'POST'), 'add', 'Admin\ErrorLinkLogsController@add')->middleware('permission:super_admin');
        Route::get('delete/{id}', 'Admin\ErrorLinkLogsController@delete')->middleware('permission:super_admin');
        Route::post('multi-delete', 'Admin\ErrorLinkLogsController@multiDelete')->middleware('permission:super_admin');
        Route::get('all-delete', 'Admin\ErrorLinkLogsController@allDelete')->middleware('permission:super_admin');
        Route::get('{id}', 'Admin\ErrorLinkLogsController@update');
        Route::post('{id}', 'Admin\ErrorLinkLogsController@update');
    });
});

