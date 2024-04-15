<?php

namespace App\CRMBDS\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Mail\MailServer;
use App\Models\Admin;
use App\Models\Setting;
use Auth;
use Illuminate\Http\Request;
use App\CRMBDS\Controllers\Helpers\MDHelper;
use App\CRMBDS\Models\Bill;
use App\CRMBDS\Models\BillHistory;
use App\CRMBDS\Models\BillFinance;
use App\CRMBDS\Models\BillProgress;
use App\CRMBDS\Models\BillProgressHistory;
use App\CRMBDS\Models\Service;
use Validator;

class DHBillController extends CURDBaseController
{
 
    protected $orderByRaw = 'id DESC';

    protected $module = [
        'code' => 'dhbill',
        'table_name' => 'bills',
        'label' => 'CRMBDS_admin.dhbill',
        'modal' => '\App\CRMBDS\Models\Bill',
        'list' => [
//            ['name' => 'domain', 'type' => 'text_edit', 'label' => 'Tên miền'],
            ['name' => 'customer_id', 'type' => 'relation', 'label' => 'CRMBDS_admin.khach_hang', 'object' => 'admin', 'display_field' => 'name'],
            ['name' => 'customer_id', 'type' => 'relation', 'label' => 'CRMBDS_admin.dhbill_tel', 'object' => 'admin', 'display_field' => 'tel'],
//            ['name' => 'customer_id', 'type' => 'relation', 'label' => 'Loại khách', 'object' => 'user', 'display_field' => 'classify'],
            ['name' => 'service_id', 'type' => 'custom', 'label' => 'CRMBDS_admin.dhbill_dich_vu', 'td' => 'CRMBDS.dhbill.list.td.service'],
            ['name' => 'progress_id', 'type' => 'relation', 'label' => 'CRMBDS_admin.dhbill_tien_do_du_an', 'object' => 'bill_progress', 'display_field' => 'status'],
            ['name' => 'finance_id', 'type' => 'custom', 'td' => 'CRMBDS.dhbill.list.td.thanh_toan', 'label' => 'CRMBDS_admin.dhbill_pay', 'object' => 'bill_finance', 'display_field' => 'debt'],
            ['name' => 'saler_id', 'type' => 'relation', 'label' => 'CRMBDS_admin.dhbill_business', 'object' => 'saler', 'display_field' => 'name'],
            ['name' => 'progress_id', 'type' => 'relation_through', 'label' => 'CRMBDS_admin.dhbill_tutors', 'object' => 'bill_progress', 'object2' => 'dieu_hanh', 'display_field' => 'name'],
            ['name' => 'progress_id', 'type' => 'relation_through', 'label' => 'CRMBDS_admin.dhbill_lecturers', 'object' => 'bill_progress', 'object2' => 'ky_thuat', 'display_field' => 'name'],
            // ['name' => 'reminder_customer', 'type' => 'custom', 'td' => 'CRMBDS.dhbill.list.td.reminder_customer', 'label' => 'Deadline', 'sort' => true],
            ['name' => 'registration_date', 'type' => 'date_vi', 'label' => 'CRMBDS_admin.dhbill_contract_signing_date', 'sort' => true],
            ['name' => 'rate_content', 'type' => 'custom', 'td' => 'CRMBDS.dhbill.list.td.rate_content', 'label' => 'CRMBDS_admin.dhbill_evaluate', 'sort' => true],

        ],
        'form' => [
            'general_tab' => [
                
                ['name' => 'domain', 'type' => 'text', 'label' => 'CRMBDS_admin.dhbill_Domain', 'class' => ' required', 'group_class' => 'col-md-3'],
                ['name' => 'service_id', 'type' => 'select2_model', 'class' => ' required',  'label' => 'CRMBDS_admin.dhbill_Goi_DV', 'model' => Service::class, 'display_field' => 'name_vi', 'group_class' => 'col-md-6', 'inner' => 'disabled'],
                ['name' => 'registration_date', 'type' => 'date', 'label' => 'CRMBDS_admin.dhbill_Ngay_ki_HD', 'class' => 'required', 'group_class' => 'col-md-3', 'inner' => 'disabled'],
                ['name' => 'customer_note', 'type' => 'textarea', 'class' => 'required', 'label' => 'CRMBDS_admin.dhbill_Ghi_chu_KH'],

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
                ['name' => 'saler_id', 'type' => 'select2_ajax_model', 'label' => 'Người sale', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'code', 'inner' => 'disabled'],
                ['name' => 'customer_id', 'type' => 'custom', 'type_history' => 'relation_multiple', 'field' => 'CRMBDS.form.fields.select_customer',
                    'label' => 'Khách hàng', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'tel', 'class' => 'required', 'inner' => 'disabled'],
                ['name' => 'customer_legal_id', 'type' => 'custom', 'type_history' => 'relation_multiple', 'field' => 'CRMBDS.form.fields.select_customer',
                    'label' => 'Đại diện pháp lý', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'tel', 'class' => '', 'inner' => 'disabled'],
            ],
            'gia_han_tab' => [],
            'service_tab' => [
                ['name' => 'status', 'type' => 'checkbox', 'label' => 'Kích hoạt', 'value' => 1, ],
                ['name' => 'note', 'type' => 'textarea', 'label' => 'Ghi chú'],
                
            ],
            'wp_tab' => [],
            'ldp_tab' => [],
        ],
    ];

    protected $filter = [
        'progress' => [
            'label' => 'Tiến độ',
            'type' => 'select',
            'options' => [
                '' => '',
                'Đang làm' => 'Đang làm',
                'Khách xác nhận xong' => 'Khách xác nhận xong',
                'Kết thúc' => 'Kết thúc',
                'Bỏ' => 'Bỏ',
            ],
            'query_type' => 'custom'
        ],
        'customer_id' => [
            'label' => 'Tên khách hàng',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'model' => \App\Models\Admin::class,
            'object' => 'admin',
            'query_type' => '='
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
        'ky_thuat_id' => [
            'label' => 'Điều hành / kỹ thuật',
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
        'ngay_hoan_thanh' => [
            'label' => 'Ngày hoàn thành',
            'type' => 'select',
            'options' => [
                'khong' => 'Không lọc',
                'thang_truoc' => 'Tháng trước',
                'thang_nay' => 'Tháng này',
            ],
            'query_type' => 'custom'
        ],
        'bo_loc' => [
            'label' => 'Lọc khác',
            'type' => 'select',
            'query_type' => 'Custom',
            'options' => [
                '' => '',
            ],
        ],
        'check_chua_xong' => [
            'label' => 'Check D.Án chưa xong',
            'type' => 'textarea',
            'query_type' => 'custom'
        ],
    ];

    protected $quick_search = [
        'label' => 'ID, tên miền, giá, note',
        'fields' => 'id, domain, note'
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('CRMBDS.dhbill.list')->with($data);
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


        //  Tính điểm
        //  id người tính điểm
        $ky_thuat_id = $request->has('ky_thuat_id') ? $request->ky_thuat_id : \Auth::guard('admin')->user()->id;

        //  Khai báo điểm
        $dh = [
            17 => 1,    //  ldp tiết kiệm
            18 => 1,    //  ldp cơ bản
            19 => 1,    //  ldp chuyên nghiệp
            20 => 3,    //  ldp cao cấp

            10 => 1,    //  wp tiết kiệm
            11 => 3,    //  wp cơ bản
            12 => 2,    //  wp giao diện
            13 => 5,    //  wp chuyên nghiệp
            14 => '6,5',    //  wp special
            15 => 5,    //  wp cao cấp
        ];

        $kt = [
            17 => 2,    //  ldp tiết kiệm
            18 => 3,    //  ldp cơ bản
            19 => 5,    //  ldp chuyên nghiệp
            20 => 5,    //  ldp cao cấp

            10 => 1,    //  wp tiết kiệm
            11 => 2,    //  wp cơ bản
            12 => 8,    //  wp giao diện
            13 => 8,    //  wp chuyên nghiệp
            14 => 11,   //  wp special
            15 => 8,    //  wp cao cấp
        ];


        $bills = $listItem->get();
        $data['tong_diem'] = 0;
        foreach($bills as $bill) {
            $bill_process = BillProgress::where('bill_id', $bill->id)->whereIn('status', ['Khách xác nhận xong', 'Kết thúc'])->first();
            
            if(is_object($bill_process)) {
                if ($bill_process->dh_id == $ky_thuat_id) {
                    //  nếu dự án do mình điều hành thì tính điểm điều hành
                    if (isset($dh[$bill->service_id])) {

                        $data['tong_diem'] += (float) @$dh[$bill->service_id];
                    }
                }
                if ($bill_process->kt_id == $ky_thuat_id) {
                    //  nếu dự án do mình triển khai thì tính điểm kỹ thuật
                    $data['tong_diem'] += @$kt[$bill->service_id];
                }
            }
        } 



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
        //  Mặc định là không lấy dự án bỏ, nếu có lọc ra dự án bỏ thì mới hiển thị
        if (@$request->progress != 'Bỏ') {
            //  Không lấy các dự án đã bỏ
            $bill_bo_ids = BillProgress::whereIn('status', ['Bỏ'])
                                    ->pluck('bill_id')->toArray();
            $query = $query->whereNotIn('id', $bill_bo_ids);
        }

        if(@$request->progress == 'Bỏ') {
            //  Không lấy các dự án đã bỏ
            $bill_bo_ids = BillProgress::whereIn('status', ['Bỏ'])
                                    ->pluck('bill_id')->toArray();
            $query = $query->whereIn('id', $bill_bo_ids);
        } elseif(@$request->progress == 'Đang làm') {
            //  Lọc dự án đang làm
            $bill_bo_ids = BillProgress::whereIn('status', [
                'Thu thập YCTK L1',
                'Triển khai L1',
                'Nghiệm thu L1 & thu thập YCTK L2',
                'Triển khai L2',
                'Nghiệm thu L2 & thu thập YCTK L3',
                'Triển khai L3',
                'Nghiệm thu L3 & thu thập YCTK L4',
                'Triển khai L4',
                'Nghiệm thu L4 & thu thập YCTK L5',
                'Triển khai L5',
                'Nghiệm thu L5 & thu thập YCTK L6',
                'Triển khai L6',
            ])
                ->pluck('bill_id')->toArray();
            $query = $query->whereIn('id', $bill_bo_ids);
        } elseif(@$request->progress == 'Khách xác nhận xong') {
            //  Lọc dự án khách xác nhận xong
            $bill_bo_ids = BillProgress::whereIn('status', [
                'Khách xác nhận xong'
            ])
                ->pluck('bill_id')->toArray();
            $query = $query->whereIn('id', $bill_bo_ids);
        } elseif(@$request->progress == 'Kết thúc') {
            //  Lọc dự án kết thúc - tạm dừng
            $bill_bo_ids = BillProgress::whereIn('status', [
                'Tạm dừng', 'Kết thúc'
            ])
                ->pluck('bill_id')->toArray();
            $query = $query->whereIn('id', $bill_bo_ids);
        }

        //  Không lấy các dự án là duy trì web,
        $query = $query->whereNotIn('service_id', [3,   //  tên miền
            4,  //  email
            7,  // duy trì web
            ]);


        //  Chỉ truy vấn ra các đơn đang triển khai
        $bill_ids = BillProgress::select('bill_id');
        // ->where(function ($query) use ($request) {
        //             // $query->orWhereNotIn('status', ['Kết thúc']);
        //             $query->orWhere('status', null);
        //             $query->orWhere('status', '');
        //         });

        // //  Nếu ko tìm kiếm thì ko hiện ra trạng thái đã kết thúc
        // if (!$request->has('quick_search')) {

        //     $bill_ids = $bill_ids->whereNotIn('status', ['Kết thúc']);
        // }

        if (@$request->ngay_hoan_thanh != null) {
            if ($request->ngay_hoan_thanh == 'thang_truoc') {
                $bill_ids_hoan_thanh = BillProgressHistory::where('created_at', '>', date('Y-m-01 00:00:00', strtotime(date('Y-m')." -1 month")))
                                ->where('created_at', '<', date('Y-m-t 23:59:00', strtotime(date('Y-m')." -1 month")))
                                ->whereIn('new_value', ['Khách xác nhận xong', 'Kết thúc'])
                                ->pluck('bill_id')->toArray();
                $query = $query->whereIn('id', $bill_ids_hoan_thanh);
            } elseif ($request->ngay_hoan_thanh == 'thang_nay') {
                $bill_ids_hoan_thanh = BillProgressHistory::where('created_at', '>', date('Y-m-01 00:00:00'))
                                ->where('created_at', '<', date('Y-m-t 23:59:00'))
                                ->whereIn('new_value', ['Khách xác nhận xong', 'Kết thúc'])
                                ->pluck('bill_id')->toArray();
                $query = $query->whereIn('id', $bill_ids_hoan_thanh);
            }
        }

        //  Lọc theo người điều hành / kỹ thuật
        if (@$request->ky_thuat_id != null) {
            $bill_ids = BillProgress::where(function ($query) use ($request) {
                            $query->orWhere('dh_id', $request->ky_thuat_id);    //  tìm các dự án điều hành
                            $query->orWhere('kt_id', $request->ky_thuat_id);    //  tìm các dự án kỹ thuật
                        })
                        ->pluck('bill_id')->toArray();
            $query = $query->whereIn('id', $bill_ids);
        }
        

        if (\Auth::guard('admin')->user()->super_admin != 1) {
            //  Nếu ko phải super_admin thì truy vấn theo dữ liệu cty đó
            // $query = $query->where('company_id', \Auth::guard('admin')->user()->last_company_id);
        }

        //  Kiểu tra dự án chưa làm xong, nhập vào ds dự án báo ra dự án nào chưa làm xong
        if (@$request->check_chua_xong != null) {
            $domains = $request->check_chua_xong;
            $domains = preg_split('/\r\n|[\r\n]/',$domains);

            $bill_ids = [];
            foreach($domains as $domain) {
                $bill = Bill::select('id', 'domain')->where('domain', $domain)->first();

                if (is_object($bill)) {
                    if (BillProgress::where('bill_id', $bill->id)->whereIn('status', [
                            'Khách xác nhận xong',
                            'Kết thúc',
                        ])->count() == 0) {
                        //  Nếu dự án này chưa xong thì cho vào bộ lọc để báo cáo ra màn hình
                        $bill_ids[] = $bill->id;
                    }
                } else {
                    echo '<br><br><br><h3 style="text-align: center;">Không có tên miền: ' . $domain . '</h3>';
                }
            }

            $query = $query->whereIn('id', $bill_ids);
        }

        return $query;
    }

    public function sort($request, $model)
    {
        $orderRaw = 'CASE ';

        //  - Nếu dự án chưa set trạng thái thì đẩy lên đầu
        //  lấy id các dự án đang làm
        $bill_id_dang_lam = BillProgress::where(function ($query) use ($request) {
                            $query->orWhere('status', '');    //  
                            $query->orWhereNull('status');    //  
                        })->pluck('bill_id')->toArray();


        if (!empty($bill_id_dang_lam)) {
            $orderRaw .= " WHEN id in (";

            foreach($bill_id_dang_lam as $k => $v) {
                if ($k == 0) {
                    $orderRaw .= $v;
                } else {
                    $orderRaw .= "," . $v;
                }
            }
            $orderRaw .= ') THEN 1';
        }


        //  - Nếu dự án đang làm thì đẩy lên trên
        //  lấy id các dự án đang làm
        $bill_id_dang_lam = BillProgress::whereNotIn('status', ['Kết thúc', 'Tạm dừng', 'Khách xác nhận xong'])->pluck('bill_id')->toArray();

        if (!empty($bill_id_dang_lam)) {
            //  Nếu là dự án đang làm thì ưu tiên hiển thị lên trên
            $orderRaw .= " WHEN id in (";

            foreach($bill_id_dang_lam as $k => $v) {
                if ($k == 0) {
                    $orderRaw .= $v;
                } else {
                    $orderRaw .= "," . $v;
                }
            }
            $orderRaw .= ') THEN 2';
        }

        //  - Nếu dự án chưa thanh toán thì đẩy lên trên
        // $orderRaw .= ' WHEN total_price_contract != total_received THEN 3';


        //  - Nếu dự án "khách xác nhận xong" đẩy lên trên
        //  lấy id các dự án đang làm
        $bill_id_dang_lam = BillProgress::whereIn('status', ['Khách xác nhận xong'])->pluck('bill_id')->toArray();

        if (!empty($bill_id_dang_lam)) {
            //  Nếu là dự án đang làm thì ưu tiên hiển thị lên trên
            $orderRaw .= " WHEN id in (";

            foreach($bill_id_dang_lam as $k => $v) {
                if ($k == 0) {
                    $orderRaw .= $v;
                } else {
                    $orderRaw .= "," . $v;
                }
            }
            $orderRaw .= ') AND total_price_contract != total_received THEN 3';
        }


        //  - Nếu dự án "tạm dừng" đẩy lên trên
        //  lấy id các dự án đang làm
        $bill_id_dang_lam = BillProgress::whereIn('status', ['Tạm dừng'])->pluck('bill_id')->toArray();

        if (!empty($bill_id_dang_lam)) {
            //  Nếu là dự án đang làm thì ưu tiên hiển thị lên trên
            $orderRaw .= " WHEN id in (";

            foreach($bill_id_dang_lam as $k => $v) {
                if ($k == 0) {
                    $orderRaw .= $v;
                } else {
                    $orderRaw .= "," . $v;
                }
            }
            $orderRaw .= ') AND total_price_contract != total_received THEN 4';
        }


        //  Nếu là các dự án chưa xong thì cho xuống dưới
        $orderRaw .= ' ELSE id END ASC';

        $model = $model->orderByRaw($orderRaw);
        $model = $model->orderBy('id', 'desc'); 

        return $model;
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


                return view('CRMBDS.dhbill.edit')->with($data);
            }

            \DB::beginTransaction();

            $data = $this->processingValueInFields($request, $this->getAllFormFiled());

            $billProgress = BillProgress::where('Bill_id', $item->id)->first();

            if (in_array($request->progress_status, ['Khách xác nhận xong'])) {
                //  Nếu khách xác nhận xong mà ko có ảnh bằng chứng thì không lưu
                if ($request->kh_xong_image == null && !is_object(BillProgress::where('Bill_id', $item->id)->whereNotNull('kh_xong_image')->first())) {
                    CommonHelper::one_time_message('error', 'Cần nhập ảnh bằng chứng!');
                    return back()->withInput();
                }
            }

            if (!in_array($billProgress->progress_status, ['Kết thúc', 'Tạm dừng', 'Bỏ']) && in_array($request->progress_status, ['Kết thúc', 'Tạm dừng', 'Bỏ'])) {
                //  Chỉ super_admin mới đưc phép chuyển dự án sang trạng thái này
                if (\Auth::guard('admin')->user()->super_admin != 1) {
                    CommonHelper::one_time_message('error', 'Bạn không có quyền chuyển trạng thái dự án sang : '.$request->progress_status.' !');
                    return back()->withInput();
                }
            }

            //  Tùy chỉnh dữ liệu insert

            // //  nếu thuê tên miền bên mình thì tạo 1 hóa đơn cho tên miền đó
            // if ($data['domain_owner'] == 'hobasoft') {
            //     $this->createBillForDomain($data, $item);
            // }

            // //  nếu thuê hosting bên mình thì tạo 1 hóa đơn cho hosting đó
            // if ($data['hosting_owner'] == 'hobasoft') {
            //     $this->createBillForHosting($data, $item);
            // }

            //  Làm chuẩn domain
            $data['domain'] = str_replace('http://', '', $data['domain']);
            $data['domain'] = str_replace('https://', '', $data['domain']);
            $data['domain'] = str_replace('www.', '', $data['domain']);
            $data['domain'] = strtolower($data['domain']);
            if (in_array($item->service_id, [17, 18, 19, 20]) && strpos($data['domain'], 'www.') === false) {
                //  nếu là gói thiết kế LDP mà tên miền ko có www. ở đầu thì cho thêm vào đầu
                $data['domain'] = 'www.' . $data['domain'];
            }

            unset($data['file_ldp']);

            foreach ($data as $k => $v) {
                $item->$k = $v;
            }
            if ($item->save()) {

                //  Cập nhật lịch sử thay đổi trạng thái triển khai
                $this->updateBillProgressHistory($request, $item);

                //  cập nhật tiến độ triển khai dự án
                $this->updateBillProgress($request, $item);

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
                    $leadController = new \App\CRMBDS\Admin\LeadController();
                    $leadController->LeadContactedLog([
                        'title' => $request->log_name,
                        'note' => $request->log_note,
                        'lead_id' => $item->id,
                        'type' => 'hđ',
                    ]);
                }

                \DB::commit();
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

    //  cập nhật lịch sử thay đổi trạng thái triển khai dự án
    public function updateBillProgressHistory($request, $bill){
        $bill_process = BillProgress::where('bill_id', $bill->id)->first();

        if (is_object($bill_process) && @$bill_process->status != $request->progress_status) {
            //  Nếu trang thái triển khai bị thay đổi thì lưu lịch sử
            BillProgressHistory::create([
                'admin_id' => @\Auth::guard('admin')->user()->id,
                'bill_id' => $bill->id,
                'old_value' => $bill_process->status,
                'new_value' => $request->progress_status,
                'note' => 'Thay đổi trạng thái triển khai',
                'type' => 'bill_progress_status_change' //  thay đổi trạng thái triển khai dự án
            ]);
        }
        return true;
    }

    //  cập nhật tiến độ triển khai dự án
    public function updateBillProgress($request, $bill) {
        $BillProgress = BillProgress::where('bill_id', $bill->id)->first();

        //  Nếu dự án chuyển sang trạng thái khách xác nhận xong thì log ngày khác xác nhận xong
        if (in_array($request->progress_status, ['Khách xác nhận xong', 'Kết thúc', 'Bỏ']) && !in_array($BillProgress->status, ['Khách xác nhận xong', 'Kết thúc', 'Bỏ'])) {
            $BillProgress->kh_xong_date = date('Y-m-d H:i:s');
        }

        //  lấy thông tin triển khai ở form
        $BillProgress->status = $request->progress_status;
        $BillProgress->rate_content = $request->progress_rate_content;
        $BillProgress->dh_id = $request->progress_dh_id;
        $BillProgress->kt_id = $request->progress_kt_id;
        $BillProgress->reminder_customer = $request->progress_reminder_customer;

        if ($request->progress_rate != null) {
            $BillProgress->rate = $request->progress_rate;
        }

        if ($request->progress_rate_content != null) {
            $BillProgress->rate_content = $request->progress_rate_content;
        }


        //  kiểm tra cập nhật ảnh bằng chứng xong dự án
        if ($request->get('kh_xong_image_delete', 0) == 0) {
            if ($request->file('kh_xong_image') != null) {
                $BillProgress->kh_xong_image = CommonHelper::saveFile($request->file('kh_xong_image'), $this->module['code']. date('/Y/m/d'));
            }
        } else {
            $BillProgress->kh_xong_image = '';
        }


        //  Cập nhật tiến độ triển khai dự án
        $BillProgress->save();

        return $BillProgress;
    }

    public function changeStatus(Request $r) {

        BillProgress::whereNotIn('status', [
            'Kết thúc',
            'Bỏ',
            'Tạm dừng'])->whereIn('bill_id', explode(',', $r->bill_ids))->update(['status' => $r->status]);

        CommonHelper::one_time_message('success', 'Thành công!');
        return redirect()->back();
    }
}
