<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Auth;
use Illuminate\Http\Request;
use Validator;

class QueueController extends CURDBaseController
{

    protected $module = [
        'code' => 'queue',
        'table_name' => 'queue',
        'label' => 'admin.queue',
        'modal' => '\App\Models\Queue',
        'list' => [
            ['name' => 'queue', 'type' => 'text', 'label' => 'admin.type'],
//            ['name' => 'payload', 'type' => 'text', 'label' => 'payload'],
            ['name' => 'attempts', 'type' => 'text', 'label' => 'admin.attempts'],
            ['name' => 'reserved_at', 'type' => 'text', 'label' => 'admin.reserved_at'],
            ['name' => 'available_at', 'type' => 'text', 'label' => 'admin.available_at'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'admin.date_creat'],


        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'admin.name'],
                ['name' => 'content', 'type' => 'textarea_editor2', 'label' => 'admin.content'],


            ],

            'info_tab' => [
                ['name' => 'status', 'type' => 'checkbox', 'label' => 'admin.active', 'value' => 0],


            ],

        ],
    ];

    protected $filter = [
        'module' => [
            'label' => 'queue',
            'type' => 'text',
            'query_type' => 'like'
        ],

    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('admin.themes.metronic1.queue.view')->with($data);
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

    public function update()
    {
        return redirect('/admin/themes/metronic1/queue/edit');
    }
}
