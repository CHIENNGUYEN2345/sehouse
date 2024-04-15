<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Models\Admin;
use App\Models\Job;
use App\Models\JobType;
use App\Models\PermissionRole;
use App\Models\RoleAdmin;
use App\Models\Roles;
use Auth;
use Illuminate\Http\Request;
use Validator;

class RoleController extends CURDBaseController
{
    protected $view = 'role';

    protected $module = [
        'code' => 'role',
        'label' => 'admin.role',
        'table_name' => 'roles',
        'modal' => '\App\Models\Roles',
        'list' => [
            ['name' => 'display_name', 'type' => 'text_edit', 'label' => 'admin.permission'],
            ['name' => 'description', 'type' => 'text', 'label' => 'admin.describe'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'display_name', 'type' => 'text', 'class' => 'require', 'label' => 'admin.name'],
                ['name' => 'description', 'type' => 'textarea', 'label' => 'admin.describe'],
            ],
        ],
    ];

    protected $filter = [
        'display_name' => [
            'label' => 'admin.permission',
            'type' => 'text',
            'query_type' => 'like'
        ],
    ];

    public function getIndex(Request $request)
    {


        $data = $this->getDataList($request);

        return view(config('core.admin_theme') . '.role.list')->with($data);
    }

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view(config('core.admin_theme') . '.role.add')->with($data);
            } else if ($_POST) {
                $validator = Validator::make($request->all(), [
                    'display_name' => 'required'
                ], [
                    'display_name.required' => 'Bắt buộc phải nhập tên',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert
                    $data['admin_id'] = \Auth::guard('admin')->user()->id;
                    #
                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }
                    if ($this->model->save()) {
                        $this->model->name = str_slug($data['display_name'], '_') . '_' . $this->model->id;
                        $this->model->save();
                        if (is_array($request->permission)) {
                            foreach ($request->permission as $key => $value) {
                                PermissionRole::firstOrCreate(['permission_id' => $value, 'role_id' => $this->model->id]);
                            }
                        }
                        CommonHelper::flushCache($this->module['table_name']);
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
                return view(config('core.admin_theme') . '.role.edit')->with($data);
            } else if ($_POST) {



                $validator = Validator::make($request->all(), [
                    'display_name' => 'required'
                ], [
                    'display_name.required' => 'Bắt buộc phải nhập tên',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert
                    #
                    foreach ($data as $k => $v) {
                        $item->$k = $v;
                    }
                    if ($item->save()) {
                        $stored_permissions = Roles::permission_role($request->id);
                        foreach ($stored_permissions as $key => $value) {
                            if (!in_array($value, $request->permission))
                                PermissionRole::where(['permission_id' => $value, 'role_id' => $request->id])->delete();
                        }

                        if (is_array($request->permission)) {
                            if (is_array($request->permission)) {
                                foreach ($request->permission as $key => $value) {
                                    PermissionRole::firstOrCreate(['permission_id' => $value, 'role_id' => $request->id]);
                                }
                            }
                        }

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
                        return redirect('admin/' . $this->module['code'] . '/edit/' . $item->id);
                    } elseif ($request->return_direct == 'save_create') {
                        return redirect('admin/' . $this->module['code'] . '/add');
                    }

                    return redirect('admin/' . $this->module['code']);
                }
            }
        } catch (\Exception $ex) {
//            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function getPublish(Request $request)
    {
        try {
            $id = $request->get('id', 0);
            $item = $this->model->find($id);

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
            CommonHelper::flushCache($this->module['table_name']);
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
            if (is_array($ids)) {
                $this->model->whereIn('id', $ids)->delete();
            }
            CommonHelper::one_time_message('success', 'Xóa thành công!');
            CommonHelper::flushCache($this->module['table_name']);
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
