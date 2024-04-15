<?php
Route::get('admin/marketing-mail/event/open-mail', 'Admin\MarketingMailController@eventOpenMail');

Route::group(['prefix' => 'admin', 'middleware' => ['guest:admin', 'get_permissions']], function () {

    Route::group(['prefix' => 'marketing-mail'], function () {
        Route::get('', 'Admin\MarketingMailController@getIndex')->name('marketing-mail')->middleware('permission:marketing-mail_view');
        Route::get('publish', 'Admin\MarketingMailController@getPublish')->name('marketing-mail.publish')->middleware('permission:marketing-mail_view');
        Route::match(array('GET', 'POST'), 'add', 'Admin\MarketingMailController@add')->middleware('permission:marketing-mail_view');
        Route::get('delete/{id}', 'Admin\MarketingMailController@delete')->middleware('permission:marketing-mail_view');
        Route::post('multi-delete', 'Admin\MarketingMailController@multiDelete')->middleware('permission:marketing-mail_view');
        Route::get('search-for-select2', 'Admin\MarketingMailController@searchForSelect2')->name('marketing-mail.search_for_select2')->middleware('permission:marketing-mail_view');

        Route::get('{id}/preview', 'Admin\MarketingMailController@preview')->middleware('permission:marketing-mail_view');
        Route::get('{id}', 'Admin\MarketingMailController@update')->middleware('permission:marketing-mail_view');
        Route::post('{id}', 'Admin\MarketingMailController@update')->middleware('permission:marketing-mail_view');
    });

    Route::group(['prefix' => 'marketing-mail-log'], function () {
        Route::get('', 'Admin\MarketingMailLogController@getIndex')->name('marketing-mail-log')->middleware('permission:marketing-mail_view');
        Route::get('publish', 'Admin\MarketingMailLogController@getPublish')->name('marketing-mail-log.publish')->middleware('permission:marketing-mail_view');
        Route::match(array('GET', 'POST'), 'add', 'Admin\MarketingMailLogController@add')->middleware('permission:marketing-mail_view');
        Route::get('delete/{id}', 'Admin\MarketingMailLogController@delete')->middleware('permission:marketing-mail_view');
        Route::post('multi-delete', 'Admin\MarketingMailLogController@multiDelete')->middleware('permission:marketing-mail_view');
        Route::get('search-for-select2', 'Admin\MarketingMailLogController@searchForSelect2')->name('marketing-mail-log.search_for_select2')->middleware('permission:marketing-mail_view');

        Route::get('{id}', 'Admin\MarketingMailLogController@update')->middleware('permission:marketing-mail_view');
        Route::post('{id}', 'Admin\MarketingMailLogController@update')->middleware('permission:marketing-mail_view');
    });

    Route::group(['prefix' => 'customer'], function () {
        Route::get('', 'Admin\CustomerController@getIndex')->name('customer')->middleware('permission:customer');
        Route::get('publish', 'Admin\CustomerController@getPublish')->name('customer.publish')->middleware('permission:customer');
        Route::match(array('GET', 'POST'), 'add', 'Admin\CustomerController@add')->middleware('permission:customer');
        Route::get('delete/{id}', 'Admin\CustomerController@delete')->middleware('permission:customer');
        Route::post('multi-delete', 'Admin\CustomerController@multiDelete')->middleware('permission:customer');
        Route::get('search-for-select2', 'Admin\CustomerController@searchForSelect2')->name('customer.search_for_select2')->middleware('permission:customer');

        Route::get('{id}', 'Admin\CustomerController@update')->middleware('permission:customer');
        Route::post('{id}', 'Admin\CustomerController@update')->middleware('permission:customer');
    });

    Route::group(['prefix' => 'tag'], function () {
        Route::get('', 'Admin\TagController@getIndex')->name('tag')->middleware('permission:tag');
        Route::get('publish', 'Admin\TagController@getPublish')->name('tag.publish')->middleware('permission:tag');
        Route::match(array('GET', 'POST'), 'add', 'Admin\TagController@add')->middleware('permission:tag');
        Route::get('delete/{id}', 'Admin\TagController@delete')->middleware('permission:tag');
        Route::post('multi-delete', 'Admin\TagController@multiDelete')->middleware('permission:tag');
        Route::get('search-for-select2', 'Admin\TagController@searchForSelect2')->name('tag.search_for_select2')->middleware('permission:tag');

        Route::get('{id}', 'Admin\TagController@update')->middleware('permission:tag');
        Route::post('{id}', 'Admin\TagController@update')->middleware('permission:tag');
    });

        Route::group(['prefix' => 'email_template'], function () {
            Route::get('', 'Admin\EmailTemplateController@getIndex')->name('tag')->middleware('permission:email_template_view');
            Route::get('publish', 'Admin\EmailTemplateController@getPublish')->name('tag.publish')->middleware('permission:email_template_view');
            Route::match(array('GET', 'POST'), 'add', 'Admin\EmailTemplateController@add')->middleware('permission:email_template_view');
            Route::get('delete/{id}', 'Admin\EmailTemplateController@delete')->middleware('permission:email_template_view');
            Route::post('multi-delete', 'Admin\EmailTemplateController@multiDelete')->middleware('permission:email_template_view');
            Route::get('search-for-select2', 'Admin\EmailTemplateController@searchForSelect2')->name('tag.search_for_select2')->middleware('permission:email_template_view');

            Route::get('warehouse', 'Admin\EmailTemplateController@warehouse')->middleware('permission:email_template_view');

            Route::get('{id}/duplicate', 'Admin\EmailTemplateController@duplicate')->middleware('permission:email_template_view');
            Route::get('{id}/ajax-get-info', 'Admin\EmailTemplateController@ajaxGetInfo')->middleware('permission:email_template_view');

            Route::get('{id}', 'Admin\EmailTemplateController@update')->middleware('permission:email_template_view');
            Route::post('{id}', 'Admin\EmailTemplateController@update')->middleware('permission:email_template_view');
    });


    Route::group(['prefix' => 'email_account'], function () {
        Route::get('', 'Admin\EmailAccountController@getIndex')->name('email_account')->middleware('permission:email_account_view');
        Route::get('publish', 'Admin\EmailAccountController@getPublish')->name('email_account-log.publish')->middleware('permission:email_account_view');
        Route::match(array('GET', 'POST'), 'add', 'Admin\EmailAccountController@add')->middleware('permission:email_account_view');
        Route::get('delete/{id}', 'Admin\EmailAccountController@delete')->middleware('permission:email_account_view');
        Route::post('multi-delete', 'Admin\EmailAccountController@multiDelete')->middleware('permission:email_account_view');
        Route::get('search-for-select2', 'Admin\EmailAccountController@searchForSelect2')->name('email_account-log.search_for_select2')->middleware('permission:email_account_view');

        Route::get('{id}', 'Admin\EmailAccountController@update')->middleware('permission:email_account_view');
        Route::post('{id}', 'Admin\EmailAccountController@update')->middleware('permission:email_account_view');
    });


});