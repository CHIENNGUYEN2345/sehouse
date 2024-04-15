<?php
Route::group(['prefix' => 'admin', 'middleware' => ['guest:admin', 'get_permissions','locale']], function () {
    Route::group(['prefix' => 'theme'], function () {
        Route::get('', 'Admin\ThemeController@getIndex')->name('theme')->middleware('permission:theme');
        Route::get('active', 'Admin\ThemeController@active')->name('theme.active')->middleware('permission:theme');
    });

    Route::group(['prefix' => 'plugin'], function () {
        Route::get('', 'Admin\PluginController@getIndex')->name('plugin')->middleware('permission:plugin');
        Route::get('active', 'Admin\PluginController@active')->name('plugin.active')->middleware('permission:plugin');

    });




    // Route::post('{module}/import-excel', '\App\Http\Controllers\Admin\ImportController@importExcel')->middleware('permission:super_admin');
});
