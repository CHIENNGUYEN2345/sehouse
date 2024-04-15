<?php

namespace App\CRMEdu\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Illuminate\Http\Request;
use Modules\EworkingCompany\Models\Company;
use App\CRMEdu\Models\Service;
use App\CRMEdu\Models\ServiceHistory;
use Validator;
use App\CRMEdu\Models\LeadContactedLog;
use App\CRMEdu\Models\UngVien;
use App\Models\Admin;
use DB;

class UngVienaController extends CURDBaseController
{

    protected $module = [
        'code' => 'Ung-Vien',
        'table_name' => 'UngVien',
        'label' => 'Ứng Viên',
        'modal' => '\App\CRMEdu\Models\UngVien',
        'list' => [
            ['name' => 'name', 'type' => 'custom', 'td' => 'Tên', 'label' => 'Tên', 'sort' => true],
            ['name' => 'Phone', 'type' => 'text', 'label' => 'CRMEdu_admin.UngVien_SĐT', 'sort' => true],
            ['name' => 'Email', 'type' => 'custom', 'td' => 'CRMEdu_admin.UngVien_Email', 'label' => 'CRMEdu_admin.UngVien_Email', 'sort' => true],
            ['name' => 'Vi_Tri', 'type' => 'custom', 'td' => 'CRMEdu_admin.UngVien_Vi_Tri', 'label' => 'CRMEdu_admin.UngVien_Vi_Tri', 'sort' => true],
            ['name' =>'CV', 'type' => 'custom', 'td' => 'CRMEdu_admin.UngVien_CV', 'label' => 'CRMEdu_admin.UngVien_CV', 'sort' => true],
            ['name' =>'Trang_Ca_Nhan', 'type' => 'custom', 'td' => 'CRMEdu_admin.UngVien_Ca_nhan', 'label' => 'CRMEdu_admin.UngVien_Ca_nhan', 'sort' => true],

            ['name' => 'Tuyen_Dung', 'type' => 'text', 'label' => 'CRMEdu_admin.UngVien_Tuyen_Dung', 'sort' => true],
            ['name' => 'Phong_Van', 'type' => 'text', 'label' => 'CRMEdu_admin.UngVien_Phong_Van', 'sort' => true],
            ['name' => 'status', 'type' => 'text', 'label' => 'CRMEdu_admin.UngVien_status', 'sort' => true],

        ],
        'form' =>[
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'CRMEdu_admin.UngVien_name', 'group_class' => 'col-md-3'],
                ['name' => 'Phone', 'type' => 'text', 'field' => 'CRMEdu_admin.UngVien_SĐT', 'class' => 'required', 'label' => 'CRMEdu_admin.UngVien_SĐT', 'group_class' => 'col-md-3'],
                ['name' => 'Email', 'type' => 'text', 'label' => 'CRMEdu_admin.UngVien_Email', 'group_class' => 'col-md-3'],
                ['name' => 'Vi_Tri', 'type' => 'text', 'field' => 'CRMEdu_admin.UngVien_Vi_Tri', 'class' => 'required', 'label' => 'CRMEdu_admin.UngVien_Vi_Tri', 'group_class' => 'col-md-3'],
                ['name' => 'CV', 'type' => 'text', 'field' => 'CRMEdu_admin.UngVien_CV', 'class' => 'required', 'label' => 'CRMEdu_admin.UngVien_CV', 'group_class' => 'col-md-3'],
                ['name' => 'Trang_Ca_Nhan', 'type' => 'text', 'field' => 'CRMEdu_admin.UngVien_Ca_nhan', 'class' => 'required', 'label' => 'CRMEdu_admin.UngVien_Ca_nhan', 'group_class' => 'col-md-3'],
                ['name' => 'Tuyen_Dung', 'type' => 'text', 'field' => 'CRMEdu_admin.UngVien_Tuyen_Dung', 'class' => 'required', 'label' => 'CRMEdu_admin.UngVien_Tuyen_Dung', 'group_class' => 'col-md-3'],



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
    ];
    public function getIndex()
    {
        //dd($request->all());



        return view('CRMEdu.UngVien.list');
    }
  /*  public function getIndex(Request $request)
    {
        //dd($request->all());
        $data = $this->getDataList($request);
//dd($data);

        return view('CRMEdu.UngVien.list')->with($data);
    }*/

    public function appendWhere($query, $request)
    {

        return $query;
    }

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMEdu.UngVien.add')->with($data);
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
                return view('CRMEdu.UngVien.edit')->with($data);
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