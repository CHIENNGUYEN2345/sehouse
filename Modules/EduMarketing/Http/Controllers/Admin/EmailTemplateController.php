<?php

namespace Modules\EduMarketing\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Mail\MailServer;
use App\Models\Setting;
use Auth;
use Illuminate\Http\Request;

use Validator;

class EmailTemplateController extends CURDBaseController
{

    protected $module = [
        'code' => 'email_template',
        'table_name' => 'email_templates',
        'label' => 'Mẫu Email',
        'modal' => '\Modules\EduMarketing\Models\EmailTemplate',
        'list' => [
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Tên mẫu', 'duplicate' => true],
            ['name' => 'quick_view', 'type' => 'custom', 'td' => 'edumarketing::list.td.quick_view_template', 'label' => 'Xem trước', 'duplicate' => true],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text','class' => 'required', 'label' => 'Tên mẫu'],
//                ['name' => 'header', 'type' => 'textarea_editor','class' => 'required', 'label' => 'Đầu email'],
                ['name' => 'content', 'type' => 'textarea_editor','class' => 'required', 'label' => 'Nội dung email', 'height' => '700px'],
//                ['name' => 'footer', 'type' => 'textarea_editor','class' => 'required', 'label' => 'Chân email'],
            ],
            'info_tab' => [

            ],
        ],
    ];

    protected $filter = [
        'name' => [
            'label' => 'Tên mẫu',
            'type' => 'text',
            'query_type' => 'like'

        ],
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('edumarketing::email_template.list')->with($data);
    }



    public function add(Request $request)
    {
        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('edumarketing::email_template.add')->with($data);
            } else if ($_POST) {
//                dd($request);
                $validator = Validator::make($request->all(), [
                    'name' => 'required'
                ], [
                    'name.required' => 'Bắt buộc phải nhập tên',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
//                    dd($request->all());
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    //  Tùy chỉnh dữ liệu insert
                    if ($request->has('customer_tags')) {
                        $data['customer_tags'] = '|' . implode('|', $request->customer_tags) . '|';
                    }
                    if ($request->has('object')) {
                        $data['object'] = '|' . implode('|', $request->object) . '|';
                    }
                    /*if ($request->has('multi_cat')) {
                        $data['multi_cat'] = '|' . implode('|', $request->multi_cat) . '|';
                        $data['category_id'] = $request->multi_cat[0];
                    }
                    if ($request->has('student_ids')) {
                        $data['student_ids'] = '|' . implode('|', $request->student_ids) . '|';
                    }
                    if ($request->has('class_id')) {
                        $data['class_id'] = '|' . implode('|', $request->class_id) . '|';
                    }*/
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
                    } elseif ($request->return_direct == 'save_editor') {
                        return redirect('admin/' . $this->module['code'] . '/' . $this->model->id . '/editor');
                    }

                    return redirect('admin/' . $this->module['code'].'/'.$this->model->id);
                }
            }
        } catch (\Exception $ex) {
            dd($ex->getMessage());
            CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function update(Request $request)
    {
        $item = $this->model->find($request->id);


        if (!is_object($item)) abort(404);
        if (!$_POST) {
            $data = $this->getDataUpdate($request, $item);
            return view('edumarketing::email_template.edit')->with($data);
        } else if ($_POST) {

            $validator = Validator::make($request->all(), [
                'name' => 'required'
            ], [
                'name.required' => 'Bắt buộc phải nhập tên',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            } else {
                $data = $this->processingValueInFields($request, $this->getAllFormFiled());

//                  Tùy chỉnh dữ liệu insert



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
                    return redirect('admin/' . $this->module['code'] . '/' . $this->model->id);
                } elseif ($request->return_direct == 'save_create') {
                    return redirect('admin/' . $this->module['code'] . '/add');
                } elseif ($request->return_direct == 'save_editor') {
                    return redirect('admin/' . $this->module['code'] . '/' . $this->model->id . '/editor');
                }

                return redirect('admin/' . $this->module['code']);
            }
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

            //  Xóa các lịch gửi mail chưa được gửi

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
                //  Xóa các lịch gửi mail chưa được gửi
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

    public function ajaxGetInfo(Request $r, $id) {
        $item = $this->model->find($id);
        return response()->json([
            'status' => true,
            'data' => $item
        ]);
    }

    public function warehouse(Request $r) {
        $data = $this->getDataAdd($r);
        return view('edumarketing::email_template.warehouse')->with($data);
    }
}
