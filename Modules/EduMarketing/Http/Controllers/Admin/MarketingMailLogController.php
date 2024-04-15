<?php

namespace Modules\EduMarketing\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Auth;
use Illuminate\Http\Request;
use Modules\EduMarketing\Models\MaketingMail;
use Modules\EduMarketing\Models\Category;
use Modules\EduMarketing\Models\Course;
use Modules\EduMarketing\Models\Lesson;
use Modules\ThemeEdu\Models\Student;
use Validator;

class MarketingMailLogController extends CURDBaseController
{
//    protected $orderByRaw = 'order_no desc, id desc';
    protected $module = [
        'code' => 'maketing-mail-log',
        'table_name' => 'maketing_mail_log',
        'label' => 'Lịch sử gửi mail',
        'modal' => '\Modules\EduMarketing\Models\MarketingMailLog',
        'list' => [
            ['name' => 'marketing_mail_id', 'type' => 'relation', 'label' => 'Chiến dịch', 'object' => 'campaign', 'display_field' => 'name'],
            ['name' => 'email', 'type' => 'text_edit', 'label' => 'Email nhận'],
            ['name' => 'type', 'type' => 'select', 'label' => 'Đối tượng', 'options' => [
                'customer' => 'Khách hàng',
                'student' => 'Học sinh',
                'lecturer' => 'Giáo viên',
                'other' => 'Khác',
            ]],
//            ['name' => 'student_id', 'type' => 'relation', 'label' => 'Học viên', 'object' => 'studentmail', 'display_field' => 'name'],
//            ['name' => 'action', 'type' => 'custom', 'td' => 'EduMarketing::list.td.action_lesson', 'class' => '', 'label' => '#'],
//            ['name' => 'class_id', 'type' => 'relation', 'label' => 'Lớp học', 'object' => 'classmail', 'display_field' => 'name'],
//            ['name' => 'date_send', 'type' => 'text', 'label' => 'Cập nhật lúc'],
            ['name' => 'error', 'type' => 'status2', 'options' => [
                0 => 'Có lỗi',
                1 => ''
            ], 'label' => 'Bị lỗi'],
            ['name' => 'sent', 'type' => 'status2', 'label' => 'Trạng thái', 'options' => [
                0 => 'Chờ gửi',
                1 => 'Đã gửi'
            ],],
            ['name' => 'opened', 'type' => 'status2', 'label' => 'Khách đã xem', 'options' => [
                0 => '',
                1 => 'Đã xem'
            ]],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Cập nhật lúc'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên bài học'],
                ['name' => 'order_no', 'type' => 'number', 'label' => 'Thứ tự ưu tiên', 'value' => 0, 'group_class' => 'col-md-3', 'des' => 'Số to ưu tiên hiển thị trước'],
            ],

        ],
    ];

    protected $filter = [
        'marketing_mail_id' => [
            'label' => 'Chiến dịch',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'object' => 'marketing-mail',
            'model' => \Modules\EduMarketing\Models\MaketingMail::class,
            'query_type' => 'custom'
        ],
        'email' => [
            'label' => 'Email',
            'type' => 'text',
            'query_type' => '='
        ],
        'type' => [
            'label' => 'Đối tượng',
            'type' => 'select',
            'options' => [
                '' => '',
                'customer' => 'Khách hàng',
                'student' => 'Học sinh',
                'lecturer' => 'Giáo viên',
                'other' => 'Khác',
            ],
            'query_type' => '='
        ],
        'sent' => [
            'label' => 'Trạng thái',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => '',
                0 => 'Chưa gửi',
                1 => 'Đã gửi',
            ],
        ],
        'error' => [
            'label' => 'Lỗi',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => '',
                0 => 'Không lỗi',
                1 => 'Có lỗi',
            ],
        ],
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('edumarketing::marketingmaillog.list')->with($data);
    }

    public function appendWhere($query, $request)
    {
        if ($request->marketing_mail_id != null) {
            $query = $query->where('marketing_mail_id', $request->marketing_mail_id);
        }
        return $query;
    }

    public function add(Request $request)
    {

        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('edumarketing::marketingmaillog.add')->with($data);
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
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    //  Tùy chỉnh dữ liệu insert

                    if ($request->has('multi_cat')) {
                        $data['multi_cat'] = '|' . implode('|', $request->multi_cat) . '|';
                        $data['category_id'] = $request->multi_cat[0];
                    }
//                    if ($request->has('tags')) {
//                        $data['tags'] = '|' . implode('|', $request->tags) . '|';
//                    }
//                    if ($request->has('image_extra')) {
//                        $data['image_extra'] = implode('|', $request->image_extra);
//                    }
//                    if ($request->has('input_image_extra')) {
//                        $data['input_image_extra'] = implode('|', $request->input_image_extra);
//                    }
                    $data['marketing_mail_id'] = $request->marketing_mail_id;
                    $data['company_id'] = \Auth::guard('admin')->user()->last_company_id;
                    #
//dd($data);
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
                    $url = '';
                    if ($request->has('marketing_mail_id')) {
                        $url .= '?marketing_mail_id=' . $request->marketing_mail_id;
                    }
                    if ($request->return_direct == 'save_continue') {
                        return redirect('admin/' . $this->module['code'] . '/' . $this->model->id. $url);
                    } elseif ($request->return_direct == 'save_create') {
                        return redirect('admin/' . $this->module['code'] . '/add'. $url);
                    } elseif ($request->return_direct == 'save_editor') {
                        return redirect('admin/' . $this->module['code'] . '/' . $this->model->id . '/editor'. $url);
                    }

                    return redirect('admin/' . $this->module['code'].'/'.$this->model->id. $url);
                }
            }
        } catch (\Exception $ex) {
            CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function update(Request $request)
    {
        $item = $this->model->find($request->id);

        //  Chỉ sửa được liệu công ty mình đang vào
//            if (strpos(\Auth::guard('admin')->user()->company_ids, '|' . $item->company_id . '|') === false) {
//                CommonHelper::one_time_message('error', 'Bạn không có quyền!');
//                return back();
//            }

        if (!is_object($item)) abort(404);
        if (!$_POST) {
            $data = $this->getDataUpdate($request, $item);
            return view('edumarketing::marketingmaillog.edit')->with($data);
        } else if ($_POST) {
//            dd($request->all());
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
                if ($request->has('multi_cat')) {
                    $data['multi_cat'] = '|' . implode('|', $request->multi_cat) . '|';
                    $data['category_id'] = $request->multi_cat[0];
                }
//                if ($request->has('tags')) {
//                    $data['tags'] = '|' . implode('|', $request->tags) . '|';
//                }
//                if ($request->has('image_extra')) {
//                    $data['image_extra'] = implode('|', $request->image_extra);
//                }
//                if ($request->has('input_image_extra')) {
//                    $data['input_image_extra'] = implode('|', $request->input_image_extra);
//                }
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
                $url = '';
                if ($request->has('marketing_mail_id')) {
                    $url .= '?marketing_mail_id=' . $request->marketing_mail_id;
                }
                if ($request->return_direct == 'save_continue') {
                    return redirect('admin/' . $this->module['code'] . '/' . $item->id . $url);
                } elseif ($request->return_direct == 'save_create') {
                    return redirect('admin/' . $this->module['code'] . '/add' . $url);
                }

                return redirect('admin/' . $this->module['code'] . $url);
            }
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
            $url = '';
            if ($request->has('marketing_mail_id')) {
                $url .= '?marketing_mail_id=' . $request->marketing_mail_id;
            }
            return redirect('admin/' . $this->module['code'] . $url);
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

    public function duplicate(Request $request, $id)
    {
        $poduct = Lesson::find($id);
        $poduct_new = $poduct->replicate();
        $poduct_new->company_id = \Auth::guard('admin')->user()->last_company_id;
        $poduct_new->admin_id = \Auth::guard('admin')->user()->id;
        $poduct_new->save();
        return $poduct_new;
    }
}
