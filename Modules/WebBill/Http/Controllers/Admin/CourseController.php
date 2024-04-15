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

class CourseController extends CURDBaseController
{

    protected $module = [
        'code' => 'course',
        'table_name' => 'courses',
        'label' => 'Đầu mối',
        'modal' => '\Modules\WebBill\Models\Lead',
        'list' => [
            ['name' => 'name', 'type' => 'custom', 'td' => 'webbill::lead.list.name', 'label' => 'Tên', 'sort' => true],
            ['name' => 'project', 'type' => 'text', 'label' => 'Dự án', 'sort' => true],
            ['name' => 'rate', 'type' => 'custom', 'td' => 'webbill::lead.list.rate', 'label' => 'Đánh giá', 'sort' => true],
            ['name' => 'tel', 'type' => 'custom', 'td' => 'webbill::lead.list.tel', 'label' => 'SĐT', 'sort' => true],
            ['name' => 'service', 'type' => 'text', 'label' => 'Dịch vụ', 'sort' => true],
            // ['name' => 'dating', 'type' => 'custom', 'td' => 'webbill::lead.list.dating', 'label' => 'Hẹn ngày', 'sort' => true],
            ['name' => 'last_log_at', 'type' => 'custom', 'td' => 'webbill::lead.list.last_log_at', 'label' => 'TT lần cuối', 'sort' => true],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên', 'group_class' => 'col-md-3'],
                ['name' => 'tel', 'type' => 'number', 'class' => 'required', 'label' => 'SĐT', 'group_class' => 'col-md-3'],
                ['name' => 'email', 'type' => 'text', 'label' => 'Email', 'group_class' => 'col-md-3'],
                ['name' => 'rate', 'type' => 'select', 'options' => [
                    '' => '',
                    'Không liên lạc được' => 'Không liên lạc được',
                    'Không có nhu cầu' => 'Không có nhu cầu',
                    'Đang tìm hiểu' => 'Đang tìm hiểu',
                    'Quan tâm cao' => 'Quan tâm cao',
                    'Đang chốt HĐ' => 'Đang chốt HĐ',
                    'Đã ký HĐ' => 'Đã ký HĐ',
                ], 'label' => 'Đánh giá', 'group_class' => 'col-md-3'],
                ['name' => 'service', 'type' => 'text', 'label' => 'Dịch vụ', 'group_class' => 'col-md-3'],
                ['name' => 'project', 'type' => 'text', 'label' => 'Tên dự án', 'group_class' => 'col-md-3'],
                ['name' => 'dating', 'type' => 'custom', 'field' => 'webbill::lead.form.dating', 'class' => 'required', 'label' => 'Đặt hẹn ngày tương tác', 'group_class' => 'col-md-3'],
                ['name' => 'status', 'type' => 'select', 'options' => [
                    'Đang chăm sóc' => 'Đang chăm sóc',
                    'Tạm dừng' => 'Tạm dừng',
                    'Bỏ' => 'Bỏ',
                    'Đã ký HĐ' => 'Đã ký HĐ',
                ], 'label' => 'Trạng thái', 'group_class' => 'col-md-3'],
            ],
            'tab_2' => [
                ['name' => 'profile', 'type' => 'textarea', 'label' => 'Chân dung KH', 'group_class' => 'col-md-12', 'inner' => 'rows=15'],
                ['name' => 'need', 'type' => 'textarea', 'label' => 'Nhu cầu & khó khăn', 'group_class' => 'col-md-12', 'inner' => 'rows=15'],
                ['name' => 'terms', 'type' => 'textarea', 'label' => 'Thương hiệu sở hữu', 'group_class' => 'col-md-12', 'inner' => 'rows=3 placeholder="Lấy thương hiệu cá nhân sale"'],
                ['name' => 'discount', 'type' => 'text', 'label' => '% tiền dự án trả lại cho hệ thống', 'group_class' => 'col-md-12', 'inner' => 'rows=3 placeholder="Mặc định 15%"', 'des' => 'Bạn tự báo giá chênh lên số % này để trả tiền giới thiệu khách từ hệ thống'],
            ],
        ]
    ];

    protected $quick_search = [
        'label' => 'ID, tên, sđt, đánh giá, mô tả',
        'fields' => 'id, name, tel, rate, profile, need'
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
        'status' => [
            'label' => 'Trạng thái',
            'type' => 'select',
            'options' => [
                '' => 'Tất cả',
                'Đang chăm sóc' => 'Đang chăm sóc',
                'Tạm dừng' => 'Tạm dừng',
                'Bỏ' => 'Bỏ',
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
                'Đang chốt HĐ' => 'Đang chốt HĐ',
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
            'label' => 'Tình trạng đầu mối',
            'type' => 'select',
            'options' => [
                '' => 'Tất cả',
                'Sắp thả nổi' => 'Sắp thả nổi',
            ],
            'query_type' => 'custom'
        ],
    ];

    public function appendWhere($query, $request)
    {
    
        if (@$request->marketer_ids != null) {
            $query = $query->where('marketer_ids', 'like', '%|' . $request->marketer_ids . '|%');
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

            //  thì truy vấn dữ liệu của mình
            $query = $query->where(function ($query) use ($request) {
                $query->orWhere('saler_ids', 'like', '%|'.\Auth::guard('admin')->user()->id.'|%');
                $query->orWhere('admin_id', \Auth::guard('admin')->user()->id);
            });

            //  thì truy vấn lead mình đang chăm
            $query = $query->whereIn('status', ['Đang chăm sóc', 'Tạm dừng']);
        }

        return $query;
    }

    public function sort($request, $model)
    {
        if (@$request->lead_status != null) {
            if ($request->lead_status == 'Sắp thả nổi') {
                $model = $model->orderBy('contacted_log_last', 'asc');
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

        return view('webbill::lead.list')->with($data);
    }

    public function getView() {
        return view('webbill::course.view_list');
    }

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('webbill::lead.add')->with($data);
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
                    if (\Auth::guard('admin')->user()->super_admin == 1) {
                        $data['marketer_ids'] = '|' . implode('|', $request->get('marketer_ids', [])) . '|';
                        $data['saler_ids'] = '|' . implode('|', $request->get('saler_ids', [])) . '|';
                    }
                    $data['admin_id'] = \Auth::guard('admin')->user()->id;
                   
                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
                        $this->afterAddLog($request, $this->model);

                        LeadContactedLog::create([
                            'title' => '', 
                            'admin_id' => \Auth::guard('admin')->user()->id, 
                            'lead_id' => $this->model->id,
                            'note' => 'Tạo mới'
                        ]);

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
                return view('webbill::lead.edit')->with($data);
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
                    if (\Auth::guard('admin')->user()->super_admin == 1) {
                        $data['marketer_ids'] = '|' . implode('|', $request->get('marketer_ids', [])) . '|';
                        $data['saler_ids'] = '|' . implode('|', $request->get('saler_ids', [])) . '|';
                    }
                    
                    //  Nếu thay đổi sale thì tạo log
                    if (@$data['saler_ids'] != $item->saler_ids) {
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
                            'note' => 'Đổi sale ' . $txt
                        ];
                    }
                    
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

            LeadContactedLog::where('lead_id', $request->id)->delete();

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
        $log->admin_id = \Auth::guard('admin')->user()->id;
        $log->save();

        Lead::where('id', $r->lead_id)->update([
            'contacted_log_last' => date('Y-m-d H:i:s'),
        ]);

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
        $logs = LeadContactedLog::where('created_at', '>', date('Y-m-d 00:00:00'))->get();

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
}
