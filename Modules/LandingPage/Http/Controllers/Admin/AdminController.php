<?php

namespace Modules\LandingPage\Http\Controllers\Admin;

use App\Http\Controllers\Admin\RoleController;
use App\Http\Helpers\CommonHelper;
use App\Models\{Admin, RoleAdmin, Setting, Roles, User};
use Auth;
use Illuminate\Http\Request;
use Mail;
use Session;
use Validator;

class AdminController extends CURDBaseController
{
    protected $_role;

    public function __construct()
    {
        parent::__construct();
        $this->_role = new RoleController();
    }

    protected $module = [
        'code' => 'user',
        'label' => 'User',
        'modal' => '\App\Models\Admin',
        'list' => [
            ['name' => 'image', 'type' => 'image', 'label' => 'admin.image'],
            ['name' => 'name', 'type' => 'text_admin_edit', 'label' => 'admin.name'],
            ['name' => 'role_id', 'type' => 'role_name', 'label' => 'admin.permission'],
            ['name' => 'tel', 'type' => 'text', 'label' => 'admin.phone'],
            ['name' => 'email', 'type' => 'text', 'label' => 'admin.email'],
            ['name' => 'status', 'type' => 'status', 'label' => 'admin.status'],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'admin.update']

        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'admin.full_name', 'group_class' => 'col-md-6'],
                ['name' => 'short_name', 'type' => 'text', 'class' => '', 'label' => 'Tên ngắn gọn', 'group_class' => 'col-md-6'],
                ['name' => 'email', 'type' => 'text', 'class' => 'required', 'label' => 'admin.email', 'group_class' => 'col-md-6'],
                ['name' => 'tel', 'type' => 'text', 'label' => 'admin.phone', 'group_class' => 'col-md-6'],
                ['name' => 'password', 'type' => 'password', 'class' => 'required', 'label' => 'admin.password', 'group_class' => 'col-md-6'],
                ['name' => 'password_confimation', 'type' => 'password', 'class' => 'required', 'label' => 'admin.re_password', 'group_class' => 'col-md-6'],
                ['name' => 'role_id', 'type' => 'select2_model', 'label' => 'Quyền','class' => 'required', 'model' => \App\Models\Roles::class, 'display_field' => 'display_name', 'group_class' => 'col-md-6'],
                ['name' => 'status', 'type' => 'checkbox', 'label' => 'admin.active', 'value' => 1, 'group_class' => 'col-md-6'],
            ],
            'more_info_tab' => [
                ['name' => 'image', 'type' => 'file_editor', 'label' => 'Ảnh đại diện'],
                ['name' => 'facebook', 'type' => 'text', 'class' => '', 'label' => 'facebook'],
                ['name' => 'skype', 'type' => 'text', 'class' => '', 'label' => 'skype'],
                ['name' => 'zalo', 'type' => 'text', 'class' => '', 'label' => 'zalo'],
            ],
        ]
    ];

    protected $filter = [
        'name_vi' => [
            'label' => 'admin.name',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'tel' => [
            'label' => 'admin.phone',
            'type' => 'number',
            'query_type' => 'like'
        ],
        'email' => [
            'label' => 'admin.email',
            'type' => 'text',
            'query_type' => 'like'
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


    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('webbill::admin.list')->with($data);
    }

    public function appendWhere($query, $request)
    {
        $not_customer = RoleAdmin::where('role_id', '!=', 3)->pluck('admin_id')->toArray();
        $not_customer_vip = RoleAdmin::where('role_id', '!=', 4)->pluck('admin_id')->toArray();
        $query = $query->whereIn('id', $not_customer);
        $query = $query->whereIn('id', $not_customer_vip);

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
}



