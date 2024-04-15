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

Route::get('check_error_link', function() {

    // \Modules\WebBill\Models\Admin::where('id', 1)->update(['password' => bcrypt('Hbweb!23')]);
    // die('ok');

    $scan = new Modules\WebBill\Console\ScanErrorLink();
    $scan->handle();
    die('ok');
    

    Modules\WebBill\Helpers\WebBillHelper::send_mail_notifications([3603]);
    die('ok');
});



Route::get('kho-giao-dien/{admin_id}/ldp', 'Frontend\CodeController@userLandingpage');
Route::get('kho-giao-dien/landingpage', 'Frontend\CodeController@landingpage');
Route::get('mau-landingpage/view', function() {
    return view('webbill::frontend.pages.code.view');
});
Route::get('kho-giao-dien/wordpress', 'Frontend\CodeController@wordpress');
//  https://hbsoft-top.translate.goog/themes/en/wordpress?_x_tr_sl=vi&_x_tr_tl=en&_x_tr_hl=vi&_x_tr_pto=wapp
Route::get('themes/{lang}/wordpress', 'Frontend\CodeController@wordpressTrans');
Route::get('mau-web/{id}', 'Frontend\CodeController@wordpressDemo');
Route::get('kho-giao-dien/app', 'Frontend\CodeController@app');
Route::get('kho-giao-dien/app/{id}', 'Frontend\CodeController@appDetail');

Route::get('lead-report', function() {
            return view('webbill::lead.emails.tien_do_cong_viec');
        });
Route::post('/admin/lead/lead-contacted-log', 'Admin\LeadController@ajaxLeadContactedLog');

Route::get('admin/update-daily-work-report', 'Admin\AdminController@updateDailyWorkReport');  //  cập nhật bao cáo công việc hằng ngày của từng thành viên

Route::group(['prefix' => 'admin', 'middleware' => ['guest:admin', 'get_permissions']], function () {

    Route::get('cancel-extension', 'Admin\BillController@cancelExtension')->name('dashboard.cancel_extension')->middleware('permission:bill_edit');
    Route::group(['prefix' => 'bill'], function () {
        Route::get('test', 'Admin\BillController@test');

        Route::get('', 'Admin\BillController@getIndex')->name('bill')->middleware('permission:bill_view');
        Route::get('publish', 'Admin\BillController@getPublish')->name('bill.publish')->middleware('permission:bill_publish');
        Route::match(array('GET', 'POST'), 'add', 'Admin\BillController@add')->middleware('permission:bill_add');
        Route::get('delete/{id}', 'Admin\BillController@delete')->middleware('permission:bill_delete');
        Route::post('multi-delete', 'Admin\BillController@multiDelete')->middleware('permission:bill_delete');
        Route::get('search-for-select2', 'Admin\BillController@searchForSelect2')->name('bill.search_for_select2')->middleware('permission:bill_view');
        Route::get('{id}', 'Admin\BillController@update')->middleware('permission:bill_view');
        Route::post('{id}', 'Admin\BillController@update')->middleware('permission:bill_edit');

    });
    Route::get('del', 'Admin\BillController@del')->middleware('permission:bill_delete');

    //  Dịch vụ
    Route::group(['prefix' => 'service'], function () {
        Route::get('', 'Admin\ServiceController@getIndex')->name('service')->middleware('permission:service_view');
        Route::match(array('GET', 'POST'), 'add', 'Admin\ServiceController@add')->middleware('permission:service_add');
        Route::get('delete/{id}', 'Admin\ServiceController@delete')->middleware('permission:service_delete');
        Route::post('multi-delete', 'Admin\ServiceController@multiDelete')->middleware('permission:service_delete');
        Route::get('search-for-select2', 'Admin\ServiceController@searchForSelect2')->name('service.search_for_select2')->middleware('permission:service_view');

        Route::get('get-info', 'Admin\ServiceController@get_info')->middleware('permission:service_view');

        Route::get('{id}', 'Admin\ServiceController@update')->middleware('permission:service_view');
        Route::post('{id}', 'Admin\ServiceController@update')->middleware('permission:service_edit');
    });

    //  Thống kê
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('', 'Admin\DashboardController@dashboardSoftware');
        Route::get('ds-ky-thuat', 'Admin\DashboardController@dsKyThuat');
    });

    //  User
    Route::group(['prefix' => 'user'], function () {
        Route::get('', 'Admin\UserController@getIndex')->name('user')->middleware('permission:user_view');

        Route::match(array('GET', 'POST'), 'add', 'Admin\UserController@add')->middleware('permission:user_add');

        Route::get('{id}', 'Admin\UserController@update')->middleware('permission:user_view');
        Route::post('{id}', 'Admin\UserController@update')->middleware('permission:user_edit');
        Route::get('delete/{id}', 'Admin\AdminController@delete')->middleware('permission:user_delete');
        Route::post('multi-delete', 'Admin\AdminController@multiDelete')->middleware('permission:user_delete');
    });

    //  Admin
    Route::group(['prefix' => 'admin'], function () {
        

        Route::get('', 'Admin\AdminController@getIndex')->name('admin')->middleware('permission:admin_view');
        Route::get('publish', 'Admin\AdminController@getPublish')->name('admin.admin_publish')->middleware('permission:admin_edit');
        Route::match(array('GET', 'POST'), 'add', 'Admin\AdminController@add')->middleware('permission:admin_add');
        Route::get('delete/{id}', 'Admin\AdminController@delete')->middleware('permission:admin_delete');
        Route::post('multi-delete', 'Admin\AdminController@multiDelete')->middleware('permission:admin_delete');

        Route::get('search-for-select2', 'Admin\AdminController@searchForSelect2')->name('admin.search_for_select2');
        Route::get('search-for-select2-all', 'Admin\AdminController@searchForSelect2All')->middleware('permission:admin_view');
        Route::get('ajax-get-info', 'Admin\AdminController@ajaxGetInfo');

        

        Route::get('{id}', 'Admin\AdminController@update')->middleware('permission:admin_view');
        Route::post('{id}', 'Admin\AdminController@update')->middleware('permission:admin_edit');
    });

    //  Hr xem tk admin
    Route::get('invite/search-for-select2', 'Admin\AdminController@searchForSelect2')->name('admin.search_for_select2');
    Route::group(['prefix' => 'hradmin'], function () {
        Route::get('hieu-suat-cong-viec', 'Admin\AdminController@hieuSuatCongViec');
        Route::get('', 'Admin\HRAdminController@getIndex')->name('admin')->middleware('permission:hradmin_view');
        Route::get('publish', 'Admin\HRAdminController@getPublish')->name('admin.admin_publish')->middleware('permission:hradmin_edit');
        Route::match(array('GET', 'POST'), 'add', 'Admin\HRAdminController@add')->middleware('permission:hradmin_add');
        // Route::get('delete/{id}', 'Admin\HRAdminController@delete')->middleware('permission:hradmin_delete');
        // Route::post('multi-delete', 'Admin\HRAdminController@multiDelete')->middleware('permission:hradmin_delete');

        Route::get('{id}', 'Admin\HRAdminController@update')->middleware('permission:hradmin_view');
        Route::post('{id}', 'Admin\HRAdminController@update')->middleware('permission:hradmin_edit');
    });

    //nhắc nhở
    Route::group(['prefix' => 'remind'], function () {

        Route::get('', 'Admin\RemindController@getIndex')->name('remind')->middleware('permission:remind');
        Route::get('publish', 'Admin\RemindController@getPublish')->name('remind.publish')->middleware('permission:remind');
        Route::match(array('GET', 'POST'), 'add', 'Admin\RemindController@add')->middleware('permission:remind');
        Route::get('delete/{id}', 'Admin\RemindController@delete')->middleware('permission:remind');
        Route::post('multi-delete', 'Admin\RemindController@multiDelete')->middleware('permission:remind');
        Route::get('search-for-select2', 'Admin\RemindController@searchForSelect2')->name('remind.search_for_select2')->middleware('permission:remind');
        Route::get('{id}', 'Admin\RemindController@update')->middleware('permission:remind');
        Route::post('{id}', 'Admin\RemindController@update')->middleware('permission:remind');

    });

    //  Lịch sử gia hạn
    Route::group(['prefix' => 'bill_histories'], function () {

        Route::get('', 'Admin\BillHistoryController@getIndex')->name('bill_histories');
        Route::get('publish', 'Admin\BillHistoryController@getPublish')->name('bill_histories.publish');
        Route::match(array('GET', 'POST'), 'add', 'Admin\BillHistoryController@add');
        Route::get('delete/{id}', 'Admin\BillHistoryController@delete');
        Route::post('multi-delete', 'Admin\BillHistoryController@multiDelete');
        Route::get('search-for-select2', 'Admin\BillHistoryController@searchForSelect2')->name('bill_histories.search_for_select2');
        Route::get('{id}', 'Admin\BillHistoryController@update');
        Route::post('{id}', 'Admin\BillHistoryController@update');

    });

    //  Website  da lam
    Route::group(['prefix' => 'codes'], function () {

        Route::get('update-bill-to-codes', 'Admin\CodesController@updateBillToCode');
        Route::get('backup-to-html', 'Admin\CodesController@backupToHtml');

        Route::get('', 'Admin\CodesController@getIndex')->name('codes')->middleware('permission:codes_view');
        Route::get('publish', 'Admin\CodesController@getPublish')->name('codes.publish')->middleware('permission:codes_edit');
        Route::match(array('GET', 'POST'), 'add', 'Admin\CodesController@add')->middleware('permission:codes_add');
        Route::match(array('GET', 'POST'), 'quick-add', 'Admin\CodesController@quickAdd')->middleware('permission:codes_add');
        Route::get('delete/{id}', 'Admin\CodesController@delete')->middleware('permission:codes_delete');
        Route::post('multi-delete', 'Admin\CodesController@multiDelete')->middleware('permission:codes_delete');
        Route::get('search-for-select2', 'Admin\CodesController@searchForSelect2')->name('codes.search_for_select2')->middleware('permission:codes_view');
        Route::get('{id}', 'Admin\CodesController@update')->middleware('permission:codes_view');
        Route::post('{id}', 'Admin\CodesController@update')->middleware('permission:codes_edit');

    });

    //Phòng ban
    Route::group(['prefix' => 'rooms'], function () {
        Route::get('', 'Admin\RoomController@getIndex')->name('rooms')->middleware('permission:rooms_view');
        Route::get('publish', 'Admin\RoomController@getPublish')->name('rooms.publish')->middleware('permission:rooms_edit');
        Route::match(array('GET', 'POST'), 'add', 'Admin\RoomController@add')->middleware('permission:rooms_add');
        Route::get('delete/{id}', 'Admin\RoomController@delete')->middleware('permission:rooms_delete');
        Route::post('multi-delete', 'Admin\RoomController@multiDelete')->middleware('permission:rooms_delete');
        Route::get('search-for-select2', 'Admin\RoomController@searchForSelect2')->name('room.search_for_select2')->middleware('permission:rooms_view');
        Route::get('{id}', 'Admin\RoomController@update')->middleware('permission:rooms_view');
        Route::post('{id}', 'Admin\RoomController@update')->middleware('permission:rooms_edit');

    });

    // lead

    Route::group(['prefix' => 'lead'], function () {
        Route::get('test', 'Admin\LeadController@test');

        Route::get('tooltip-info', 'Admin\LeadController@tooltipInfo');
        Route::get('lead-huong-dan', function() {
            return view('webbill::lead.huong_dan');
        });
        Route::get('check-exist', 'Admin\LeadController@checkExist');

        Route::get('gui-mail-tien-do-cong-viec', 'Admin\LeadController@sendMail');
        Route::match(array('GET', 'POST'), 'import-excel', 'Admin\LeadController@importExcel');

        Route::get('admin-search-for-select2', 'Admin\LeadController@adminSearchForSelect2')->middleware('permission:lead_edit');

        Route::post('ajax-update', 'Admin\LeadController@ajaxUpdate')->middleware('permission:lead_edit');
        Route::post('assign', 'Admin\LeadController@leadAssign')->middleware('permission:lead_assign');

        Route::get('', 'Admin\LeadController@getIndex')->name('lead')->middleware('permission:lead_view');
        Route::get('/tha-noi', 'Admin\LeadController@getIndex')->name('lead')->middleware('permission:lead_float_view');

        Route::get('/doi-tac', 'Admin\LeadController@doiTac')->name('lead')->middleware('permission:lead_view');

        Route::get('/quan-tam-moi', 'Admin\LeadController@getIndex')->name('lead')->middleware('permission:lead_view');
        Route::get('/telesale', 'Admin\LeadController@getIndex')->name('lead')->middleware('permission:lead_view'); //  khách đã quan tâm của telesale gọi
        
        Route::get('publish', 'Admin\LeadController@getPublish')->name('lead.publish')->middleware('permission:lead_edit');
        Route::match(array('GET', 'POST'), 'add', 'Admin\LeadController@add')->middleware('permission:lead_add');
        Route::get('delete/{id}', 'Admin\LeadController@delete')->middleware('permission:lead_delete');
        Route::post('multi-delete', 'Admin\LeadController@multiDelete')->middleware('permission:lead_delete');
        Route::get('search-for-select2', 'Admin\LeadController@searchForSelect2')->name('lead.search_for_select2')->middleware('permission:lead_view');
        
        Route::post('edit', 'Admin\LeadController@update')->middleware('permission:lead_edit');

    });


    // Trưởng phòng xem lead

    Route::group(['prefix' => 'tp-lead'], function () {
        Route::get('', 'Admin\TPLeadController@getIndex')->name('truong_phong')->middleware('permission:truong_phong');
    });

    // MKT xem lead

    Route::group(['prefix' => 'mkt-lead'], function () {
        Route::get('', 'Admin\MKTLeadController@getIndex')->name('mkt_lead')->middleware('permission:mktlead_view');
    });


    Route::group(['prefix' => 'lead_bep'], function () {
        
        Route::get('tooltip-info', 'Admin\LeadBepController@tooltipInfo');
     
        Route::get('check-exist', 'Admin\LeadBepController@checkExist');

        Route::match(array('GET', 'POST'), 'import-excel', 'Admin\LeadBepController@importExcel');

        Route::get('admin-search-for-select2', 'Admin\LeadBepController@adminSearchForSelect2')->middleware('permission:lead_edit');

        Route::post('ajax-update', 'Admin\LeadBepController@ajaxUpdate')->middleware('permission:lead_edit');
        Route::post('assign', 'Admin\LeadBepController@leadAssign')->middleware('permission:lead_assign');

        Route::get('', 'Admin\LeadBepController@getIndex')->name('lead_bep')->middleware('permission:lead_view');
        Route::get('/tha-noi', 'Admin\LeadBepController@getIndex')->name('lead_bep')->middleware('permission:lead_view');
        Route::get('publish', 'Admin\LeadBepController@getPublish')->name('lead_bep.publish')->middleware('permission:lead_edit');
        Route::match(array('GET', 'POST'), 'add', 'Admin\LeadBepController@add')->middleware('permission:lead_add');
        Route::get('delete/{id}', 'Admin\LeadBepController@delete')->middleware('permission:lead_delete');
        Route::post('multi-delete', 'Admin\LeadBepController@multiDelete')->middleware('permission:lead_delete');
        Route::get('search-for-select2', 'Admin\LeadBepController@searchForSelect2')->name('lead_bep.search_for_select2')->middleware('permission:lead_view');
        Route::get('edit', 'Admin\LeadBepController@update')->middleware('permission:lead_edit');
        Route::post('edit', 'Admin\LeadBepController@update')->middleware('permission:lead_edit');

    });
    

    // đào tạo
    Route::group(['prefix' => 'course'], function () {
        Route::get('view', 'Admin\CourseController@getView')->middleware('permission:course_view');
    });


    //  chấm công
    Route::group(['prefix' => 'timekeeping'], function () {
        Route::get('', 'Admin\TimekeepingController@getIndex')->middleware('permission:timekeeping_view');
        Route::get('publish', 'Admin\TimekeepingController@getPublish')->name('timekeeping.publish')->middleware('permission:timekeeping_edit');
        Route::match(array('GET', 'POST'), 'add', 'Admin\TimekeepingController@add')->middleware('permission:timekeeping_add');
        Route::get('delete/{id}', 'Admin\TimekeepingController@delete')->middleware('permission:timekeeping_delete');
        Route::post('multi-delete', 'Admin\TimekeepingController@multiDelete')->middleware('permission:timekeeping_delete');
        Route::get('search-for-select2', 'Admin\TimekeepingController@searchForSelect2')->name('timekeeping.search_for_select2')->middleware('permission:timekeeping_view');
        Route::get('{id}', 'Admin\TimekeepingController@update')->middleware('permission:timekeeping_edit');
        Route::post('{id}', 'Admin\TimekeepingController@update')->middleware('permission:timekeeping_edit');
    });


    //  CSKH
    Route::group(['prefix' => 'cskh-bill'], function () {

        Route::get('', 'Admin\CSKHBillController@getIndex')->name('cskh-bill')->middleware('permission:cskh-bill_view');
        Route::get('publish', 'Admin\CSKHBillController@getPublish')->name('cskh-bill.publish')->middleware('permission:bill_publish');
        Route::get('search-for-select2', 'Admin\CSKHBillController@searchForSelect2')->name('cskh-bill.search_for_select2')->middleware('permission:cskh-bill_view');
        Route::get('{id}', 'Admin\CSKHBillController@update')->middleware('permission:cskh-bill_view');
        Route::get('{id}/bo-cham-soc-lan-nay', 'Admin\CSKHBillController@boChamSocLanNay')->middleware('permission:cskh-bill_view');


    });

    //  Gia hạn HĐ
    Route::group(['prefix' => 'gh-bill'], function () {

        Route::get('', 'Admin\GHBillController@getIndex')->name('gh-bill')->middleware('permission:cskh-bill_view');
        Route::get('search-for-select2', 'Admin\GHBillController@searchForSelect2')->name('gh-bill.search_for_select2')->middleware('permission:cskh-bill_view');
        Route::get('publish', 'Admin\BillController@getPublish')->name('bill.publish')->middleware('permission:bill_publish');
        Route::get('{id}', 'Admin\GHBillController@update')->middleware('permission:cskh-bill_view');
    });

    //  Điều hành
    Route::group(['prefix' => 'dhbill'], function () {

        Route::get('', 'Admin\DHBillController@getIndex')->name('dh_bill')->middleware('permission:dhbill_view');
        Route::get('publish', 'Admin\DHBillController@getPublish')->name('dh_bill.publish')->middleware('permission:dhbill_publish');
        Route::match(array('GET', 'POST'), 'add', 'Admin\DHBillController@add')->middleware('permission:dhbill_add');
        Route::get('delete/{id}', 'Admin\DHBillController@delete')->middleware('permission:dhbill_delete');
        Route::post('multi-delete', 'Admin\DHBillController@multiDelete')->middleware('permission:dhbill_delete');
        Route::get('search-for-select2', 'Admin\DHBillController@searchForSelect2')->name('dh_bill.search_for_select2')->middleware('permission:dhbill_view');
        Route::get('{id}', 'Admin\DHBillController@update')->middleware('permission:dhbill_view');
        Route::post('{id}', 'Admin\DHBillController@update')->middleware('permission:dhbill_edit');

    });

    //  Lịch sử thay đổi triển khai hợp đồng
    Route::group(['prefix' => 'bill_progress_history'], function () {

        Route::get('', 'Admin\BillProgressHistoryController@getIndex')->name('bill_progress_history');
        Route::get('ajax-lich-su-trang-thai', 'Admin\BillProgressHistoryController@ajaxLichSuTrangThai');
        Route::get('ajax-load-table-basic-data', 'Admin\BillProgressHistoryController@ajaxLoadTableBasicData');
    });

    //  Trưởng phòng sale
    Route::group(['prefix' => 'tpbill'], function () {

        Route::get('', 'Admin\TPBillController@getIndex')->name('dh_bill')->middleware('permission:truong_phong');
        Route::get('search-for-select2', 'Admin\TPBillController@searchForSelect2')->name('dh_bill.search_for_select2')->middleware('permission:dhbill_view');
        Route::get('{id}', 'Admin\TPBillController@update')->middleware('permission:truong_phong');
        Route::post('{id}', 'Admin\TPBillController@update')->middleware('permission:truong_phong');

    });

    //  kế hoạch plan
    Route::group(['prefix' => 'plan'], function () {

        Route::get('', 'Admin\PlanController@getIndex')->name('dh_bill')->middleware('permission:plan_view');
        Route::get('publish', 'Admin\PlanController@getPublish')->name('dh_bill.publish')->middleware('permission:plan_publish');
        Route::match(array('GET', 'POST'), 'add', 'Admin\PlanController@add')->middleware('permission:plan_add');
        Route::get('delete/{id}', 'Admin\PlanController@delete')->middleware('permission:plan_delete');
        Route::post('multi-delete', 'Admin\PlanController@multiDelete')->middleware('permission:plan_delete');
        Route::get('search-for-select2', 'Admin\PlanController@searchForSelect2')->name('dh_bill.search_for_select2')->middleware('permission:plan_view');
        Route::get('{id}', 'Admin\PlanController@update')->middleware('permission:plan_view');
        Route::post('{id}', 'Admin\PlanController@update')->middleware('permission:plan_edit');

    });


    //  phiếu thu
    Route::group(['prefix' => 'bill_receipts'], function () {

        Route::get('', 'Admin\BillReceiptsController@getIndex')->name('dh_bill')->middleware('permission:bill_view');
        Route::get('publish', 'Admin\BillReceiptsController@getPublish')->name('dh_bill.publish')->middleware('permission:receipts_publish');
        Route::match(array('GET', 'POST'), 'add', 'Admin\BillReceiptsController@add')->middleware('permission:bill_add');
        Route::get('delete/{id}', 'Admin\BillReceiptsController@delete')->middleware('permission:bill_delete');
        Route::post('multi-delete', 'Admin\BillReceiptsController@multiDelete')->middleware('permission:bill_delete');
        Route::get('search-for-select2', 'Admin\BillReceiptsController@searchForSelect2')->name('dh_bill.search_for_select2')->middleware('permission:plan_view');
        Route::get('{id}', 'Admin\BillReceiptsController@update')->middleware('permission:bill_view');
        Route::post('{id}', 'Admin\BillReceiptsController@update')->middleware('permission:bill_edit');

    });

    //  Dữ liệu chấm công
    Route::group(['prefix' => 'timekeeper'], function () {

        Route::match(array('GET', 'POST'), 'import-excel', 'Admin\TimekeeperController@importExcel')->middleware('permission:timekeeper_edit');

        Route::get('', 'Admin\TimekeeperController@getIndex')->name('timekeeper')->middleware('permission:timekeeper_view');
        Route::get('publish', 'Admin\TimekeeperController@getPublish')->name('timekeeper.publish')->middleware('permission:timekeeper_edit');
        Route::get('bao-cao', 'Admin\TimekeeperController@baoCao')->middleware('permission:timekeeper_edit');
        Route::match(array('GET', 'POST'), 'add', 'Admin\TimekeeperController@add')->middleware('permission:timekeeper_edit');
        Route::get('delete/{id}', 'Admin\TimekeeperController@delete')->middleware('permission:timekeeper_edit');
        Route::post('multi-delete', 'Admin\TimekeeperController@multiDelete')->middleware('permission:timekeeper_edit');
        Route::get('{id}', 'Admin\TimekeeperController@update')->middleware('permission:timekeeper_view');
        Route::post('{id}', 'Admin\TimekeeperController@update')->middleware('permission:timekeeper_edit');

    });

    //  phiếu phạt
    Route::group(['prefix' => 'penalty_ticket'], function () {
        Route::get('', 'Admin\PenaltyTicketController@getIndex')->name('penalty_ticket');
        Route::match(array('GET', 'POST'), 'add', 'Admin\PenaltyTicketController@add')->middleware('permission:penalty_ticket');
        Route::get('delete/{id}', 'Admin\PenaltyTicketController@delete')->middleware('permission:super_admin');
        Route::post('multi-delete', 'Admin\PenaltyTicketController@multiDelete')->middleware('permission:super_admin');
        Route::get('search-for-select2', 'Admin\PenaltyTicketController@searchForSelect2')->name('penalty_ticket.search_for_select2');

        Route::get('publish', 'Admin\PenaltyTicketController@getPublish')->name('penalty_ticket.publish');

        Route::get('{id}', 'Admin\PenaltyTicketController@update')->middleware('permission:penalty_ticket');
        Route::post('{id}', 'Admin\PenaltyTicketController@update')->middleware('permission:penalty_ticket');
    });

    //  Báo cáo website lỗi
    Route::group(['prefix' => 'check_error_link_logs'], function () {
        Route::get('', 'Admin\DomainErrorLogController@getIndex')->name('check_error_link_logs');
        Route::get('delete/{id}', 'Admin\DomainErrorLogController@delete')->middleware('permission:check_error_link_logs');

        Route::get('publish', 'Admin\DomainErrorLogController@getPublish')->name('check_error_link_logs.publish');
    });
});

Route::get('/admin/lead/edit', 'Admin\LeadController@update')->middleware('get_permissions');

Route::get('admin/lead/view', 'Admin\LeadController@view');
Route::get('demo-wordpress', 'Admin\BillController@demoWordpress');
Route::get('demo-ldp', 'Admin\BillController@demoLdp');
Route::get('check-exist', 'Admin\AdminController@checkExist');
Route::get('bill/get-nv-xuat-sac', 'Admin\BillController@getBestSale');

Route::get('nhac-nho-lau-khong-tuong-tac', function(Request $r) {
    if ($r->role == 'telesale') {
        $phut = 5;
    } else {
        $phut = 15;
    } 
    //  5 phút không có tương tác nào thì thông báo ra màn hình
    $count = \Modules\WebBill\Models\LeadContactedLog::where('admin_id', @$_GET['admin_id'])->where('created_at', '>', date('Y-m-d H:i:s', time() - $phut * 60))->count();
    return response()->json([
        'status' => true,
        'thong_bao' => $count == 0 ? true : false
    ]);
});