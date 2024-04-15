<?php

namespace App\CRMEdu\Controllers\Admin;

use App\CRMEdu\Models\BillReceipts;
use App\CRMEdu\Models\Tag;
use App\Http\Helpers\CommonHelper;
use App\Mail\MailServer;
use App\Models\Admin;
use App\Models\RoleAdmin;
use App\Models\Setting;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use App\CRMEdu\Controllers\Helpers\CRMEduHelper;
use App\CRMEdu\Models\Bill;
use App\CRMEdu\Models\BillHistory;
use App\CRMEdu\Models\BillFinance;
use App\CRMEdu\Models\BillProgress;
use App\CRMEdu\Models\Service;
use Validator;
use Cache;

class BillController extends CURDBaseController
{

    protected $orderByRaw = 'status DESC, id DESC';

//    protected $whereRaw = 'service_id IN (1, 17, 18, 19, 20, 21)';

    protected $module = [
        'code' => 'bill',
        'table_name' => 'bills',
        'label' => 'Hợp đồng',
        'modal' => '\App\CRMEdu\Models\Bill',
        'list' => [
//            ['name' => 'domain', 'type' => 'text_edit', 'label' => 'Tên miền', 'sort' => true],
            ['name' => 'customer_id', 'type' => 'relation_edit', 'label' => 'CRMEdu_admin.bills_client', 'object' => 'user', 'display_field' => 'name'],
            ['name' => 'total_price', 'type' => 'price_vi', 'label' => 'CRMEdu_admin.bills_total_price', 'sort' => true],
            ['name' => 'total_price_contract', 'type' => 'price_vi', 'label' => 'CRMEdu_admin.bills_total_money'],
            ['name' => 'total_received', 'type' => 'price_vi', 'label' => 'CRMEdu_admin.bills_money_received'],
            ['name' => 'finance_id', 'type' => 'custom', 'td' => 'CRMEdu.dhbill.list.td.chua_thu', 'label' => 'CRMEdu_admin.bills_uncollected_money'],
            ['name' => 'service_id', 'type' => 'relation', 'label' => 'CRMEdu_admin.service', 'object' => 'service', 'display_field' => 'name_vi', 'sort' => true],
//            ['name' => 'count_product', 'type' => 'custom', 'td' => 'CRMEdu.list.td.count_product', 'label' => 'Tổng SP'],
            ['name' => 'registration_date', 'type' => 'date_vi', 'label' => 'CRMEdu_admin.bills_contract_signing_date', 'sort' => true],
//            ['name' => 'expiry_date', 'type' => 'date_vi', 'label' => 'Ngày hết hạn', 'sort' => true],
            // ['name' => 'exp_price', 'type' => 'price_vi', 'label' => 'Giá gia hạn', 'sort' => true],
            // ['name' => 'expiry_date', 'type' => 'date_vi', 'label' => 'Hết hạn', 'sort' => true],
//            ['name' => 'auto_extend', 'type' => 'custom', 'td' => 'CRMEdu.list.td.status', 'label' => 'Tự động gia hạn', 'options' => [
//                    0 => 'Không kích hoạt',
//                    1 => 'Kích hoạt',
//                ], 'sort' => true
//            ],
            ['name' => 'status', 'type' => 'custom', 'td' => 'CRMEdu.list.td.status', 'label' => 'CRMEdu_admin.bills_status', 'options' => [
                    0 => 'Không kích hoạt',
                    1 => 'Kích hoạt',
                ], 'sort' => true
            ],
            ['name' => 'dating', 'type' => 'date_vi', 'label' => 'CRMEdu_admin.bills_dating', 'sort' => true],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'total_price', 'type' => 'price_vi', 'class' => 'required', 'label' => 'CRMEdu_admin.bills_total_price', 'group_class' => 'col-md-4'],
                ['name' => 'total_price_contract', 'type' => 'price_vi', 'class' => 'required', 'label' => 'CRMEdu_admin.bills_total_price_contract', 'group_class' => 'col-md-4'],
//                ['name' => 'domain', 'type' => 'text', 'label' => 'Tên miền', 'group_class' => 'col-md-4'],
                ['name' => 'service_id', 'type' => 'select2_model', 'class' => '',  'label' => 'CRMEdu_admin.service', 'multiple' => true,
                    'model' => Service::class, 'display_field' => 'name_vi', 'group_class' => 'col-md-4'],
                ['name' => 'registration_date', 'type' => 'date', 'label' => 'CRMEdu_admin.bills_registration_date', 'class' => 'required',
                    'value' => 'now', 'group_class' => 'col-md-3'],
                ['name' => 'contract_time', 'type' => 'number', 'label' => 'CRMEdu_admin.contract_time', 'class' => '', 'group_class' => 'col-md-2'],

                ['name' => 'status', 'type' => 'checkbox', 'label' => 'CRMEdu_admin.bills_activated', 'value' => 1, 'group_class' => 'col-md-4'],
                ['name' => 'dating', 'type' => 'date', 'label' => 'CRMEdu_admin.bills_dating', 'class' => '', 'group_class' => 'col-md-3'],
                ['name' => 'note', 'type' => 'textarea', 'label' => 'CRMEdu_admin.bills_note', 'group_class' => 'col-md-12'],

                
                
                // ['name' => 'curator_ids', 'type' => 'select2_ajax_model', 'label' => 'Người KH phụ trách', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'email', 'multiple' => true, 'group_class' => 'col-md-6'],
                // ['name' => 'staff_care', 'type' => 'select2_ajax_model', 'label' => 'Nhân viên phụ trách', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'email', 'multiple' => true, 'group_class' => 'col-md-6'],
                /*['name' => 'retention_time', 'type' => 'select', 'options' =>
                    [
                        0 => 'Không bảo hành',
                        1 => '1 tháng',
                        3 => '3 tháng',
                        6 => '6 tháng',
                        8 => '8 tháng',
                        12 => '12 tháng',
                        36 => '36 tháng',
                    ], 'class' => '', 'label' => 'Thời hạn duy trì', 'value' => 5, 'group_class' => 'col-md-6'],*/

            ],
            'customer_tab' => [
                ['name' => 'marketer_ids', 'type' => 'select2_ajax_model', 'label' => 'CRMEdu_admin.bills_marketing', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'code', 'multiple' => true, 'group_class' => 'col-md-6'],
                ['name' => 'saler_id', 'type' => 'custom', 'field' => 'CRMEdu.form.fields.select_sale','label' => 'CRMEdu_admin.bills_sale', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'code', 'class' => 'required'],
                ['name' => 'customer_id', 'type' => 'custom', 'type_history' => 'relation_multiple', 'field' => 'CRMDV.form.fields.select_customer',
                    'label' => 'Khách hàng', 'model' => User::class, 'object' => 'user', 'display_field' => 'name', 'display_field2' => 'tel', 'class' => 'required'],
                ['name' => 'customer_legal_id', 'type' => 'custom', 'type_history' => 'relation_multiple', 'field' => 'CRMDV.form.fields.select_customer',
                    'label' => 'Đại diện pháp lý', 'model' => User::class, 'object' => 'user', 'display_field' => 'name', 'display_field2' => 'tel', 'class' => ''],
            ],
            'gia_han_tab' => [
//                ['name' => 'expiry_date', 'type' => 'date', 'field' => 'CRMEdu.form.fields.expiry_date', 'label' => 'Ngày hết hạn', 'class' => '', 'group_class' => 'col-md-6', 'inner' => ''],
//                ['name' => 'exp_price', 'type' => 'price_vi', 'class' => ' required', 'label' => 'Giá gia hạn', 'group_class' => 'col-md-6', 'des' => 'Thuê hosting bên mình thì. 1,4tr cho 3G, 1,76tr cho 6G'],
//                ['name' => 'auto_extend', 'type' => 'checkbox', 'label' => 'Kích hoạt tự động gia hạn', 'value' => 1, 'group_class' => 'col-md-6'],
            ],
            'domain_tab' => [

            ],
            'hosting_tab' => [

            ],
            'ldp_tab' => [

            ],
            'wp_tab' => [

                ],
            'service_tab' => [
//                ['name' => 'status', 'type' => 'checkbox', 'label' => 'Kích hoạt', 'value' => 1, ],
//                ['name' => 'note', 'type' => 'textarea', 'label' => 'Ghi chú'],
//                ['name' => 'customer_note', 'type' => 'textarea', 'class' => 'required', 'label' => 'Ghi chú của Khách'],
            ],
            'account_tab' => [

            ],
        ],
    ];

    protected $filter = [
        'marketer_ids' => [
            'label' => 'Nguồn marketing',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'display_field2' => 'code',
            'model' => \App\Models\Admin::class,
            'object' => 'admin',
            'query_type' => 'custom'
        ],
        'saler_id' => [
            'label' => 'Sale',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'display_field2' => 'code',
            'model' => \App\Models\Admin::class,
            'object' => 'admin',
            'query_type' => '='
        ],
        'service_id' => [
            'label' => 'Khóa học',
            'type' => 'select2_model',
            'display_field' => 'name_vi',
            'model' => Service::class,
            'query_type' => '='
        ],
        /*'total_price' => [
            'label' => 'Tổng tiền',
            'type' => 'number',
            'query_type' => 'like'
        ],*/
        'filter_date' => [
            'label' => 'Lọc theo',
            'type' => 'filter_date',
            'options' => [
                '' => '',
                'created_at' => 'Ngày tạo',
                'expiry_date' => 'Hết hạn',
                'registration_date' => 'Ngày ký HĐ',
            ],
            'query_type' => 'filter_date'
        ],
        'auto_extend' => [
            'label' => 'Tự động gia hạn',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => '',
                0 => 'Không kích hoạt',
                1 => 'Kích hoạt',
            ],
        ],
        'status' => [
            'label' => 'Trạng thái',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => 'Tất cả',
                0 => 'Không kích hoạt',
                1 => 'Kích hoạt',
            ],
        ],
    ];

    protected $quick_search = [
        'label' => 'ID, tên miền, giá, note',
        'fields' => 'id, exp_price, total_price, note'
    ];

    public function getIndex(Request $request)
    {
        
        $data = $this->getDataList($request);

        return view('CRMEdu.list')->with($data);
    }

    public function quickSearch($listItem, $r) {
        if (@$r->quick_search != '') {
            $listItem = $listItem->where(function ($query) use ($r) {
                foreach (explode(',', $this->quick_search['fields']) as $field) {
                    $query->orWhere(trim($field), 'LIKE', '%' . $r->quick_search . '%');    //  truy vấn các tin thuộc các danh mục con của danh mục hiện tại
                }

                //  Tìm kiếm nhanh theo khách hàng
                $customer_ids = Admin::select('id')->where(function ($query) use ($r) {
                    $query->orWhere('name', 'like', "%".$r->quick_search."%");
                    $query->orWhere('tel', 'like', "%".$r->quick_search."%");
                    $query->orWhere('email', 'like', "%".$r->quick_search."%");
                })->pluck('id')->toArray();
//                dd($customer_ids);

                $query->orWhereIn('customer_id', $customer_ids);
            });

        }

        return $listItem;
    }


    public function getDataList(Request $request) {
        //  Filter
        $where = $this->filterSimple($request);
        $listItem = $this->model->whereRaw($where);
        $listItem = $this->quickSearch($listItem, $request);
        if ($this->whereRaw) {
            $listItem = $listItem->whereRaw($this->whereRaw);
        }
        $listItem = $this->appendWhere($listItem, $request);

        //  Export
        if ($request->has('export')) {
            $this->exportExcel($request, $listItem->get());
        }

        //  Sort
        $listItem = $this->sort($request, $listItem);

        $data['record_total'] = $listItem->count();

        $data['doanh_so'] = $listItem->sum('total_price');
        $data['total_received'] = $listItem->sum('total_received');
        $data['total_price_contract'] = $listItem->sum('total_price_contract');

        if ($request->has('limit')) {
            $data['listItem'] = $listItem->paginate($request->limit);
            $data['limit'] = $request->limit;
        } else {
            $data['listItem'] = $listItem->paginate($this->limit_default);
            $data['limit'] = $this->limit_default;
        }
        $data['page'] = $request->get('page', 1);

        $data['param_url'] = $request->all();

        //  Get data default (param_url, filter, module) for return view
        $data['module'] = $this->module;
        $data['quick_search'] = $this->quick_search;
        $data['filter'] = $this->filter;

        //  Set data for seo
        $data['page_title'] = $this->module['label'];
        $data['page_type'] = 'list';
        return $data;
    }

    public function appendWhere($query, $request)
    {
        if (CRMEduHelper::getRoleType(\Auth::guard('admin')->user()->id) == 'customer') {
            //  Nếu là khách hàng thì chỉ truy vấn ra đơn hàng của mình
            $query = $query->where('customer_id', \Auth::guard('admin')->user()->id);
        }
        if (@$request->marketer_ids != null) {
            $query = $query->where('marketer_ids', 'like', '%|' . $request->marketer_ids . '|%');
        }

        if (\Auth::guard('admin')->user()->super_admin != 1) {
            //  Nếu ko phải super_admin thì truy vấn theo dữ liệu cty đó
            // $query = $query->where('company_id', \Auth::guard('admin')->user()->last_company_id);
        }

        //  Chỉ truy vấn ra các đơn hàng của mình
        if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'view_all_data')) {
            $query = $query->where('saler_id', \Auth::guard('admin')->user()->id);
        }

        return $query;
    }

    public function add(Request $request)
    {
        /*$billHistory=new BillHistory();
        $billHistory->bill_id=$request->id;
        $billHistory->price=$request->exp_price;
        $billHistory->expiry_date=$request->expiry_date;
        $billHistory->save();*/

        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMEdu.add')->with($data);
            }

            \DB::beginTransaction();

            $data = $this->processingValueInFields($request, $this->getAllFormFiled());
            //  Tùy chỉnh dữ liệu insert

            $customer = Admin::find($data['customer_id']);
            // $data['customer_name'] = $customer->name;
            // $data['customer_tel'] = $customer->tel;
            // $data['customer_email'] = $customer->email;
            // $data['customer_address'] = $customer->address;
            $data['curator_ids'] = '|' . implode('|', $request->get('curator_ids', [])) . '|';
            $data['staff_care'] = '|' . implode('|', $request->get('staff_care', [])) . '|';
            $data['marketer_ids'] = '|' . implode('|', $request->get('marketer_ids', [])) . '|';
            if ($request->service_id != null) {
                $data['service_id'] = '|' . implode('|', $request->get('service_id', [])) . '|';
            }



            if ($request->contract_time != null) {
                //  Tính thời gian hết hạn hợp đồng
                $data['expiry_date'] = date('Y-m-d', strtotime('+'.$request->contract_time.' month', strtotime($data['registration_date'])));
            }

            // $data['company_id'] = \Auth::guard('admin')->user()->last_company_id;

            if ($request->has('bill_parent')) $data['bill_parent'] = $request->bill_parent;

            unset($data['file_ldp']);
            if (isset($data['quick_note'])) unset($data['quick_note']);
            #
            foreach ($data as $k => $v) {
                $this->model->$k = $v;
            }

            if ($this->model->save()) {
                $this->afterAddLog($request, $this->model);

                //  Nếu không nằm trong DV mua tài nguyên thì tạo phiếu triển khai tự động
                if (!in_array($this->model->service_id, [
                    2,  // mua hosting
                    3,  // mua tên miền
                    4,  // mua mail
                    7,  // duy trì web
                    8,  // nâng cấp hosting
                ])) {
                    BillProgress::updateOrCreate([
                        'bill_id' => $this->model->id,
                    ],[
                        // 'status' => $request->progress_status,
                        // 'yctk' => $request->progress_yctk,
                    ]);
                }

                //  Cập nhật tiền đự án
                BillFinance::updateOrCreate([
                    'bill_id' => $this->model->id,
                ],[
                    // 'debt' => $request->finance_debt,
                    // 'received' => $request->finance_received,
                    // 'total' => $request->finance_total,
                    'detail' => $request->finance_detail,
                ]);

                //  Cập nhật thể loại khách hàng
                Admin::where('id', $this->model->customer_id)->update([
                    'classify' => $request->customer_classify,
                ]);

                

                // //  nếu thuê tên miền bên mình thì tạo 1 hóa đơn cho tên miền đó
                // if ($data['domain_owner'] == 'hobasoft') {
                //     $this->createBillForDomain($data, $this->model);
                // }

                // //  nếu thuê hosting bên mình thì tạo 1 hóa đơn cho hosting đó
                // if ($data['hosting_owner'] == 'hobasoft') {
                //     $this->createBillForHosting($data, $this->model);
                // }

                CommonHelper::flushCache($this->module['table_name']);
                \DB::commit();
                CommonHelper::one_time_message('success', 'Tạo mới thành công!');
            } else {
                \DB::rollback();
                CommonHelper::one_time_message('error', 'Lỗi tao mới. Vui lòng load lại trang và thử lại!');
            }

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'msg' => '',
                    'data' => $this->model
                ]);
            }

            // if ($request->return_direct == 'save_exit') {
            //     return redirect('admin/' . $this->module['code']);
            // } elseif ($request->return_direct == 'save_create') {
            //     return redirect('admin/' . $this->module['code'] . '/add');
            // }

            return redirect('admin/' . $this->module['code'] . '/edit/' . $this->model->id);
        } catch (\Exception $ex) {
            \DB::rollback();
            CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function update(Request $request)
    {
        try {
            $item = $this->model->find($request->id);

            //  Khách hàng ko được xem bản ghi của người khác
            if (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['customer', 'customer_ldp_vip'])
                && $item->customer_id != \Auth::guard('admin')->user()->id) {
                CommonHelper::one_time_message('error', 'Bạn không có quyền!');
                return back();
            }

            if (!is_object($item)) abort(404);

            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);

                return view('CRMEdu.edit')->with($data);
            }


            \DB::beginTransaction();
            
            $data = $this->processingValueInFields($request, $this->getAllFormFiled());

            //  Tùy chỉnh dữ liệu insert
            //  Khách hàng tự sửa hóa đơn của mình
            if (CRMEduHelper::getRoleType(\Auth::guard('admin')->user()->id) == 'customer') {
                unset($data['customer_id']);
            }

            // //  nếu thuê hosting bên mình thì tạo 1 hóa đơn cho hosting đó
            // if ($data['hosting_owner'] == 'hobasoft') {
            //     $this->createBillForHosting($data, $item);
            // }

            //  Lấy người phụ trách
            $data['curator_ids'] = '|' . implode('|', $request->get('curator_ids', [])) . '|';
            $data['staff_care'] = '|' . implode('|', $request->get('staff_care', [])) . '|';
            $data['marketer_ids'] = '|' . implode('|', $request->get('marketer_ids', [])) . '|';
            $data['service_id'] = '|' . implode('|', $request->get('service_id', [])) . '|';

            unset($data['file_ldp']);

            if (isset($data['quick_note'])) unset($data['quick_note']);


            foreach ($data as $k => $v) {
                $item->$k = $v;
            }
            if ($item->save()) {

                //  Cập nhật tiến độ dự án
                BillProgress::updateOrCreate([
                    'bill_id' => $item->id,
                ],[
                    // 'status' => $request->progress_status,
                    // 'yctk' => $request->progress_yctk,
                ]);

                //  Cập nhật tiền đự án
                BillFinance::updateOrCreate([
                    'bill_id' => $item->id,
                ],[
                    // 'debt' => $request->finance_debt,
                    // 'received' => $request->finance_received,
                    // 'total' => $request->finance_total,
                    'detail' => $request->finance_detail,
                ]);

                //  Cập nhật thể loại khách hàng
                Admin::where('id', $item->customer_id)->update([
                    'classify' => $request->customer_classify,
                ]);

                /*$expiry_date_db = Bill::find($request->id)->expiry_date;
                $price_db = Bill::find($request->id)->exp_price;
                if ($expiry_date_db != $request->expiry_date) {
                    $billHistory = new BillHistory();
                    $billHistory->bill_id = $request->id;
                    $billHistory->price = $price_db;
                    $billHistory->expiry_date = $expiry_date_db;

                    $billHistory->save();
                }*/

                if ($request->log_name != null || $request->log_note != null) {
                    //  Nếu có viết vào lịch sử tư vấn thì tạo lịch sử tư vấn
                    $leadController = new LeadController();
                    $leadController->LeadContactedLog([
                        'title' => $request->log_name,
                        'note' => $request->log_note,
                        'lead_id' => $item->id,
                        'type' => 'hđ',
                    ]);
                }

                CommonHelper::flushCache($this->module['table_name']);
                \DB::commit();

                if ($request->return_direct == 'mail_ban_giao_ldp') {
                    //  Gửi mail bàn giao LDP
                    $this->sendMailBanGiao($item, 1);

                    $item->handover_landingpage ++;

                    $item->save();

                    CommonHelper::one_time_message('success', 'Bàn giao thành công!');
                    return redirect('admin/' . $this->module['code'] . '/edit/' . $item->id);
                }

                if ($request->return_direct == 'mail_ban_giao_wp') {
                    //  Gửi mail bàn giao LDP
                    $this->sendMailBanGiao($item, 5);

                    $item->handover_wp ++;

                    $item->save();

                    CommonHelper::one_time_message('success', 'Bàn giao thành công!');
                    return redirect('admin/' . $this->module['code'] . '/edit/' . $item->id);
                }

                CommonHelper::one_time_message('success', 'Cập nhật thành công!');
            } else {
                \DB::rollback();
                CommonHelper::one_time_message('error', 'Lỗi cập nhật. Vui lòng load lại trang và thử lại!');
            }
            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'msg' => '',
                    'data' => $item
                ]);
            }

            if ($request->return_direct == 'save_continue') {
                return redirect('admin/' . $this->module['code'] . '/edit/' . $item->id);
            } elseif ($request->return_direct == 'save_create') {
                return redirect('admin/' . $this->module['code'] . '/add');
            }

            return redirect('admin/' . $this->module['code']);

        } catch (\Exception $ex) {
            \DB::rollback();
            dd($ex->getMessage());
//            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            return redirect()->back()->withInput();
        }
    }

    public function getContentEmailBanGiao($bill, $camp) {
        return $this->processContentMail($camp->email_template->content, $bill);
    }

    public function sendMailBanGiao($bill, $camp_id = 1) {
        //  Gửi mail cho khách
        $camp = MarketingMail::find($camp_id);
        $this->_mailSetting = Setting::whereIn('type', ['mail'])->pluck('value', 'name')->toArray();

        $user = (object)[
            'email' => $bill->customer_email == null ? $bill->user->email : $bill->customer_email,
            'name' => $bill->customer_name == null ? $bill->user->name : $bill->customer_name,
            'id' => $bill->customer_id
        ];
        $data = [
            'sender_account' => $camp->email_account,
            'user' => $user,
            'subject' => $camp->subject,
            'content' => $this->getContentEmailBanGiao($bill, $camp)
        ];

        \Mail::to($data['user'])->send(new MailServer($data));
    }

    public function processContentMail($html, $bill)
    {

        return $html;
    }

    public function getPublish(Request $request)
    {
        try {


            $id = $request->get('id', 0);
            $item = $this->model->find($id);

            // Không được sửa dữ liệu của cty khác, cty mình ko tham gia
//            if (strpos(Auth::guard('admin')->user()->company_ids, '|' . $item->company_id . '|') === false) {
//                return response()->json([
//                    'status' => false,
//                    'msg' => 'Bạn không có quyền xuất bản!'
//                ]);
//            }

            if (!is_object($item))
                return response()->json([
                    'status' => false,
                    'msg' => 'Không tìm thấy bản ghi'
                ]);

            if ($item->{$request->column} == 0)
                $item->{$request->column} = 1;
            else
                $item->{$request->column} = 0;

            $item->save();

            CommonHelper::flushCache($this->module['table_name']);

            return response()->json([
                'status' => true,
                'published' => $item->{$request->column} == 1 ? true : false
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'published' => null,
                'msg' => 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.'
            ]);
        }
    }

    public function cancelExtension(Request $request)
    {
        try {
            $item = $this->model->find($request->id);
            if (!is_object($item))
                return response()->json([
                    'status' => false,
                    'msg' => 'Không tìm thấy bản ghi'
                ]);
            $item->auto_extend = 0;

            $item->save();
            CommonHelper::flushCache($this->module['table_name']);
            return response()->json([
                'status' => true,
                'msg' => 'Hủy kích hoạt tự động gia hạn thành công!'
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'published' => null,
                'msg' => 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.'
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {


            $item = $this->model->find($request->id);

//            //  Không được xóa dữ liệu của cty khác, cty mình ko tham gia
//            if (strpos(Auth::guard('admin')->user()->company_ids, '|' . $item->company_id . '|') === false) {
//                CommonHelper::one_time_message('error', 'Bạn không có quyền xóa!');
//                return back();
//            }
            \DB::beginTransaction();
            BillReceipts::where('bill_id', $item->id)->delete();
            $item->delete();
            \DB::commit();

            CommonHelper::flushCache($this->module['table_name']);
            CommonHelper::one_time_message('success', 'Xóa thành công!');
            return redirect('admin/' . $this->module['code']);
        } catch (\Exception $ex) {
            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            \DB::rollback();
            return back();
        }
    }

    public function multiDelete(Request $request)
    {
        try {


            $ids = $request->ids;
            if (is_array($ids)) {
                $this->model->whereIn('id', $ids)->delete();
            }

            CommonHelper::flushCache($this->module['table_name']);
            CommonHelper::one_time_message('success', 'Xóa thành công!');
            return response()->json([
                'status' => true,
                'msg' => ''
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'msg' => 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên'
            ]);
        }
    }

    public function del(Request $request)
    {
        try {


            $del = BillHistory::find($request->id);
//            //  Không được xóa dữ liệu của cty khác, cty mình ko tham gia
//            if (strpos(Auth::guard('admin')->user()->company_ids, '|' . $item->company_id . '|') === false) {
//                CommonHelper::one_time_message('error', 'Bạn không có quyền xóa!');
//                return back();
//            }
            $del->delete();

            CommonHelper::flushCache($this->module['table_name']);
            CommonHelper::one_time_message('success', 'Xóa thành công!');
            return back();
        } catch (\Exception $ex) {
            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            return back();
        }
    }

    public function searchForSelect2(Request $request)
    {
        $data = $this->model->select([$request->col, 'id'])->where($request->col, 'like', '%' . $request->keyword . '%');
        if ($request->where != '') {
            $data = $data->whereRaw(urldecode(str_replace('&#039;', "'", $request->where)));
        }
        if (@$request->company_id != null) {
            $data = $data->where('company_id', $request->company_id);
        }

        //  Khách hàng ko được xem hóa đơn người khác
        if (CRMEduHelper::getRoleType(\Auth::guard('admin')->user()->id) == 'customer') {
            $data = $data->where('customer_id', \Auth::guard('admin')->user()->id);
        }

        $data = $data->limit(5)->get();
        return response()->json([
            'status' => true,
            'items' => $data
        ]);
    }

    public function getBestSale() {
        $bills = Bill::selectRaw('Sum(total_price) as total_price, saler_id')->where('registration_date', '>=', date('Y-m-01 00:00:00'))
                ->whereNotIn('saler_id', [170])->groupBy('saler_id')->orderBy('total_price', 'desc')->limit(1)->get();

        foreach($bills as $k => $bill) {
            $bills[$k] = [
                'name' => @$bill->saler->name,
                'id' => @$bill->saler_id,
                'image' => @$bill->saler->image,
                'phong' => 'Kinh doanh',
                'doanh_so' => @$bill->total_price
            ];
        }
        Setting::where('name', 'nv_xuat_sac')->update([
            'value' => json_encode($bills),
        ]);
        Cache::flush();
        die('Đã cập nhật nhân viên xuất sắc tháng');
    }

    public function test() {
        die('f');
        //  chuyển bill_finance sang bill
        $bill_finance = BillFinance::all();
        foreach($bill_finance as $v) {
            $bill = Bill::find($v->bill_id);
            if(is_object($bill)) {
                $bill->total_price_contract = $v->total;
                $bill->total_received = $v->received;
                // dd($bill);
                $bill->save();
            }
        }
        die('ok');
    }

    public function exportExcel($request, $data)
    {
        \Excel::create(str_slug($this->module['label'], '_') . '_' . date('d_m_Y'), function ($excel) use ($data) {

            // Set the title
            $excel->setTitle($this->module['label'] . ' ' . date('d m Y'));

            $excel->sheet(str_slug($this->module['label'], '_') . '_' . date('d_m_Y'), function ($sheet) use ($data) {

                $field_name = ['ID'];
                $field_name[] = 'Tên miền';
                $field_name[] = 'Ngày ký';
                $field_name[] = 'Doanh số';
                $field_name[] = 'Tổng tiền';
                $field_name[] = 'Đã thu';
                $field_name[] = 'Chưa thu';
                $field_name[] = 'Khoá học';
                $field_name[] = 'Tên khách';
                $field_name[] = 'SĐT';
                $field_name[] = 'Email';
                $field_name[] = 'Kinh doanh';
                $field_name[] = 'Tạo lúc';
                $field_name[] = 'Cập nhập lần cuối';

                $sheet->row(1, $field_name);

                $k = 2;

                foreach ($data as $value) {
                    $data_export = [];
                    $data_export[] = $value->id;
                    $data_export[] = date('d-m-Y', strtotime($value->registration_date));
                    $data_export[] = number_format($value->total_price, 0, '.', '.');
                    $data_export[] = number_format($value->total_price_contract, 0, '.', '.');
                    $data_export[] = number_format($value->total_received, 0, '.', '.');
                    $data_export[] = number_format($value->total_price_contract - $value->total_received, 0, '.', '.');
                    $data_export[] = @$value->service->name_vi;
                    $data_export[] = @$value->user->name;
                    $data_export[] = @$value->user->tel;
                    $data_export[] = @$value->user->mail;
                    $data_export[] = @$value->saler->name;
                    $data_export[] = @$value->created_at;
                    $data_export[] = @$value->updated_at;
                    // dd($this->getAllFormFiled());
                    $sheet->row($k, $data_export);
                    $k++;
                }
            });
        })->download('xlsx'); 
    }

    /**
     * Tối đa import được 999 dòng
     */
    public function importExcel(Request $r)
    {

        $table_import = $r->has('table') ? $r->table : $this->module['table_name'];
        $validator = Validator::make($r->all(), [
            'module' => 'required',
        ], [
            'module.required' => 'Bắt buộc phải nhập module!',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {

            $importController = new \App\Http\Controllers\Admin\ImportController();
            $data = $importController->processingValueInFields($r, $importController->getAllFormFiled());
            //  Tùy chỉnh dữ liệu insert

            if ($r->has('file')) {
                $file_name = $r->file('file')->getClientOriginalName();
                $file_name = str_replace(' ', '', $file_name);
                $file_name_insert = date('s_i_') . $file_name;
                $r->file('file')->move(base_path() . '/public_html/filemanager/userfiles/imports/', $file_name_insert);
                $data['file'] = 'imports/' . $file_name_insert;
            }

            unset($data['field_options_key']);
            unset($data['field_options_value']);
            #

            $item = new \App\Models\Import();
            foreach ($data as $k => $v) {
                $item->$k = $v;
            }
            if ($item->save()) {

                //  Import dữ liệu vào
                $importController->updateAttributes($r, $item);

                $this->processingImport($r, $item);

                // CommonHelper::flushCache($table_import);
                CommonHelper::one_time_message('success', 'Tạo mới thành công!');
                return redirect('/admin/import');
            } else {
                CommonHelper::one_time_message('error', 'Lỗi tao mới. Vui lòng load lại trang và thử lại!');
            }

            if ($r->ajax()) {
                return response()->json([
                    'status' => true,
                    'msg' => '',
                    'data' => $item
                ]);
            }

            return redirect('/admin/import');
        }
    }

    public function processingImport($r, $item)
    {

        $table_import = $r->has('table') ? $r->table : $this->module['table_name'];
        $record_total = $record_success = 0;
        $dataInsertFix =\App\Models\Attribute::where('table', $table_import)->where('type', 'field_options')->where('item_id', @$item->id)->pluck('value', 'key')->toArray();

        echo '<a style="padding: 20px; background-color: blue; color: #FFF; font-weight: bold;" href="/admin/lead/tha-noi">Quay lại</a><br><br><br>';

        $rates = Tag::where('type', 'lead_rate')->pluck('id', 'name')->toArray();
        $services = Service::pluck('name_vi', 'id')->toArray();

        \Excel::load('public_html/filemanager/userfiles/' . $item->file, function ($reader) use ($r, $dataInsertFix, &$record_total, &$record_success, $rates, $services) {

            $reader->each(function ($sheet) use ($r, $reader, $dataInsertFix, &$record_total, &$record_success, $rates, $services) {

                if ($reader->getSheetCount() == 1) {
                    echo '<br><hr>bắt đầu import sđt : ' . @$sheet->all()['sdt_khach'] . '<br>';
                    $result = $this->importItem($sheet, $r, $dataInsertFix, $rates, $services);
                    if (isset($result['msg'])) {
                        echo '&nbsp;&nbsp;&nbsp;&nbsp; => '.$result['msg'].'<br>';
                    }

                    if (@$result['status'] == true) {
                        $record_total++;
                    }
                    if (@$result['import'] == true) {
                        $record_success++;
                        echo '&nbsp;&nbsp;&nbsp;&nbsp;=> <span style="background: green; padding: 5px 10px;color: #fff;">Import thành công</span><br>';
                    }
                } else {

                    $sheet->each(function ($row) use ($r, $dataInsertFix, &$model, &$record_total, &$record_success, $rates, $services) {
                        $result = $this->importItem($row, $r, $dataInsertFix, $rates, $services);
                        if ($result['status']) {
                            $record_total++;
                        }
                        if ($result['import']) {
                            $record_success++;
                        }
                    });
                }

            });
            dd($reader->getSheetCount());
        });
        $item->record_total = $record_total;
        $item->record_success = $record_total;
        $item->save();
        return true;
    }

    //  Xử lý import 1 dòng excel
    public function importItem($row, $r, $dataInsertFix, $rates, $services)
    {
        try {
            $tel = $row->all()['sdt_khach'];

            //  Chuẩn lại sđt
            if (substr($tel, 0, 1) != '0') {

                //  Nếu sđt thiếu 0 ở đầu thì nối vào
                $tel = '0' . $tel;
            }
            $tel = str_replace('.', '', $tel);
            $tel = str_replace(',', '', $tel);
            $tel = str_replace(' ', '', $tel);
            $tel = trim($tel);

            //  Kiểm tra trường dữ liêu bắt buộc có
            /*$fields_require = ['tel'];
            foreach ($fields_require as $field_require) {
                if (!isset($row->{$field_require}) || $row->{$field_require} == '' || $row->{$field_require} == null) {
                    return false;
                }
            }*/

            $row_empty = true;
            foreach ($row->all() as $key => $value) {
                if ($value != null) {
                    $row_empty = false;
                }
            }

            //  Các trường không được trùng
            $item_model = new $this->module['modal'];

            $item = $item_model->where('note', $row->all()['note'])->first();


            if (is_object($item)) {
                $row_empty = true;
//                $item->registration_date = date('Y-m-d H:i:s', strtotime($row->all()['created_at']));
//                $item->save();
//                echo 'đã cập nhật ngày ký';

                return [
                    'status' => false,
                    'import' => false,
                    'msg' => 'Đã tồn tại',
                ];
            }

            /*if ($this->import[$request->module]['unique']) {
                $field_name = $this->import[$request->module]['fields'][$this->import[$request->module]['unique']];
                $model_new = new $this->import[$request->module]['modal'];
                $model = $model_new->where($field_name, $row->{$this->import[$request->module]['unique']})->first();
            }*/

            if (!$row_empty) {
                if ($tel == '0') {
                    return [
                        'status' => false,
                        'import' => false,
                        'msg' => 'Sđt trống',
                    ];
                }


                echo '__bắt đầu insert:' .$tel .'<br>';
                $data = [];

                //  Chèn các dữ liệu lấy vào từ excel
                foreach ($row->all() as $key => $value) {
                    switch ($key) {

                        default: {
                            if (\Schema::hasColumn($r->table, $key)) {
                                $data[$key] = $value;
                            }
                        }
                    }
                }

                $customer = Admin::select(['id'])->where(function ($query) use ($tel, $row) {
                    $query->orWhere('tel', $tel);
                    $query->orWhere('code', $row->all()['id_khach']);
                })->first();

                if (!is_object($customer)) {
                    $customer = new Admin();
                    $customer->tel = $tel;
                    $customer->name = $row->all()['ten_khach_hang'];
                    $customer->code = (int) $row->all()['id_khach'];
                    $customer->address = $row->all()['dia_chi_khach'];
                    $customer->admin_id = \Auth::guard('admin')->user()->id;

                    $customer->save();

                    //   gán quyền khách
                    RoleAdmin::create([
                        'admin_id' => $customer->id,
                        'role_id' => 3,
                    ]);
                }
                $data['customer_id'] = $customer->id;
                unset($data['ten_khach_hang']);
                unset($data['sdt_khach']);
                unset($data['dia_chi_khach']);
                unset($data['id_khach']);

                if (strpos($data['note'], '.') != false) {
                    $data['note'] = explode('.', $data['note'])[0];
                }

                if ($row->all()['created_at'] != null && $row->all()['created_at'] != '') {
                    $data['created_at'] = date('Y-m-d H:i:s', strtotime($row->all()['created_at']));
                }

                $data['saler_id'] = \Auth::guard('admin')->user()->id;
                $data['status'] = 1;


                //  Gán các dữ liệu được fix cứng từ view
                foreach ($dataInsertFix as $k => $v) {
                    $data[$k] = $v;
                }

                $bill = new Bill();
                foreach ($data as $k => $v) {
                    $bill->$k = $v;
                }
                echo '__đủ data insert:' .$tel;
                if ($bill->save()) {
                    return [
                        'status' => true,
                        'import' => true,
                        'msg' => 'import thành công sđt: ' . $tel,
                    ];
                }
            } else {
                return [
                    'status' => false,
                    'import' => false,
                    'msg' => 'Dòng trống',
                ];
            }
        } catch (\Exception $ex) {
            return [
                'status' => true,
                'import' => false,
                'msg' => $ex->getMessage()
            ];
        }
    }

    public function vuasoft($data, $row) {



        if ($row->all()['contacted_log_last'] != null && $row->all()['contacted_log_last'] != '') {
            $data['contacted_log_last'] = date('Y-m-d H:i:s', strtotime(@$row->all()['contacted_log_last']));
        }

        $data['profile'] = '';

        $data['profile'] .= 'Tên trung tâm: ' . @$row->all()['ten_trung_tam'] . '. ';

        $data['profile'] .= 'Zalo: ' . $row->all()['zalo'] . '. ';

        $data['profile'] .= 'Địa chỉ: ' . $row->all()['dia_chi'] . '. ';

        $data['profile'] .= 'Khu vực: ' . $row->all()['khu_vuc'] . '. ';

        $data['profile'] .= 'Liên hệ khác: ' . $row->all()['lien_he_khac'] . '. ';

        $data['profile'] .= 'Website: ' . $row->all()['website'] . '. ';
        return $data;
    }
}
