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



Route::get('kho-giao-dien/{admin_id}/ldp', '\App\CRMEdu\Controllers\Frontend\CodeController@userLandingpage');
Route::get('kho-giao-dien/landingpage', '\App\CRMEdu\Controllers\Frontend\CodeController@landingpage');
Route::get('mau-landingpage/view', function() {
    return view('webbill::frontend.pages.code.view');
});
Route::get('kho-giao-dien/wordpress', '\App\CRMEdu\Controllers\Frontend\CodeController@wordpress');
//  https://hbsoft-top.translate.goog/themes/en/wordpress?_x_tr_sl=vi&_x_tr_tl=en&_x_tr_hl=vi&_x_tr_pto=wapp
Route::get('themes/{lang}/wordpress', '\App\CRMEdu\Controllers\Frontend\CodeController@wordpressTrans');
Route::get('mau-web/{id}', '\App\CRMEdu\Controllers\Frontend\CodeController@wordpressDemo');
Route::get('kho-giao-dien/app', '\App\CRMEdu\Controllers\Frontend\CodeController@app');
Route::get('kho-giao-dien/app/{id}', '\App\CRMEdu\Controllers\Frontend\CodeController@appDetail');

Route::get('lead-report', function() {
    return view('webbill::lead.emails.tien_do_cong_viec');
});
Route::post('/admin/lead/lead-contacted-log', '\App\CRMEdu\Controllers\Admin\LeadController@ajaxLeadContactedLog');

Route::get('admin/update-daily-work-report', '\App\CRMEdu\Controllers\Admin\AdminController@updateDailyWorkReport');  //  cập nhật bao cáo công việc hằng ngày của từng thành viên

Route::group(['prefix' => 'admin', 'middleware' => ['guest:admin', 'get_permissions']], function () {

    Route::get('cancel-extension', '\App\CRMEdu\Controllers\Admin\BillController@cancelExtension')->name('dashboard.cancel_extension')->middleware('permission:bill_edit');
    Route::group(['prefix' => 'bill'], function () {
        Route::get('test', '\App\CRMEdu\Controllers\Admin\BillController@test');

        Route::get('', '\App\CRMEdu\Controllers\Admin\BillController@getIndex')->name('bill')->middleware('permission:bill_view');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\BillController@getPublish')->name('bill.publish')->middleware('permission:bill_publish');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\BillController@add')->middleware('permission:bill_add');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\BillController@delete')->middleware('permission:bill_delete');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\BillController@multiDelete')->middleware('permission:bill_delete');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\BillController@searchForSelect2')->name('bill.search_for_select2')->middleware('permission:bill_view');
        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\BillController@update')->middleware('permission:bill_view');
        Route::post('edit/{id}', '\App\CRMEdu\Controllers\Admin\BillController@update')->middleware('permission:bill_edit');

    });

    //  Thu - chi
    Route::group(['prefix' => 'receipt_payment'], function () {

        Route::get('', '\App\CRMEdu\Controllers\Admin\ReceiptPaymentController@getIndex')->name('dh_bill')->middleware('permission:bill_view');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\ReceiptPaymentController@getPublish')->name('dh_bill.publish')->middleware('permission:receipts_publish');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\ReceiptPaymentController@add')->middleware('permission:bill_add');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\ReceiptPaymentController@delete')->middleware('permission:bill_delete');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\ReceiptPaymentController@multiDelete')->middleware('permission:bill_delete');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\ReceiptPaymentController@searchForSelect2')->name('dh_bill.search_for_select2')->middleware('permission:plan_view');
        Route::get('{id}', '\App\CRMEdu\Controllers\Admin\ReceiptPaymentController@update')->middleware('permission:bill_view');
        Route::post('{id}', '\App\CRMEdu\Controllers\Admin\ReceiptPaymentController@update')->middleware('permission:bill_edit');

    });

    Route::get('del', '\App\CRMEdu\Controllers\Admin\BillController@del')->middleware('permission:bill_delete');

    //  Dịch vụ
    Route::group(['prefix' => 'service'], function () {
        Route::get('', '\App\CRMEdu\Controllers\Admin\ServiceController@getIndex')->name('service')->middleware('permission:service_view');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\ServiceController@add')->middleware('permission:service_add');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\ServiceController@delete')->middleware('permission:service_delete');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\ServiceController@multiDelete')->middleware('permission:service_delete');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\ServiceController@searchForSelect2')->name('service.search_for_select2')->middleware('permission:service_view');

        Route::get('get-info', '\App\CRMEdu\Controllers\Admin\ServiceController@get_info')->middleware('permission:service_view');

        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\ServiceController@update')->middleware('permission:service_view');
        Route::post('edit/{id}', '\App\CRMEdu\Controllers\Admin\ServiceController@update')->middleware('permission:service_edit');
    });

    //  Thống kê
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('', '\App\CRMEdu\Controllers\Admin\DashboardController@dashboardSoftware');
        Route::get('ds-ky-thuat', '\App\CRMEdu\Controllers\Admin\DashboardController@dsKyThuat');
    });

    //  User
    Route::group(['prefix' => 'user'], function () {
        Route::get('', '\App\CRMEdu\Controllers\Admin\UserController@getIndex')->name('user')->middleware('permission:user_view');

        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\UserController@add')->middleware('permission:user_add');

        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\UserController@update')->middleware('permission:user_view');
        Route::post('edit/{id}', '\App\CRMEdu\Controllers\Admin\UserController@update')->middleware('permission:user_edit');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\AdminController@delete')->middleware('permission:user_delete');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\AdminController@multiDelete')->middleware('permission:user_delete');
    });

    //  Admin
    Route::group(['prefix' => 'admin'], function () {


        Route::get('', '\App\CRMEdu\Controllers\Admin\AdminController@getIndex')->name('admin')->middleware('permission:admin_view');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\AdminController@getPublish')->name('admin.admin_publish')->middleware('permission:admin_edit');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\AdminController@add')->middleware('permission:admin_add');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\AdminController@delete')->middleware('permission:admin_delete');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\AdminController@multiDelete')->middleware('permission:admin_delete');

        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\AdminController@searchForSelect2')->name('admin.search_for_select2');
        Route::get('search-for-select2-all', '\App\CRMEdu\Controllers\Admin\AdminController@searchForSelect2All')->middleware('permission:admin_view');
        Route::get('ajax-get-info', '\App\CRMEdu\Controllers\Admin\AdminController@ajaxGetInfo');



        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\AdminController@update')->middleware('permission:admin_view');
        Route::post('edit/{id}', '\App\CRMEdu\Controllers\Admin\AdminController@update')->middleware('permission:admin_edit');
    });

    //  Hr xem tk admin
    Route::get('invite/search-for-select2', '\App\CRMEdu\Controllers\Admin\AdminController@searchForSelect2')->name('admin.search_for_select2');
    Route::group(['prefix' => 'hradmin'], function () {
        Route::get('hieu-suat-cong-viec', '\App\CRMEdu\Controllers\Admin\AdminController@hieuSuatCongViec');
        Route::get('', '\App\CRMEdu\Controllers\Admin\HRAdminController@getIndex')->name('admin')->middleware('permission:hradmin_view');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\HRAdminController@getPublish')->name('admin.admin_publish')->middleware('permission:hradmin_edit');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\HRAdminController@add')->middleware('permission:hradmin_add');
        // Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\HRAdminController@delete')->middleware('permission:hradmin_delete');
        // Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\HRAdminController@multiDelete')->middleware('permission:hradmin_delete');

        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\HRAdminController@update')->middleware('permission:hradmin_view');
        Route::post('edit/{id}', '\App\CRMEdu\Controllers\Admin\HRAdminController@update')->middleware('permission:hradmin_edit');
    });

    //nhắc nhở
    Route::group(['prefix' => 'remind'], function () {

        Route::get('', '\App\CRMEdu\Controllers\Admin\RemindController@getIndex')->name('remind')->middleware('permission:remind');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\RemindController@getPublish')->name('remind.publish')->middleware('permission:remind');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\RemindController@add')->middleware('permission:remind');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\RemindController@delete')->middleware('permission:remind');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\RemindController@multiDelete')->middleware('permission:remind');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\RemindController@searchForSelect2')->name('remind.search_for_select2')->middleware('permission:remind');
        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\RemindController@update')->middleware('permission:remind');
        Route::post('edit/{id}', '\App\CRMEdu\Controllers\Admin\RemindController@update')->middleware('permission:remind');

    });

    //  Lịch sử gia hạn
    Route::group(['prefix' => 'bill_histories'], function () {

        Route::get('', '\App\CRMEdu\Controllers\Admin\BillHistoryController@getIndex')->name('bill_histories');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\BillHistoryController@getPublish')->name('bill_histories.publish');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\BillHistoryController@add');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\BillHistoryController@delete');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\BillHistoryController@multiDelete');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\BillHistoryController@searchForSelect2')->name('bill_histories.search_for_select2');
        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\BillHistoryController@update');
        Route::post('edit/{id}', '\App\CRMEdu\Controllers\Admin\BillHistoryController@update');

    });

    //  Website  da lam
    Route::group(['prefix' => 'codes'], function () {

        Route::get('update-bill-to-codes', '\App\CRMEdu\Controllers\Admin\CodesController@updateBillToCode');
        Route::get('backup-to-html', '\App\CRMEdu\Controllers\Admin\CodesController@backupToHtml');

        Route::get('', '\App\CRMEdu\Controllers\Admin\CodesController@getIndex')->name('codes')->middleware('permission:codes_view');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\CodesController@getPublish')->name('codes.publish')->middleware('permission:codes_edit');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\CodesController@add')->middleware('permission:codes_add');
        Route::match(array('GET', 'POST'), 'quick-add', '\App\CRMEdu\Controllers\Admin\CodesController@quickAdd')->middleware('permission:codes_add');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\CodesController@delete')->middleware('permission:codes_delete');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\CodesController@multiDelete')->middleware('permission:codes_delete');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\CodesController@searchForSelect2')->name('codes.search_for_select2')->middleware('permission:codes_view');
        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\CodesController@update')->middleware('permission:codes_view');
        Route::post('edit/{id}', '\App\CRMEdu\Controllers\Admin\CodesController@update')->middleware('permission:codes_edit');

    });


    // lead

    Route::group(['prefix' => 'lead'], function () {
        Route::get('test', '\App\CRMEdu\Controllers\Admin\LeadController@test');

        Route::get('tooltip-info', '\App\CRMEdu\Controllers\Admin\LeadController@tooltipInfo');
        Route::get('lead-huong-dan', function() {
            return view('webbill::lead.huong_dan');
        });
        Route::get('check-exist', '\App\CRMEdu\Controllers\Admin\LeadController@checkExist');

        Route::get('gui-mail-tien-do-cong-viec', '\App\CRMEdu\Controllers\Admin\LeadController@sendMail');
        Route::match(array('GET', 'POST'), 'import-excel', '\App\CRMEdu\Controllers\Admin\LeadController@importExcel');

        Route::get('admin-search-for-select2', '\App\CRMEdu\Controllers\Admin\LeadController@adminSearchForSelect2')->middleware('permission:lead_edit');

        Route::post('ajax-update', '\App\CRMEdu\Controllers\Admin\LeadController@ajaxUpdate')->middleware('permission:lead_edit');
        Route::post('assign', '\App\CRMEdu\Controllers\Admin\LeadController@leadAssign')->middleware('permission:lead_assign');

        Route::get('', '\App\CRMEdu\Controllers\Admin\LeadController@getIndex')->name('lead')->middleware('permission:lead_view');
        Route::get('/tha-noi', '\App\CRMEdu\Controllers\Admin\LeadController@getIndex')->name('lead')->middleware('permission:lead_float_view');

        Route::get('/doi-tac', '\App\CRMEdu\Controllers\Admin\LeadController@doiTac')->name('lead')->middleware('permission:lead_view');

        Route::get('/quan-tam-moi', '\App\CRMEdu\Controllers\Admin\LeadController@getIndex')->name('lead')->middleware('permission:lead_view');
        Route::get('/telesale', '\App\CRMEdu\Controllers\Admin\LeadController@getIndex')->name('lead')->middleware('permission:lead_view'); //  khách đã quan tâm của telesale gọi

        Route::get('publish', '\App\CRMEdu\Controllers\Admin\LeadController@getPublish')->name('lead.publish')->middleware('permission:lead_edit');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\LeadController@add')->middleware('permission:lead_add');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\LeadController@delete')->middleware('permission:lead_delete');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\LeadController@multiDelete')->middleware('permission:lead_delete');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\LeadController@searchForSelect2')->name('lead.search_for_select2')->middleware('permission:lead_view');

        Route::post('edit', '\App\CRMEdu\Controllers\Admin\LeadController@update')->middleware('permission:lead_edit');

    });


    // Trưởng phòng xem lead

    Route::group(['prefix' => 'tp-lead'], function () {
        Route::get('', '\App\CRMEdu\Controllers\Admin\TPLeadController@getIndex')->name('truong_phong')->middleware('permission:truong_phong');
    });

    // MKT xem lead

    Route::group(['prefix' => 'mkt-lead'], function () {
        Route::get('', '\App\CRMEdu\Controllers\Admin\MKTLeadController@getIndex')->name('mkt_lead')->middleware('permission:mktlead_view');
    });


    Route::group(['prefix' => 'lead_bep'], function () {

        Route::get('tooltip-info', '\App\CRMEdu\Controllers\Admin\LeadBepController@tooltipInfo');

        Route::get('check-exist', '\App\CRMEdu\Controllers\Admin\LeadBepController@checkExist');

        Route::match(array('GET', 'POST'), 'import-excel', '\App\CRMEdu\Controllers\Admin\LeadBepController@importExcel');

        Route::get('admin-search-for-select2', '\App\CRMEdu\Controllers\Admin\LeadBepController@adminSearchForSelect2')->middleware('permission:lead_edit');

        Route::post('ajax-update', '\App\CRMEdu\Controllers\Admin\LeadBepController@ajaxUpdate')->middleware('permission:lead_edit');
        Route::post('assign', '\App\CRMEdu\Controllers\Admin\LeadBepController@leadAssign')->middleware('permission:lead_assign');

        Route::get('', '\App\CRMEdu\Controllers\Admin\LeadBepController@getIndex')->name('lead_bep')->middleware('permission:lead_view');
        Route::get('/tha-noi', '\App\CRMEdu\Controllers\Admin\LeadBepController@getIndex')->name('lead_bep')->middleware('permission:lead_view');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\LeadBepController@getPublish')->name('lead_bep.publish')->middleware('permission:lead_edit');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\LeadBepController@add')->middleware('permission:lead_add');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\LeadBepController@delete')->middleware('permission:lead_delete');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\LeadBepController@multiDelete')->middleware('permission:lead_delete');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\LeadBepController@searchForSelect2')->name('lead_bep.search_for_select2')->middleware('permission:lead_view');
        Route::get('edit', '\App\CRMEdu\Controllers\Admin\LeadBepController@update')->middleware('permission:lead_edit');
        Route::post('edit', '\App\CRMEdu\Controllers\Admin\LeadBepController@update')->middleware('permission:lead_edit');

    });


    // đào tạo
    Route::group(['prefix' => 'course'], function () {
        Route::get('view', '\App\CRMEdu\Controllers\Admin\CourseController@getView')->middleware('permission:course_view');
    });
    Route::group(['prefix' => 'course'], function () {
        Route::get('view/{category_id}', '\App\CRMEdu\Controllers\Admin\CourseController@view')->middleware('permission:course_view');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\CourseController@getPublish')->name('course.publish')->middleware('permission:course');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\CourseController@add')->middleware('permission:course');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\CourseController@delete')->middleware('permission:course');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\CourseController@multiDelete')->middleware('permission:course');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\CourseController@searchForSelect2')->name('course.search_for_select2')->middleware('permission:course_view');
        Route::get('{id}', '\App\CRMEdu\Controllers\Admin\CourseController@update')->middleware('permission:course');
        Route::post('{id}', '\App\CRMEdu\Controllers\Admin\CourseController@update')->middleware('permission:course');
    });
    Route::group(['prefix' => 'category_course'], function () {
        Route::get('', '\App\CRMEdu\Controllers\Admin\CategoryCourseController@getIndex')->middleware('permission:course');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\CategoryCourseController@getPublish')->name('category_course.publish')->middleware('permission:course');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\CategoryCourseController@add')->middleware('permission:course');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\CategoryCourseController@delete')->middleware('permission:course');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\CategoryCourseController@multiDelete')->middleware('permission:course');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\CategoryCourseController@searchForSelect2')->name('category_course.search_for_select2')->middleware('permission:course');
        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\CategoryCourseController@update')->middleware('permission:course');
        Route::post('edit/{id}', '\App\CRMEdu\Controllers\Admin\CategoryCourseController@update')->middleware('permission:course');
    });

    // Tài liệu giảng dạy
    Route::group(['prefix' => 'document'], function () {
        Route::get('view/{category_id}', '\App\CRMEdu\Controllers\Admin\DocumentController@view')->middleware('permission:document_view');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\DocumentController@getPublish')->name('document.publish')->middleware('permission:document');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\DocumentController@add')->middleware('permission:document');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\DocumentController@delete')->middleware('permission:document');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\DocumentController@multiDelete')->middleware('permission:document');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\DocumentController@searchForSelect2')->name('document.search_for_select2')->middleware('permission:document_view');
        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\DocumentController@update')->middleware('permission:document');
        Route::post('edit/{id}', '\App\CRMEdu\Controllers\Admin\DocumentController@update')->middleware('permission:document');
    });
    Route::group(['prefix' => 'category_document'], function () {
        Route::get('', '\App\CRMEdu\Controllers\Admin\CategoryDocumentController@getIndex')->middleware('permission:document');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\CategoryDocumentController@getPublish')->name('category_document.publish')->middleware('permission:document');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\CategoryDocumentController@add')->middleware('permission:document');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\CategoryDocumentController@delete')->middleware('permission:document');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\CategoryDocumentController@multiDelete')->middleware('permission:document');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\CategoryDocumentController@searchForSelect2')->name('category_document.search_for_select2')->middleware('permission:document');
        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\CategoryDocumentController@update')->middleware('permission:document');
        Route::post('edit/{id}', '\App\CRMEdu\Controllers\Admin\CategoryDocumentController@update')->middleware('permission:document');
    });


    //  chấm công
    Route::group(['prefix' => 'timekeeping'], function () {
        Route::get('', '\App\CRMEdu\Controllers\Admin\TimekeepingController@getIndex')->middleware('permission:timekeeping_view');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\TimekeepingController@getPublish')->name('timekeeping.publish')->middleware('permission:timekeeping_edit');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\TimekeepingController@add')->middleware('permission:timekeeping_add');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\TimekeepingController@delete')->middleware('permission:timekeeping_delete');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\TimekeepingController@multiDelete')->middleware('permission:timekeeping_delete');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\TimekeepingController@searchForSelect2')->name('timekeeping.search_for_select2')->middleware('permission:timekeeping_view');
        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\TimekeepingController@update')->middleware('permission:timekeeping_edit');
        Route::post('edit/{id}', '\App\CRMEdu\Controllers\Admin\TimekeepingController@update')->middleware('permission:timekeeping_edit');
    });


    //  CSKH
    Route::group(['prefix' => 'cskh-bill'], function () {

        Route::get('', '\App\CRMEdu\Controllers\Admin\CSKHBillController@getIndex')->name('cskh-bill')->middleware('permission:cskh-bill_view');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\CSKHBillController@getPublish')->name('cskh-bill.publish')->middleware('permission:bill_publish');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\CSKHBillController@searchForSelect2')->name('cskh-bill.search_for_select2')->middleware('permission:cskh-bill_view');
        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\CSKHBillController@update')->middleware('permission:cskh-bill_view');
        Route::get('{id}/bo-cham-soc-lan-nay', '\App\CRMEdu\Controllers\Admin\CSKHBillController@boChamSocLanNay')->middleware('permission:cskh-bill_view');


    });

    //  Gia hạn HĐ
    Route::group(['prefix' => 'gh-bill'], function () {

        Route::get('', '\App\CRMEdu\Controllers\Admin\GHBillController@getIndex')->name('gh-bill')->middleware('permission:cskh-bill_view');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\GHBillController@searchForSelect2')->name('gh-bill.search_for_select2')->middleware('permission:cskh-bill_view');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\BillController@getPublish')->name('bill.publish')->middleware('permission:bill_publish');
        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\GHBillController@update')->middleware('permission:cskh-bill_view');
    });

    //  Trợ giảng
    Route::group(['prefix' => 'dhbill'], function () {

        Route::get('', '\App\CRMEdu\Controllers\Admin\DHBillController@getIndex')->name('dh_bill')->middleware('permission:dhbill_view');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\DHBillController@getPublish')->name('dh_bill.publish')->middleware('permission:dhbill_publish');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\DHBillController@add')->middleware('permission:dhbill_add');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\DHBillController@delete')->middleware('permission:dhbill_delete');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\DHBillController@multiDelete')->middleware('permission:dhbill_delete');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\DHBillController@searchForSelect2')->name('dh_bill.search_for_select2')->middleware('permission:dhbill_view');
        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\DHBillController@update')->middleware('permission:dhbill_view');
        Route::post('edit/{id}', '\App\CRMEdu\Controllers\Admin\DHBillController@update')->middleware('permission:dhbill_edit');

    });

    //  Lịch sử thay đổi triển khai hợp đồng
    Route::group(['prefix' => 'bill_progress_history'], function () {

        Route::get('', '\App\CRMEdu\Controllers\Admin\BillProgressHistoryController@getIndex')->name('bill_progress_history');
        Route::get('ajax-lich-su-trang-thai', '\App\CRMEdu\Controllers\Admin\BillProgressHistoryController@ajaxLichSuTrangThai');
        Route::get('ajax-load-table-basic-data', '\App\CRMEdu\Controllers\Admin\BillProgressHistoryController@ajaxLoadTableBasicData');
    });

    //  Trưởng phòng sale
    Route::group(['prefix' => 'tpbill'], function () {

        Route::get('', '\App\CRMEdu\Controllers\Admin\TPBillController@getIndex')->name('dh_bill')->middleware('permission:truong_phong');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\TPBillController@searchForSelect2')->name('dh_bill.search_for_select2')->middleware('permission:dhbill_view');
        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\TPBillController@update')->middleware('permission:truong_phong');
        Route::post('edit/{id}', '\App\CRMEdu\Controllers\Admin\TPBillController@update')->middleware('permission:truong_phong');

    });

    //  kế hoạch plan
    Route::group(['prefix' => 'plan'], function () {

        Route::get('', '\App\CRMEdu\Controllers\Admin\PlanController@getIndex')->name('dh_bill')->middleware('permission:plan_view');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\PlanController@getPublish')->name('dh_bill.publish')->middleware('permission:plan_publish');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\PlanController@add')->middleware('permission:plan_add');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\PlanController@delete')->middleware('permission:plan_delete');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\PlanController@multiDelete')->middleware('permission:plan_delete');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\PlanController@searchForSelect2')->name('dh_bill.search_for_select2')->middleware('permission:plan_view');
        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\PlanController@update')->middleware('permission:plan_view');
        Route::post('edit/{id}', '\App\CRMEdu\Controllers\Admin\PlanController@update')->middleware('permission:plan_edit');

    });


    //  phiếu thu
    Route::group(['prefix' => 'bill_receipts'], function () {

        Route::get('', '\App\CRMEdu\Controllers\Admin\BillReceiptsController@getIndex')->name('dh_bill')->middleware('permission:bill_view');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\BillReceiptsController@getPublish')->name('dh_bill.publish')->middleware('permission:receipts_publish');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\BillReceiptsController@add')->middleware('permission:bill_add');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\BillReceiptsController@delete')->middleware('permission:bill_delete');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\BillReceiptsController@multiDelete')->middleware('permission:bill_delete');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\BillReceiptsController@searchForSelect2')->name('dh_bill.search_for_select2')->middleware('permission:plan_view');
        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\BillReceiptsController@update')->middleware('permission:bill_view');
        Route::post('edit/{id}', '\App\CRMEdu\Controllers\Admin\BillReceiptsController@update')->middleware('permission:bill_edit');

    });

    //  Dữ liệu chấm công
    Route::group(['prefix' => 'timekeeper'], function () {

        Route::match(array('GET', 'POST'), 'import-excel', '\App\CRMEdu\Controllers\Admin\TimekeeperController@importExcel')->middleware('permission:timekeeper_edit');

        Route::get('', '\App\CRMEdu\Controllers\Admin\TimekeeperController@getIndex')->name('timekeeper')->middleware('permission:timekeeper_view');
        Route::get('publish', '\App\CRMEdu\Controllers\Admin\TimekeeperController@getPublish')->name('timekeeper.publish')->middleware('permission:timekeeper_edit');
        Route::get('bao-cao', '\App\CRMEdu\Controllers\Admin\TimekeeperController@baoCao')->middleware('permission:timekeeper_edit');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\TimekeeperController@add')->middleware('permission:timekeeper_edit');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\TimekeeperController@delete')->middleware('permission:timekeeper_edit');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\TimekeeperController@multiDelete')->middleware('permission:timekeeper_edit');
        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\TimekeeperController@update')->middleware('permission:timekeeper_view');
        Route::post('edit/{id}', '\App\CRMEdu\Controllers\Admin\TimekeeperController@update')->middleware('permission:timekeeper_edit');

    });

    //  phiếu phạt
    Route::group(['prefix' => 'penalty_ticket'], function () {
        Route::get('', '\App\CRMEdu\Controllers\Admin\PenaltyTicketController@getIndex')->name('penalty_ticket');
        Route::match(array('GET', 'POST'), 'add', '\App\CRMEdu\Controllers\Admin\PenaltyTicketController@add')->middleware('permission:penalty_ticket');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\PenaltyTicketController@delete')->middleware('permission:super_admin');
        Route::post('multi-delete', '\App\CRMEdu\Controllers\Admin\PenaltyTicketController@multiDelete')->middleware('permission:super_admin');
        Route::get('search-for-select2', '\App\CRMEdu\Controllers\Admin\PenaltyTicketController@searchForSelect2')->name('penalty_ticket.search_for_select2');

        Route::get('publish', '\App\CRMEdu\Controllers\Admin\PenaltyTicketController@getPublish')->name('penalty_ticket.publish');

        Route::get('edit/{id}', '\App\CRMEdu\Controllers\Admin\PenaltyTicketController@update')->middleware('permission:penalty_ticket');
        Route::post('edit/{id}', '\App\CRMEdu\Controllers\Admin\PenaltyTicketController@update')->middleware('permission:penalty_ticket');
    });

    //  Báo cáo website lỗi
    Route::group(['prefix' => 'check_error_link_logs'], function () {
        Route::get('', '\App\CRMEdu\Controllers\Admin\DomainErrorLogController@getIndex')->name('check_error_link_logs');
        Route::get('delete/{id}', '\App\CRMEdu\Controllers\Admin\DomainErrorLogController@delete')->middleware('permission:check_error_link_logs');

        Route::get('publish', '\App\CRMEdu\Controllers\Admin\DomainErrorLogController@getPublish')->name('check_error_link_logs.publish');
    });
});

Route::get('/admin/lead/edit', '\App\CRMEdu\Controllers\Admin\LeadController@update')->middleware('get_permissions');

Route::get('admin/lead/view', '\App\CRMEdu\Controllers\Admin\LeadController@view');
Route::get('demo-wordpress', '\App\CRMEdu\Controllers\Admin\BillController@demoWordpress');
Route::get('demo-ldp', '\App\CRMEdu\Controllers\Admin\BillController@demoLdp');
Route::get('check-exist', '\App\CRMEdu\Controllers\Admin\AdminController@checkExist');
Route::get('bill/get-nv-xuat-sac', '\App\CRMEdu\Controllers\Admin\BillController@getBestSale');

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