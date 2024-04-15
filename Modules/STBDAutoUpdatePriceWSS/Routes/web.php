<?php

Route::get('test-update-price', function() {
   $x = new Modules\STBDAutoUpdatePriceWSS\Console\UpdatePriceWss();
   $x->handle();
   die('ok');
});

Route::group(['prefix' => 'admin', 'middleware' => ['guest:admin', 'get_permissions']], function () {
    Route::group(['prefix' => 'update-product-price'], function () {
        Route::get('', 'UpdateProductPriceLogController@getIndex')->name('update-product-price');
        Route::get('publish', 'UpdateProductPriceLogController@getPublish')->name('update-product-price.publish');
        Route::match(array('GET', 'POST'), 'add', 'UpdateProductPriceLogController@add');
        Route::get('delete/{id}', 'UpdateProductPriceLogController@delete');
        Route::post('multi-delete', 'UpdateProductPriceLogController@multiDelete');
        Route::get('crawl', 'UpdateProductPriceLogController@crawl');
        Route::get('delete-links-error', 'UpdateProductPriceLogController@deleteLinksError');
        Route::get('search-for-select2', 'UpdateProductPriceLogController@searchForSelect2')->name('update-product-price.search_for_select2');
        Route::get('ajax_html_select_category', 'DoomController@ajaxGetHtmlSelectCategory');
        Route::get('{id}', 'UpdateProductPriceLogController@update');
        Route::post('{id}', 'UpdateProductPriceLogController@update');
    });

    /*Route::group(['prefix' => 'doom-product'], function () {
        Route::get('', 'ProductController@getIndex')->name('doom-product');
        Route::get('publish', 'ProductController@getPublish')->name('doom-product.publish');
        Route::match(array('GET', 'POST'), 'add', 'ProductController@add');
        Route::get('delete/{id}', 'ProductController@delete');
        Route::post('multi-delete', 'ProductController@multiDelete');
        Route::get('delete-all', 'ProductController@allDelete');
        Route::get('get-data-export', 'ProductController@getDataExport');
        Route::get('get-data-image-export', 'ProductController@getDataExportImage');
        Route::get('search-for-select2', 'ProductController@searchForSelect2')->name('doom-product.search_for_select2');
        Route::get('{id}', 'ProductController@update');
        Route::post('{id}', 'ProductController@update');
    });*/
});
