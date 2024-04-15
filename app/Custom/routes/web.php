<?php

Route::get('/', function () {
    /*$bills = \Modules\WebBill\Models\Bill::all();
    foreach ($bills as $bill) {
        $bill->registration_date = $bill->created_at;
        $bill->save();
    }
    die('ok');*/
    return redirect('/admin');
});

Route::get('diem-danh', function (\Illuminate\Http\Request $r) {
//    dd($r);
    $admin = App\Models\Admin::select('id', 'name', 'code', 'may_cham_cong_id')->where('id', $r->admin_id)
        ->where('status', 1)->first();
    if (!is_object($admin)) {
        CommonHelper::one_time_message('error', 'Không tìm thấy thành viên!');
        return back();
    }

    $timekeeper = \App\CRMDV\Models\Timekeeper::where('may_cham_cong_id', $admin->may_cham_cong_id)->where('time', '>=', date('Y-m-d 00:00:00'))->first();

    if (!is_object($timekeeper)) {

        $timekeeper = new \App\CRMDV\Models\Timekeeper();
        $timekeeper->admin_id = $r->admin_id;
        $timekeeper->may_cham_cong_id = $admin->may_cham_cong_id;
        $timekeeper->time = date('Y-m-d H:i:s');
        $timekeeper->create_by = $r->admin_id;
        $timekeeper->save();
        CommonHelper::one_time_message('success', 'Điểm danh buổi sáng lúc ' . date('H:i:s Y-m-d') . '!');
        return back();
    } else {
        $timekeeper = new \App\CRMDV\Models\Timekeeper();
        $timekeeper->admin_id = $r->admin_id;
        $timekeeper->may_cham_cong_id = $admin->may_cham_cong_id;
        $timekeeper->time = date('Y-m-d H:i:s');
        $timekeeper->create_by = $r->admin_id;
        $timekeeper->save();
        CommonHelper::one_time_message('success', 'Đã chấm ra về lúc ' . date('H:i:s Y-m-d') . '!');
        return back();
    }


});


Route::group(['prefix' => 'admin', 'middleware' => ['guest:admin', 'get_permissions']], function () {
    Route::group(['prefix' => 'bill'], function () {
        Route::get('gia-han', '\App\Custom\Controllers\Admin\BillController@giaHan');
        Route::get('ko-gia-han', '\App\Custom\Controllers\Admin\BillController@koGiaHan');
    });

    Route::group(['prefix' => 'landingpage'], function () {
        Route::get('', '\App\Custom\Controllers\Admin\LandingpageController@getIndex')->name('landingpage')->middleware('permission:landingpage_view');
        Route::get('publish', '\App\Custom\Controllers\Admin\LandingpageController@getPublish')->name('landingpage.publish')->middleware('permission:landingpage_publish');
        Route::match(array('GET', 'POST'), 'add', '\App\Custom\Controllers\Admin\LandingpageController@add')->middleware('permission:landingpage_add');
        Route::get('delete/{id}', '\App\Custom\Controllers\Admin\LandingpageController@delete')->middleware('permission:landingpage_delete');
        Route::post('multi-delete', '\App\Custom\Controllers\Admin\LandingpageController@multiDelete')->middleware('permission:landingpage_delete');
        Route::get('search-for-select2', '\App\Custom\Controllers\Admin\LandingpageController@searchForSelect2')->name('landingpage.search_for_select2')->middleware('permission:landingpage_view');
        Route::get('{id}/duplicate', '\App\Custom\Controllers\Admin\LandingpageController@duplicate')->middleware('permission:landingpage_add');

        Route::get('{id}/ban-giao', '\App\Custom\Controllers\Admin\LandingpageController@banGiao');

        Route::get('update-to-bill', '\App\Custom\Controllers\Admin\LandingpageController@updateToBill')->middleware('permission:bill_add');

        Route::get('get-gg-form-fields', '\App\Custom\Controllers\Admin\LandingpageController@getGGFormFields');

        Route::get('update-link-ldp', function () {
            $landingpages = \Modules\LandingPage\Models\Landingpage::where('ladi_link', 'like', '%ladi.demopage.me%')->get();
            foreach ($landingpages as $ldp) {
                $ldp->ladi_link = str_replace('http://ladi.demopage.me/', 'http://preview.pagedemo.me/', $ldp->ladi_link);
//                dd($ldp->ladi_link);
                $ldp->save();
            }
            die('xong!');
        });

        Route::get('edit/{id}', '\App\Custom\Controllers\Admin\LandingpageController@update')->middleware('permission:landingpage_view');
        Route::post('edit/{id}', '\App\Custom\Controllers\Admin\LandingpageController@update')->middleware('permission:landingpage_edit');
    });

    //  Admin
    Route::group(['prefix' => 'admin'], function () {
        Route::get('ajax-get-info', '\App\CRMDV\Controllers\Admin\AdminController@ajaxGetInfo');
    });

    //  quản lý công ty
    Route::group(['prefix' => 'company'], function () {
        Route::get('', '\App\Custom\Controllers\Admin\CompanyController@getIndex')->name('company')->middleware('permission:lead_view');
        Route::match(array('GET', 'POST'), 'add', '\App\Custom\Controllers\Admin\CompanyController@add')->middleware('permission:lead_view');
        Route::get('edit/{id}', '\App\Custom\Controllers\Admin\CompanyController@update')->middleware('permission:lead_view');
        Route::post('edit/{id}', '\App\Custom\Controllers\Admin\CompanyController@update')->middleware('permission:lead_view');
        Route::get('delete/{id}', '\App\Custom\Controllers\Admin\CompanyController@delete')->middleware('permission:super_admin');
        Route::post('multi-delete', '\App\Custom\Controllers\Admin\CompanyController@multiDelete')->middleware('permission:super_admin');
        Route::get('publish', '\App\Custom\Controllers\Admin\CompanyController@getPublish')->name('company.publish')->middleware('permission:lead_view');
    });

    //  báo cáo dẫn khách
    Route::group(['prefix' => 'bao_cao_dan_khach'], function () {
        Route::get('', '\App\Custom\Controllers\Admin\BaoCaoDanKhachController@getIndex')->name('bao_cao_dan_khach')->middleware('permission:bao_cao_dan_khach_view');
        Route::match(array('GET', 'POST'), 'add', '\App\Custom\Controllers\Admin\BaoCaoDanKhachController@add')->middleware('permission:bao_cao_dan_khach_view');
        Route::get('edit/{id}', '\App\Custom\Controllers\Admin\BaoCaoDanKhachController@update')->middleware('permission:bao_cao_dan_khach_view');
        Route::post('edit/{id}', '\App\Custom\Controllers\Admin\BaoCaoDanKhachController@update')->middleware('permission:bao_cao_dan_khach_view');
        Route::get('delete/{id}', '\App\Custom\Controllers\Admin\BaoCaoDanKhachController@delete')->middleware('permission:super_admin');
        Route::post('multi-delete', '\App\Custom\Controllers\Admin\BaoCaoDanKhachController@multiDelete')->middleware('permission:super_admin');
        Route::get('publish', '\App\Custom\Controllers\Admin\BaoCaoDanKhachController@getPublish')->name('bao_cao_dan_khach.publish')->middleware('permission:bao_cao_dan_khach_view');
        Route::get('ajax-get-info/{id}', '\App\Custom\Controllers\Admin\BaoCaoDanKhachController@ajaxGetInfo')->middleware('permission:bao_cao_dan_khach_view');
        Route::get('ajax-get-image/{id}', '\App\Custom\Controllers\Admin\BaoCaoDanKhachController@ajaxGetImage')->middleware('permission:bao_cao_dan_khach_view');


    });

    //  ngành nghề
    Route::group(['prefix' => 'company_category'], function () {
        Route::get('', '\App\Custom\Controllers\Admin\CompanyCategoryController@getIndex')->name('company_category')->middleware('permission:lead_view');
        Route::match(array('GET', 'POST'), 'add', '\App\Custom\Controllers\Admin\CompanyCategoryController@add')->middleware('permission:lead_view');
        Route::get('edit/{id}', '\App\Custom\Controllers\Admin\CompanyCategoryController@update')->middleware('permission:lead_view');
        Route::post('edit/{id}', '\App\Custom\Controllers\Admin\CompanyCategoryController@update')->middleware('permission:lead_view');
        Route::get('delete/{id}', '\App\Custom\Controllers\Admin\CompanyCategoryController@delete')->middleware('permission:super_admin');
        Route::post('multi-delete', '\App\Custom\Controllers\Admin\CompanyCategoryController@multiDelete')->middleware('permission:super_admin');
        Route::get('search-for-select2', '\App\Custom\Controllers\Admin\CompanyCategoryController@searchForSelect2')->name('company_category.search_for_select2')->middleware('permission:lead_view');
    });
});

Route::group(['prefix' => 'admin'], function () {
    Route::group(['prefix' => 'landingpage'], function () {
        Route::get('down-load-file/{bill_id}/{ldp_id}', '\App\Custom\Controllers\Admin\LandingpageController@downLoadFile');
    });
});
