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

Route::group(['prefix' => 'admin', 'middleware' => ['guest:admin', 'get_permissions']], function () {
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('', 'Admin\DashboardController@dashboardSoftware');
//        Route::get('', 'Admin\DashboardController@dashboardCompany');
    });
});
