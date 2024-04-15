<?php

namespace Modules\EduMarketing\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Mail\MailServer;
use App\Models\EmailTemplate;
use App\Models\Setting;
use Auth;
use Illuminate\Http\Request;
use Modules\EduMarketing\Models\Classs;
use Modules\EduMarketing\Console\CampaignEmail;
use Modules\EduMarketing\Models\Admin;
use Modules\EduMarketing\Models\Category;
use Modules\EduMarketing\Models\Customer;
use Modules\EduMarketing\Models\Lesson;
use Modules\EduMarketing\Models\MarketingMailLog;
use Modules\EduMarketing\Models\Tag;
use Modules\EduSettings\Entities\Register;
use Modules\ThemeEdu\Models\Student;
use Validator;

class MarketingMailController extends CURDBaseController
{
    protected $orderByRaw = 'status DESC, updated_at DESC';

    protected $module = [
        'code' => 'marketing-mail',
        'table_name' => 'marketing_mail',
        'label' => 'Chiến dịch email marketing',
        'modal' => '\Modules\EduMarketing\Models\MarketingMail',
        'list' => [
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Tên chiến dịch'],
            ['name' => 'customer_tags', 'type' => 'custom','td' => 'edumarketing::list.td.multi_tag', 'label' => 'Tags được target', 'object' => 'tag', 'display_field' => 'name'],
            ['name' => 'date_send', 'type' => 'custom','td' => 'edumarketing::list.td.date_send', 'label' => 'Gửi trong khoảng'],
//            ['name' => 'send_method', 'type' => 'select', 'label' => 'Phương thức gửi', 'options' => [
//                '1' => 'Mailgun',
//                '2' => 'Smpt',
//            ],],
//            ['name' => 'student_ids', 'type' => 'custom','td' => 'edumarketing::list.td.multi_cat', 'label' => 'Học viên', 'object' => 'student', 'display_field' => 'name'],
//            ['name' => 'class_id', 'type' => 'custom','td' => 'edumarketing::list.td.multi_class', 'label' => 'Lớp học', 'object' => 'classs', 'display_field' => 'name'],
//            ['name' => 'action', 'type' => 'custom', 'td' => 'edumarketing::list.td.action', 'class' => '', 'label' => '#'],
            ['name' => 'status', 'type' => 'custom', 'td' => 'edumarketing::list.td.status_camp', 'label' => 'Trạng thái'],
            ['name' => 'sent', 'type' => 'custom','td' => 'edumarketing::list.td.count_sent', 'label' => 'Đã mở/Đã gửi/Tổng'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text','class' => 'required', 'label' => 'Tên chiến dịch'],
                ['name' => 'object', 'type' => 'custom', 'field' => 'edumarketing::form.fields.checkbox_multi', 'class' => '', 'label' => 'Đối tượng gửi mail', 'value' => 1, 'group_class' => ''],
                ['name' => 'customer_tags', 'type' => 'select2_ajax_model', 'multiple' => true,  'label' => 'Tagert đặc tính đối tượng Khách hàng', 'object' => 'tag', 'model' => Tag::class, 'display_field' => 'name',
                    'des' => 'Target sâu hơn vào đối tượng gửi mail'],
                ['name' => 'emails_extra', 'type' => 'textarea', 'field' => 'edumarketing::form.fields.emails_extra', 'label' => 'Thêm danh sách email khác',
                    'des' => 'Nhập vào danh sách email & cách nhau bởi dấu phẩy. VD: a1@gmail.com, a2@gmail.com, a3@gmail.com. Hoặc mỗi email trên 1 dòng', 'inner' => 'rows=10'],
//                ['name' => 'send_method', 'type' => 'select','class' => 'required', 'label' => 'Phương thức gửi', 'options' => [
//                    '1' => 'Mailgun',
//                    '2' => 'Smpt',
//                ]],
//                ['name' => 'student_ids', 'type' => 'select2_model', 'multiple' => true,  'label' => 'Học viên', 'model' => Student::class, 'display_field' => 'name'],
//                ['name' => 'class_id', 'type' => 'select2_model','multiple' => true, 'label' => 'Lớp học', 'model' => Classs::class, 'display_field' => 'name'],
            ],
            'time_tab' => [
                ['name' => 'date_send', 'type' => 'datetimepicker', 'class' => 'required', 'label' => 'Ngày bắt đầu gửi','group_class' => 'col-md-6'],
                ['name' => 'finish_send', 'type' => 'datetimepicker','value'=> null, 'label' => 'Ngày cuối cùng được phép gửi','group_class' => 'col-md-6'],
                ['name' => 'status', 'type' => 'checkbox', 'class' => '', 'label' => 'Kích hoạt gửi', 'value' => 1,],
            ],
            'info_tab' => [
                ['name' => 'email_template_id', 'type' => 'select2_ajax_model', 'label' => 'Template gửi mail', 'object' => 'email_template', 'model' => \Modules\EduMarketing\Models\EmailTemplate::class, 'display_field' => 'name',
                    'des' => 'Chọn mẫu email để gửi. <a href="/admin/email_template" target="_blank">Xem tất cả các mẫu</a>'],
                ['name' => 'email_account_id', 'type' => 'select2_model', 'label' => 'Tài khoản gửi', 'object' => 'email_account', 'model' => \Modules\EduMarketing\Models\EmailAccount::class, 'display_field' => 'username',
                    'des' => 'Chọn tài khoản email để gửi. <a href="/admin/email_account" target="_blank">Xem tất cả các tài khoản</a>'],
                ['name' => 'subject', 'type' => 'text', 'class' => 'required', 'label' => 'Tiêu đề'],
//                ['name' => 'content', 'type' => 'textarea_editor', 'class' => 'required', 'label' => 'Nội dung', 'height' => '700px'],
            ],

        ],
    ];

    protected $filter = [
        'name' => [
            'label' => 'Tên chiến dịch',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'customer_tags' => [
            'label' => 'customer_tags',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'object' => 'customer_tags',
            'model' => Tag::class,
            'query_type' => 'like'
        ],
//        'student_ids' => [
//            'label' => 'Học viên',
//            'type' => 'select2_ajax_model',
//            'display_field' => 'name',
//            'object' => 'student',
//            'model' => Student::class,
//            'query_type' => 'custom'
//        ],
//        'class_id' => [
//            'label' => 'Lớp học',
//            'type' => 'select2_ajax_model',
//            'display_field' => 'name',
//            'object' => 'class',
//            'model' => Classs::class,
//            'query_type' => 'custom'
//        ],
        'status' => [
            'label' => 'Trạng thái',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => 'Trạng thái',
                0 => 'Không kích hoạt',
                1 => 'Kích hoạt',
            ],
        ],
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('edumarketing::list')->with($data);
    }

    public function appendWhere($query, $request)
    {
        if (!is_null($request->get('customer_tags'))) {
            $query = $query->where('multi_cat', 'LIKE', '%|' . $request->customer_tags . '|%');
        }
        return $query;
    }

    public function add(Request $request)
    {
        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('edumarketing::add')->with($data);
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
                        $this->createListEmailQueue($request, $this->model);

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

        //  Chỉ sửa được liệu công ty mình đang vào
//            if (strpos(\Auth::guard('admin')->user()->company_ids, '|' . $item->company_id . '|') === false) {
//                CommonHelper::one_time_message('error', 'Bạn không có quyền!');
//                return back();
//            }

        if (!is_object($item)) abort(404);

        if (!$_POST) {
            $data = $this->getDataUpdate($request, $item);
            return view('edumarketing::edit')->with($data);
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
                if ($request->has('customer_tags')) {
                    $data['customer_tags'] = '|' . implode('|', $request->customer_tags) . '|';
                }
                if ($request->has('object')) {
                    $data['object'] = '|' . implode('|', $request->object) . '|';
                }

                if ($request->has('multi_cat')) {
                    $data['multi_cat'] = '|' . implode('|', $request->multi_cat) . '|';
                    $data['category_id'] = $request->multi_cat[0];
                }

                if ($request->has('student_ids')) {
                        $data['student_ids'] = '|' . implode('|', $request->student_ids) . '|';
                    }
                if ($request->has('class_id')) {
                    $data['class_id'] = '|' . implode('|', $request->class_id) . '|';
                }

                #

                foreach ($data as $k => $v) {
                    $item->$k = $v;
                }
                if ($item->save()) {
                    $this->createListEmailQueue($request, $item);
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
                } elseif ($request->return_direct == 'save_preview') {
                    return redirect('admin/' . $this->module['code'] . '/' . $item->id . '/preview');
                } elseif ($request->return_direct == 'save_send_now') {
                    $campaignEmail = new CampaignEmail();
                    $result = $campaignEmail->handle($item->id, true);
                    CommonHelper::one_time_message('success', $result['msg']);
                    return back();
                }

                return redirect('admin/' . $this->module['code']);
            }
        }
    }

    public function createListEmailQueue($r, $campaign) {
        try {
            if ($campaign->name == 'Chúc mừng sinh nhật tự động') {
                return true;
            }

            //  Truy vấn các đối tượng cần gửi mail rồi lưu vào bảng lịch sử gửi
            if ($r->has('object')) {
                foreach ($r->object as $obj) {
                    if ($obj == 'customer') {
                        $objs = \Modules\EduMarketing\Models\Customer::where(function ($query) use ($r) {
                            if ($r->customer_tags != null) {
                                foreach ($r->customer_tags as $tag) {
                                    $query->orWhere('tags', 'LIKE', '%|' . $tag . '|%');
                                }
                            }
                        })->pluck('email', 'id');
                        foreach ($objs as $id => $mail) {
                            MarketingMailLog::updateOrCreate([
                                'marketing_mail_id' => $campaign->id,
                                'object_id' => $id,
                                'type' => 'customer',
                                'email' => $mail
                            ]);
                        }
                    }

                    if ($obj == 'student') {
                        $objs = \Modules\EduMarketing\Models\Student::where(function ($query) use ($r) {
                            if ($r->student_tags != null) {
                                foreach ($r->student_tags as $tag) {
                                    $query->orWhere('tags', 'LIKE', '%|' . $tag . '|%');
                                }
                            }
                        })->pluck('email', 'id');
                        foreach ($objs as $id => $mail) {
                            MarketingMailLog::updateOrCreate([
                                'marketing_mail_id' => $campaign->id,
                                'object_id' => $id,
                                'type' => 'student',
                                'email' => $mail
                            ]);
                        }
                    }


                    if ($obj == 'lecturer') {
                        $objs = \App\Models\Admin::where(function ($query) use ($r) {
                            if ($r->lecturer_tags != null) {
                                foreach ($r->lecturer_tags as $tag) {
                                    $query->orWhere('tags', 'LIKE', '%|' . $tag . '|%');
                                }
                            }
                        })->pluck('email', 'id');
                        foreach ($objs as $id => $mail) {
                            MarketingMailLog::updateOrCreate([
                                'marketing_mail_id' => $campaign->id,
                                'object_id' => $id,
                                'type' => 'customer',
                                'email' => $mail
                            ]);
                        }
                    }
                }
            }

            //  Chèn các mail fix cứng thêm
            foreach (explode(',', $r->emails_extra) as $str_mail) {
                foreach (preg_split('/\r\n|[\r\n]/', $str_mail) as $mail) {
                    MarketingMailLog::updateOrCreate([
                        'marketing_mail_id' => $campaign->id,
                        'type' => 'other',
                        'email' => trim($mail)
                    ]);
                }
            }
            return true;
        } catch (\Exception $ex) {
            return false;
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

            //  Xóa các lịch gửi mail chưa được gửi
            MarketingMailLog::where('marketing_mail_id', $item->id)->where('sent', '!=', 1)->where('error', '!=', 1)->delete();

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
                MarketingMailLog::whereIn('marketing_mail_id', $ids)->where('sent', '!=', 1)->where('error', '!=', 1)->delete();
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

    public function preview($id) {
        $data['settings'] = Setting::whereIn('type', ['mail', 'general_tab'])->pluck('value', 'name')->toArray();
        $data['campaign'] = $this->model->find($id);

        $mailServer = new MailServer([]);
        $data['settings'] = $mailServer->processContentMail($data['settings']);
        return view('edumarketing::emails.content_from_setting')->with($data);
    }

    public function eventOpenMail(Request $r) {
//        dd($r->camp_id);
        MarketingMailLog::updateOrCreate([
            'marketing_mail_id' => $r->camp_id,
            'object_id' => $r->user_id,
            'type' => $r->type,
        ],[
            'sent' => 1,
            'opened' => 1,
            'email' => $r->email
        ]);
        die('Đã xác nhận tài khoản id:' . $r->user_id . ' đã mở mail');
    }
}
