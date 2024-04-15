<?php

namespace App\CRMEdu\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Illuminate\Http\Request;
use App\CRMEdu\Models\Service;
use Validator;
use App\Models\Admin;

class CategoryDocumentController extends CURDBaseController
{
    protected $whereRaw = 'type in (5)';

    protected $orderByRaw = 'role_ids asc, order_no desc';
 
    protected $module = [
        'code' => 'category_document',
        'table_name' => 'categories',
        'label' => 'Danh mục tài liệu',
        'modal' => '\App\CRMEdu\Models\Category',
        'list' => [
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Tiêu đề', 'sort' => true],
            ['name' => 'parent_id', 'type' => 'relation', 'label' => 'Danh mục cha', 'object' => 'parent', 'display_field' => 'name', 'sort' => true],
            ['name' => 'status', 'type' => 'status', 'label' => 'Trang thái'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tiêu đề', 'group_class' => 'col-md-6'],
                ['name' => 'parent_id', 'type' => 'custom', 'field' => 'CRMEdu.form.fields.select_model_tree', 'class' => '', 'label' => 'Danh mục cha', 'model' => \App\CRMEdu\Models\Category::class, 'where' => 'type in (5)', 'group_class' => 'col-md-6', 'display_field' => 'name'],
                ['name' => 'order_no', 'type' => 'number', 'class' => '', 'label' => 'Thứ tự', 'des' => 'Số to hiển thị trước', 'group_class' => 'col-md-4'],
                ['name' => 'status', 'type' => 'checkbox', 'label' => 'Hiển thị', 'value' => 1, 'group_class' => 'col-md-4'],
            ],
        ]
    ];

    protected $quick_search = [
        'label' => 'ID, tên',
        'fields' => 'id, name'
    ];

    public function appendWhere($query, $request)
    {
        if (!$request->has('search')) {
            //  nếu ko tìm kiếm thì hiển thị ra dạng cây
            //  nếu ko tìm kiếm thì hiển thị ra dạng cây
            $query = $query->where(function ($query) use ($request) {
                    $query->orWhereNull('parent_id');
                    $query->orWhere('parent_id', 0);
                });
        }
        return $query;
    }

    public function getIndex(Request $request)
    {

        $data = $this->getDataList($request);

        return view('CRMEdu.category_document.list')->with($data);
    }

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMEdu.category_document.add')->with($data);
            } else if ($_POST) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                ], [
                    'name.required' => 'Bắt buộc phải nhập tiêu đề',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert
                    $data['admin_id'] = \Auth::guard('admin')->user()->id;
                    $data['type'] = 5;
                   
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
                        return redirect('admin/' . $this->module['code'] . '/edit/' . $this->model->id);
                    } elseif ($request->return_direct == 'save_create') {
                        return redirect('admin/' . $this->module['code'] . '/add');
                    }

                    return redirect('admin/' . $this->module['code']);
                }
            }
        } catch (\Exception $ex) {
            // dd($ex->getMessage());
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
                return view('CRMEdu.category_document.edit')->with($data);
            } else if ($_POST) {
                \DB::beginTransaction();

                $validator = Validator::make($request->all(), [
//                    'name' => 'required',
                    // 'link' => 'required',
                ], [
//                    'name.required' => 'Bắt buộc phải nhập tên gói',
//                    'link.unique' => 'Web này đã đăng!',
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
