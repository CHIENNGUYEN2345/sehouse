<?php

namespace Modules\EduMarketing\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Mail\MailServer;
use App\Models\Setting;
use Auth;
use Illuminate\Http\Request;

use Validator;

class EmailAccountController extends CURDBaseController
{

    protected $module = [
        'code' => 'email_account',
        'table_name' => 'email_accounts',
        'label' => 'Tài khoản email',
        'modal' => '\Modules\EduMarketing\Models\EmailAccount',
        'list' => [
            ['name' => 'mail_name', 'type' => 'text_edit', 'label' => 'Tên người gửi', 'duplicate' => true],
            ['name' => 'username', 'type' => 'text', 'label' => 'Tên tài khoản', 'duplicate' => true],
            ['name' => 'driver', 'type' => 'text', 'label' => 'Loại', 'duplicate' => true],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'username', 'type' => 'text','class' => 'required', 'label' => 'Tên tài khoản', 'group_class' => 'col-md-6'],
                ['name' => 'password', 'type' => 'text', 'class' => 'required', 'label' => 'Mật khẩu', 'group_class' => 'col-md-6'],
                ['name' => 'driver', 'type' => 'select', 'options' => [
                    'smtp' => 'smtp',
                    'mailgun' => 'mailgun',
                ],'class' => 'required', 'label' => 'Loại', 'group_class' => 'col-md-6'],
                ['name' => 'mail_name', 'type' => 'text','class' => 'required', 'label' => 'Têm người gửi email', 'group_class' => 'col-md-6'],
                ['name' => 'host', 'type' => 'text','class' => 'required', 'label' => 'Máy chủ', 'group_class' => 'col-md-6'],
                ['name' => 'port', 'type' => 'number','class' => 'number_price', 'label' => 'Cổng', 'group_class' => 'col-md-6'],
                ['name' => 'smtp_encryption', 'type' => 'select', 'options' => [
                    'tls' => 'tls',
                    'ssl' => 'ssl',
                ],'class' => '', 'label' => 'Smtp encryption', 'group_class' => 'col-md-6'],
                ['name' => 'mailgun_domain', 'type' => 'text','class' => '', 'label' => 'Mailgun domain', 'group_class' => 'col-md-6'],
                ['name' => 'mailgun_secret', 'type' => 'text','class' => '', 'label' => 'Mailgun secret', 'group_class' => 'col-md-6'],
            ],
            'info_tab' => [

            ],
        ],
    ];

    protected $filter = [
        'username' => [
            'label' => 'Tên tài khoản',
            'type' => 'text',
            'query_type' => 'like'

        ],
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('edumarketing::email_account.list')->with($data);
    }



    public function add(Request $request)
    {
        try {
            if (!$_POST) {

                $data = $this->getDataAdd($request);

                return view('edumarketing::email_account.add')->with($data);
            } else if ($_POST) {
                $validator = Validator::make($request->all(), [
                    'username' => 'required'
                ], [
                    'username.required' => 'Bắt buộc phải nhập tên',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    //  Tùy chỉnh dữ liệu insert
                    if ($request->has('multi_cat')) {
                        $data['multi_cat'] = '|' . implode('|', $request->multi_cat) . '|';
                        $data['category_id'] = $request->multi_cat[0];
                    }
                    if ($request->has('tags')) {
                        $data['tags'] = '|' . implode('|', $request->tags) . '|';
                    }
                    #

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
                        return redirect('admin/' . $this->module['code'] . '/' . $this->model->id);
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

            //  Chỉ sửa được liệu công ty mình đang vào
//            if (strpos(\Auth::guard('admin')->user()->company_ids, '|' . $item->company_id . '|') === false) {
//                CommonHelper::one_time_message('error', 'Bạn không có quyền!');
//                return back();
//            }

            if (!is_object($item)) abort(404);
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('edumarketing::email_account.edit')->with($data);
            } else if ($_POST) {



                $validator = Validator::make($request->all(), [
                    'username' => 'required'
                ], [
                    'username.required' => 'Bắt buộc phải nhập tên',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());


                    //  Tùy chỉnh dữ liệu insert
                    if ($request->has('multi_cat')) {
                        $data['multi_cat'] = '|' . implode('|', $request->multi_cat) . '|';
                        $data['category_id'] = $request->multi_cat[0];
                    }
                    if ($request->has('tags')) {
                        $data['tags'] = '|' . implode('|', $request->tags) . '|';
                    }
                    #

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
                        return redirect('admin/' . $this->module['code'] . '/' . $item->id);
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
//            if (strpos(Auth::guard('admin')->user()->company_ids, '|' . $item->company_id . '|') === false) {
//                return response()->json([
//                    'status' => false,
//                    'msg' => 'Bạn không có quyền xuất bản!'
//                ]);
//            }

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
//            if (strpos(Auth::guard('admin')->user()->company_ids, '|' . $item->company_id . '|') === false) {
//                CommonHelper::one_time_message('error', 'Bạn không có quyền xóa!');
//                return back();
//            }

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
