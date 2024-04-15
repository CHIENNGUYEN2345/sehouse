<?php

namespace Modules\WebBill\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Illuminate\Http\Request;
use Modules\EworkingCompany\Models\Company;
use Modules\WebBill\Models\Service;
use Modules\WebBill\Models\ServiceHistory;
use Validator;
use Modules\WebBill\Models\LeadContactedLog;
use Modules\WebBill\Models\Lead;
use App\Models\Admin;
use Modules\WebBill\Models\Bill;

class LeadBepController extends CURDBaseController
{

    protected $module = [
        'code' => 'lead_bep',
        'table_name' => 'leads',
        'label' => 'Đầu mối',
        'modal' => '\Modules\WebBill\Models\Lead',
        'list' => [
            ['name' => 'inner', 'type' => 'inner', 'label' => 'Ảnh', 'html' => '<img  class="file_image_thumb lazy" title="CLick để phóng to ảnh" style="width: 57px;cursor: pointer; opacity: 1;" src="https://anhdep123.com/wp-content/uploads/2021/01/hinh-gai-xinh-deo-mat-kinh-toc-dai.jpg">'],
            ['name' => 'name', 'type' => 'inner', 'html' => 'eh-dih366', 'label' => 'Mã', 'sort' => true],
            ['name' => 'name', 'type' => 'inner', 'html' => 'Bếp từ', 'label' => 'Sản phẩm', 'sort' => true],
            ['name' => 'name', 'type' => 'inner', 'html' => 'Chefs', 'label' => 'Nhà sản xuất', 'sort' => true],
            ['name' => 'name', 'type' => 'custom', 'td' => 'webbill::lead.list.name', 'label' => 'Khách hàng', 'sort' => true],
            ['name' => 'name', 'type' => 'inner', 'html' => 'Hà Nội', 'label' => 'Tỉnh', 'sort' => true],
            ['name' => 'name', 'type' => 'inner', 'html' => 'Đống Đa', 'label' => 'Quận', 'sort' => true],
            ['name' => 'name', 'type' => 'inner', 'html' => '', 'label' => 'Trao đổi Q.lý', 'sort' => true],
            ['name' => 'project', 'type' => 'text', 'label' => 'Dự án', 'sort' => true],
          
            ['name' => 'tel', 'type' => 'custom', 'td' => 'webbill::lead.list.tel', 'label' => 'SĐT', 'sort' => true],
            ['name' => 'dating', 'type' => 'custom', 'td' => 'webbill::lead.list.dating', 'label' => 'Hẹn ngày', 'sort' => true],
            ['name' => 'contacted_log_last', 'type' => 'custom', 'td' => 'webbill::lead.list.contacted_log_last', 'label' => 'TT lần cuối', 'sort' => true],
            ['name' => 'name', 'type' => 'inner', 'html' => 'Đ.X.Bách', 'label' => 'Người đăng', 'sort' => true],
            ['name' => 'name', 'type' => 'inner', 'html' => 'Web', 'label' => 'Nguồn', 'sort' => true],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên', 'group_class' => 'col-md-4'],
                ['name' => 'tel', 'type' => 'custom', 'field' => 'webbill::lead.form.tel', 'class' => 'required', 'label' => 'SĐT', 'group_class' => 'col-md-4'],
                ['name' => 'email', 'type' => 'text', 'label' => 'Email', 'group_class' => 'col-md-4'],
                ['name' => 'province_id', 'type' => 'select_location', 'label' => '', 'group_class' => 'col-md-9'],
                ['name' => 'address', 'type' => 'text', 'class' => '', 'label' => 'Địa chỉ', 'group_class' => 'col-md-3'],

                ['name' => 'bep_dmsp', 'type' => 'select', 'options' => [
                    '' => '',
                    'Không liên lạc được' => 'Bếp từ',
                    'Không có nhu cầu' => 'Hút mùi',
                    'Đang tìm hiểu' => 'Máy rửa bát',
                   
                ], 'label' => 'Danh mục sản phẩm', 'group_class' => 'col-md-3'],

                ['name' => 'bep_hang', 'type' => 'select', 'options' => [
                    '' => '',
                    'Không liên lạc được' => 'Bosch',
                    'Không có nhu cầu' => 'Teka',
                    'Đang tìm hiểu' => 'Hafele',
                   
                ], 'label' => 'Hãng sản xuất', 'group_class' => 'col-md-3'],

                ['name' => 'bep_masp', 'type' => 'text', 'options' => [
                    '' => '',
                    'Không liên lạc được' => 'Không liên lạc được',
                    'Không có nhu cầu' => 'Không có nhu cầu',
                    'Đang tìm hiểu' => 'Đang tìm hiểu',
                    'Quan tâm cao' => 'Quan tâm cao',
                    'Đã ký HĐ' => 'Đã ký HĐ',
                ], 'label' => 'Mã sản phẩm', 'group_class' => 'col-md-3'],
                ['name' => 'bep_diengiai', 'type' => 'textarea', 'label' => 'Diễn giải thêm', 'group_class' => 'col-md-6', 'inner' => 'rows=5'],

                ['name' => 'bep_noidungtuvan', 'type' => 'textarea', 'label' => 'Nội dung tư vấn', 'group_class' => 'col-md-6', 'inner' => 'rows=5'],
                ['name' => 'bep_tiendo', 'type' => 'select', 'options' => [
                    '' => '',
                    'Không liên lạc được' => 'Đang xây nhà',
                    'Không có nhu cầu' => 'Đang thiết kế nội thất',
                    'Đang tìm hiểu' => 'Đã làm xong nội thất',
                  
                ], 'label' => 'Tiến độ', 'group_class' => 'col-md-3'],

                ['name' => 'bep_nguoncohoi', 'type' => 'select', 'options' => [
                    '' => '',
                    'Không liên lạc được' => 'Facebook',
                    'Không có nhu cầu' => 'Google',
                    'Đang tìm hiểu' => 'Hotline',
                    'Quan tâm cao' => 'Web',
                    
                ], 'label' => 'Nguồn cơ hội', 'group_class' => 'col-md-3'],
                ['name' => 'rate', 'type' => 'select', 'options' => [
                    '' => '',
                    'Không liên lạc được' => 'Không liên lạc được',
                    'Không có nhu cầu' => 'Không có nhu cầu',
                    'Đang tìm hiểu' => 'Đang tìm hiểu',
                    'Quan tâm cao' => 'Quan tâm cao',
                    'Đã ký HĐ' => 'Đã ký HĐ',
                ], 'label' => 'Đánh giá', 'group_class' => 'col-md-3'],
                ['name' => 'rate', 'type' => 'select', 'options' => [
                    '' => '',
                    'Khách lẻ' => 'Khách lẻ',
                    'Không có nhu cầu' => 'Khách công ty',
                    'Không có nhu cầu' => 'Khách nội thất',
                ], 'label' => 'Phân loại', 'group_class' => 'col-md-3'],
                ['name' => 'status', 'type' => 'select', 'options' => [
                    'Thả nổi' => 'Thả nổi',
                    'Đang chăm sóc' => 'Đang chăm sóc',
                    'Tạm dừng' => 'Tạm dừng',
                    'Đã ký HĐ' => 'Đã ký HĐ',
                ], 'label' => 'Trạng thái', 'group_class' => 'col-md-3'],

                ['name' => 'dating', 'type' => 'custom', 'field' => 'webbill::lead.form.dating', 'class' => 'required', 'label' => 'Đặt hẹn ngày tương tác', 'group_class' => 'col-md-3'],
                
            ],
            'tab_2' => [
                ['name' => 'bep_image', 'type' => 'file_image', 'label' => 'Ảnh đại diện'],
                ['name' => 'bep_tinh', 'type' => 'text', 'class' => 'required', 'label' => 'Tính cách KH'],
                ['name' => 'bep_fb', 'type' => 'text', 'class' => 'required', 'label' => 'Facebook'],
                ['name' => 'bep_skype', 'type' => 'text', 'class' => 'required', 'label' => 'Skype'],
                ['name' => 'bep_zl', 'type' => 'text', 'class' => 'required', 'label' => 'Zalo'],
                ['name' => 'bep_cty', 'type' => 'text', 'class' => 'required', 'label' => 'Công ty'],
                ['name' => 'bep_chuc', 'type' => 'text', 'class' => 'required', 'label' => 'Chức vụ'],
                ['name' => 'bep_diachi', 'type' => 'text', 'class' => 'required', 'label' => 'Địa chỉ'],
                ['name' => 'bep_nganh', 'type' => 'text', 'class' => 'required', 'label' => 'Ngành nghề'],
                
            ],
        ]
    ];

    protected $quick_search = [
        'label' => 'ID, tên, sđt, đánh giá, mô tả',
        'fields' => 'id, name, tel, rate, profile, need, product'
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
        'marketer_ids' => [
            'label' => 'Nguồn marketing',
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
                    '' => 'Tất cả',
                    'landingpage' => 'landingpage',
                    'wordpress' => 'wordpress',
                    'laravel' => 'laravel',
                    'web khác' => 'web khác',
                    'app' => 'app',
                    'game' => 'game',
                    'ads' => 'ads',
                    'seo' => 'seo',
                    'content' => 'content',
                    'logo' => 'logo',
                    'banner' => 'banner',
                    'design khác' => 'design khác',
                ],
                'query_type' => 'like'
        ],
        'status' => [
            'label' => 'Trạng thái',
            'type' => 'select',
            'options' => [
                '' => 'Tất cả',
                'Đang chăm sóc' => 'Đang chăm sóc',
                'Tạm dừng' => 'Tạm dừng',
                'Thả nổi' => 'Thả nổi',
                'Đã ký HĐ' => 'Đã ký HĐ',
            ],
            'query_type' => '='
        ],
        'rate' => [
            'label' => 'Đánh giá',
            'type' => 'select',
            'options' => [
                '' => 'Tất cả',
                'Không liên lạc được' => 'Không liên lạc được',
                'Không có nhu cầu' => 'Không có nhu cầu',
                'Đang tìm hiểu' => 'Đang tìm hiểu',
                'Quan tâm cao' => 'Quan tâm cao',
                'Đã ký HĐ' => 'Đã ký HĐ',
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
        'lead_status' => [
            'label' => 'Sắp xếp',
            'type' => 'select',
            'options' => [
                '' => 'Không',
                'Sắp thả nổi' => 'Sắp thả nổi',
                'Ngày tạo: Mới -> cũ' => 'Ngày tạo: Mới -> cũ',
                'Ngày nhận: Mới -> cũ' => 'Ngày nhận: Mới -> cũ',
                'Đến ngày TT' => 'Đến ngày TT',
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
                $query = $query->whereIn('status', ['Thả nổi', '', null]);
            } else {
                //  Truy vấn lead mình đang chăm
                $query = $query->whereIn('status', ['Đang chăm sóc', 'Tạm dừng']);

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

        return view('webbill::lead_bep.list')->with($data);
    }

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('webbill::lead_bep.add')->with($data);
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

                        //  Nếu là nv tạo & gán cho sale khác chăm thì đặt ngay trạng thái là Đang chăm sóc 
                        if (\Auth::guard('admin')->user()->super_admin != 1 && @$request->saler_ids != null) {
                            $data['status'] = 'Đang chăm sóc';
                        }
                    }
                    if ($data['marketer_ids'] == '||') {
                        $data['marketer_ids'] = '|'.\Auth::guard('admin')->user()->id.'|';
                    }
                    if ($data['saler_ids'] == '||') {
                        $data['saler_ids'] = '|'.\Auth::guard('admin')->user()->id.'|';   
                    }
                    $data['admin_id'] = \Auth::guard('admin')->user()->id;
                    $data['received_date'] = date('Y-m-d H:i:s');
                    $data['status'] = 'Đang chăm sóc';


                    if ($request->has('service')) {
                        $data['service'] = '|' . implode('|', $data['service']) . '|';
                    } else {
                        $data['service'] = '';
                    }

                    // Gắn nhanh người sale_ctv khác dành cho sale cty
                    /*if ($request->has('select_marketer_id')) {
                        $data['status'] = 'Đang chăm sóc';
                        $data['saler_ids'] = '|'.$request->select_marketer_id.'|';
                    }*/
                   
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
                return view('webbill::lead_bep.edit')->with($data);
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
                            $data['saler_ids'] = '|'. \Auth::guard('admin')->user()->id .'|';
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
        return view('webbill::lead.view')->with($data);
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
        return view('webbill::lead.show')->with($data);
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


        return view('webbill::lead.tooltip_info')->with($data);
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
                    'view' => 'webbill::lead.emails.tien_do_cong_viec',
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
}

//  Lệnh sql update data Hoàng Hạnh -> thả nổi
// update leads set status = "Thả nổi" WHERE `admin_id` = '324' AND `status` = 'Đang chăm sóc' AND `saler_ids` = '|324|'