<?php

namespace Modules\WebBill\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Illuminate\Http\Request;
use Modules\WebBill\Models\PenaltyTicket;
use App\Models\Admin;
use Validator;

class DomainErrorLogController extends CURDBaseController
{

	protected $limit_default = 50;

    protected $module = [
        'code' => 'check_error_link_logs',
        'table_name' => 'check_error_link_logs',
        'label' => 'Website lỗi',
        'modal' => '\Modules\WebBill\Models\CheckErrorLinkLogs',
        'list' => [
        	['name' => 'links', 'type' => 'link', 'label' => 'Link'],
            ['name' => 'error_code', 'type' => 'text', 'label' => 'Mã lỗi',],
           	['name' => 'error_messenger', 'type' => 'text', 'label' => 'Chi tiết lỗi'],
           	['name' => 'edit_status', 'type' => 'status', 'label' => 'Trạng thái sửa'],
           	['name' => 'created_at', 'type' => 'date_vi', 'label' => 'Thời gian phát hiện'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'staff_id', 'type' => 'select2_ajax_model', 'class' => ' required',  'label' => 'Thành viên', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'code', 'group_class' => 'col-md-3'],
                ['name' => 'regulations', 'type' => 'text', 'class' => 'required', 'label' => 'Vi phạm quy định gì?'],
                ['name' => 'date', 'type' => 'date_vi', 'class' => 'required', 'label' => 'Ngày vi phạm'],
                ['name' => 'money', 'type' => 'number', 'class' => 'required', 'label' => 'Số tiền phạt'],
                ['name' => 'image', 'type' => 'file_image', 'class' => 'required', 'label' => 'Ảnh bằng chứng'],
            ],
        ]
    ];

    protected $filter = [
        /*'name_vi' => [
            'label' => 'Tên',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'price_vi' => [
            'label' => 'Giá',
            'type' => 'number',
            'query_type' => '='
        ],*/
    ];

    public function appendWhere($query, $request)
    {

        return $query;
    }

    public function getIndex(Request $request)
    {

        $data = $this->getDataList($request);

        return view('webbill::check_error_link_logs.list')->with($data);
    }

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('webbill::check_error_link_logs.add')->with($data);
            } else if ($_POST) {
                $validator = Validator::make($request->all(), [
                    // 'name_vi' => 'required',
                ], [
                    // 'name_vi.required' => 'Bắt buộc phải nhập tên',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert
                    $data['admin_id'] = \Auth::guard('admin')->user()->id;
                    
                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {

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

            if (!is_object($item)) abort(404);
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('webbill::check_error_link_logs.edit')->with($data);
            } else if ($_POST) {


                $validator = Validator::make($request->all(), [
                    // 'name_vi' => 'required',
                ], [
                    // 'name_vi.required' => 'Bắt buộc phải nhập tên gói',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert
                    
                    foreach ($data as $k => $v) {
                        $item->$k = $v;
                    }
                    if ($item->save()) {
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
            }
        } catch (\Exception $ex) {
            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
//            CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
        }
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

            if ($item->{$request->column} == 0) {
            	$item->{$request->column} = 1;
            }
            else {
            	if (\Auth::guard('admin')->user()->id != $item->staff_id) {
            		//	người không bị phạt mới sửa được trạng thái
            		$item->{$request->column} = 0;
            	}
            }

            $item->save();

            return response()->json([
                'status' => true,
                'published' => $item->{$request->column} == 1 ? true : false
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'published' => null,
                // 'msg' => 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.',
                'msg' => $ex->getMessage(),
            ]);
        }
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
