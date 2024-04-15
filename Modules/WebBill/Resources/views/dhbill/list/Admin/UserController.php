<?php

namespace Modules\WebBill\Http\Controllers\Admin;

use App\Http\Controllers\Admin\RoleController;
use App\Http\Helpers\CommonHelper;
use App\Models\{Admin, RoleAdmin, Setting, Roles, User};
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
            ['name' => 'image', 'type' => 'image', 'label' => 'admin.image'],
            ['name' => 'name', 'type' => 'text_admin_edit', 'label' => 'admin.name'],
            ['name' => 'tel', 'type' => 'text', 'label' => 'admin.phone'],
            ['name' => 'email', 'type' => 'text', 'label' => 'admin.email'],
            ['name' => 'status', 'type' => 'status', 'label' => 'admin.status'],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'admin.update']

        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'admin.full_name', 'group_class' => 'col-md-6'],
                ['name' => 'short_name', 'type' => 'text', 'class' => '', 'label' => 'Tên ngắn gọn', 'group_class' => 'col-md-6'],
                ['name' => 'email', 'type' => 'custom', 'field' => 'webbill::admin.form.email', 'class' => '', 'label' => 'admin.email', 'group_class' => 'col-md-4'],
                ['name' => 'tel', 'type' => 'custom', 'class' => 'required', 'field' => 'webbill::admin.form.tel', 'label' => 'admin.phone', 'group_class' => 'col-md-4'],
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

        return view('webbill::admin.list')->with($data);
    }

    public function appendWhere($query, $request)
    {
        $admin_ids = RoleAdmin::where('role_id', '!=', 3)->where('role_id', '!=', 4)->pluck('admin_id');
        $query = $query->whereNotIn('id', $admin_ids);

        if (\Auth::guard('admin')->user()->super_admin != 1) {
            //  Nếu ko phải super_admin thì truy vấn theo dữ liệu cty đó
            $query = $query->where('company_id', \Auth::guard('admin')->user()->last_company_id);
        }

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
                return view('webbill::user.add')->with($data);
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
                        return redirect('admin/' . $this->module['code'] . '/' . $this->model->id);
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

    public function update(Request $request)
    {
        try {
            $item = $this->model->find($request->id);

            if (!is_object($item)) {
                abort(404);
            }
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('webbill::user.edit')->with($data);
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
                    return redirect('admin/' . $this->module['code'] . '/' . $item->id);
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
}



