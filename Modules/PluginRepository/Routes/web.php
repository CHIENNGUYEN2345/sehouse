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
    Route::group(['prefix' => 'plugin_repository'], function () {
        Route::get('', 'Admin\PluginRepositoryController@getIndex')->name('plugin_repository')->middleware('permission:setting');
        Route::get('publish', 'Admin\PluginRepositoryController@getPublish')->name('plugin_repository.publish')->middleware('permission:setting');
        Route::match(array('GET', 'POST'), 'add', 'Admin\PluginRepositoryController@add')->middleware('permission:setting');
        Route::get('delete/{id}', 'Admin\PluginRepositoryController@delete')->middleware('permission:setting');
        Route::post('multi-delete', 'Admin\PluginRepositoryController@multiDelete')->middleware('permission:setting');
        Route::get('search-for-select2', 'Admin\PluginRepositoryController@searchForSelect2')->name('plugin_repository.search_for_select2')->middleware('permission:setting');
        Route::get('{id}', 'Admin\PluginRepositoryController@update')->middleware('permission:setting');
        Route::post('{id}', 'Admin\PluginRepositoryController@update')->middleware('permission:setting');
    });
});
