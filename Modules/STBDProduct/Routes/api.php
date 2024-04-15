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

Route::group(['prefix' => 'product'], function () {
    Route::post('get-top1-wss', 'Admin\ProductController@getTop1Wss');
    Route::post('update-top1-wss', 'Admin\ProductController@updateTop1Wss');
});