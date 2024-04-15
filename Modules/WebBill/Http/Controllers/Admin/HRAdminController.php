<?php 

namespace Modules\WebBill\Http\Controllers\Admin;

use App\Http\Controllers\Admin\RoleController;
use App\Http\Helpers\CommonHelper;
use App\Models\{RoleAdmin, Setting, Roles, User};
use Modules\WebBill\Models\Admin;
use Auth;
use Illuminate\Http\Request;
use Mail;
use Session;
use Validator;

class HRAdminController extends CURDBaseController
{
    protected $_role;

    public function __construct()
    {
        parent::__construct();
        $this->_role = new RoleController();
    }

    protected $module = [
        'code' => 'hradmin',
        'label' => 'Thành viên',
        'modal' => 'Modules\WebBill\Models\Admin',
        'table_name' => 'admin',
        'list' => [
            ['name' => 'image', 'type' => 'image', 'label' => 'admin.image'],
            ['name' => 'name', 'type' => 'text_admin_edit', 'label' => 'admin.name'],
            ['name' => 'role_id', 'type' => 'role_name', 'label' => 'admin.permission'],
            ['name' => 'tel', 'type' => 'text', 'label' => 'admin.phone'],
            ['name' => 'code', 'type' => 'text', 'label' => 'Mã NV'],
            ['name' => 'email', 'type' => 'text', 'label' => 'admin.email'],
            ['name' => 'room_id', 'type' => 'select', 'options' => [
                '' => '',
                1 => 'Phòng kinh doanh 1',
                2 => 'Phòng kinh doanh 2',
                3 => 'Phòng kinh doanh 3',
                4 => 'Phòng kinh doanh 4',
                5 => 'Phòng kinh doanh 5',
                6 => 'Phòng Telesale',
                10 => 'Kỹ thuật',
                15 => 'Điều hành',
                20 => 'Marketing',
                25 => 'Tuyển dụng',
                30 => 'CSKH',
            ], 'label' => 'Phòng', 'sort' => true],
            ['name' => 'work_time', 'type' => 'select', 'options' => [
                '' => '',
                1 => 'Fulltime',
                2 => 'Parttime',
                3 => 'Online',
            ], 'label' => 'Thời gian', 'sort' => true],
            ['name' => 'status', 'type' => 'status', 'label' => 'admin.status'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Ngày tạo'],
            ['name' => 'invite_by', 'type' => 'relation', 'label' => 'Người tuyển', 'object' => 'invite', 'display_field' => 'name', 'sort' => true],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'admin.full_name', 'group_class' => 'col-md-6'],
                ['name' => 'short_name', 'type' => 'text', 'class' => '', 'label' => 'Tên ngắn gọn', 'group_class' => 'col-md-6'],
                ['name' => 'email', 'type' => 'custom', 'field' => 'webbill::admin.form.email', 'class' => 'required', 'label' => 'admin.email', 'group_class' => 'col-md-3'],
                ['name' => 'tel', 'type' => 'custom', 'class' => 'required', 'field' => 'webbill::admin.form.tel', 'label' => 'admin.phone', 'group_class' => 'col-md-2'],
                ['name' => 'code', 'type' => 'custom', 'class' => 'required', 'field' => 'webbill::admin.form.code', 'label' => 'Mã nhân viên', 'group_class' => 'col-md-2'],
                ['name' => 'may_cham_cong_id', 'type' => 'number', 'class' => '', 'label' => 'ID máy chấm công', 'group_class' => 'col-md-2'],
                ['name' => 'status', 'type' => 'checkbox', 'label' => 'admin.active', 'value' => 1, 'group_class' => 'col-md-3'],
                ['name' => 'password', 'type' => 'password', 'class' => 'required', 'label' => 'admin.password', 'group_class' => 'col-md-6'],
                ['name' => 'password_confimation', 'type' => 'password', 'class' => 'required', 'label' => 'admin.re_password', 'group_class' => 'col-md-6'],
                /*['name' => 'role_id', 'type' => 'custom', 'field' => 'webbill::hradmin.partials.select_role', 'label' => 'Quyền', 'class' => 'required', 'model' => \App\Models\Roles::class, 'display_field' => 'display_name', 'group_class' => 'col-md-6'],*/
                
                ['name' => 'address', 'type' => 'text', 'class' => '', 'label' => 'admin.address', 'group_class' => 'col-md-3'],
                ['name' => 'province_id', 'type' => 'select_location', 'label' => 'admin.choose_place', 'group_class' => 'col-md-9'],
                ['name' => 'date_start_work', 'type' => 'date', 'class' => '', 'label' => 'Ngày bắt đầu tính lương', 'group_class' => 'col-md-4'],
                ['name' => 'intro', 'type' => 'textarea', 'class' => '', 'label' => 'admin.introduce'],
                ['name' => 'note', 'type' => 'textarea', 'class' => '', 'label' => 'admin.note', 'inner' => 'rows=10'],
            ],
            'more_info_tab' => [
                ['name' => 'image', 'type' => 'file_image', 'label' => 'Ảnh đại diện'],
                ['name' => 'facebook', 'type' => 'text', 'class' => '', 'label' => 'facebook'],
                ['name' => 'skype', 'type' => 'text', 'class' => '', 'label' => 'skype'],
                ['name' => 'zalo', 'type' => 'text', 'class' => '', 'label' => 'zalo'],
                ['name' => 'invite_by', 'type' => 'select2_ajax_model', 'label' => 'Người tuyển', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'tel', 'class' => 'required'],
                ['name' => 'room_id', 'type' => 'select', 'options' => [
                    '' => '',
                    1 => 'Phòng kinh doanh 1',
                    2 => 'Phòng kinh doanh 2',
                    3 => 'Phòng kinh doanh 3',
                    4 => 'Phòng kinh doanh 4',
                    5 => 'Phòng kinh doanh 5',
                    6 => 'Phòng Telesale',
                    10 => 'Kỹ thuật',
                    15 => 'Điều hành',
                    20 => 'Marketing',
                    25 => 'Tuyển dụng',
                    30 => 'CSKH',
                ], 'label' => 'Phòng', 'group_class' => 'col-md-12'],
                ['name' => 'work_time', 'type' => 'select', 'options' => [
                    '' => '',
                    1 => 'Fulltime',
                    2 => 'Parttime',
                    3 => 'Online',
                ], 'label' => 'Thời gian làm', 'group_class' => 'col-md-12'],
                // ['name' => 'role_id', 'type' => 'select', 'options' => [
                //     '' => '',
                //     2 => 'Kinh doanh',
                //     176 => 'CTV kinh doanh',
                //     182 => 'Trưởng phòng kinh doanh',
                //     183 => 'Telesale'
                //     173 => 'Kỹ thuật',
                //     174 => 'Marketing',
                //     178 => 'Điều hành',
                //     179 => 'CSKH',
                //     180 => 'HR Tuyển dụng',
                // ], 'label' => 'Phân quyền'],
            ],
        ]
    ];

    protected $filter = [
        'status' => [
            'label' => 'admin.status',
            'type' => 'select',
            'options' => [
                1 => 'admin.active',
                0 => 'admin.hidden',
                'all' => 'Tất cả'
            ],
            'query_type' => 'custom',
        ],
        'room_id' => [
            'label' => 'Phòng',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => '',
                1 => 'Phòng kinh doanh 1',
                2 => 'Phòng kinh doanh 2',
                3 => 'Phòng kinh doanh 3',
                4 => 'Phòng kinh doanh 4',
                5 => 'Phòng kinh doanh 5',
                10 => 'Kỹ thuật',
                15 => 'Điều hành',
                20 => 'Marketing',
                25 => 'Tuyển dụng',
                30 => 'CSKH',
            ]
        ],
        'role_id' => [
            'label' => 'Quyền',
            'type' => 'select2_model',
            'display_field' => 'display_name',
            'model' => \App\Models\Roles::class,
            'object' => 'role',
            'query_type' => 'custom'
        ],
        'work_time' => [
            'label' => 'Thời gian làm',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => '',
                1 => 'Fulltime',
                2 => 'Parttime',
                3 => 'Online',
            ]
        ],
        'invite_by' => [
            'label' => 'Người tuyển',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'display_field2' => 'code',
            'model' => \App\Models\Admin::class,
            'object' => 'invite',
            'query_type' => '='
        ],
    ];

    protected $quick_search = [
        'label' => 'ID, tên, sđt, email',
        'fields' => 'id, name, tel, code, email'
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('webbill::hradmin.list')->with($data);
    }

    public function appendWhere($query, $request)
    {
        if($request->status == null) {
            //  Khi ko dùng bộ lọc trạng thái thì mặc định ko hiện ra các thành viên trạng thái huỷ kick hoạt
            $query = $query->where('status', '!=', 0);
        } else{
            //  Nếu dùng bộ lọc trạng thái
            if($request->status == 'all') {
                //  lọc theo tất cả trạng thái
            } else {
                //  lọc theo trạng thái cụ thể
                $query = $query->where('status', $request->status);
            }
        }
        
        $not_customer = RoleAdmin::whereNotIn('role_id', [3, 4])->pluck('admin_id')->toArray();
        $not_superadmin = RoleAdmin::whereNotIn('role_id', [1, 175, 181])->pluck('admin_id')->toArray();

        $query = $query->whereIn('id', $not_customer)->whereIn('id', $not_superadmin);

        if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'admin_view_all')) {
            //  không được xem toàn bộ thành viên thì chỉ hiển thị ra thành viên trong phòng mình
            $query = $query->where('room_id', \Auth::guard('admin')->user()->room_id);
        }


        if($request->role_id != null) {
            $admin_ids = RoleAdmin::select('admin_id')->where('role_id', $request->role_id)->pluck('admin_id')->toArray();
            $query = $query->whereIn('id', $admin_ids);
        } else {
            //  Không tìm theo quyền thì mặc định không hiện các quyền không làm việc trực tiếp tại cty
            $admin_ids = RoleAdmin::select('admin_id')->where(function ($query) use ($request) {
                    $query->orWhere('role_id', 176);    //  CTV sale
                    // $query->orWhere('role_id', 176);
                })->pluck('admin_id')->toArray();
            $query = $query->whereNotIn('id', $admin_ids);
        }

        return $query;
    }

    public function ajaxGetInfo(Request $r) {
        $admin = Admin::find($r->id);
        if (!is_object($admin)) {
            return response()->json([
                'status' => false,
                'msg' => 'Không tìm thấy bản ghi',
                'data' => $admin
            ]);
        }
        $admin->image = CommonHelper::getUrlImageThumb(@$admin->image, 250, null);
        $admin->province_name = @$admin->province->name;
        $admin->district_name = @$admin->district->name;
        $admin->ward_name = @$admin->ward->name;
        $admin->role_name = CommonHelper::getRoleName($admin->id);

        return response()->json([
            'status' => true,
            'msg' => '',
            'data' => $admin
        ]);
    }

    public function add(Request $request)
    {
        try {

            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('webbill::hradmin.add')->with($data);
            } else if ($_POST) {

                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required',
                    'tel' => 'required',
                    'password' => 'required|min:5',
                    'password_confimation' => 'required|same:password',
                ], [
                    'name.required' => 'Bắt buộc phải nhập tên!',
//                    'email.required' => 'Bắt buộc phải nhập email!',
//                    'email.unique' => 'Địa chỉ email đã tồn tại!',
                    'password.required' => 'Bắt buộc phải nhập mật khẩu!',
                    'password.min' => 'Mật khẩu phải trên 5 ký tự!',
                    'password_confimation.required' => 'Bắt buộc nhập lại mật khẩu!',
                    'password_confimation.same' => 'Nhập lại sai mật khẩu!',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {

                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    $data['api_token'] = base64_encode(rand(1, 100) . time());

                    //  Tùy chỉnh dữ liệu insert

//                    $data['role_id']=1;
                    unset($data['password_confimation']);
                    unset($data['role_id']);
                    $data['password'] = bcrypt($request->password);

                    $data['district_id'] = $request->get('district_id', null);
                    $data['ward_id'] = $request->get('ward_id', null);

                    $data['company_id'] = \Auth::guard('admin')->user()->last_company_id;

                    if ($request->role_id == 1) {
                        //  Nếu set làm super_admin thì cho thêm type
                        $data['super_admin'] = 1;
                    }

                    if ($request->role_id == 2 && \Auth::guard('admin')->user()->super_admin == 1) {
                        //  super_admin tao ra admin shop
                        $company_id_max = Admin::orderBy('last_company_id', 'desc')->first()->last_company_id;
                        $company_id_max ++;
                        $data['last_company_id'] = $company_id_max;
                    } elseif(\Auth::guard('admin')->user()->super_admin == 0) {
                        //  admin shop tạo tk nhân viên
                        $data['last_company_id'] = \Auth::guard('admin')->user()->last_company_id;
                    }

                    #
                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
                        RoleAdmin::create([
                            'admin_id' => $this->model->id,
                            'role_id' => $request->role_id,
                        ]);
                        
                        CommonHelper::flushCache($this->module['table_name']);
                        CommonHelper::one_time_message('success', 'Tạo mới thành công!');

                        // if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'role_view')) {
                        //     //  Nếu ko có quyền xem phân quyền thì là nv tạo tk kh => tạo xong chuyển hướng về trang kh
                        //     return redirect('hradmin/user');
                        // }
                    
                        return redirect('/admin/hradmin');
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

                    return redirect('admin/' . $this->module['code']);
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

            if (!is_object($item)) {
                abort(404);
            }
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('webbill::hradmin.edit')->with($data);
            } else if ($_POST) {
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

                if ($request->password != null) {
                    if ($request->password != $request->password_confimation) {
                        CommonHelper::one_time_message('error', 'Mật khẩu không khớp');
                        return redirect()->back()->withInput();
                    }

                    $data['password'] = bcrypt($request->password);
                } else {
                    unset($data['password']);
                }
                unset($data['role_id']);
                unset($data['password_confimation']);


                $data['district_id'] = $request->get('district_id', null);
                $data['ward_id'] = $request->get('ward_id', null);
                #
                foreach ($data as $k => $v) {
                    $item->$k = $v;
                }
                if ($item->save()) {
                    RoleAdmin::updateOrCreate([
                        'admin_id' => $item->id,
//                        'company_id' => \Auth::guard('admin')->user()->company_id
                    ],
                        [
                            'role_id' => $request->role_id,
                            'status' => 1
                        ]);
                    CommonHelper::flushCache($this->module['table_name']);
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
                    return redirect('admin/' . $this->module['code'] . '/' . $item->id);
                } elseif ($request->return_direct == 'save_create') {
                    return redirect('admin/' . $this->module['code'] . '/add');
                }

                return redirect('admin/' . $this->module['code']);
            }
        } catch (\Exception $ex) {
            dd($ex->getMessage());
            CommonHelper::one_time_message('error', $ex->getMessage());
//            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            return redirect()->back()->withInput();
        }
    }

    public function validator(array $data)
    {
        $rules = array(
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|max:50',
        );

        $fieldNames = array(
            'name' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
        );

        $validator = Validator::make($data, $rules);
        $validator->setAttributeNames($fieldNames);
        return $validator;
    }

    public function resetPassword(Request $request, $id)
    {
        if (!$_POST) {
            $data['result'] = Admin::find($id);
            $data['module'] = $this->module;
            $data['page_title'] = 'Reset password' . trans($this->module['label']);
            $data['page_type'] = 'Reset password';
            $data['id_user'] = $id;
            return view(config('core.admin_theme') . '.admin.reset_password', $data);
        } elseif ($_POST) {
            $user = Admin::find($id);
            $user->password = bcrypt($request->reset_pass);
            $user->password_md5 = md5($request->reset_pass, true);
            $user->save();
            CommonHelper::one_time_message('success', 'Reset password thành công!');
            CommonHelper::flushCache($this->module['table_name']);
            return redirect()->back();
        }
    }

    public function changePassword(Request $request)
    {
        if (!$_POST) {
            $data['result'] = \Auth::guard('admin')->user();
            $data['module'] = [
                'code' => 'profile',
                'label' => 'profile',
            ];
            $data['page_title'] = 'Đổi mật khẩu';
            $data['page_type'] = 'update';
            return view(config('core.admin_theme') . '.admin.change_password', $data);
        } else if ($_POST) {
            $validator = Validator::make($request->all(), [
                'password_current' => 'required|max:20',
                'password' => 'required|max:20|min:5',
                're_password' => 'required|max:20|min:5|same:password',
            ], [
                'password_current.required' => 'Bắt buộc phải nhập mật khẩu hiện tại!',
                'password_current.max' => 'Mật khẩu hiện tại tối đa 20 ký tự!',

                'password.required' => 'Bắt buộc phải nhập mật khẩu!',
                'password.min' => 'Mật khẩu phải trên 5 ký tự!',
                'password.max' => 'Mật khẩu tối đa 20 ký tự!',

                're_password.same' => 'Nhập lại sai mật khẩu!',
                're_password.max' => 'Mật khẩu nhập lại tối đa 20 kí tự',
                're_password.min' => 'Mật khẩu nhập lại phải trên 5 ký tự!',
                're_password.required' => 'Bắt buộc phải nhập lại mật khẩu!',
            ]);

            if (!\Auth::guard('admin')->attempt(['email' => \Auth::guard('admin')->user()->email, 'password' => trim($request->password_current)])) {
                CommonHelper::one_time_message('error', 'Mật khẩu không đúng!');
                return back();
            }
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            } else {

//                if ($request->password != $request->re_password) {
//                    CommonHelper::one_time_message('error', 'Nhập lại mật khẩu không khớp!');
//                    return back();
//                }
                \Auth::guard('admin')->user()->password = bcrypt($request->password);
                \Auth::guard('admin')->user()->password_md5 = md5($request->password, true);
                \Auth::guard('admin')->user()->save();
                CommonHelper::flushCache($this->module['table_name']);
                CommonHelper::one_time_message('success', 'Cập nhật thành công!');
                return back();
            }
        }
    }

    public function checkExist(Request $request) {
        if ($request->has('email')) {
            $admin = Admin::select('email', 'name', 'tel', 'id')->where('email', $request->email);
            if ($request->has('id')) {
                $admin = $admin->where('id', '!=', $request->id)->first();
            }
            $admin = $admin->first();
            if (is_object($admin)) {
                return response()->json([
                    'status' => false,
                    'html' => 'Email này đã được tạo cho <a  target="_blank" href="/admin/hradmin/'.$admin->id.'">'.$admin->name .' - email: ' . $admin->email.' - sđt: ' . $admin->tel.'</a>'
                ]);
            }
        }
        if ($request->has('tel')) {
            $admin = Admin::select('id', 'email', 'name', 'tel')->where('tel', $request->tel);
            if ($request->has('id')) {
                $admin = $admin->where('id', '!=', $request->id)->first();
            }
            $admin = $admin->first();
            if (is_object($admin)) {
                return response()->json([
                    'status' => false,
                    'html' => 'SĐT này đã được tạo cho <a target="_blank" href="/admin/hradmin/'.$admin->id.'">'.$admin->name .' - email: ' . $admin->email.' - sđt: ' . $admin->tel.'</a>'
                ]);
            }
        }
        return response()->json([
            'status' => true,
            'html' => ''
        ]);
    }

    public function searchForSelect2(Request $request)
    {
        $col2 = $request->get('col2', '') == '' ? '' : ', ' . $request->get('col2');

        $data = $this->model->selectRaw('id, ' . $request->col . $col2)
        ->where(function ($query) use ($request) {
                    $query->orWhere($request->col, 'like', '%' . $request->keyword . '%');
                    $query->orWhere('tel', 'like', '%' . $request->keyword . '%');
                    $query->orWhere('short_name', 'like', '%' . $request->keyword . '%');
                });

        if ($request->where != '') {
            $data = $data->whereRaw(urldecode(str_replace('&#039;', "'", $request->where)));
        }

        $data = $data->limit(10)->get();

        return response()->json([
            'status' => true,
            'items' => $data
        ]);
    }
}



