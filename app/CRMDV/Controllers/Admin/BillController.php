<?php

namespace App\CRMDV\Controllers\Admin;

use App\CRMDV\Models\BillReceipts;
use App\Http\Helpers\CommonHelper;
use App\Mail\MailServer;
use App\Models\Admin;
use App\Models\Setting;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use App\CRMDV\Controllers\Helpers\CRMDVHelper;
use App\CRMDV\Models\Bill;
use App\CRMDV\Models\BillHistory;
use App\CRMDV\Models\BillFinance;
use App\CRMDV\Models\BillProgress;
use App\CRMDV\Models\Service;
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
        'modal' => '\App\CRMDV\Models\Bill',
        'list' => [
            ['name' => 'domain', 'type' => 'text_edit', 'label' => 'Tên miền', 'sort' => true],
            ['name' => 'customer_id', 'type' => 'relation', 'label' => 'Khách hàng', 'object' => 'user', 'display_field' => 'name'],
            ['name' => 'total_price', 'type' => 'price_vi', 'label' => 'Doanh số', 'sort' => true],
            ['name' => 'total_price_contract', 'type' => 'price_vi', 'label' => 'Tổng $'],
            ['name' => 'total_received', 'type' => 'price_vi', 'label' => '$ đã thu'],
            ['name' => 'finance_id', 'type' => 'custom', 'td' => 'CRMDV.dhbill.list.td.chua_thu', 'label' => '$ chưa thu'],
            ['name' => 'service_id', 'type' => 'relation', 'label' => 'Dịch vụ', 'object' => 'service', 'display_field' => 'name_vi', 'sort' => true],
//            ['name' => 'count_product', 'type' => 'custom', 'td' => 'CRMDV.list.td.count_product', 'label' => 'Tổng SP'],
            ['name' => 'registration_date', 'type' => 'date_vi', 'label' => 'Ngày ký', 'sort' => true],
            ['name' => 'expiry_date', 'type' => 'date_vi', 'label' => 'Ngày hết hạn', 'sort' => true],
            // ['name' => 'exp_price', 'type' => 'price_vi', 'label' => 'Giá gia hạn', 'sort' => true],
            // ['name' => 'expiry_date', 'type' => 'date_vi', 'label' => 'Hết hạn', 'sort' => true],
            ['name' => 'auto_extend', 'type' => 'custom', 'td' => 'CRMDV.list.td.status', 'label' => 'Duy trì', 'sort' => true
            ],
            ['name' => 'status', 'type' => 'custom', 'td' => 'CRMDV.list.td.status', 'label' => 'Trạng thái',  'sort' => true
            ],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'total_price', 'type' => 'price_vi', 'class' => 'required', 'label' => 'Doanh số', 'group_class' => 'col-md-4'],
                ['name' => 'total_price_contract', 'type' => 'price_vi', 'class' => 'required', 'label' => 'Tổng tiền HĐ', 'group_class' => 'col-md-4'],
                ['name' => 'domain', 'type' => 'text', 'label' => 'Tên miền', 'group_class' => 'col-md-4'],
                ['name' => 'service_id', 'type' => 'select2_model', 'class' => ' required',  'label' => 'Gói DV', 'model' => Service::class, 'display_field' => 'name_vi', 'group_class' => 'col-md-3'],
                ['name' => 'registration_date', 'type' => 'date', 'label' => 'Ngày ký HĐ', 'class' => 'required', 'group_class' => 'col-md-3'],
                ['name' => 'contract_time', 'type' => 'number', 'label' => 'Thời gian sử dụng (tháng)', 'class' => 'required', 'group_class' => 'col-md-3'],

                ['name' => 'bill_cost_id', 'type' => 'custom', 'label' => 'Bảng thống kê', 'field' => 'CRMDV.bill.form.table','group_class' => 'col-md-12'],

                // ['name' => 'curator_ids', 'type' => 'select2_ajax_model', 'label' => 'Người KH phụ trách', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'email', 'multiple' => true, 'group_class' => 'col-md-6'],

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
                ['name' => 'marketer_ids', 'type' => 'select2_ajax_model', 'label' => 'Marketing', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'code', 'multiple' => true, 'group_class' => 'col-md-6'],
                ['name' => 'saler_id', 'type' => 'custom', 'field' => 'CRMDV.form.fields.select_sale','label' => 'Sale', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'code', 'class' => 'required'],
                ['name' => 'staff_care', 'type' => 'select2_ajax_model', 'label' => 'NV theo dõi', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'code', 'multiple' => true, 'group_class' => 'col-md-12'],
                ['name' => 'customer_id', 'type' => 'custom', 'type_history' => 'relation_multiple', 'field' => 'CRMDV.form.fields.select_customer',
                    'label' => 'Khách hàng', 'model' => User::class, 'object' => 'user', 'display_field' => 'name', 'display_field2' => 'tel', 'class' => 'required'],
                ['name' => 'customer_legal_id', 'type' => 'custom', 'type_history' => 'relation_multiple', 'field' => 'CRMDV.form.fields.select_customer',
                    'label' => 'Đại diện pháp lý', 'model' => User::class, 'object' => 'user', 'display_field' => 'name', 'display_field2' => 'tel', 'class' => ''],
            ],
            'gia_han_tab' => [
                ['name' => 'auto_extend', 'type' => 'checkbox', 'label' => 'Duy trì bên mình', 'value' => 1, 'group_class' => 'col-md-4'],
                ['name' => 'expiry_date', 'type' => 'date', 'field' => 'CRMDV.form.fields.expiry_date', 'label' => 'Ngày hết hạn', 'class' => '', 'group_class' => 'col-md-4', 'inner' => ''],
                ['name' => 'exp_price', 'type' => 'price_vi', 'class' => ' required', 'label' => 'Giá gia hạn', 'group_class' => 'col-md-4', 'des' => 'Thuê hosting bên mình thì. 1,4tr cho 3G, 1,76tr cho 6G'],
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
                ['name' => 'status', 'type' => 'checkbox', 'label' => 'Kích hoạt', 'value' => 1, ],
                ['name' => 'note', 'type' => 'textarea', 'label' => 'Ghi chú'],
                ['name' => 'customer_note', 'type' => 'textarea', 'class' => 'required', 'label' => 'Có đky tên miền cho khách không? Nếu có thì: đuôi gì? mấy năm? đã trả tiền chưa?'],
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
        'fields' => 'id, domain, exp_price, total_price, note, customer_note'
    ];

    public function quickSearch($listItem, $r) {
        if (@$r->quick_search != '') {
            $listItem = $listItem->where(function ($query) use ($r) {
                foreach (explode(',', $this->quick_search['fields']) as $field) {
                    $query->orWhere(trim($field), 'LIKE', '%' . $r->quick_search . '%');    //  truy vấn các tin thuộc các danh mục con của danh mục hiện tại
                }

                //  Tìm kiếm nhanh theo khách hàng
                $customer_ids = User::select('id')->where(function ($query) use ($r) {
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

    public function getIndex(Request $request)
    {
        
        $data = $this->getDataList($request);

        return view('CRMDV.list')->with($data);
    }

    public function koDuyTri(Request $request) {
        $where = $this->filterSimple($request);
        $listItem = $this->model->whereRaw($where);
        $listItem = $this->quickSearch($listItem, $request);
        if ($this->whereRaw) {
            $listItem = $listItem->whereRaw($this->whereRaw);
        }
        $listItem = $this->appendWhere($listItem, $request);
        $listItem = $listItem->where('expiry_date', '<', date('Y-m-d'))->where('auto_extend', 0);

        //  Export
        if ($request->has('export')) {
            $this->exportExcel($request, $listItem->get());
        }

        //  Sort
        $listItem = $listItem->orderBy('expiry_date', 'desc');

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
        $data['page_title'] = 'Hợp đồng không duy trì';
        $data['page_type'] = 'list';

        return view('CRMDV.list')->with($data);
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
        if (CRMDVHelper::getRoleType(\Auth::guard('admin')->user()->id) == 'customer') {
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

    public function demoWordpress() {
        $data['bills'] = Bill::select('domain')->whereIn('service_id', [2, 5])->get();
        return view('CRMDV.demo_wordpress')->with($data);
    }

    public function demoLdp() {
        $data['bills'] = Bill::select('domain', 'id')->whereIn('service_id', [1])->get();
        return view('CRMDV.demo_ldp')->with($data);
    }


    public function add(Request $request)
    {
        /*$billHistory=new BillHistory();
        $billHistory->bill_id=$request->id;
        $billHistory->price=$request->exp_price;
        $billHistory->expiry_date=$request->expiry_date;
        $billHistory->save();*/
//        dd($request->all());
        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMDV.add')->with($data);
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

            //  Làm chuẩn domain
            $data['domain'] = str_replace('http://', '', $data['domain']);
            $data['domain'] = str_replace('https://', '', $data['domain']);
            $data['domain'] = str_replace('www.', '', $data['domain']);
            $data['domain'] = strtolower($data['domain']);
            if (in_array($data['service_id'], [17, 18, 19, 20]) && strpos($data['domain'], 'www.') === false) {
                //  nếu là gói thiết kế LDP mà tên miền ko có www. ở đầu thì cho thêm vào đầu
                $data['domain'] = 'www.' . $data['domain'];
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
            $input = $request->all(); // Lấy tất cả dữ liệu từ request

            $rowCount = count($input['row_text']); // Số lượng phần tử trong mảng row_text
            $result = [];

            for ($i = 0; $i < $rowCount; $i++) {
                $result[] = [
                    'row_text' => $input['row_text'][$i],
                    'row_price' => $input['row_price'][$i],
                    'row_quantity' => $input['row_quantity'][$i],
                ];
            }
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

                foreach ($result as $data) {
//                    dd($data);
                    \App\CRMDV\Models\BillCostList::updateOrCreate([
                        'hang_muc' => $data['row_text'],
                        'don_gia' => $data['row_price'],
                        'so_luong' => $data['row_quantity'],
                        'bill_id' => $this->model->id,
                        'admin_id' => \Auth::guard('admin')->user()->id,
                    ]);
                }

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

                return view('CRMDV.edit')->with($data);
            }


            \DB::beginTransaction();
            
            $data = $this->processingValueInFields($request, $this->getAllFormFiled());

            //  Tùy chỉnh dữ liệu insert
            //  Khách hàng tự sửa hóa đơn của mình
            if (CRMDVHelper::getRoleType(\Auth::guard('admin')->user()->id) == 'customer') {
                unset($data['customer_id']);
            }

            //  Làm chuẩn domain
            $data['domain'] = str_replace('http://', '', $data['domain']);
            $data['domain'] = str_replace('https://', '', $data['domain']);
            $data['domain'] = str_replace('www.', '', $data['domain']);
            $data['domain'] = strtolower($data['domain']);
            if (in_array($data['service_id'], [17, 18, 19, 20]) && strpos($data['domain'], 'www.') === false) {
                //  nếu là gói thiết kế LDP mà tên miền ko có www. ở đầu thì cho thêm vào đầu
                $data['domain'] = 'www.' . $data['domain'];
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
        $html = str_replace('{service_name}', $bill->service_name == '' ? $bill->service->name_vi : $bill->service_name, $html);
        $html = str_replace('{domain}', '<a target="_blank" href="'.$bill->domain.'">'.$bill->domain.'</a>', $html);
        $html = str_replace('{guarantee}', $bill->guarantee, $html);
        $html = str_replace('{web_link}', '<a target="_blank" href="'.$bill->web_link.'">'.$bill->web_link.'</a>', $html);
        $html = str_replace('{web_username}', $bill->web_username, $html);
        $html = str_replace('{web_password}', $bill->web_password, $html);
        $html = str_replace('{hosting_link}', '<a target="_blank" href="'.$bill->hosting_link.'">'.$bill->hosting_link.'</a>', $html);
        $html = str_replace('{hosting_username}', $bill->hosting_username, $html);
        $html = str_replace('{hosting_password}', $bill->hosting_password, $html);
        $html = str_replace('{file_ldp}', '<a target="_blank" href="'.url('admin/landingpage/down-load-file/' . $bill->id . '/' . @$bill->ldp->id).'">Click để tải File thiết kế</a>', $html);
        $html = str_replace('{customer_link}', '<a target="_blank" href="'.@$bill->ldp->customer_link.'">Danh sách khách hàng</a>', $html);

        return $html;
    }

    /**
     * Tạo hóa đơn tên miền
    */
    public function createBillForDomain($data, $bill_parent = null) {
        $service_id = 3;
        $bill_exist = Bill::where('domain', $data['domain'])->where('service_id', $service_id)->count();
        if ($bill_exist > 0) {
            return false;
        }

        //  Lấy thông tin dịch vụ tên miền
        $service = Service::find($service_id);
        $servicePrice = (array) json_decode($service->price);
        foreach ($servicePrice as $v) {
            $v = (array)$v;
            $service_price[$v['day']] = $v['price'];
        }

        //  nếu LDP chưa ứng với HĐ nào thì tạo mới HĐ
        $bill = new Bill();
        $bill->service_id = $service_id;

        //  Thông tin khách hàng
        $bill->customer_id = @$bill_parent->customer_id;
        // $bill->customer_tel = @$bill_parent->user->tel;
        // $bill->customer_name = @$bill_parent->user->name;
        // $bill->customer_email = @$bill_parent->user->email;
        // $bill->customer_address = @$bill_parent->user->address;

        //  thông tin cơ bản đơn hàng
        $duoi_ten_mien = str_replace(explode('.', $data['domain'])[0], '', $data['domain']);
        $bill->total_price = $service_price[$duoi_ten_mien. '_start'];
        $bill->status = 1;
        $bill->registration_date = date('Y-m-d H:i:s');  //   ngày ký HĐ
        $bill->domain = $data['domain'];
        $bill->bill_parent = $bill_parent->id;

        //  thông tin gia hạn
        $bill->expiry_date = date('Y-m-d H:i:s', time() + $service->expiry_date * 24 * 60 * 60); //  ngày hết hạn
        $bill->exp_price = $service_price[$duoi_ten_mien. '_365'];   //  giá ra hạn
        $bill->auto_extend = 1; //  kich hoạt tự động gia hạn
//        dd($bill);

        //  Thông tin bàn giao
        $bill->save();
    }

    /**
     * Tạo hóa đơn tên miền
     */
    public function createBillForHosting($data, $bill_parent = null) {
        $service_id = 2;
        $bill_exist = Bill::where('domain', $data['domain'])->where('service_id', $service_id)->count();
        if ($bill_exist > 0) {
            return false;
        }

        //  Lấy thông tin dịch vụ tên miền
        $service = Service::find($service_id);
        $servicePrice = (array) json_decode($service->price);
        foreach ($servicePrice as $v) {
            $v = (array)$v;
            $service_price[$v['day']] = $v['price'];
        }

        //  nếu LDP chưa ứng với HĐ nào thì tạo mới HĐ
        $bill = new Bill();
        $bill->service_id = $service_id;

        //  Thông tin khách hàng
        $bill->customer_id = @$bill_parent->customer_id;
        // $bill->customer_tel = @$bill_parent->user->tel;
        // $bill->customer_name = @$bill_parent->user->name;
        // $bill->customer_email = @$bill_parent->user->email;
        // $bill->customer_address = @$bill_parent->user->address;

        //  thông tin cơ bản đơn hàng
        $duoi_ten_mien = str_replace(explode('.', $data['domain'])[0], '', $data['domain']);
        $bill->total_price = $service_price[$data['hosting_plan']];
        $bill->status = 1;
        $bill->registration_date = date('Y-m-d H:i:s');  //   ngày ký HĐ
        $bill->domain = $data['domain'];
        $bill->bill_parent = $bill_parent->id;

        //  thông tin gia hạn
        $bill->expiry_date = date('Y-m-d H:i:s', time() + $service->expiry_date * 24 * 60 * 60); //  ngày hết hạn
        $bill->exp_price = $service_price[$data['hosting_plan']];   //  giá ra hạn
        $bill->auto_extend = 1; //  kich hoạt tự động gia hạn
//        dd($bill);

        //  Thông tin bàn giao
        $bill->save();
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
        if (CRMDVHelper::getRoleType(\Auth::guard('admin')->user()->id) == 'customer') {
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
                $field_name[] = 'Dịch vụ';
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
                    $data_export[] = $value->domain;
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
}
