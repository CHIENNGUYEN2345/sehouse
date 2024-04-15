<?php
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
    Route::group(['prefix' => 'plugin_repositories'], function () {
        Route::get('', 'Admin\PluginRepositoryController@index');
        Route::get('detail', 'Admin\PluginRepositoryController@detail');
        Route::get('{id}', 'Admin\PluginRepositoryController@show');
    });
});