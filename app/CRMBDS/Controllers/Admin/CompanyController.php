<?php

namespace App\CRMBDS\Controllers\Admin;

use App\CRMBDS\Models\Bill;
use App\CRMBDS\Models\Service;
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

class CompanyController extends CURDBaseController
{

    protected $module = [
        'code' => 'company',
        'table_name' => 'company_category',
        'label' => 'Hồ sơ công ty',
        'modal' => '\App\CRMBDS\Models\Company',
        'list' => [
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'CRMBDS_admin.company_name'],
            ['name' => 'mst', 'type' => 'text', 'label' => 'CRMBDS_admin.company_mst'],
            ['name' => 'address', 'type' => 'text', 'label' => 'CRMBDS_admin.company_address', 'object' => 'tutors', 'display_field' => 'name', 'sort' => true],
            ['name' => 'dai_dien', 'type' => 'text','label' => 'CRMBDS_admin.company_representative',],
            ['name' => 'tel', 'type' => 'text', 'label' => 'CRMBDS_admin.company_tel'],
            ['name' => 'nganh_nghe', 'type' => 'text', 'label' => 'CRMBDS_admin.company_career'],
            ['name' => 'tel', 'type' => 'date_vi', 'label' => 'CRMBDS_admin.company_Registration_date'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => '', 'label' => 'CRMBDS_admin.company_name', 'group_class' => 'col-md-6'],
                ['name' => 'mst', 'type' => 'text', 'class' => '', 'label' => 'CRMBDS_admin.company_mst', 'group_class' => 'col-md-4'],
                ['name' => 'address', 'type' => 'text', 'class' => '', 'label' => 'CRMBDS_admin.company_address', 'group_class' => 'col-md-6'],
                ['name' => 'dai_dien', 'type' => 'text', 'class' => '', 'label' => 'CRMBDS_admin.company_representative', 'group_class' => 'col-md-4'],
                ['name' => 'tel', 'type' => 'text', 'class' => '', 'label' => 'CRMBDS_admin.company_tel', 'group_class' => 'col-md-4'],
                ['name' => 'email', 'type' => 'text', 'class' => '', 'label' => 'CRMBDS_admin.company_email', 'group_class' => 'col-md-6'],
                ['name' => 'ngay_cap', 'type' => 'date', 'class' => '', 'label' => 'CRMBDS_admin.company_ngaycap', 'group_class' => 'col-md-4'],
                ['name' => 'nganh_nghe', 'type' => 'text', 'class' => '', 'label' => 'CRMBDS_admin.company_career', 'group_class' => 'col-md-6'],
            ],
        ]
    ];

    protected $quick_search = [
        'label' => 'ID',
        'fields' => 'id, type, name, mst, daidien, tel, email'
    ];

    protected $filter = [
        'name' => [
            'label' => 'CRMBDS_admin.company_name',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'display_field2' => 'code',
            'model' => \App\CRMBDS\Models\Company::class,
            'object' => 'company_profile',
            'query_type' => '='
        ],
        'dai_dien' => [
            'label' => 'CRMBDS_admin.company_representative',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'display_field2' => 'code',
            'model' => \App\CRMBDS\Models\Company::class,
            'object' => 'company_profile',
            'query_type' => '='
        ],


    ];

    public function getIndex(Request $request)
    {

        $data = $this->getDataList($request);

        return view('CRMBDS.company.list')->with($data);
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
                return view('CRMBDS.company.add')->with($data);
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

                    $data['bill_ids'] = '|' . implode('|', $request->get('bill_ids', [])) . '|';

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
                return view('CRMBDS.company.edit')->with($data);
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

                    $data['bill_ids'] = '|' . implode('|', $request->get('bill_ids', [])) . '|';

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
