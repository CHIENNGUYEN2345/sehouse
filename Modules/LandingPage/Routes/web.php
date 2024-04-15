<?php

Route::group(['prefix' => 'admin', 'middleware' => ['guest:admin', 'get_permissions']], function () {
    Route::group(['prefix' => 'landingpage'], function () {
        Route::get('', 'Admin\LandingpageController@getIndex')->name('landingpage')->middleware('permission:landingpage_view');
        Route::get('publish', 'Admin\LandingpageController@getPublish')->name('landingpage.publish')->middleware('permission:landingpage_publish');
        Route::match(array('GET', 'POST'), 'add', 'Admin\LandingpageController@add')->middleware('permission:landingpage_add');
        Route::get('delete/{id}', 'Admin\LandingpageController@delete')->middleware('permission:landingpage_delete');
        Route::post('multi-delete', 'Admin\LandingpageController@multiDelete')->middleware('permission:landingpage_delete');
        Route::get('search-for-select2', 'Admin\LandingpageController@searchForSelect2')->name('landingpage.search_for_select2')->middleware('permission:landingpage_view');
        Route::get('{id}/duplicate', 'Admin\LandingpageController@duplicate')->middleware('permission:landingpage_add');

        Route::get('{id}/ban-giao', 'Admin\LandingpageController@banGiao');

        Route::get('update-to-bill', 'Admin\LandingpageController@updateToBill')->middleware('permission:bill_add');

        Route::get('get-gg-form-fields', 'Admin\LandingpageController@getGGFormFields');

        Route::get('update-link-ldp', function () {
            $landingpages = \Modules\LandingPage\Models\Landingpage::where('ladi_link', 'like', '%ladi.demopage.me%')->get();
            foreach ($landingpages as $ldp) {
                $ldp->ladi_link = str_replace('http://ladi.demopage.me/', 'http://preview.pagedemo.me/', $ldp->ladi_link);
//                dd($ldp->ladi_link);
                $ldp->save();
            }
            die('xong!');
        });

        Route::get('edit/{id}', 'Admin\LandingpageController@update')->middleware('permission:landingpage_view');
        Route::post('edit/{id}', 'Admin\LandingpageController@update')->middleware('permission:landingpage_edit');
    });

    //  Admin
    Route::group(['prefix' => 'admin'], function () {
        Route::get('ajax-get-info', 'Admin\AdminController@ajaxGetInfo');
    });
});

Route::group(['prefix' => 'admin'], function () {
    Route::group(['prefix' => 'landingpage'], function () {
        Route::get('down-load-file/{bill_id}/{ldp_id}', 'Admin\LandingpageController@downLoadFile');
    });
});
