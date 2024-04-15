<?php
Route::get('admin/ticket/report', 'Admin\TicketController@report');
Route::group(['prefix' => 'admin', 'middleware' => ['guest:admin', 'get_permissions']], function () {

    Route::group(['prefix' => 'ticket'], function () {
        Route::get('test', 'Admin\TicketController@test');

        Route::get('', 'Admin\TicketController@getIndex')->name('ticket');
        Route::get('publish', 'Admin\TicketController@getPublish')->name('ticket.publish')->middleware('permission:ticket_view');
        Route::match(array('GET', 'POST'), 'add', 'Admin\TicketController@add')->middleware('permission:ticket_view');
        Route::get('delete/{id}', 'Admin\TicketController@delete')->middleware('permission:ticket_view');
        Route::post('multi-delete', 'Admin\TicketController@multiDelete')->middleware('permission:ticket_view');
        Route::get('search-for-select2', 'Admin\TicketController@searchForSelect2')->name('ticket.search_for_select2')->middleware('permission:ticket_view');

        Route::get('{ticket_id}/comment/{comment_id}/delete', 'Admin\TicketController@deleteComment');

        Route::get('{id}', 'Admin\TicketController@update')->middleware('permission:ticket_view');
        Route::post('{id}', 'Admin\TicketController@update')->middleware('permission:ticket_view');
    });
});
