<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1', 'namespace' => 'Api'], function () {

    // login register
    Route::group(['prefix' => 'admin'], function () {
        Route::post('login', 'Admin\AdminController@login');
        Route::post('register', 'Admin\AdminController@register');
        Route::post('forgot-password', 'Admin\AdminController@forgotPassword');
        Route::post('forgot-password', 'Admin\AdminController@forgotPasswordCode');
        Route::post('restore-password', 'Admin\AdminController@restorePassword');
        Route::post('restore-password-by-code', 'Admin\AdminController@restorePasswordByCode');

        //  Login social
        Route::post('/login/{param}', 'Admin\AdminController@loginSocial');
    });

    //  profile
    Route::group(['prefix' => 'admin'], function () {
        Route::post('profile', 'Admin\AdminController@postProfile');
        Route::get('profile', 'Admin\AdminController@getProfile');
        Route::get('profile/{id}', 'Admin\AdminController@getProfileAdmin');
    });

    //  Admin
    Route::group(['prefix' => 'admins', 'middleware' => \App\Http\Middleware\CheckApiTokenAdmin::class], function () {
        Route::get('', 'Admin\AdminController@index')->middleware('api_permission:admin_view');
        Route::post('', 'Admin\AdminController@store')->middleware('api_permission:admin_add');
        Route::get('{id}', 'Admin\AdminController@show')->middleware('api_permission:admin_view');
        Route::post('{id}', 'Admin\AdminController@update')->middleware('api_permission:admin_edit');
        Route::delete('{id}', 'Admin\AdminController@delete')->middleware('api_permission:admin_delete');
    });

    //  Settings
    Route::get('setting', 'Admin\SettingController@index');
});

