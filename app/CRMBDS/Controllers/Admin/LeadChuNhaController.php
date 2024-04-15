<?php

namespace App\CRMBDS\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Illuminate\Http\Request;
use App\CRMBDS\Models\Service;
use App\CRMBDS\Models\ServiceHistory;
use Validator;
use App\CRMBDS\Models\LeadContactedLog;
use App\CRMBDS\Models\Lead;
use App\Models\Admin;
use App\CRMBDS\Models\Bill;

class LeadChuNhaController extends CURDBaseController
{

    protected $whereRaw = "type = 'chủ nhà'";

    protected $module = [
        'code' => 'lead_chu_nha',
        'table_name' => 'leads',
        'label' => 'Chủ nhà',
        'modal' => '\App\CRMBDS\Models\Lead',
        'list' => [
            ['name' => 'name', 'type' => 'custom', 'td' => 'CRMBDS.lead_chu_nha.list.name', 'label' => 'Tên', 'sort' => true],
            ['name' => 'tel', 'type' => 'custom', 'td' => 'CRMBDS.lead_chu_nha.list.tel', 'label' => 'SĐT', 'sort' => true],
            ['name' => 'can_so', 'type' => 'text', 'label' => 'Căn số', 'sort' => true],
            ['name' => 'dien_tich', 'type' => 'text', 'label' => 'Diện tích', 'sort' => true],
            ['name' => 'project', 'type' => 'text', 'label' => 'Dự án', 'sort' => true],
            ['name' => 'service', 'type' => 'select', 'options' => [
                    '' => '',
                    'Cho thuê' => 'Cho thuê',
                    'Muốn bán' => 'Muốn bán',
                    'Muốn mua' => 'Muốn mua',
                    'Đang cho thuê' => 'Đang cho thuê',
                    'Chưa bán' => 'Chưa bán',
                ], 'label' => 'Dịch vụ', 'sort' => true],
            ['name' => 'status', 'type' => 'select', 'options' => [
                    'Chưa bán' => 'Chưa bán',
                    'Đang chào bán' => 'Đang chào bán',
                    'Bán rồi' => 'Bán rồi',
                    'Mua thêm' => 'Mua thêm',
                    'Ở' => 'Ở',
                ], 'label' => 'Trạng thái', 'sort' => true],
            ['name' => 'contacted_log_last', 'type' => 'custom', 'td' => 'CRMBDS.lead_chu_nha.list.contacted_log_last', 'label' => 'TT lần cuối', 'sort' => true],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên', 'group_class' => 'col-md-3'],
                ['name' => 'tel', 'type' => 'text', 'field' => 'CRMBDS.lead_chu_nha.form.tel', 'class' => 'required', 'label' => 'SĐT', 'group_class' => 'col-md-3'],
                ['name' => 'can_so', 'type' => 'text', 'label' => 'Căn số', 'group_class' => 'col-md-3'],
                ['name' => 'dien_tich', 'type' => 'text', 'class' => '', 'label' => 'Diện tích', 'group_class' => 'col-md-3'],

                ['name' => 'loai_hinh', 'type' => 'select', 'options' => [
                    '' => '',
                    'Liền kề' => 'Liền kề',
                    'Nhà vườn' => 'Nhà vườn',
                    'Biệt thự đơn lập' => 'Biệt thự đơn lập',
                    'Biệt thự song lập' => 'Biệt thự song lập',
                ], 'label' => 'Loại hình', 'group_class' => 'col-md-3'],
                ['name' => 'project', 'type' => 'text', 'label' => 'Tên dự án', 'group_class' => 'col-md-3'],
                ['name' => 'status', 'type' => 'select', 'options' => [
                    'Chưa bán' => 'Chưa bán',
                    'Đang chào bán' => 'Đang chào bán',
                    'Bán rồi' => 'Bán rồi',
                    'Mua thêm' => 'Mua thêm',
                    'Ở' => 'Ở',
                    
                ], 'label' => 'Trạng thái', 'group_class' => 'col-md-3'],
                ['name' => 'service', 'type' => 'select2', 'options' => [
                   '' => '',
                    'Cho thuê' => 'Cho thuê',
                    'Muốn bán' => 'Muốn bán',
                    'Muốn mua' => 'Muốn mua',
                    'Đang cho thuê' => 'Đang cho thuê',
                    'Chưa bán' => 'Chưa bán',
                ], 'label' => 'Dịch vụ', 'multiple' => true, 'group_class' => 'col-md-3'],

                // ['name' => 'rate', 'type' => 'select', 'options' => [
                //     '' => '',
                //     'Đang muốn bán' => 'Đang muốn bán',
                //     'Bán tương lai' => 'Bán tương lai',
                //     'Chưa bán' => 'Chưa bán',
                // ], 'label' => 'Đánh giá', 'group_class' => 'col-md-3'],
                
                
                // ['name' => 'dating', 'type' => 'custom', 'field' => 'CRMBDS.lead_chu_nha.form.dating', 'class' => '', 'label' => 'Đặt hẹn ngày tương tác', 'group_class' => 'col-md-3'],
                
                
            ],
            'tab_2' => [
                ['name' => 'need', 'type' => 'textarea', 'label' => 'Nhu cầu chủ nhà', 'group_class' => 'col-md-12', 'inner' => 'rows=10'],
                ['name' => 'product', 'type' => 'text', 'label' => 'Sản phẩm/dịch vụ', 'group_class' => 'col-md-12'],
                ['name' => 'profile', 'type' => 'textarea', 'label' => 'Ghi chú khác', 'group_class' => 'col-md-12', 'inner' => 'rows=10'],
            ],
        ]
    ];

    protected $quick_search = [
        'label' => 'ID, tên, sđt, đánh giá, mô tả',
        'fields' => 'id, name, tel, rate, profile, need, product, can_so'
    ];

    protected $filter = [
        'saler_ids' => [
            'label' => 'Sale',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'model' => \App\Models\Admin::class,
            'object' => 'admin',
            'query_type' => 'custom'
        ],
        
        'service' => [
            'label' => 'Dịch vụ',
            'type' => 'select',
            'options' => [
                    '' => '',
                    '|Cho thuê|' => 'Cho thuê',
                    'Muốn bán' => 'Muốn bán',
                    'Muốn mua' => 'Muốn mua',
                    'Đang cho thuê' => 'Đang cho thuê',
                    'Chưa bán' => 'Chưa bán',
                ],
                'query_type' => 'like'
        ],
        'status' => [
            'label' => 'Trạng thái',
            'type' => 'select',
            'options' => [
                '' => '',
                    'Chưa bán' => 'Chưa bán',
                    'Đang chào bán' => 'Đang chào bán',
                    'Bán rồi' => 'Bán rồi',
                    'Mua thêm' => 'Mua thêm',
                    'Ở' => 'Ở',
            ],
            'query_type' => '='
        ],
        'loai_hinh' => [
            'label' => 'Loại hình',
            'type' => 'select',
            'options' => [
                '' => '',
                    'Liền kề' => 'Liền kề',
                    'Nhà vườn' => 'Nhà vườn',
                    'Biệt thự đơn lập' => 'Biệt thự đơn lập',
                    'Biệt thự song lập' => 'Biệt thự song lập',
            ],
            'query_type' => '='
        ],
       
        'sale_status' => [
            'label' => 'Tình trạng sale',
            'type' => 'select',
            'options' => [
                '' => 'Tất cả',
                'Chưa có sale' => 'Chưa có sale',
                'Đã có sale' => 'Đã có sale',
            ],
            'query_type' => 'custom'
        ],
        
        'contacted_log_last' => [
            'label' => 'Ngày TT',
            'type' => 'from_to_date',
            'query_type' => 'from_to_date'
        ],
        
    ];

    public function appendWhere($query, $request)
    {

        if($request->status == null) {
            $query = $query->where('status', '!=', 'Đã ký HĐ');
        }
    
        if (@$request->marketer_ids != null) {
            $query = $query->where(function ($query) use ($request) {
                    $query->orWhere('marketer_ids', 'like', '%|' . $request->marketer_ids . '|%');
                    $query->orWhere('admin_id', $request->marketer_ids);
                });
        }
        
        if (@$request->saler_ids != null) {
            $query = $query->where('saler_ids', 'like', '%|' . $request->saler_ids . '|%');
        }
        if (@$request->sale_status != null) {
            if ($request->sale_status == 'Chưa có sale') {
                $query = $query->where(function ($query) use ($request) {
                    $query->orWhere('saler_ids', '|');
                    $query->orWhere('saler_ids', '||');
                    $query->orWhere('saler_ids', '');
                    $query->orWhere('saler_ids', null);
                });
            } else if ($request->sale_status == 'Đã có sale') {
                $query = $query->where('saler_ids', '!=', '|')->where('saler_ids', '!=', '||')
                    ->where('saler_ids', '!=', '')
                    ->where('saler_ids', '!=', null);
            }
        }
        if (@$request->lead_status != null) {
            if ($request->lead_status == 'Sắp thả nổi') {
                $query = $query->where('saler_ids', '!=', '|')->where('saler_ids', '!=', '||')
                    ->where('saler_ids', '!=', '')
                    ->where('saler_ids', '!=', null)
                    ->where('status', 'Đang chăm sóc');
            }
        }
      

        if (\Auth::guard('admin')->user()->super_admin != 1) {
            //  Nếu ko phải super_admin thì truy vấn theo dữ liệu cty đó
            // $query = $query->where('company_id', \Auth::guard('admin')->user()->last_company_id);
        }

        if (\Auth::guard('admin')->user()->super_admin != 1) {
            //  Nếu ko phải super_admin
            
            if (strpos($request->url(), '/tha-noi') !== false) {
                //  Truy vấn ra lead thả nổi
                $query = $query->whereIn('status', ['Thả nổi']);
            } else {
                //  Truy vấn lead mình đang chăm
                $query = $query->whereNotIn('status', ['Thả nổi']);

                //  thì truy vấn dữ liệu của mình
                $query = $query->where(function ($query) use ($request) {
                    $query->orWhere('saler_ids', 'like', '%|'.\Auth::guard('admin')->user()->id.'|%');
                    // $query->orWhere('admin_id', \Auth::guard('admin')->user()->id);
                });
            }
        
        }

        return $query;
    }

    public function sort($request, $model)
    {
        if (@$request->lead_status != null) {
            if ($request->lead_status == 'Sắp thả nổi') {
                $model = $model->orderBy('contacted_log_last', 'asc');
            } elseif ($request->lead_status == 'Ngày tạo: Mới -> cũ') {
                $model = $model->orderBy('id', 'desc');
            } elseif ($request->lead_status == 'Ngày nhận: Mới -> cũ') {
                $model = $model->orderBy('received_date', 'desc');
            } elseif ($request->lead_status == 'Đến ngày TT') {
                $model = $model->orderBy('dating', 'asc')
                            ->where('dating', '<=', date('Y-m-d 23:59:59'));
            }
        }
        if ($request->sorts != null) {
            foreach ($request->sorts as $sort) {
                if ($sort != null) {
                    $sort_data = explode('|', $sort);
                    $model = $model->orderBy($sort_data[0], $sort_data[1]);
                }
            }
        } else {
            $model = $model->orderByRaw($this->orderByRaw);
        }
        return $model;
    }

    public function quickSearch($listItem, $r) {
        if (@$r->quick_search != '') {
            $listItem = $listItem->where(function ($query) use ($r) {
                foreach (explode(',', $this->quick_search['fields']) as $field) {
                    $query->orWhere(trim($field), 'LIKE', '%' . $r->quick_search . '%');    //  truy vấn các tin thuộc các danh mục con của danh mục hiện tại
                }

                //  Tìm theo sđt 
                $search_tel = str_replace('.', '', $r->quick_search);
                $search_tel = str_replace(',', '', $search_tel);
                $search_tel = trim($search_tel);
                $query->orWhere('tel', 'LIKE', '%' . $search_tel . '%');
            });

        }
        return $listItem;
    }

    public function getIndex(Request $request)
    {

        $data = $this->getDataList($request);

        return view('CRMBDS.lead_chu_nha.list')->with($data);
    }

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMBDS.lead_chu_nha.add')->with($data);
            } else if ($_POST) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'tel' => 'required',
                ], [
                    'name.required' => 'Bắt buộc phải nhập tên',
                    'tel.required' => 'Bắt buộc phải nhập sđt',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert
                    if (CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'lead_assign')) {
                        $data['marketer_ids'] = '|' . implode('|', $request->get('marketer_ids', [])) . '|';
                        $data['saler_ids'] = '|' . implode('|', $request->get('saler_ids', [])) . '|';

                       
                    }
                    if ($data['marketer_ids'] == '||') {
                        $data['marketer_ids'] = '|'.\Auth::guard('admin')->user()->id.'|';
                    }
                    if ($data['saler_ids'] == '||') {
                        $data['saler_ids'] = '|'.\Auth::guard('admin')->user()->id.'|';   
                    }
                    $data['admin_id'] = \Auth::guard('admin')->user()->id;
                    $data['received_date'] = date('Y-m-d H:i:s');
                    

                    $data['type'] = 'chủ nhà';


                    if ($request->has('service')) {
                        $data['service'] = '|' . implode('|', $data['service']) . '|';
                    } else {
                        $data['service'] = '';
                    }

                   
                   
                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
                        $this->afterAddLog($request, $this->model);

                        /*LeadContactedLog::create([
                            'title' => '', 
                            'admin_id' => \Auth::guard('admin')->user()->id, 
                            'lead_id' => $this->model->id,
                            'note' => 'Tạo mới'
                        ]);*/

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
                        return redirect('admin/' . $this->module['code'] . '/' . $this->model->id);
                    } elseif ($request->return_direct == 'save_create') {
                        return redirect('admin/' . $this->module['code'] . '/add');
                    }

                    return redirect('admin/' . $this->module['code'] . '/edit?code=' . $this->model->tel .'-' .date('d-m-Y', strtotime($this->model->created_at)) . '-' . $this->model->id);
                }
            }
        } catch (\Exception $ex) {
            CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function getTimePrice($request)
    {
        $time_price = [];
        if ($request->has('time_price_use_date_max')) {
            foreach ($request->time_price_use_date_max as $k => $key) {
                if ($key != null) {
                    $time_price[] = [
                        'use_date_max' => $key,
                        'price' => $request->time_price_price[$k],
                    ];
                }
            }
        }
        return $time_price;
    }

    public function getDataUpdate(Request $request, $item)
    {
        $data['module'] = $this->module;
        $data['result'] = $item;
        $data['page_title'] = 'Thông tin ' . trans($this->module['label']);
        $data['page_type'] = 'update';
        return $data;
    }

    public function update(Request $request)
    {
        try {

            $code = $request->get('code', '');
            $id = explode('-', $code)[count(explode('-', $code)) - 1];
            $item = $this->model->find($id);

            if (!is_object($item)) abort(404);
            if (!$_POST) {
                if ($item->tel != @explode('-', $code)[0]) {
                    CommonHelper::one_time_message('error', 'Đường dẫn không tồn tại');
                    return redirect('/admin');
                }

                $data = $this->getDataUpdate($request, $item);
                return view('CRMBDS.lead_chu_nha.edit')->with($data);
            } else if ($_POST) {

                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'tel' => 'required',
                ], [
                    'name.required' => 'Bắt buộc phải nhập tên',
                    'tel.required' => 'Bắt buộc phải nhập sđt',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert
                    if (\Auth::guard('admin')->user()->super_admin == 1 || \Auth::guard('admin')->user()->id == $item->admin_id) {
                        //  Nếu là super admin hoặc là người tạo đầu mối thì được phép sửa mkt
                        $data['marketer_ids'] = '|' . implode('|', $request->get('marketer_ids', [])) . '|';
                    }

                    if (\Auth::guard('admin')->user()->super_admin == 1 || strpos($item->saler_ids, '|'.\Auth::guard('admin')->user()->id.'|') !== false) {
                        //  Nếu là super admin hoặc đầu mối của mình là sale thì được phép sửa sale
                        $data['saler_ids'] = '|' . implode('|', $request->get('saler_ids', [])) . '|';
                    } else {
                        //  Nếu chuyển trạng thái từ Thả nổi về đang chăm sóc thì reset lại sale
                        if ($item->status == 'Thả nổi' && $data['status'] == 'Đang chăm sóc') {
                            // $data['saler_ids'] = '|'. \Auth::guard('admin')->user()->id .'|';
                        }
                    }

                    if ($request->has('service')) {
                        $data['service'] = '|' . implode('|', $data['service']) . '|';
                    } else {
                        $data['service'] = '';
                    }
                    
                    //  Nếu thay đổi sale thì tạo log
                    $data = $this->changeSale($data, $item);
                    
                    //  Nếu thay đổi trạng thái thì tạo log
                    $this->changeStatus($data, $item);

                    foreach ($data as $k => $v) {
                        $item->$k = $v;
                    }
                    if ($item->save()) {
                        if (isset($log_create)) {
                            LeadContactedLog::create($log_create);
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
                        return redirect('admin/' . $this->module['code'] . '/edit?code=' . $item->tel .'-' .date('d-m-Y', strtotime($item->created_at)) . '-' . $item->id);
                    } elseif ($request->return_direct == 'save_create') {
                        return redirect('admin/' . $this->module['code'] . '/add');
                    } elseif ($request->return_direct == 'save_exit') {
                        return redirect('admin/' . $this->module['code']);
                    }

                    return redirect('admin/' . $this->module['code']);
                }
            }
        } catch (\Exception $ex) {
            // CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
           CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
        }
    }

    //  Kiểm tra thay đổi sale thì log lại lịch sử
    public function changeSale($data, $item) {
        if (@$data['saler_ids'] != null && @$data['saler_ids'] != $item->saler_ids) {
            $data['received_date'] = date('Y-m-d H:i:s');    //  reset lai ngay nhan lead
            $data['contacted_log_last'] = null; //  reset ngày cuối tương tác
            $sales_old = Admin::whereIn('id', explode('|', $item->saler_ids))->pluck('name');
            $sales_new = Admin::whereIn('id', explode('|', @$data['saler_ids']))->pluck('name');
            $txt = 'từ ';
            foreach($sales_old as $v) {
                $txt .= $v . ', ';
            }
            $txt .= ' sang ';
            foreach($sales_new as $v) {
                $txt .= $v . ', ';
            }
            $log_create = [
                'title' => '', 
                'admin_id' => \Auth::guard('admin')->user()->id, 
                'lead_id' => $item->id,
                'note' => 'Đổi sale ' . $txt,
                'type' => 'lead',
            ];
        }
        return $data;
    }

    //  Kiểm tra thay đổi trạng thái thì log lại lịch sử
    public function changeStatus($data, $item) {
        if (@$data['status'] != $item->status) {
            $log_create = [
                'title' => '', 
                'admin_id' => \Auth::guard('admin')->user()->id, 
                'lead_id' => $item->id,
                'note' => 'Đổi trạng thái: từ "' . $item->status . '" sang "'.$data['status'].'"',
                'type' => 'lead',
            ];
        }
        return true;
    }

    public function view(Request $request) {
        $code = $request->get('code', '');
        $id = explode('-', $code)[count(explode('-', $code)) - 1];
        $item = $this->model->find($id);

        if (!is_object($item)) abort(404);

        if ($item->tel != @explode('-', $code)[0]) {
            CommonHelper::one_time_message('error', 'Đường dẫn không tồn tại');
            return redirect('/admin');
        }
        
   
        $data = $this->getDataUpdate($request, $item);
        return view('CRMBDS.lead_chu_nha.view')->with($data);
    }

    public function getPublish(Request $request)
    {
        try {

            $item = $this->model->find($request->id);

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
            \DB::beginTransaction();
            $item = $this->model->find($request->id);

            LeadContactedLog::where('lead_id', $request->id)->where('type', 'lead')->delete();

            $item->delete();
            \DB::commit();
            CommonHelper::one_time_message('success', 'Xóa thành công!');
            return redirect('admin/' . $this->module['code']);
        } catch (\Exception $ex) {
            \DB::rollback();
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

    public function getPriceInfo($request)
    {
        $price = [];
        if ($request->has('price_day')) {
            foreach ($request->price_day as $k => $key) {
                if ($key != null) {
                    $price[] = [
                        'day' => $key,
                        'price' => str_replace(',', '', str_replace('.', '', $request->price_price[$k])),
                    ];
                }
            }
        }
        return $price;
    }

    public function show()
    {
        $data['page_title'] = 'Các gói dịch vụ';
        $data['page_type'] = 'list';
        return view('CRMBDS.lead_chu_nha.show')->with($data);
    }

    public function get_info(Request $r) {
        $service = Service::find($r->service_id);
        $configPrice = json_decode($service->price);
        foreach ($configPrice as $v) {
            if ($v->day == 'start') {
                $priceStart = $v->price;
            }
            if ($v->day == '365') {
                $priceExpiry = $v->price;
            }
        }
        return response()->json([
            'total_price' => $priceStart,
            'total_price_format' => number_format($priceStart),
            'exp_price' => $priceExpiry,
            'exp_price_format' => number_format($priceExpiry)
        ]);
    }

    public function leadContactedLog(Request $r) {

        $data = $r->except('');

        $log = new LeadContactedLog();
        foreach ($data as $k => $v) {
            $log->$k = $v;
        }
        $log->admin_id = @\Auth::guard('admin')->user()->id;
       
        $log->save();

        if($data['type'] == 'lead') {
            Lead::where('id', $r->lead_id)->update([
                'contacted_log_last' => date('Y-m-d H:i:s'),
            ]);
        } elseif($data['type'] == 'hđ') {
            Bill::where('id', $r->lead_id)->update([
                'contacted_log_last' => date('Y-m-d H:i:s'),
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $log,
            'msg' => 'Thành công'
        ]);

    }

    public function tooltipInfo(Request $r) {
        $data['lead'] = Lead::find($r->id);


        return view('CRMBDS.lead_chu_nha.tooltip_info')->with($data);
    }

    public function sendMail() {
        $logs = LeadContactedLog::where('created_at', '>', date('Y-m-d 00:00:00'))->where('type', 'lead')->get();

        $settings = Setting::whereIn('name', ['admin_emails', 'mail_name', 'admin_email', 'admin_receives_mail'])->pluck('value', 'name')->toArray();
        $admins = explode(',', $settings['admin_emails']);
        if ($settings['admin_receives_mail'] == 1) {
            $admins = explode(',', $settings['admin_emails']);
            foreach ($admins as $admin) {
                $user = (object)[
                    'email' => trim($admin),
                    'name' => $settings['mail_name'],
                ];
                $data = [
                    'view' => 'CRMBDS.lead_chu_nha.emails.tien_do_cong_viec',
                    'link_view_more' => \URL::to('/admin/lead'),
                    'user' => $user,
                    'name' => $settings['mail_name'],
                    'subject' => 'Cập nhật công việc trong ngày'
                ];
                Mail::to($user)->send(new MailServer($data));
            }
        }
        die('xong!');
    }

    
    public function importExcel(Request $request)
    {
        $leads = Lead::where('profile', 'like', '%~~~%')->get();

        foreach($leads as $v) {
        
            $v->profile = str_replace('~~~', ',', $v->profile);
            $v->save();
        }
        die('xong');
    }

    public function checkExist(Request $request) {
        if ($request->has('tel')) {
            $leads = Lead::select('id', 'email', 'name', 'tel', 'created_at', 'admin_id')->where('tel', $request->tel);
            if ($request->has('id')) {
                $leads = $leads->where('id', '!=', $request->id)->first();
            }
            $leads = $leads->get();
            if (count($leads) > 0) {
                $txt = 'SĐT này trùng với đầu mối:<br>';
                foreach($leads as $v) {
                    $txt .= ' - <a target="_blank" href="/admin/lead/edit?code='.$v->tel.'-'.$v->created_at.'-'.$v->id.'">'.$v->name.'-'.$v->tel.'. Tạo bởi: ' . @$v->admin->name . '. Lúc: ' . @date('H:i d/m/Y', strtotime($v->created_at)) .'</a><br>';
                }
                return response()->json([
                    'status' => false,
                    'html' => $txt,
                ]);
            }
        }
        return response()->json([
            'status' => true,
            'html' => ''
        ]);
    }

    public function adminSearchForSelect2(Request $request)
    {
        $col2 = $request->get('col2', '') == '' ? '' : ', ' . $request->get('col2');

        $data = Admin::selectRaw('id, name')->where($request->col, 'like', '%' . $request->keyword . '%');

        if ($request->where != '') {
            $data = $data->whereRaw(urldecode(str_replace('&#039;', "'", $request->where)));
        }

        $data = $data->limit(5)->get();

        return response()->json([
            'status' => true,
            'items' => $data
        ]);
    }

    public function test() {
        //  Convert cột kq1 => log
        $leads = Lead::where('admin_id', 324)->get();
        foreach($leads as $lead) {
            if ($lead->kq1 != null || $lead->kq2 != null || $lead->kq3 != null) {
                $log = new LeadContactedLog();
                $log->admin_id = $lead->admin_id;
                $log->lead_id = $lead->id;
                $log->title = $lead->kq3 . '. ' .$lead->kq4;
                $log->note = $lead->kq1 . '. ' .$lead->kq2;
                $log->save();
            }
        }
        die('xong');
    }

    public function ajaxUpdate(Request $r) {
        $lead = Lead::where('id', $r->id)->update($r->data);
        CommonHelper::one_time_message('success', 'Cập nhật thành công!');
        return response()->json($lead);
    }

    public function leadAssign(Request $r) {
        $str = '';
        foreach(explode(',', $r->lead_ids) as $lead_id) {
            if ($lead_id != '') {
                $lead = Lead::where('id', trim($lead_id));
                if (\Auth::guard('admin')->user()->super_admin != 1) {
                    //  Nếu mình ko phải super admin thì truy vấn ra đầu mối của mình là sale
                    $lead = $lead->where(function ($query) use ($request) {
                        $query->orWhere('saler_ids', 'like', '%|'.\Auth::guard('admin')->user()->id.'|%');  //  đầu mối của mình là sale
                    });
                }
                $lead = $lead->first();
                $lead->saler_ids = '|'.$r->sale_id.'|';
                $lead->save();
                $str .= $lead->id . ',';
            }
        }
        CommonHelper::one_time_message('success', 'Chuyển thành công đầu mối: ' . $str);
        return redirect()->back();
        return response()->json([
            'status' => true,
            'msg' => 'Chuyển thành công: ' . $str
        ]);
    }

    public function exportExcel($request, $data)
    {

        \Excel::create(str_slug($this->module['label'], '_') . '_' . date('d_m_Y'), function ($excel) use ($data) {

            // Set the title
            $excel->setTitle($this->module['label'] . ' ' . date('d m Y'));

            $excel->sheet(str_slug($this->module['label'], '_') . '_' . date('d_m_Y'), function ($sheet) use ($data) {

                $field_name = ['ID'];
                foreach ($this->getAllFormFiled() as $field) {
                    if (!isset($field['no_export']) && isset($field['label'])) {
                        $field_name[] = $field['label'];
                    }
                }
                $field_name[] = 'Người phụ trách';
                $field_name[] = 'Tạo lúc';
                $field_name[] = 'Cập nhập lần cuối';

                $sheet->row(1, $field_name);

                $k = 2;
                foreach ($data as $value) {
                    $data_export = [];
                    $data_export[] = $value->id;
                    foreach ($this->getAllFormFiled() as $field) {
                        if (!isset($field['no_export']) && isset($field['label'])) {
                            try {
                                if($field['name'] == 'service') {
                                    $data_export[] = str_replace('|', ', ', @$value->{$field['name']});
                                } elseif($field['name'] == 'dating') {
                                    $data_export[] = date('H:i:s d/m/Y', strtotime(@$value->{$field['dating']}));
                                } elseif (in_array($field['type'], ['text', 'number', 'textarea', 'textarea_editor', 'date', 'datetime-local', 'email', 'hidden', 'checkbox', 'textarea_editor', 'textarea_editor2'])) {
                                    $data_export[] = $value->{$field['name']};
                                } elseif (in_array($field['type'], [
                                    'relation', 'select_model', 'select2_model', 'select2_ajax_model', 'select_model_tree',

                                ])) {
                                    $data_export[] = @$value->{$field['object']}->{$field['display_field']};
                                } elseif ($field['type'] == 'select') {
                                    $data_export[] = @$field['options'][$value->{$field['name']}];
                                } elseif (in_array($field['type'], ['file', 'file_editor2'])) {
                                    $data_export[] = \URL::asset('public/filemanager/userfiles/' . @$value->{$field['name']});
                                } elseif (in_array($field['type'], ['file_editor_extra'])) {
                                    $items = explode('|', @$value->{$field['name']});
                                    foreach ($items as $item) {
                                        $data_export[] = \URL::asset('public/filemanager/userfiles/' . @$item) . ' | ';
                                    }
                                } else {
                                    $data_export[] = $field['label'];
                                }
                            } catch (\Exception $ex) {
                                $data_export[] = $ex->getMessage();
                            }
                        }
                    }
                    $sales = \App\Models\Admin::select('name', 'tel')->whereIn('id', explode('|', $value->saler_ids))->get();
                    $str = '';
                    foreach($sales as $sale) {
                        $str .= $sale->name . ', ';
                    }
                    $data_export[] = $str;
                    $data_export[] = @$value->created_at;
                    $data_export[] = @$value->updated_at;
                    $sheet->row($k, $data_export);
                    $k++;
                }
            });
        })->download('xlsx');
    }
}

//  Lệnh sql update data Hoàng Hạnh -> thả nổi
// update leads set status = "Thả nổi" WHERE `admin_id` = '324' AND `status` = 'Đang chăm sóc' AND `saler_ids` = '|324|'