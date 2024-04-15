<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Auth;
use Illuminate\Http\Request;
use Mail;
use Session;
use Validator;

class ChangeDataHistoryController extends CURDBaseController
{

    protected $module = [
        'code' => 'change_data_history',
        'label' => 'Lịch sử thanh đổi',
        'modal' => '\App\Models\ChangeDataHistory',
        'table_name' => 'change_data_history',
        'list' => [
            ['name' => 'admin_id', 'type' => 'relation', 'label' => 'Người thực hiện', 'object' => 'admin', 'display_field' => 'name'],
            ['name' => 'data', 'type' => 'custom', 'td' => 'change_data_history.list.td.data', 'label' => 'Hành động'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'admin.update']
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'admin.full_name'],
                ['name' => 'email', 'type' => 'text', 'class' => '', 'label' => 'admin.email'],
                ['name' => 'tel', 'type' => 'text', 'label' => 'admin.phone'],
                ['name' => 'Facebook', 'type' => 'text', 'label' => 'Facebook'],
//                ['name' => 'password', 'type' => 'password', 'class' => 'required', 'label' => 'Mật khẩu'],
//                ['name' => 'password_confimation', 'type' => 'password', 'class' => 'required', 'label' => 'Nhập lại mật khẩu'],
            ],
        ]
    ];

    protected $filter = [
        'name' => [
            'label' => 'admin.name',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'tel' => [
            'label' => 'admin.phone',
            'type' => 'number',
            'query_type' => '='
        ],
        'email' => [
            'label' => 'admin.email',
            'type' => 'number',
            'query_type' => '='
        ],
//        'status' => [
//            'label' => 'Trạng thái',
//            'type' => 'select',
//            'query_type' => '=',
//            'options' => [
//                '' => 'Trạng thái',
//                0 => 'Ẩn',
//                1 => 'Kich hoạt'
//            ]
//        ],
    ];

    /*protected $validate = [
        'request' => [
            'name' => 'required',
        ],
        'label' => [
            'name' => 'Tên',
            'email' => 'Email'
        ]
    ];*/

    /*protected $validate_add = [
        'request' => [
            'email' => 'required|email|max:255|unique:admin'
        ],
        'label' => [
            'email' => 'Email'
        ]
    ];*/
    public function getIndex(Request $request)
    {
//        dd(1);
        $data = $this->getDataList($request);

        return view('admin.themes.metronic1.'.$this->module['code'].'.list')->with($data);
    }

    public function add(Request $request)
    {
        try {

            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('admin.themes.metronic1.'.$this->module['code'].'.add')->with($data);
            } else if ($_POST) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                ], [
                    'name.required' => 'Bắt buộc phải nhập tên!',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert

                    #
                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
                        CommonHelper::flushCache($this->module['table_name']);
                        CommonHelper::one_time_message('success', 'Tạo mới thành công!');
                        return redirect('/admin/user');
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

//            //  Chỉ sửa được liệu công ty mình đang vào
//            if (strpos(\Auth::guard('admin')->user()->company_ids, '|' . $item->company_id . '|') === false) {
//                CommonHelper::one_time_message('error', 'Bạn không có quyền!');
//                return back();
//            }

            if (!is_object($item)) {
                abort(404);
            }
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('admin.themes.metronic1.'.$this->module['code'].'.edit')->with($data);
            } else if ($_POST) {
                if ($item->id == \Auth::guard('admin')->user()->id) {
                    $validator = Validator::make($request->all(), [
                        'name' => 'required'
                    ], [
                        'name.required' => 'Bắt buộc phải nhập tên',
                    ]);

                    if ($validator->fails()) {
                        return back()->withErrors($validator)->withInput();
                    }
                }
                $data = $this->processingValueInFields($request, $this->getAllFormFiled());
//                    dd($data);
                //  Tùy chỉnh dữ liệu edit
                $data['status'] = $request->has('status') ? 1 : 0;

                #
                foreach ($data as $k => $v) {
                    $item->$k = $v;
                }
                if ($item->save()) {
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
        } catch (\Exception $ex) {
//            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function validator(array $data)
    {
        $rules = array(
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|max:50',
        );

        $fieldNames = array(
            'name' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
        );

        $validator = Validator::make($data, $rules);
        $validator->setAttributeNames($fieldNames);
        return $validator;
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

    public function searchForSelect2(Request $request)
    {
        $data = $this->model->select([$request->col, 'id'])->where($request->col, 'like', '%' . $request->keyword . '%');
        if ($request->where != '') {
            $data = $data->whereRaw(urldecode(str_replace('&#039;', "'", $request->where)));
        }

        $data = $data->limit(5)->get();
        return response()->json([
            'status' => true,
            'items' => $data
        ]);
    }

    public function addLog($r, $item, $module, $data_update, $admin_id = null, $note = '') {
        $this->model->note = $note;
        $this->model->admin_id = $admin_id;
        $this->model->table_name = $module['table_name'];
        $this->model->item_id = $item->id;
        $data = [];
        foreach ($data_update as $k => $v) {
            if ($item->{$k} != $v) {
                $data[] = [
                    'column' => $k,
                    'old_value' => $item->{$k},
                    'new_value' => $v
                ];
            }
        }
        if (!empty($data) || $note != '') {
            $this->model->data = json_encode($data);
            $this->model->save();
        }
        return true;
    }
}



