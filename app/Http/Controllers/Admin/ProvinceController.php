<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Helpers\CommonHelper;
use Validator;
use DB;


class ProvinceController extends CURDBaseController
{

    protected $module = [
        'code' => 'province',
        'table_name' => 'provinces',
        'label' => 'Tỉnh / Thành',
        'modal' => '\App\Models\Province',
        'list' => [
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Tên'],
//            ['name' => 'district_id', 'type' => 'belongsTo', 'label' => 'Khu vực', 'belongsTo' => 'district', 'display_field' => 'name'],
//            ['name' => 'code', 'type' => 'text', 'label' => 'Mã'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên'],
                ['name' => 'code', 'type' => 'text', 'class' => 'required', 'label' => 'Mã'],
//                      ['name' => 'district_id', 'type' => 'select2_model', 'class' => 'required', 'label' => 'Khu vực', 'model' => \App\Models\District::class, 'display_field' => 'name',],
            ],
        ],

    ];

    protected $filter = [
        /*'district_id' => [
              'label' => 'Khu vực',
              'type' => 'select2_model',
              'display_field' => 'name',
              'model' => \App\Models\District::class,
              'query_type' => '='
          ],*/
        'name' => [
            'label' => 'Tên',   //  Tên hiển thị
            'type' => 'text',   // Loại input
            'query_type' => 'like',  // Kiểu truy vấn
        ],
//        'code' => [
//            'label' => 'Mã',   //  Tên hiển thị
//            'type' => 'text',   // Loại input
//            'query_type' => '=',  // Kiểu truy vấn
//        ],

    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);


        return view('a4ilocation::list')->with($data);
    }

    public function add(Request $request)
    {
        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('a4ilocation::add')->with($data);
            } else if ($_POST) {
//                dd($request->trace_element_ids_image);
                $validator = Validator::make($request->all(), [
                    'name' => 'required'
                ], [
                    'name.required' => 'Bắt buộc phải nhập tên',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    $data['admin_id'] = \Auth::guard('admin')->user()->id;
                    $data['trace_element_ids'] = json_encode($this->getLand_info($request));

                    DB::beginTransaction();
                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {


//                            luu anh vao he thong
                        foreach ($request->trace_element_ids_image as $image) {
                            $file_name = $image->getClientOriginalName();
                            $file_name = str_replace(' ', '', $file_name);
                            $image->move(base_path() . '/public/filemanager/userfiles/land_info/', $file_name);
                        }
                        DB::commit();


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
            DB::rollback();
            CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function update(Request $request)
    {
        try {


            $item = $this->model->find($request->id);

            //  Chỉ sửa được liệu công ty mình đang vào
//            if (strpos(Auth::guard('admin')->user()->company_ids, '|' . $item->company_id . '|') === false) {
//                CommonHelper::one_time_message('error', 'Bạn không có quyền!');
//                return back();
//            }

            if (!is_object($item)) abort(404);
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('a4ilocation::edit')->with($data);
            } else if ($_POST) {


                $validator = Validator::make($request->all(), [
                    'name' => 'required'
                ], [
                    'name.required' => 'Bắt buộc phải nhập tên gắn gọn',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    $data['	trace_element_ids'] = json_encode($this->getLand_info($request));

                    dd(1123);
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
                        return redirect('admin/' . $this->module['code'] . '/edit/' . $item->id);
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


            $id = $request->get('id', 0);
            $item = $this->model->find($id);

            // Không được sửa dữ liệu của cty khác, cty mình ko tham gia


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

            //  Không được xóa dữ liệu của cty khác, cty mình ko tham gia


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
