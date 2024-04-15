<?php

namespace App\CRMEdu\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Illuminate\Http\Request;
use Modules\EworkingCompany\Models\Company;
use App\CRMEdu\Models\Service;
use App\CRMEdu\Models\ServiceHistory;
use Validator;
use App\CRMEdu\Models\LeadContactedLog;
use App\CRMEdu\Models\Lead;
use App\Models\Admin;
 
class TimekeepingController extends CURDBaseController
{

    protected $module = [
        'code' => 'timekeeping',
        'table_name' => 'timekeepings',
        'label' => 'CRMEdu_admin.timekeepings',
        'modal' => '\App\CRMEdu\Models\Timekeeping',
        'list' => [
            ['name' => 'day', 'type' => 'date_vi', 'label' => 'CRMEdu_admin.timekeepings_date', 'sort' => true],
            ['name' => 'time', 'type' => 'text', 'label' => 'CRMEdu_admin.timekeepings_time', 'sort' => true],
            ['name' => 'created_at', 'type' => 'datetime_vi', 'label' => 'CRMEdu_admin.timekeepings_Tao', 'sort' => true],
            ['name' => 'status', 'type' => 'status', 'label' => 'CRMEdu_admin.timekeepings_Pay', 'sort' => true],
        ],
        'form' => [
            'general_tab' => [
                
            ],
            'tab_2' => [
        
                ['name' => 'saler_id', 'type' => 'custom', 'field' => 'CRMEdu.timekeeping.form.fields.select_admin', 'label' => 'Sale', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'code', 'class' => 'required'],
                ['name' => 'job_other', 'type' => 'textarea', 'label' => 'CRMEdu_admin.timekeepings_Cong_viec_khac', 'group_class' => 'col-md-12', 'inner' => 'rows=15'],
                ['name' => 'time', 'type' => 'select', 'options' => [
                    '' => '',
                    '0.5' => '0.5',
                    '1' => '1',
                    '1.5' => '1.5',
                    '2' => '2',
                    '2.5' => '2.5',
                    '3' => '3',
                    '3.5' => '3.5',
                    '4' => '4',
                    '4.5' => '4.5',
                    '5' => '5',
                    '5.5' => '5.5',
                    '6' => '6',
                    '6.5' => '6.5',
                    '7' => '7',
                    '7.5' => '7.5',
                    '8' => '8',
                    '8.5' => '8.5',
                    '9' => '9',
                    '9.5' => '9.5',
                    '10' => '10',
                    '10.5' => '10.5',
                    
                    
                ], 'label' => 'Tổng giờ làm', 'class' => 'required', 'group_class' => 'col-md-12', 'inner' => 'min=0'],
            ],
        ]
    ];

    protected $quick_search = [
        'label' => 'ID, day, start, end, job_other, note, time',
        'fields' => 'id, day, start, end, job_other, note, time'
    ];

    protected $filter = [
        'admin_id' => [
            'label' => 'CRMEdu_admin.timekeepings_Nguoi_tao',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'display_field2' => 'code',
            'model' => \App\Models\Admin::class,
            'object' => 'admin',
            'query_type' => '='
        ],
        'status' => [
            'label' => 'CRMEdu_admin.timekeepings_Status_Pay',
            'type' => 'select',
            'options' => [
                '' => 'Tất cả',
                0 => 'Chưa thanh toán',
                1 => 'Đã thanh toán',
            ],
            'query_type' => '='
        ],
    ];

    public function appendWhere($query, $request)
    {
    

        if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'view_all_data')) {
            //  Nếu ko phải super_admin
            
            //  Truy vấn lead mình đang chăm

            //  thì truy vấn dữ liệu của mình
            $query = $query->where(function ($query) use ($request) {
                $query->orWhere('admin_id', \Auth::guard('admin')->user()->id);
            });
        
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

        return view('CRMEdu.timekeeping.list')->with($data);
    }

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMEdu.timekeeping.add')->with($data);
            } else if ($_POST) {
                $validator = Validator::make($request->all(), [
                    'day' => 'required',
                
                ], [
                    'day.required' => 'Bắt buộc phải nhập ngày chấm công',
                  
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert
                    
                    $data['day'] = $request->day;
                    $data['admin_id'] = \Auth::guard('admin')->user()->id;

                    $logs = [];
                    $lead_contacted_logs = \App\CRMEdu\Models\LeadContactedLog::select('lead_id', 'note', 'created_at')->where('admin_id', \Auth::guard('admin')->user()->id)
                        ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($data['day'])))
                        ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($data['day'])))->orderBy('id', 'asc')->get();
                    foreach($lead_contacted_logs as $v) {
                        $logs[strtotime($v->created_at)] = 'tư vấn đầu mối: ' . @$v->lead->name . ': ' . $v->note;
                    }
                    

                    $leads = \App\CRMEdu\Models\Lead::select('tel', 'name', 'created_at')->where('admin_id', \Auth::guard('admin')->user()->id)
                        ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($data['day'])))
                        ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($data['day'])))->orderBy('id', 'asc')->get();
                    foreach($leads as $v) {
                        $logs[strtotime($v->created_at)] = 'tạo mới đầu mối: ' . @$v->lead->name . ': ' . $v->tel;
                    }
                    
                    $data['log_text'] = '';
                    foreach($logs as $k => $v) {
                        $data['log_text'] .= '- ' . date('H:i', $k) . ':' .  $v . '<br>';
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
                        return redirect('admin/' . $this->module['code'] . '/edit/' . $this->model->id);
                    } elseif ($request->return_direct == 'save_create') {
                        return redirect('admin/' . $this->module['code'] . '/add');
                    }

                    return redirect('admin/' . $this->module['code']);
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
                return view('CRMEdu.timekeeping.edit')->with($data);
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
        return view('CRMEdu.timekeeping.view')->with($data);
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
        return view('CRMEdu.timekeeping.show')->with($data);
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


        return view('CRMEdu.timekeeping.tooltip_info')->with($data);
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
                    'view' => 'CRMEdu.timekeeping.emails.tien_do_cong_viec',
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

}
