<?php
namespace App\Custom\Controllers\Admin;

use App\Models\Admin;
use App\CRMDV\Controllers\Admin\CURDBaseController;
use Auth;
use Illuminate\Http\Request;
use App\Http\Helpers\CommonHelper;
use App\CRMDV\Models\Category;
use App\CRMDV\Models\Landingpage;
use App\CRMDV\Models\Bill;
use App\CRMDV\Models\Service;
use App\Custom\Controllers\Helpers\CustomHelper;
use Validator;

class LandingpageController extends CURDBaseController
{


    protected $module = [
        'code' => 'landingpage',
        'table_name' => 'landingpages',
        'label' => 'Landingpage',
        'modal' => '\App\CRMDV\Models\Landingpage',
        'list' => [
            ['name' => 'domain', 'type' => 'custom', 'td' => 'custom.list.td.domain', 'label' => 'Tên miền'],
            ['name' => 'career_id', 'type' => 'relation', 'object' => 'career', 'display_field' => 'name', 'label' => 'Loại sản phẩm'],
            ['name' => 'customer_id', 'type' => 'relation', 'label' => 'Khách', 'object' => 'customer', 'display_field' => 'name'],
            ['name' => 'status', 'type' => 'status', 'label' => 'Trạng thái'],
        ],
        'form' => [
            'general_tab' => [
//                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên web', 'group_class' => 'col-md-6'],
               /* ['name' => 'customer_id', 'object' => 'admin', 'type' => 'custom', 'field' => 'custom.form.fields.customer_id',
                    'model' => '\App\Models\Admin', 'class' => 'required', 'label' => 'Khách hàng', 'group_class' => 'col-md-6',
                    'display_field' => 'name', 'display_field2' => 'tel',
                    'popup' => [
                        'name' => 'customer_id',
                        'label' => 'Khách hàng',
                        'model' => '\App\Models\User',

                    ]],*/
                ['name' => 'domain', 'type' => 'text', 'class' => 'required', 'label' => 'Tên miền', 'group_class' => 'col-md-6'],
                ['name' => 'status', 'type' => 'checkbox', 'label' => 'Kích hoạt', 'value' => 1, 'group_class' => 'col-md-6'],
                ['name' => 'ladi_link', 'type' => 'text', 'class' => 'required', 'label' => 'Link demo landing page', 'group_class' => 'col-md-6'],
                ['name' => 'email_storage', 'type' => 'text', 'class' => 'required', 'label' => 'Dùng mail nào để thiết kế?', 'group_class' => 'col-md-6'],

                ['name' => 'file_ldp', 'type' => 'file', 'class' => 'required', 'label' => 'File .ladipage'],
                ['name' => 'form_action', 'type' => 'text', 'label' => 'Form action'],
                ['name' => 'form_fields', 'type' => 'custom', 'field' => 'custom.landingpage.form.fields.form_fields', 'object' => 'tours', 'class' => '',
                    'label' => 'Các trường lưu trữ', 'des' => 'name, phone, address, email, message, quantity, color, size, field_1, field_2, field_3, state, district, ward'],
                ['name' => 'career_id', 'type' => 'select2_model', 'label' => 'Loại sản phẩm', 'model' => Category::class, 'object' => 'career', 'where' => 'type=10', 'display_field' => 'name', 'class' => 'required'],
                ['name' => 'customer_link', 'type' => 'text', 'label' => 'Link driver chứa dự án', 'des' => 'Link google driver để bàn giao cho khách'],
                ['name' => 'head_code', 'type' => 'textarea', 'class' => '', 'label' => '<head code', 'inner' => 'rows=13'],
                ['name' => 'body_code', 'type' => 'textarea', 'class' => '', 'label' => '<body code', 'inner' => 'rows=13'],
                ['name' => 'note', 'type' => 'textarea', 'class' => '', 'label' => 'Ghi chú', 'inner' => 'rows=13'],
            ],
            'customer_tab' => [
                ['name' => 'customer_id', 'type' => 'custom', 'type_history' => 'relation_multiple', 'field' => 'crmdv.form.fields.select_customer',
                    'label' => 'Khách hàng', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'tel', 'class' => 'required'],
            ],
            'info_tab' => [

            ]
        ],
    ];

    protected $quick_search = [
        'label' => 'ID, tên, tên miền, link ladi, link GG',
        'fields' => 'id, name, domain, ladi_link, form_action, customer_link'
    ];

    protected $filter = [
        'customer_id' => [
            'label' => 'Khách',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'object' => 'admin',
            'model' => Admin::class,
            'query_type' => '='
        ],
        'admin_id' => [
            'label' => 'Người tạo',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'object' => 'admin',
            'model' => Admin::class,
            'query_type' => '='
        ],
        'career_id' => [
            'label' => 'Loại sản phẩm',
            'type' => 'select2_model',
            'display_field' => 'name',
            'object' => 'category',
            'model' => Category::class,
            'where' => 'type=10',
            'query_type' => '='
        ],
        'created_at' => [
            'label' => 'Ngày ký HĐ',
            'type' => 'from_to_date',
            'query_type' => 'from_to_date'
        ],
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('custom.landingpage.list')->with($data);
    }

    public function appendWhere($query, $request)
    {
        if (CustomHelper::getRoleType(\Auth::guard('admin')->user()->id) == 'customer') {
            $query = $query->where('customer_id', \Auth::guard('admin')->user()->id);
        }

        if (\Auth::guard('admin')->user()->super_admin != 1) {
            //  Nếu ko phải super_admin thì truy vấn theo dữ liệu cty đó
            $query = $query->where('company_id', \Auth::guard('admin')->user()->last_company_id);
        }

        return $query;
    }

    public function add(Request $request)
    {
        try {

            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('custom.landingpage.add')->with($data);
            } else if ($_POST) {
                $validator = Validator::make($request->all(), [
                    'domain' => 'required',
                ], [
                    'domain.required' => 'Bắt buộc phải nhập tên miền',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    //  Tùy chỉnh dữ liệu insert
                    if (CustomHelper::getRoleType(\Auth::guard('admin')->user()->id) == 'customer') {
                        $data['customer_id'] = \Auth::guard('admin')->user()->id;
                    }
                    $data['admin_id'] = \Auth::guard('admin')->user()->id;
                    $data['company_id'] = \Auth::guard('admin')->user()->last_company_id;

                    if ($request->has('form_fields_names')) {

                        foreach ($request->form_fields_names as $k => $v) {
                            $form_field[$v] = $request->form_fields_fields[$k];
                        }
                        $data['form_fields'] = $form_field;
                    }
                    $data['form_fields'] = json_encode($data['form_fields']);

                    if ($request->file('file_ldp') != null) {
                        $data['file_ldp'] = CommonHelper::saveFile($request->file('file_ldp'), $this->module['code']);
                    }

                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
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

                    if ($request->return_direct == 'save_continue') {
                        return redirect('admin/' . $this->module['code'] . '/edit/' . $this->model->id);
                    } elseif ($request->return_direct == 'save_create') {
                        return redirect('admin/' . $this->module['code'] . '/add');
                    }

                    return redirect('admin/' . $this->module['code'] . '/edit/' . $this->model->id);
                }
            }
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
                return view('custom.landingpage.edit')->with($data);
            } else if ($_POST) {


                $validator = Validator::make($request->all(), [
                    'domain' => 'required',
                ], [
                    'domain.required' => 'Bắt buộc phải nhập tên miền',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    //  Tùy chỉnh dữ liệu insert
                    if ($request->has('form_fields_names')) {

                        foreach ($request->form_fields_names as $k => $v) {
                            $form_field[$v] = $request->form_fields_fields[$k];
                        }
                        $data['form_fields'] = $form_field;
                    }
                    $data['form_fields'] = json_encode($data['form_fields']);

                    if (CustomHelper::getRoleType(\Auth::guard('admin')->user()->id) == 'customer') {
                        unset($data['customer_id']);
                    }

                    if ($request->file('file_ldp') != null) {
                        $data['file_ldp'] = CommonHelper::saveFile($request->file('file_ldp'), $this->module['code']);
                    } else {
                        unset($data['file_ldp']);
                    }

                    foreach ($data as $k => $v) {
                        $item->$k = $v;
                    }
                    if ($item->save()) {
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
                }
            }
        } catch (\Exception $ex) {
//            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            CommonHelper::one_time_message('error', $ex->getMessage());
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

            //  Không được xóa dữ liệu của cty khác, cty mình ko tham gia
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
        if (CustomHelper::getRoleType(\Auth::guard('admin')->user()->id) == 'customer') {
            $data = $data->where('customer_id', \Auth::guard('admin')->user()->id);
        }

        $data = $data->limit(5)->get();
        return response()->json([
            'status' => true,
            'items' => $data
        ]);
    }

    /*
     * Tạo bill tự động từ các LDP sale tạo mà quên chưa tạo bill
     * */
    public function updateToBill() {
        //  Lấy giá khởi tạo DB landingpage
        $ldp_service = Service::where('code', 'landingpage')->first();
        $configPrice = json_decode($ldp_service->price);
        foreach ($configPrice as $v) {
            if ($v->day == 'start') {
                $priceStart = $v->price;
            }
            if ($v->day == '365') {
                $priceExpiry = $v->price;
            }
        }

        //  Lấy các LDP chưa được update
        $ldps = Landingpage::whereNull('update_to_bill')->get();
        $count_bill_create = 0; //  Đếm các đơn được tạo
        foreach ($ldps as $ldp) {
            //  Kiểm tra xem User này đã đăng ký gói dịch vụ LDP chưa, nếu chưa thì tạo mới
            $count_bill = Bill::where('customer_id', $ldp->customer_id)->where('service_id', 1)->where('domain', $ldp->domain)/*->where('expiry_date', '>=', date('Y-m-d'))
                ->where('status', 1)*/->count();
            if ($count_bill == 0) {
                //  Nếu ko tìm thấy đơn đó thì tạo mới
                $bill = new Bill();
                $bill->expiry_date = date('Y-m-d', strtotime('+1 years', strtotime($ldp->created_at)));
                $bill->registration_date = date('Y-m-d', strtotime($ldp->created_at));
                $bill->total_price = $priceStart;
                // $bill->customer_id = $ldp->customer_id;
                // $bill->customer_tel = @$ldp->customer->tel;
                // $bill->customer_name = @$ldp->customer->name;
                // $bill->customer_email = @$ldp->customer->email;
                // $bill->customer_address = @$ldp->customer->address;
                $bill->status = 1;
                $bill->service_id = @$ldp_service->id;
                $bill->domain = @$ldp->domain;
                $bill->exp_price = $priceExpiry;
                $bill->auto_extend = 1;
                $bill->company_id = @$ldp->customer->last_company_id;
                $bill->save();
                $count_bill_create ++;
            }
            $ldp->update_to_bill = 1;
            $ldp->save();
        }
        CommonHelper::one_time_message('success', number_format($count_bill_create) . ' hóa đơn được tạo');
        return back();
    }

    public function banGiao($id) {
        $ldp = Landingpage::find($id);
        $serviceLDP = Service::find(1);
        $servicePrice = (array) json_decode($serviceLDP->price);
        foreach ($servicePrice as $v) {
            $v = (array)$v;
            $service_price[$v['day']] = $v['price'];
        }

        $bill = Bill::where('domain', $ldp->domain)->where('service_id', 1)->first();
        if (!is_object($bill)) {

            //  nếu LDP chưa ứng với HĐ nào thì tạo mới HĐ
            $bill = new Bill();
            $bill->service_id = $serviceLDP->id;

            //  Thông tin khách hàng
            $bill->customer_id = @$ldp->customer_id;
            $bill->customer_tel = @$ldp->customer->tel;
            $bill->customer_name = @$ldp->customer->name;
            $bill->customer_email = @$ldp->customer->email;
            $bill->customer_address = @$ldp->customer->address;

            //  thông tin cơ bản đơn hàng
            $bill->total_price = $service_price['start'];
            $bill->guarantee = $serviceLDP->guarantee;
            $bill->status = 1;
            $bill->registration_date = date('Y-m-d', strtotime($ldp->created_at));  //   ngày ký HĐ
            $bill->domain = $ldp->domain;

            //  thông tin gia hạn
            $bill->expiry_date = date('Y-m-d', strtotime($ldp->created_at) + $serviceLDP->expiry_date * 24 * 60 * 60); //  ngày hết hạn
            $bill->exp_price = $service_price['365'];   //  giá ra hạn
            $bill->auto_extend = 1; //  kich hoạt tự động gia hạn

            //  Thông tin bàn giao
            $bill->hosting_link = 'http://103.48.82.186:2222/';
            $bill->web_link = 'https://service.lamlandingpage.com/admin';

            $bill->save();

            $ldp->bill_id = $bill->id;
//            dd($ldp);
            $ldp->save();
        }

        CommonHelper::one_time_message('success', 'Đã chuyển sang màn hình hóa đơn của DV ' . $serviceLDP->name_vi);
        return redirect('/admin/bill/' . $bill->id);
    }

    public function downLoadFile($bill_id, $ldp_id) {
        $ldp = Landingpage::find($ldp_id);
        if (!is_object($ldp) || $ldp->bill_id != $bill_id || $ldp->file_ldp == null) {
            die('');
        }

        $file_url = str_replace(' ', '%20', \URL::asset('public/filemanager/userfiles/' . $ldp->file_ldp));
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
        readfile($file_url); // do the double-download-dance (dirty but worky)
        header("Location: $file_url");
    }

    public function getGGFormFields(Request $r) {
        $tool = new Tool();
        $x = $tool->handle($r->link);
        dd($x);

    }
}
