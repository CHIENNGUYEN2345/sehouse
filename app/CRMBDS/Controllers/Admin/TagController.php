<?php

namespace App\CRMBDS\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\CRMBDS\Models\Category;
use App\CRMBDS\Models\Codes;
use App\CRMBDS\Models\Theme;
use App\CRMBDS\Models\Tag;
use Validator;
use App\CRMBDS\Models\PostTag;
use App\CRMBDS\Models\BillProgress;

class TagController extends CURDBaseController
{

    protected $module = [
        'code' => 'tag',
        'table_name' => 'tags',
        'label' => 'CRMBDS_admin.tag',
        'modal' => '\App\CRMBDS\Models\Tag',
        'list' => [
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'CRMBDS_admin.tag_Name'],
            ['name' => 'type', 'type' => 'select', 'options' => [
                'bill_receipts' => 'Tài khoản tiền',
                'lead_rate' => 'Đánh giá đầu mối',
                'lead_source' => 'Nguồn khách',
                'user_tick' => 'Đánh dấu khách hàng',
            ], 'label' => 'Loại',],
            ['name' => 'status', 'type' => 'status', 'label' => 'CRMBDS_admin.tag_Trang_thai'],
            ['name' => 'color', 'type' => 'color', 'label' => 'CRMBDS_admin.tag_Mau_hien_thi'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => '', 'label' => 'CRMBDS_admin.tag_Ten', 'group_class' => 'col-md-6'],
                ['name' => 'type', 'type' => 'select_type_available', 'model' => \App\Models\Tag::class,
                    'options' => [
                        'bill_receipts' => 'Tài khoản tiền',
                        'lead_rate' => 'Đánh giá đầu mối',
                        'lead_source' => 'Nguồn khách',
                        'user_tick' => 'Đánh dấu khách hàng',
                    ],'field' => 'type', 'class' => '', 'label' => 'CRMBDS_admin.tag_Loai', 'group_class' => 'col-md-6'],
                ['name' => 'status', 'type' => 'checkbox', 'label' => 'CRMBDS_admin.tag_Kich_hoat', 'value' => 1, 'group_class' => 'col-md-3'],
                ['name' => 'order_no', 'type' => 'number', 'label' => 'Thứ tự', 'des' => 'CRMBDS_admin.tag_so_to_truoc', 'value' => 1, 'group_class' => 'col-md-3'],
                ['name' => 'color', 'type' => 'color', 'class' => '', 'label' => 'CRMBDS_admin.tag_Mau', 'group_class' => 'col-md-3'],
            ],
        ]
    ];

    protected $quick_search = [
        'label' => 'ID',
        'fields' => 'id, type'
    ];

    protected $filter = [
        'status' => [
            'label' => 'Trạng thái',
            'type' => 'select',
            'options' => [
                '' => 'Tất cả',
                0 => 'Không kich hoạt',
                1 => 'Kich hoạt',
            ],
            'query_type' => '='
        ],
        'type' => [
            'label' => 'Phân loại',
            'type' => 'select',
            'options' => [
                '' => '',
                'bill_receipts' => 'Tài khoản tiền',
                'lead_rate' => 'Đánh giá đầu mối',
                'lead_source' => 'Nguồn khách',
            ],
            'query_type' => '='
        ],

    ];

    public function getIndex(Request $request)
    {
        
        $data = $this->getDataList($request);

        return view('CRMBDS.tag.list')->with($data);
    }

    public function appendWhere($query, $request)
    {

        return $query;
    }

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMBDS.tag.add')->with($data);
            } else if ($_POST) {
                \DB::beginTransaction();

                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                ], [
                    'name.required' => 'Bắt buộc phải nhập tên',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert

                    $data['admin_id'] = \Auth::guard('admin')->user()->id;
                    $data['slug'] = $request->name;

                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
                        \DB::commit();

                        CommonHelper::one_time_message('success', 'Tạo mới thành công!');
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

    public function update(Request $request)
    {
        try {


            $item = $this->model->find($request->id);

            if (!is_object($item)) abort(404);
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('CRMBDS.tag.edit')->with($data);
            } else if ($_POST) {
                \DB::beginTransaction();

                $validator = Validator::make($request->all(), [
//                    'name' => 'required',
//                    'link' => 'required',
                ], [
//                    'name.required' => 'Bắt buộc phải nhập tên gói',
//                    'link.unique' => 'Web này đã đăng!',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());


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
            }
        } catch (\Exception $ex) {
            \DB::rollback();
//            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            CommonHelper::one_time_message('error', $ex->getMessage());
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
