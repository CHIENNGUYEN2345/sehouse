<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Models\Admin;
use Illuminate\Http\Request;

class AdminLogController extends CURDBaseController
{
    protected $_role;

    public function __construct()
    {
        parent::__construct();
        $this->_role = new RoleController();

    }

    protected $module = [
        'code' => 'admin_logs',
        'label' => 'Lịch sử admin',
        'modal' => '\App\Models\AdminLog',
        'table_name' => 'admin_logs',
        'list' => [
            ['name' => 'id', 'type' => 'text', 'label' => 'ID'],
            ['name' => 'admin_id', 'type' => 'relation','object'=>'profile', 'label' => 'Nhân viên','display_field'=>'name'],
            ['name' => 'message', 'type' => 'text', 'label' => 'Nội dung'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Thời gian'],
        ]
    ];

    protected $filter = [
        'created_at' => [
            'label' => 'Thời gian',
            'type' => 'from_to_date',
            'query_type' => 'from_to_date'
        ],
        'admin_id' => [
            'label' => 'Nhân viên',
            'type' => 'select2_model',
            'display_field' => 'name',
            'model' => Admin::class,
            'query_type' => '='
        ],
        'message' => [
            'label' => 'nội dung',
            'type' => 'text',
            'query_type' => 'like'
        ],
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('admin.themes.metronic1.admin_logs.list')->with($data);
    }

    public function delete(Request $request)
    {
        try {
            $item = $this->model->find($request->id);

            $item->delete();
            CommonHelper::flushCache($this->module['table_name']);
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
//            $this->adminLog($request,$ids,'multi_delete');
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
}



