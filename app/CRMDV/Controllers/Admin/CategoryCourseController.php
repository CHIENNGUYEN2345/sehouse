<?php

namespace App\CRMDV\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Illuminate\Http\Request;
use Validator;
use App\Models\Roles;

class CategoryCourseController extends CURDBaseController
{
    protected $whereRaw = 'type in (1)';
 
    protected $module = [
        'code' => 'category_course',
        'table_name' => 'categories',
        'label' => 'Danh mục tài liệu tào đạo nội bộ',
        'modal' => '\App\CRMDV\Models\Category',
        'list' => [
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Tiêu đề', 'sort' => true],
            ['name' => 'parent_id', 'type' => 'relation', 'label' => 'Danh mục cha', 'object' => 'parent', 'display_field' => 'name', 'sort' => true],
            ['name' => 'role_ids', 'type' => 'multi_relation', 'label' => 'Quyền được xem', 'model' => '\App\Models\Roles', 'display_field' => 'display_name'],
            ['name' => 'status', 'type' => 'status', 'label' => 'Trang thái'],
            ['name' => 'order_no', 'type' => 'number', 'label' => 'Thứ tự'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tiêu đề', 'group_class' => 'col-md-6'],
                ['name' => 'parent_id', 'type' => 'select_model_tree', 'field' => 'CRMDV.form.fields.select_model_tree', 'class' => '', 'label' => 'Danh mục cha', 'model' => \App\CRMDV\Models\Category::class, 'where' => 'type in (1)', 'group_class' => 'col-md-6', 'display_field' => 'name'],
                ['name' => 'order_no', 'type' => 'number', 'class' => '', 'label' => 'Thứ tự', 'des' => 'Số to hiển thị trước', 'group_class' => 'col-md-4'],
                ['name' => 'role_ids', 'type' => 'select2_ajax_model', 'label' => 'Nhóm quyền được xem', 'model' => Roles::class,
                    'object' => 'role', 'display_field' => 'display_name', 'multiple' => true, 'group_class' => 'col-md-8'],
                ['name' => 'status', 'type' => 'checkbox', 'label' => 'Hiển thị', 'value' => 1, 'group_class' => 'col-md-4'],
            ],
        ]
    ];

    protected $quick_search = [
        'label' => 'ID, tên',
        'fields' => 'id, name'
    ];

    protected $filter = [
        'role_id' => [
            'label' => 'Quyền được xem',
            'type' => 'select2_model',
            'display_field' => 'display_name',
            'model' => \App\Models\Roles::class,
            'object' => 'role',
            'query_type' => 'custom'
        ],
    ];

    public function sort($request, $model)
    {
        $dung_sap_xep = false;
        if ($request->sorts != null) {
            foreach ($request->sorts as $sort) {
                if ($sort != null) {
                    $dung_sap_xep = true;
                    $sort_data = explode('|', $sort);
                    $model = $model->orderBy($sort_data[0], $sort_data[1]);
                }
            }
        }

        if(!$dung_sap_xep) {
            if ($request->search == null) {
                //  nếu đang không tìm kiếm thì lọc theo cả phân quyền
                $model = $model->orderByRaw('role_ids asc, order_no desc');
            } else {
                //  Nếu đang search thì không sắp xếp theo quyền nữa
                $model = $model->orderByRaw('order_no desc');
            }
        }

        return $model;
    }

    public function appendWhere($query, $request)
    {
//        if (!$request->has('search')) {
//            //  nếu ko tìm kiếm thì hiển thị ra dạng cây
//            $query = $query->where(function ($query) use ($request) {
//                    $query->orWhereNull('parent_id');
//                    $query->orWhere('parent_id', 0);
//                });
//        }

        if($request->role_id != null) {
            $query = $query->where('role_ids', 'like', '%|'.$request->role_id.'|%');
        }
        return $query;
    }

    public function getIndex(Request $request)
    {

        $data = $this->getDataList($request);

        return view('CRMDV.category_course.list')->with($data);
    }

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMDV.category_course.add')->with($data);
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
                    $data['type'] = 1;
                    if ($request->has('role_ids')) {
                        $data['role_ids'] = '|' . implode('|', $request->role_ids) . '|';
                    }
                   
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
                return view('CRMDV.category_course.edit')->with($data);
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
                    if ($request->has('role_ids')) {
                        $data['role_ids'] = '|' . implode('|', $request->role_ids) . '|';
                    }

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
