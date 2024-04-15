<?php

namespace App\CRMBDS\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Mail\MailServer;
use App\Models\Admin;
use App\Models\Setting;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use App\CRMBDS\Controllers\Helpers\MDHelper;
use App\CRMBDS\Models\Bill;
use App\CRMBDS\Models\BillHistory;
use App\CRMBDS\Models\BillProgress;
use App\CRMBDS\Models\Service;
use Validator;

class CSKHBillController extends CURDBaseController
{

    protected $orderByRaw = 'status DESC, id DESC';

    protected $module = [
        'code' => 'cskh-bill',
        'table_name' => 'bills',
        'label' => 'CRMBDS_admin.cskh-bill',
        'modal' => '\App\CRMBDS\Models\Bill',
        'list' => [
//            ['name' => 'domain', 'type' => 'text_edit', 'label' => 'CRMBDS_admin.cskh-bill_domain'],
            ['name' => 'customer_id', 'type' => 'relation', 'label' => 'CRMBDS_admin.cskh_customer', 'object' => 'customer', 'display_field' => 'name'],
            ['name' => 'total_price', 'type' => 'price_vi', 'label' => 'CRMBDS_admin.cskh_total_price'],
            ['name' => 'service_id', 'type' => 'relation', 'label' => 'CRMBDS_admin.cskh_service', 'object' => 'service', 'display_field' => 'name_vi'],
//            ['name' => 'count_product', 'type' => 'custom', 'td' => 'CRMBDS.cskh_bill.list.td.count_product', 'label' => 'Tổng SP'],
            ['name' => 'exp_price', 'type' => 'price_vi', 'label' => 'CRMBDS_admin.cskh_exp_price'],
            ['name' => 'expiry_date', 'type' => 'date_vi', 'label' => 'CRMBDS_admin.cskh_expiry_date'],
            ['name' => 'contacted_log_last', 'type' => 'date_vi', 'label' => 'CRMBDS_admin.cskh_contacted_log_last'],
            ['name' => 'action', 'type' => 'custom', 'td' => 'CRMBDS.cskh_bill.list.td.action', 'label' => 'CRMBDS_admin.cskh_action'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'total_price', 'type' => 'price_vi', 'class' => 'required', 'label' => 'CRMBDS_admin.bills_total_price', 'group_class' => 'col-md-4'],
                ['name' => 'total_price_contract', 'type' => 'price_vi', 'class' => 'required', 'label' => 'CRMBDS_admin.bills_total_price_contract', 'group_class' => 'col-md-4'],
//                ['name' => 'domain', 'type' => 'text', 'label' => 'Tên miền', 'group_class' => 'col-md-4'],
                ['name' => 'service_id', 'type' => 'select2_model', 'class' => '',  'label' => 'CRMBDS_admin.service', 'multiple' => true,
                    'model' => Service::class, 'display_field' => 'name_vi', 'group_class' => 'col-md-4'],
                ['name' => 'registration_date', 'type' => 'date', 'label' => 'CRMBDS_admin.bills_registration_date', 'class' => 'required',
                    'value' => 'now', 'group_class' => 'col-md-3'],
                ['name' => 'contract_time', 'type' => 'number', 'label' => 'CRMBDS_admin.contract_time', 'class' => '', 'group_class' => 'col-md-2'],

                ['name' => 'status', 'type' => 'checkbox', 'label' => 'CRMBDS_admin.bills_activated', 'value' => 1, 'group_class' => 'col-md-4'],
                ['name' => 'dating', 'type' => 'date', 'label' => 'CRMBDS_admin.bills_dating', 'class' => '', 'group_class' => 'col-md-3'],
                ['name' => 'note', 'type' => 'textarea', 'label' => 'CRMBDS_admin.bills_note', 'group_class' => 'col-md-12'],



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
                ['name' => 'marketer_ids', 'type' => 'select2_ajax_model', 'label' => 'CRMBDS_admin.bills_marketing', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'code', 'multiple' => true, 'group_class' => 'col-md-6'],
                ['name' => 'saler_id', 'type' => 'custom', 'field' => 'CRMBDS.form.fields.select_sale','label' => 'CRMBDS_admin.bills_sale', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'code', 'class' => 'required'],
                ['name' => 'customer_id', 'type' => 'custom', 'type_history' => 'relation_multiple', 'field' => 'CRMDV.form.fields.select_customer',
                    'label' => 'Khách hàng', 'model' => User::class, 'object' => 'user', 'display_field' => 'name', 'display_field2' => 'tel', 'class' => 'required'],
                ['name' => 'customer_legal_id', 'type' => 'custom', 'type_history' => 'relation_multiple', 'field' => 'CRMDV.form.fields.select_customer',
                    'label' => 'Đại diện pháp lý', 'model' => User::class, 'object' => 'user', 'display_field' => 'name', 'display_field2' => 'tel', 'class' => ''],
            ],
            'gia_han_tab' => [
//                ['name' => 'expiry_date', 'type' => 'date', 'field' => 'CRMBDS.form.fields.expiry_date', 'label' => 'Ngày hết hạn', 'class' => '', 'group_class' => 'col-md-6', 'inner' => ''],
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
        'customer_id' => [
            'label' => 'Tên khách hàng',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'model' => \App\Models\Admin::class,
            'object' => 'admin',
            'query_type' => '='
        ],
        'marketer_ids' => [
            'label' => 'Nguồn marketing',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'display_field2' => 'code',
            'model' => \App\Models\Admin::class,
            'object' => 'admin',
            'query_type' => 'custom'
        ],
        'service_id' => [
            'label' => 'Dịch vụ',
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
        'expiry_date' => [
            'label' => 'Hết hạn',
            'type' => 'date',
            'query_type' => '='
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
        'registration_date' => [
            'label' => 'Ngày ký HĐ',
            'type' => 'from_to_date',
            'query_type' => 'from_to_date'
        ],
    ];

    protected $quick_search = [
        'label' => 'ID, tên miền, giá, note',
        'fields' => 'id, domain, exp_price, total_price, note'
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('CRMBDS.cskh_bill.list')->with($data);
    }

    public function appendWhere($query, $request)
    {
        
//        if (@$request->marketer_ids != null) {
//            $query = $query->where('marketer_ids', 'like', '%|' . $request->marketer_ids . '|%');
//        }
//
//        if (\Auth::guard('admin')->user()->super_admin != 1) {
//            //  Nếu ko phải super_admin thì truy vấn theo dữ liệu cty đó
//            // $query = $query->where('company_id', \Auth::guard('admin')->user()->last_company_id);
//        }
//
//
//        $query = $query->where('domain', 'LIKE', '%.%')    //  lấy hđ có tên miền có dấu .
//                            ->whereNotIn('service_id', [    //   ko lấy tên miền ở các  hợp đồng cho các dịch vụ sau:
//                                4,  //  dv mail
//                                7,  //  dv duy trì
//                                8,  // dv nâng cấp hosting
//                                9,  // dv nâng cấp web
//
//                            ])
//                            ->where('status', 1);    //  chỉ lấy các dự án đang kich hoạt
//
//        $query = $query->where('registration_date', '<', date("Y-m-d H:i:s",strtotime("-1 month")))  //  Lấy HĐ đã ký cách đây 1 tháng
//                        ->where(function ($query) {
////                            $query->orWhere('contacted_log_last', '<', date("Y-m-d H:i:s",strtotime("-3 month")));  //  Lấy HĐ đã chăm sóc cách đây lâu hơn 3 tháng
//                            $query->orWhere('contacted_log_last', null);
//                        })
//                        ->where('expiry_date', '>', date("Y-m-d H:i:s",strtotime("+1 month")))  //  Lấy HĐ không gần với thời gian gia hạn
//                        ->where('status', 1)    //  HĐ đang kich hoạt
////                        ->whereNotIn('service_id', [3, 4, 6])    //  HĐ đang kich hoạt
//                        ->orderBy('contacted_log_last', 'asc')->orderBy('id', 'asc');   //  Lấy HĐ tương tác lâu nhất & cũ nhất lên trước
//
//        $query = $query->groupBy('customer_id');

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
                return view('CRMBDS.cskh_bill.add')->with($data);
            }

            $data = $this->processingValueInFields($request, $this->getAllFormFiled());
            //  Tùy chỉnh dữ liệu insert
            $data['service_id'] = @$_GET['service_id'];

            $customer = Admin::find($data['customer_id']);
            $data['customer_name'] = $customer->name;
            $data['customer_tel'] = $customer->tel;
            $data['customer_email'] = $customer->email;
            $data['customer_address'] = $customer->address;
            $data['curator_ids'] = '|' . implode('|', $request->get('curator_ids', [])) . '|';
            $data['staff_care'] = '|' . implode('|', $request->get('staff_care', [])) . '|';
            $data['marketer_ids'] = '|' . implode('|', $request->get('marketer_ids', [])) . '|';

            $data['company_id'] = \Auth::guard('admin')->user()->last_company_id;

            unset($data['file_ldp']);
            #
            foreach ($data as $k => $v) {
                $this->model->$k = $v;
            }

            if ($this->model->save()) {
                $this->afterAddLog($request, $this->model);

                // //  nếu thuê tên miền bên mình thì tạo 1 hóa đơn cho tên miền đó
                // if ($data['domain_owner'] == 'hobasoft') {
                //     $this->createBillForDomain($data, $this->model);
                // }

                // //  nếu thuê hosting bên mình thì tạo 1 hóa đơn cho hosting đó
                // if ($data['hosting_owner'] == 'hobasoft') {
                //     $this->createBillForHosting($data, $this->model);
                // }

                CommonHelper::one_time_message('success', 'Tạo mới thành công!');
            } else {
                CommonHelper::one_time_message('error', 'Lỗi tao mới. Vui lòng load lại trang và thử lại!');
            }

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'msg' => '',
                    'data' => $this->model
                ]);
            }

            if ($request->return_direct == 'save_exit') {
                return redirect('admin/' . $this->module['code']);
            } elseif ($request->return_direct == 'save_create') {
                return redirect('admin/' . $this->module['code'] . '/add');
            }

            return redirect('admin/' . $this->module['code'] . '/edit/' . $this->model->id);
        } catch (\Exception $ex) {
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


                return view('CRMBDS.cskh_bill.edit')->with($data);
            }

            $data = $this->processingValueInFields($request, $this->getAllFormFiled());

            //  Tùy chỉnh dữ liệu insert
            //  Khách hàng tự sửa hóa đơn của mình
            if (MDHelper::getRoleType(\Auth::guard('admin')->user()->id) == 'customer') {
                unset($data['customer_id']);
            }

            // //  nếu thuê tên miền bên mình thì tạo 1 hóa đơn cho tên miền đó
            // if ($data['domain_owner'] == 'hobasoft') {
            //     $this->createBillForDomain($data, $item);
            // }

            // //  nếu thuê hosting bên mình thì tạo 1 hóa đơn cho hosting đó
            // if ($data['hosting_owner'] == 'hobasoft') {
            //     $this->createBillForHosting($data, $item);
            // }

            //  Lấy người phụ trách
            $data['curator_ids'] = '|' . implode('|', $request->get('curator_ids', [])) . '|';
            $data['staff_care'] = '|' . implode('|', $request->get('staff_care', [])) . '|';
            $data['marketer_ids'] = '|' . implode('|', $request->get('marketer_ids', [])) . '|';

            unset($data['file_ldp']);

            foreach ($data as $k => $v) {
                $item->$k = $v;
            }
            if ($item->save()) {


                /*$expiry_date_db = Bill::find($request->id)->expiry_date;
                $price_db = Bill::find($request->id)->exp_price;
                if ($expiry_date_db != $request->expiry_date) {
                    $billHistory = new BillHistory();
                    $billHistory->bill_id = $request->id;
                    $billHistory->price = $price_db;
                    $billHistory->expiry_date = $expiry_date_db;

                    $billHistory->save();
                }*/

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
            dd($ex->getMessage());
//            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            return redirect()->back()->withInput();
        }
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

    public function delete(Request $request)
    {
        try {


            $item = $this->model->find($request->id);

//            //  Không được xóa dữ liệu của cty khác, cty mình ko tham gia
//            if (strpos(Auth::guard('admin')->user()->company_ids, '|' . $item->company_id . '|') === false) {
//                CommonHelper::one_time_message('error', 'Bạn không có quyền xóa!');
//                return back();
//            }

            $item->delete();

            CommonHelper::one_time_message('success', 'Xóa thành công!');
            return redirect('admin/' . $this->module['code']);
        } catch (\Exception $ex) {
            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
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
        if (MDHelper::getRoleType(\Auth::guard('admin')->user()->id) == 'customer') {
            $data = $data->where('customer_id', \Auth::guard('admin')->user()->id);
        }

        $data = $data->limit(5)->get();
        return response()->json([
            'status' => true,
            'items' => $data
        ]);
    }

    public function boChamSocLanNay(Request $request, $id) {
        $bill = Bill::where('id', $id)->update([
            'contacted_log_last' => date('Y-m-d H:i:s') //  cập nhật thời gian cập nhật đơn hàng mới nhất là hiện tại
        ]);
        return redirect()->back();
    }

    public function processContentMail() {}
}
