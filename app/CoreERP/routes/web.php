<?php
Route::group(['prefix' => 'admin', 'middleware' => ['guest:admin', 'get_permissions']], function () {

    //  Thống kê
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('ajax/load-khoi', '\App\CoreERP\Controllers\Admin\DashboardController@ajaxLoadKhoi');
    });

    //  Hướng dẫn sử dụng
    Route::group(['prefix' => 'guide'], function () {
        Route::get('', '\App\CoreERP\Controllers\Admin\GuideController@getIndex')->name('guide');
        Route::match(array('GET', 'POST'), 'add', '\App\CoreERP\Controllers\Admin\GuideController@add')->middleware('permission:guide');
        Route::get('delete/{id}', '\App\CoreERP\Controllers\Admin\GuideController@delete')->middleware('permission:super_admin');
        Route::post('multi-delete', '\App\CoreERP\Controllers\Admin\GuideController@multiDelete')->middleware('permission:super_admin');
        Route::get('search-for-select2', '\App\CoreERP\Controllers\Admin\GuideController@searchForSelect2')->name('guide.search_for_select2');

        Route::get('publish', '\App\CoreERP\Controllers\Admin\GuideController@getPublish')->name('guide.publish');

        Route::get('edit/{id}', '\App\CoreERP\Controllers\Admin\GuideController@update')->middleware('permission:guide');
        Route::post('edit/{id}', '\App\CoreERP\Controllers\Admin\GuideController@update')->middleware('permission:guide');
    });

    //  Tag
    Route::group(['prefix' => 'tag'], function () {
        Route::get('', '\App\CoreERP\Controllers\Admin\TagController@getIndex')->name('tag');
        Route::match(array('GET', 'POST'), 'add', '\App\CoreERP\Controllers\Admin\TagController@add')->middleware('permission:setting');
        Route::get('delete/{id}', '\App\CoreERP\Controllers\Admin\TagController@delete')->middleware('permission:super_admin');
        Route::post('multi-delete', '\App\CoreERP\Controllers\Admin\TagController@multiDelete')->middleware('permission:super_admin');
        Route::get('search-for-select2', '\App\CoreERP\Controllers\Admin\TagController@searchForSelect2')->name('tag.search_for_select2');

        Route::get('publish', '\App\CoreERP\Controllers\Admin\TagController@getPublish')->name('tag.publish');

        Route::get('edit/{id}', '\App\CoreERP\Controllers\Admin\TagController@update')->middleware('permission:setting');
        Route::post('edit/{id}', '\App\CoreERP\Controllers\Admin\TagController@update')->middleware('permission:setting');
    });
});