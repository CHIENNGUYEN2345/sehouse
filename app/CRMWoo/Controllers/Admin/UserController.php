<?php

namespace App\CRMWoo\Controllers\Admin;

use App\CRMWoo\Models\Lead;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Helpers\CommonHelper;
use App\Models\{Admin, RoleAdmin, Setting, Roles};
use Auth;
use Illuminate\Http\Request;
use Mail;
use Session;
use Validator;

class UserController extends CURDBaseController
{
    protected $_role;

    public function __construct()
    {
        parent::__construct();
        $this->_role = new RoleController();
    }

    protected $module = [
        'code' => 'user',
        'label' => 'Khách hàng',
        'modal' => '\App\Models\Admin',
        'list' => [
            ['name' => 'id', 'type' => 'text', 'label' => 'Mã khách hàng'],
            ['name' => 'name', 'type' => 'text_admin_edit', 'label' => 'admin.name'],
            ['name' => 'tel', 'type' => 'text', 'label' => 'admin.phone'],
            ['name' => 'address', 'type' => 'text', 'label' => 'Địa chỉ'],
            ['name' => 'inner', 'type' => 'inner', 'html' => 'Tạp hoá', 'label' => 'Loại hình KD'],
            ['name' => 'status', 'type' => 'status', 'label' => 'admin.status'],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'admin.update']

        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'admin.full_name', 'group_class' => 'col-md-6'],
                ['name' => 'short_name', 'type' => 'text', 'class' => '', 'label' => 'Tên ngắn gọn', 'group_class' => 'col-md-6'],
                ['name' => 'email', 'type' => 'custom', 'field' => 'CRMWoo.admin.form.email', 'class' => '', 'label' => 'admin.email', 'group_class' => 'col-md-4'],
                ['name' => 'tel', 'type' => 'custom', 'class' => 'required', 'field' => 'CRMWoo.admin.form.tel', 'label' => 'admin.phone', 'group_class' => 'col-md-4'],
                ['name' => 'address', 'type' => 'text', 'class' => '', 'label' => 'admin.address', 'group_class' => 'col-md-3'],
                ['name' => 'province_id', 'type' => 'select_location', 'label' => 'admin.choose_place', 'group_class' => 'col-md-9'],
                ['name' => 'intro', 'type' => 'textarea', 'class' => '', 'label' => 'admin.introduce'],
                ['name' => 'note', 'type' => 'textarea', 'class' => '', 'label' => 'admin.note', 'inner' => 'rows=10'],
            ],
            'more_info_tab' => [
                ['name' => 'invite_by', 'type' => 'select2_ajax_model', 'label' => 'admin.presenter', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'tel'],
                ['name' => 'sale_id', 'type' => 'select2_ajax_model', 'label' => 'Sale', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'tel', 'class' => 'required'],
                
                ['name' => 'image', 'type' => 'file_editor', 'label' => 'Ảnh đại diện'],
                ['name' => 'facebook', 'type' => 'text', 'class' => '', 'label' => 'facebook'],
                ['name' => 'skype', 'type' => 'text', 'class' => '', 'label' => 'skype'],
                ['name' => 'zalo', 'type' => 'text', 'class' => '', 'label' => 'zalo'],
                
            ],
        ]
    ];

    protected $filter = [
        'sale_id' => [
            'label' => 'Sale',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'display_field2' => 'code',
            'model' => \App\Models\Admin::class,
            'object' => 'admin',
            'query_type' => '='
        ],
        'status' => [
            'label' => 'admin.status',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => 'admin.status',
                0 => 'admin.hidden',
                1 => 'admin.active'
            ]
        ],
    ];

    protected $quick_search = [
        'label' => 'ID, tên, sđt, email',
        'fields' => 'id, name, tel, email'
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('CRMWoo.user.list')->with($data);
    }

    public function appendWhere($query, $request)
    {
        $admin_ids = RoleAdmin::where('role_id', '!=', 3)->where('role_id', '!=', 4)->pluck('admin_id');
        $query = $query->whereNotIn('id', $admin_ids);

        //  Chỉ truy vấn ra các đơn hàng của mình
        if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'view_all_data')) {
            $query = $query->where('sale_id', \Auth::guard('admin')->user()->id);
        }

        return $query;
    }

    public function add(Request $request)
    {
        try {

            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMWoo.user.add')->with($data);
            } else if ($_POST) {

                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    // 'email' => 'required',
                    'tel' => 'required',
                    // 'password' => 'required|min:5',
                    // 'password_confimation' => 'required|same:password',
                ], [
                    'name.required' => 'Bắt buộc phải nhập tên!',
//                    'email.required' => 'Bắt buộc phải nhập email!',
//                    'email.unique' => 'Địa chỉ email đã tồn tại!',
                    // 'password.required' => 'Bắt buộc phải nhập mật khẩu!',
                    // 'password.min' => 'Mật khẩu phải trên 5 ký tự!',
                    // 'password_confimation.required' => 'Bắt buộc nhập lại mật khẩu!',
                    // 'password_confimation.same' => 'Nhập lại sai mật khẩu!',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    \DB::beginTransaction();

                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    $data['api_token'] = base64_encode(rand(1, 100) . time());

                    //  Tùy chỉnh dữ liệu insert

//                    $data['role_id']=1;
                    
                    unset($data['role_id']);
                    

                    $data['district_id'] = $request->get('district_id', null);
                    $data['ward_id'] = $request->get('ward_id', null);

                    $data['company_id'] = \Auth::guard('admin')->user()->last_company_id;

                    #
                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
                        //  Nếu ko thì mặc định gán quyền khách hàng
                        RoleAdmin::create([
                            'admin_id' => $this->model->id,
                            'role_id' => 3,
                        ]);
                        
                        \DB::commit();
                        CommonHelper::one_time_message('success', 'Tạo mới thành công!');

                        return redirect('admin/user');
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

                    if ($request->return_direct == 'save_continue') {
                        return redirect('admin/' . $this->module['code'] . '/edit/' . $this->model->id);
                    } elseif ($request->return_direct == 'save_create') {
                        return redirect('admin/' . $this->module['code'] . '/add');
                    }

                    return redirect('admin/' . $this->module['code']);
                }
            }
        } catch (\Exception $ex) {
            \DB::rollback();
            CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function addByLead(Request $request) {

        $lead_id = @$request->lead_id;

        $lead = Lead::find($lead_id);
        $user = Admin::where('tel', $lead->tel)->first();
        if (is_object($user)) {
            $user = new Admin();
            $user->sale_id = \Auth::guard('admin')->user()->id;
            $user->name = $lead->name;
            $user->tel = $lead->tel;
            $user->intro = $lead->profile;
            $user->save();
        }
        return redirect('/admin/user/' . $user->id);
    }

    public function update(Request $request)
    {
        try {
            $item = $this->model->find($request->id);

            if (!is_object($item)) {
                abort(404);
            }
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('CRMWoo.user.edit')->with($data);
            } else if ($_POST) {
                
                \DB::beginTransaction();

                if ($item->id == \Auth::guard('admin')->user()->id) {
                    $validator = Validator::make($request->all(), [
                        'name' => 'required'
                    ], [
                        'name.required' => 'Bắt buộc phải nhập tên',
                    ]);

                    if ($validator->fails()) {
                        return back()->withErrors($validator)->withInput();
                    }
                }
                $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                //  Tùy chỉnh dữ liệu edit

               
                unset($data['role_id']);
                

                $data['district_id'] = $request->get('district_id', null);
                $data['ward_id'] = $request->get('ward_id', null);
                #
                foreach ($data as $k => $v) {
                    $item->$k = $v;
                }
                if ($item->save()) {
                    
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
            }
        } catch (\Exception $ex) {
            \DB::rollback();
            CommonHelper::one_time_message('error', $ex->getMessage());
//            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            return redirect()->back()->withInput();
        }
    }

    public function exportExcel($request, $data)
    {

        \Excel::create(str_slug($this->module['label'], '_') . '_' . date('d_m_Y'), function ($excel) use ($data) {

            // Set the title
            $excel->setTitle($this->module['label'] . ' ' . date('d m Y'));

            $excel->sheet(str_slug($this->module['label'], '_') . '_' . date('d_m_Y'), function ($sheet) use ($data) {

                $field_name = ['ID'];
                $field_name[] = 'Tên';
                $field_name[] = 'SĐT';
                $field_name[] = 'Mail';
                $field_name[] = 'Tạo lúc';
                $field_name[] = 'Cập nhập lần cuối';

                $sheet->row(1, $field_name);

                $k = 2;

                foreach ($data as $value) {
                    $data_export = [];
                    $data_export[] = $value->id;
                    $data_export[] = $value->name;
                    $data_export[] = $value->tel;
                    $data_export[] = $value->mail;
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



