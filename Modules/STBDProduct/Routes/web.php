<?php
Route::group(['prefix' => 'admin', 'middleware' => ['guest:admin', 'get_permissions']], function () {
    Route::group(['prefix' => 'product'], function () {
        Route::get('', 'Admin\ProductController@getIndex')->name('product')->middleware('permission:product_view');
        Route::post('multi-publish', 'Admin\ProductController@enabledStatus')->name('publish-status')->middleware('permission:product_view');
        Route::post('multi-dispublish', 'Admin\ProductController@disabledStatus')->name('dispublish-status')->middleware('permission:product_view');
        Route::get('publish', 'Admin\ProductController@getPublish')->name('product.publish')->middleware('permission:product_view');
        Route::match(array('GET', 'POST'), 'add', 'Admin\ProductController@add')->middleware('permission:product_add');
        Route::get('delete/{id}', 'Admin\ProductController@delete')->middleware('permission:product_delete');
        Route::post('multi-delete', 'Admin\ProductController@multiDelete')->middleware('permission:product_delete');
        Route::get('search-for-select2', 'Admin\ProductController@searchForSelect2')->name('product.search_for_select2')->middleware('permission:product_view');

        Route::get('{id}', 'Admin\ProductController@update')->middleware('permission:product_edit');
        Route::post('{id}', 'Admin\ProductController@update')->middleware('permission:product_edit');
    });

    Route::group(['prefix' => 'product_warehouse'], function () {
        Route::get('', 'Admin\ProductWarehouseController@getIndex')->name('product_warehouse')->middleware('permission:product_warehouse_view');
        Route::get('publish', 'Admin\ProductWarehouseController@getPublish')->name('product_warehouse.publish')->middleware('permission:product_warehouse_view');
        Route::match(array('GET', 'POST'), 'add', 'Admin\ProductWarehouseController@add')->middleware('permission:product_warehouse_add');
        Route::get('delete/{id}', 'Admin\ProductWarehouseController@delete')->middleware('permission:product_warehouse_delete');
        Route::post('multi-delete', 'Admin\ProductWarehouseController@multiDelete')->middleware('permission:product_warehouse_delete');
        Route::get('search-for-select2', 'Admin\ProductWarehouseController@searchForSelect2')->name('product_warehouse.search_for_select2')->middleware('permission:product_warehouse_view');

//        Route::get('{id}/editor', 'Admin\ProductWarehouseController@editor')->middleware('permission:product_warehouse_view');
        Route::get('{id}/get-to-my-company', 'Admin\ProductWarehouseController@getToMyCompany')->middleware('permission:product_add');
        Route::get('{id}', 'Admin\ProductWarehouseController@update')->middleware('permission:product_warehouse_view');
        Route::post('{id}', 'Admin\ProductWarehouseController@update')->middleware('permission:product_warehouse_edit');
    });

    Route::group(['prefix' => 'category_product'], function () {
        Route::get('', 'Admin\CategoryProductController@getIndex')->name('category_product')->middleware('permission:category_product_view');
        Route::get('publish', 'Admin\CategoryProductController@getPublish')->name('category_product.publish')->middleware('permission:category_product_edit');
        Route::match(array('GET', 'POST'), 'add', 'Admin\CategoryProductController@add')->middleware('permission:category_product_add');
        Route::get('delete/{id}', 'Admin\CategoryProductController@delete')->middleware('permission:category_product_delete');
        Route::post('multi-delete', 'Admin\CategoryProductController@multiDelete')->middleware('permission:category_product_delete');
        Route::get('search-for-select2', 'Admin\CategoryProductController@searchForSelect2')->name('category_product.search_for_select2')->middleware('permission:category_product_view');
        Route::get('{id}', 'Admin\CategoryProductController@update')->middleware('permission:category_product_view');
        Route::post('{id}', 'Admin\CategoryProductController@update')->middleware('permission:category_product_edit');
    });


    Route::group(['prefix' => 'tag_product'], function () {
        Route::get('', 'Admin\TagProductController@getIndex')->name('tag_product')->middleware('permission:tag_product_view');
        Route::get('publish', 'Admin\TagProductController@getPublish')->name('tag_product.publish')->middleware('permission:tag_product_edit');
        Route::match(array('GET', 'POST'), 'add', 'Admin\TagProductController@add')->middleware('permission:tag_product_add');
        Route::get('delete/{id}', 'Admin\TagProductController@delete')->middleware('permission:tag_product_delete');
        Route::post('multi-delete', 'Admin\TagProductController@multiDelete')->middleware('permission:tag_product_delete');
        Route::get('search-for-select2', 'Admin\TagProductController@searchForSelect2')->name('tag_product.search_for_select2')->middleware('permission:tag_product_view');
        Route::get('{id}', 'Admin\TagProductController@update')->middleware('permission:tag_product_view');
        Route::post('{id}', 'Admin\TagProductController@update')->middleware('permission:tag_product_edit');
    });
    Route::group(['prefix' => 'properties_value'], function () {
        Route::get('', 'Admin\PropertiesValueController@getIndex')->name('properties_value')->middleware('permission:product_view');
        Route::get('publish', 'Admin\PropertiesValueController@getPublish')->name('properties_value.publish')->middleware('permission:product_edit');
        Route::match(array('GET', 'POST'), 'add', 'Admin\PropertiesValueController@add')->middleware('permission:product_add');
        Route::get('delete/{id}', 'Admin\PropertiesValueController@delete')->middleware('permission:product_delete');
        Route::post('multi-delete', 'Admin\PropertiesValueController@multiDelete')->middleware('permission:product_delete');
        Route::get('search-for-select2', 'Admin\PropertiesValueController@searchForSelect2')->name('properties_value.search_for_select2')->middleware('permission:product_view');

        Route::get('{id}', 'Admin\PropertiesValueController@update')->middleware('permission:product_view');
        Route::post('{id}', 'Admin\PropertiesValueController@update')->middleware('permission:product_edit');
    });


    Route::group(['prefix' => 'category_discount'], function () {
        Route::get('', 'Admin\CategoryDiscountController@getIndex')->name('category_discount')->middleware('permission:product_view');
        Route::get('publish', 'Admin\CategoryDiscountController@getPublish')->name('category_discount.publish')->middleware('permission:product_edit');
        Route::match(array('GET', 'POST'), 'add', 'Admin\CategoryDiscountController@add')->middleware('permission:product_add');
        Route::get('delete/{id}', 'Admin\CategoryDiscountController@delete')->middleware('permission:product_delete');
        Route::post('multi-delete', 'Admin\CategoryDiscountController@multiDelete')->middleware('permission:product_delete');
        Route::get('search-for-select2', 'Admin\CategoryDiscountController@searchForSelect2')->name('category_discount.search_for_select2')->middleware('permission:product_view');

        Route::get('{id}', 'Admin\CategoryDiscountController@update')->middleware('permission:product_view');
        Route::post('{id}', 'Admin\CategoryDiscountController@update')->middleware('permission:product_edit');
    });
    Route::group(['prefix' => 'product_sale'], function () {
        Route::get('', 'Admin\ProductSaleController@getIndex')->name('product_sale')->middleware('permission:product_view');
        Route::get('publish', 'Admin\ProductSaleController@getPublish')->name('product_sale.publish')->middleware('permission:product_edit');
        Route::match(array('GET', 'POST'), 'add', 'Admin\ProductSaleController@add')->middleware('permission:product_add');
        Route::get('delete/{id}', 'Admin\ProductSaleController@delete')->middleware('permission:product_delete');
        Route::post('multi-delete', 'Admin\ProductSaleController@multiDelete')->middleware('permission:product_delete');
        Route::get('search-for-select2', 'Admin\ProductSaleController@searchForSelect2')->name('product_sale.search_for_select2')->middleware('permission:product_view');

        Route::get('{id}', 'Admin\ProductSaleController@update')->middleware('permission:product_view');
        Route::post('{id}', 'Admin\ProductSaleController@update')->middleware('permission:product_edit');
    });
    Route::group(['prefix' => 'manufacturer'], function () {
        Route::get('', 'Admin\ManufacturerController@getIndex')->name('manufacturer')->middleware('permission:product_view');
        Route::get('publish', 'Admin\ManufacturerController@getPublish')->name('manufacturer.publish')->middleware('permission:product_edit');
        Route::match(array('GET', 'POST'), 'add', 'Admin\ManufacturerController@add')->middleware('permission:product_add');
        Route::get('delete/{id}', 'Admin\ManufacturerController@delete')->middleware('permission:product_delete');
        Route::post('multi-delete', 'Admin\ManufacturerController@multiDelete')->middleware('permission:product_delete');
        Route::get('search-for-select2', 'Admin\ManufacturerController@searchForSelect2')->name('manufacturer.search_for_select2')->middleware('permission:product_view');

        Route::get('{id}', 'Admin\ManufacturerController@update')->middleware('permission:product_view');
        Route::post('{id}', 'Admin\ManufacturerController@update')->middleware('permission:product_edit');
    });
    Route::group(['prefix' => 'origin'], function () {
        Route::get('', 'Admin\OriginController@getIndex')->name('origin')->middleware('permission:product_view');
        Route::get('publish', 'Admin\OriginController@getPublish')->middleware('permission:product_edit');
        Route::match(array('GET', 'POST'), 'add', 'Admin\OriginController@add')->middleware('permission:product_add');
        Route::get('delete/{id}', 'Admin\OriginController@delete')->middleware('permission:product_delete');
        Route::post('multi-delete', 'Admin\OriginController@multiDelete')->middleware('permission:product_delete');
        Route::get('search-for-select2', 'Admin\OriginController@searchForSelect2')->middleware('permission:product_view');

        Route::get('{id}', 'Admin\OriginController@update')->middleware('permission:product_view');
        Route::post('{id}', 'Admin\OriginController@update')->middleware('permission:product_edit');
    });
    Route::group(['prefix' => 'properties_name'], function () {
        Route::get('', 'Admin\PropertiesNameController@getIndex')->name('properties_name')->middleware('permission:product_view');
        Route::get('publish', 'Admin\PropertiesNameController@getPublish')->name('properties_name.publish')->middleware('permission:product_edit');
        Route::match(array('GET', 'POST'), 'add', 'Admin\PropertiesNameController@add')->middleware('permission:product_add');
        Route::get('delete/{id}', 'Admin\PropertiesNameController@delete')->middleware('permission:product_delete');
        Route::post('multi-delete', 'Admin\PropertiesNameController@multiDelete')->middleware('permission:product_delete');
        Route::get('search-for-select2', 'Admin\PropertiesNameController@searchForSelect2')->name('properties_name.search_for_select2')->middleware('permission:product_view');

        Route::get('{id}', 'Admin\PropertiesNameController@update')->middleware('permission:product_view');
        Route::post('{id}', 'Admin\PropertiesNameController@update')->middleware('permission:product_edit');
    });
    Route::group(['prefix' => 'guarantees'], function () {
        Route::get('', 'Admin\GuaranteeController@getIndex')->name('guarantees')->middleware('permission:product_view');
        Route::get('publish', 'Admin\GuaranteeController@getPublish')->name('guarantees.publish')->middleware('permission:product_edit');
        Route::match(array('GET', 'POST'), 'add', 'Admin\GuaranteeController@add')->middleware('permission:product_add');
        Route::get('delete/{id}', 'Admin\GuaranteeController@delete')->middleware('permission:product_delete');
        Route::post('multi-delete', 'Admin\GuaranteeController@multiDelete')->middleware('permission:product_delete');
        Route::get('search-for-select2', 'Admin\GuaranteeController@searchForSelect2')->name('guarantees.search_for_select2')->middleware('permission:product_view');

        Route::get('{id}', 'Admin\GuaranteeController@update')->middleware('permission:product_view');
        Route::post('{id}', 'Admin\GuaranteeController@update')->middleware('permission:product_edit');
    });
});
