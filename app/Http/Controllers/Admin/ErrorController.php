<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Auth;
use Illuminate\Http\Request;
use App\Http\Helpers\CommonHelper;
use Validator;

class ErrorController extends CURDBaseController
{

    protected $module = [
        'code' => 'error',
        'table_name' => 'errors',
        'label' => 'admin.history_error',
        'modal' => '\App\Models\Error',
        'list' => [
            ['name' => 'module', 'type' => 'text_edit', 'label' => 'admin.module'],
            ['name' => 'message', 'type' => 'text', 'label' => 'admin.message'],
            ['name' => 'code', 'type' => 'text', 'label' => 'admin.code'],
            ['name' => 'file', 'type' => 'text', 'label' => 'admin.file'],
            ['name' => 'created_at', 'type' => 'datetime_vi', 'label' => 'Ngày tạo'],
        ],
//        'form' => [
//            'general_tab' => [
//                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên'],
//                ['name' => 'content', 'type' => 'textarea_editor2', 'label' => 'Nội dung'],
//
//
//            ],
//
//            'info_tab' => [
//                ['name' => 'status', 'type' => 'checkbox', 'label' => 'Kích hoạt', 'value' => 0],
//
//
//            ],
//
//        ],
    ];

    protected $filter = [
        'module' => [
            'label' => 'admin.module',
            'type' => 'text',
            'query_type' => 'like'
        ],

    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('error.list')->with($data);
    }

    public function delete(Request $request)
    {
        try {
            $item = $this->model->find($request->id);

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

}
