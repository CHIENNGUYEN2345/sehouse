<?php

namespace App\CRMEdu\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Models\Admin;
use Illuminate\Http\Request;
use Validator;

class RemindController extends CURDBaseController
{

    protected $module = [
        'code' => 'remind',
        'table_name' => 'reminds',
        'label' => 'Lịch nhắc nhở',
        'modal' => '\App\CRMEdu\Models\Remind',
        'list' => [
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Tiêu đề', 'sort' => true],
            ['name' => 'reminded', 'type' => 'custom', 'td' => 'CRMEdu.list.td.multi_care', 'label' => 'Người nhận', 'object' => 'reminded'],
            ['name' => 'hours', 'type' => 'custom', 'td' => 'CRMEdu.list.td.text_remind', 'label' => 'Giờ nhận', 'sort' => true],
            ['name' => 'day_l', 'type' => 'custom', 'td' => 'CRMEdu.list.td.text_remind_date', 'label' => 'Thứ trong tuần nhận', 'sort' => true],
            ['name' => 'day', 'type' => 'custom', 'td' => 'CRMEdu.list.td.text_remind', 'label' => 'Ngày nhận', 'sort' => true],
            ['name' => 'status', 'type' => 'status', 'label' => 'Trang thái'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên'],
                ['name' => 'intro', 'type' => 'text', 'class' => 'required', 'label' => 'Nội dung nhắc nhở', 'des' => '{user_name}: tên người nhận'],
                ['name' => 'reminded', 'type' => 'custom', 'type_history' => 'relation_multiple', 'field' => 'CRMEdu.form.fields.select2_remind_model', 'multiple' => true, 'label' => 'Người nhận thông báo', 'model' => Admin::class, 'display_field' => 'name'],
                ['name' => 'status', 'type' => 'checkbox', 'class' => '', 'label' => 'Kích hoạt', 'value' => 1, 'group_class' => ''],
            ],
            'remind_tab' => [
                ['name' => 'hours', 'type' => 'checkbox_multiple', 'label' => 'Chọn giờ', 'options' => [
                    /*'1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,*/
                    '09' => 9,
//                    '10' => 10,
                    '11' => 11,
//                    '12' => 12,
//                    '13' => 13,
                    '14' => 14,
//                    '15' => 15,
//                    '16' => 16,
                    '17' => 17,
//                    '18' => 18,
//                    '19' => 19,
//                    '20' => 20,
//                    '21' => 21,
//                    '22' => 22,
//                    '23' => 23,
//                    '00' => 00,
                ]],
                ['name' => 'day_l', 'type' => 'checkbox_multiple', 'label' => 'Chọn thứ', 'options' => [
                    'Monday' => 2,
                    'Tuesday' => 3,
                    'Wednesday' => 4,
                    'Thursday' => 5,
                    'Friday' => 6,
                    'Saturday' => 7,
                    'Sunday' => 8
                ]],
                ['name' => 'day', 'type' => 'checkbox_multiple', 'label' => 'Chọn ngày', 'options' => [
                    '01' => 1,
                    '02' => 2,
                    '03' => 3,
                    '04' => 4,
                    '05' => 5,
                    '06' => 6,
                    '07' => 7,
                    '08' => 8,
                    '09' => 9,
                    '10' => 10,
                    '11' => 11,
                    '12' => 12,
                    '13' => 13,
                    '14' => 14,
                    '15' => 15,
                    '16' => 16,
                    '17' => 17,
                    '18' => 18,
                    '19' => 19,
                    '20' => 20,
                    '21' => 21,
                    '22' => 22,
                    '23' => 23,
                    '24' => 24,
                    '25' => 25,
                    '26' => 26,
                    '27' => 27,
                    '28' => 28,
                    '29' => 29,
                    '30' => 30,
                    '31' => 31
                ]],
            ],
        ]
    ];

    protected $filter = [
        'name' => [
            'label' => 'Tên',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'admin_id' => [
            'label' => 'Người nhận',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'object' => 'admin',
            'model' => Admin::class,
            'query_type' => 'custom',
        ],
    ];

    public function getIndex(Request $request)
    {


        $data = $this->getDataList($request);

        return view('CRMEdu.remind.list')->with($data);
    }

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMEdu.remind.add')->with($data);
            } else if ($_POST) {
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

                    if ($request->has('reminded')) {
                        $data['reminded'] = '|' . implode('|', $request->reminded) . '|';
                    }
                    if ($request->has('day_l')) {
                        $data['day_l'] = '|' . implode('|', $request->day_l) . '|';
                    }
                    if ($request->has('day')) {
                        $data['day'] = '|' . implode('|', $request->day) . '|';
                    }
                    if ($request->has('hours')) {
                        $data['hours'] = '|' . implode('|', $request->hours) . '|';
                    }

                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
                        $this->afterAddLog($request, $this->model);

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
                return view('CRMEdu.remind.edit')->with($data);
            } else if ($_POST) {


                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                ], [
                    'name.required' => 'Bắt buộc phải nhập tên gói',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert
                    if ($request->has('price_day')) {
                        $data['price'] = json_encode($this->getPriceInfo($request));
                    }

                    if ($request->has('reminded')) {
                        $data['reminded'] = '|' . implode('|', $request->reminded) . '|';
                    }
                    if ($request->has('day_l')) {
                        $data['day_l'] = '|' . implode('|', $request->day_l) . '|';
                    }
                    if ($request->has('day')) {
                        $data['day'] = '|' . implode('|', $request->day) . '|';
                    }
                    if ($request->has('hours')) {
                        $data['hours'] = '|' . implode('|', $request->hours) . '|';
                    }
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
