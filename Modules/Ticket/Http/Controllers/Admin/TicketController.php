<?php

namespace Modules\Ticket\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Mail\MailServer;
use App\Models\Admin;
use App\Models\AdminLog;
use App\Models\Setting;
use Auth;
use Illuminate\Http\Request;
use Modules\EduMarketing\Models\MarketingMail;
use Modules\Ticket\Models\Bill;
use Modules\Ticket\Models\CommentLog;
use Modules\Ticket\Models\Ticket;
use Validator;

class TicketController extends CURDBaseController
{
    protected $orderByRaw = 'bill_id desc, id asc';

    protected $module = [
        'code' => 'ticket',
        'table_name' => 'tickets',
        'label' => 'Ticket',
        'modal' => '\Modules\Ticket\Models\Ticket',
        'list' => [
            ['name' => 'title', 'type' => 'text_edit', 'label' => 'Chủ đề'],
            ['name' => 'admin_id', 'type' => 'custom', 'td' => 'ticket::list.td.relation', 'label' => 'Người tạo', 'object' => 'admin', 'display_field' => 'name'],
            ['name' => 'admin_id', 'type' => 'relation', 'label' => 'Dịch vụ', 'object' => 'bill', 'display_field' => 'domain'],
            ['name' => 'level', 'type' => 'select', 'label' => 'Mức độ ưu tiên', 'options' => [
                0 => 'Khẩn cấp',
                1 => 'Cao',
                2 => 'Trung bình',
                3 => 'Thấp',
            ],],
            ['name' => 'status', 'type' => 'custom', 'td' => 'ticket::list.td.status_ticket', 'label' => 'Trạng thái', 'options' => [
                0 => 'Chờ xử lý',
                1 => 'Đã trả lời',
                2 => 'Khách hàng phản hồi',
                3 => 'Đã đóng',
            ],],
            ['name' => 'price', 'type' => 'price_vi', 'label' => 'Giá phát sinh', ],
            ['name' => 'created_at', 'type' => 'date_vi', 'label' => 'Tạo lúc'],
            ['name' => 'updated_at', 'type' => 'date_vi', 'label' => 'Cập nhật'],
        ],
        'form' => [
            'general_tab' => [
//                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên người gửi'],
//                ['name' => 'email', 'type' => 'text', 'class' => '', 'label' => 'Email'],
                ['name' => 'title', 'type' => 'text', 'class' => '', 'label' => 'Chủ đề'],
                ['name' => 'content', 'type' => 'textarea', 'label' => 'Mô tả lỗi'],
                ['name' => 'image_extra', 'type' => 'multiple_image_dropzone', 'count' => '6', 'label' => 'Ảnh mô tả'],
                ['name' => 'bill_id', 'type' => 'custom', 'field' => 'ticket::form.fields.select_bill', 'label' => 'Dịch vụ liên quan', 'model' => Bill::class,
                    'object' => 'bill', 'display_field' => 'domain', 'display_field2' => 'service->name_vi', 'group_class' => 'col-md-6'],
                ['name' => 'level', 'type' => 'select', 'options' => [
                    0 => 'Khẩn cấp',
                    1 => 'Cao',
                    2 => 'Trung bình',
                    3 => 'Thấp',
                ], 'label' => 'Mức độ ưu tiên', 'group_class' => 'col-md-6'],
            ],
            'info_tab' => [
                ['name' => 'status', 'type' => 'select', 'options' => [
                    0 => 'Chờ xử lý',
                    1 => 'Đã trả lời',
                    2 => 'Khách hàng phản hồi',
                    3 => 'Đã đóng',
                ], 'label' => 'Trạng thái', 'group_class' => 'col-md-6'],
                ['name' => 'rate', 'type' => 'radio', 'options' => [
                    0 => 'Không',
                    1 => '★ - Kém',
                    2 => '★★ - Hơi kém',
                    3 => '★★★ - Trung bình',
                    4 => '★★★★ - Tốt',
                    5 => '★★★★★ - Rất tốt',
                ], 'label' => 'Đánh giá chất lượng'],
                ['name' => 'price', 'type' => 'price_vi', 'class' => '', 'label' => 'Báo giá phát sinh'],
            ],
            'for_admin' => [
                ['name' => 'send_report', 'type' => 'checkbox', 'class' => '', 'label' => 'Gửi thông báo cho khách'],
                ['name' => 'note_for_admin', 'type' => 'textarea', 'class' => '', 'label' => 'Ghi chú cho kỹ thuật', 'inner' => 'rows=10'],
            ],
        ],
    ];

    protected $filter = [
        'status' => [
            'label' => 'Trạng thái',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => 'Trạng thái',
                0 => 'Chờ xử lý',
                1 => 'Đã trả lời',
                2 => 'Khách hàng phản hồi',
                3 => 'Đã đóng',
            ],
        ],
        'price' => [
            'label' => 'Phí phát sinh',
            'type' => 'select',
            'query_type' => 'custom',
            'options' => [
                '' => '',
                1 => 'Có',
                0 => 'Không',
            ],
        ],
        'level' => [
            'label' => 'Mức độ ưu tiên',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => 'Mức độ ưu tiên',
                0 => 'Khẩn cấp',
                1 => 'Cao',
                2 => 'Trung bình',
                3 => 'Thấp',
            ],
        ],
        'bill_id' => [
            'label' => 'Dịch vụ liên quan',
            'type' => 'custom',
            'field' => 'ticket::list.filter.select_bill',
            'display_field' => 'domain',
            'object' => 'bill',
            'model' => Bill::class,
            'query_type' => '='
        ],
        'admin_id' => [
            'label' => 'Người gửi',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'object' => 'admin',
            'model' => Admin::class,
            'query_type' => '='
        ],
        'created_at' => [
            'label' => 'Ngày tạo',
            'type' => 'from_to_date',
            'query_type' => 'from_to_date'
        ],
    ];

    protected $quick_search = [
        'label' => 'ID, tiêu đều, content, đánh giá',
        'fields' => 'id, title, content, rate'
    ];

    public function getIndex(Request $request)
    {

        $data = $this->getDataList($request);

        return view('ticket::list')->with($data);
    }

    public function appendWhere($query, $request)
    {
        if (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['customer', 'customer_ldp_vip'])) {
            //  Là khách hàng

            //  Truy vấn ra các HĐ của mình
            $bill_ids = Bill::where(function ($query) {
                $query->orWhere('customer_id', \Auth::guard('admin')->user()->id);
                $query->orWhere('curator_ids', 'LIKE', '%|' . \Auth::guard('admin')->user()->id . '|%');
            })->pluck('id')->toArray();

            $query = $query->where(function ($query) use ($bill_ids) {
                $query->orWhereIn('bill_id', $bill_ids);
                $query->orWhere('admin_id', \Auth::guard('admin')->user()->id);
            });

            //  Đã đóng xuống cuối
            if ($request->sorts == null) {
                $orderRaw = 'CASE ';
                $orderRaw .= " WHEN status = 0 THEN 2";
                $orderRaw .= " WHEN status = 1 THEN 1";
                $orderRaw .= " WHEN status = 2 THEN 2";
                $orderRaw .= " WHEN status = 3 THEN 3";
                $orderRaw .= ' ELSE status END ASC';
                $query = $query->orderByRaw($orderRaw);
            }
        } else {
            //  Là nhân viên

            if (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['technicians'])) {
                //  Là kỹ thuật viên

                //  Truy vấn ra các HĐ mình phụ trách
                $bill_ids = Bill::where(function ($query) {
                    $query->orWhere('staff_care', 'LIKE', '%|' . \Auth::guard('admin')->user()->id . '|%');
                })->pluck('id')->toArray();

                $query = $query->where(function ($query) use ($bill_ids) {
                    $query->orWhereIn('bill_id', $bill_ids);
                    $query->orWhere('admin_id', \Auth::guard('admin')->user()->id);
                });
            }

            //  Khách trả lời > Đang mở > NV trả lời > đã đóng
            if ($request->sorts == null) {
                $orderRaw = 'CASE ';
                $orderRaw .= " WHEN status = 0 THEN 2";
                $orderRaw .= " WHEN status = 1 THEN 3";
                $orderRaw .= " WHEN status = 2 THEN 1";
                $orderRaw .= " WHEN status = 3 THEN 4";
                $orderRaw .= ' ELSE status END ASC';
                $query = $query->orderByRaw($orderRaw);
            }
        }

        if (!is_null($request->get('price'))) {
            if ($request->price == 1) {
                $query = $query->whereNotNull('price');
            } elseif($request->price == 0) {
                $query = $query->whereNull('price');
            }
        }

        return $query;
    }

    public function add(Request $request)
    {
        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('ticket::add')->with($data);
            } else if ($_POST) {
//                dd($request->all());
                $validator = Validator::make($request->all(), [
                    'title' => 'required'
                ], [
                    'title.required' => 'Bắt buộc phải nhập chủ đề',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    //  Tùy chỉnh dữ liệu insert
                    $data['admin_id'] = \Auth::guard('admin')->user()->id;
                    if ($request->has('image_extra')) {
                        $data['image_extra'] = implode('|', $request->image_extra);
                    }

                    if (isset($data['send_report'])) {
                        unset($data['send_report']);
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
                        return redirect('admin/' . $this->module['code'] . '/' . $this->model->id);
                    } elseif ($request->return_direct == 'save_create') {
                        return redirect('admin/' . $this->module['code'] . '/add');
                    } elseif ($request->return_direct == 'save_editor') {
                        return redirect('admin/' . $this->module['code'] . '/' . $this->model->id . '/editor');
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
//        try {

        $item = $this->model->find($request->id);

        //  Chỉ sửa được liệu công ty mình đang vào
//            if (strpos(\Auth::guard('admin')->user()->company_ids, '|' . $item->company_id . '|') === false) {
//                CommonHelper::one_time_message('error', 'Bạn không có quyền!');
//                return back();
//            }

        if (!is_object($item)) abort(404);
        if (!$_POST) {
            $data = $this->getDataUpdate($request, $item);
            return view('ticket::edit')->with($data);
        } else if ($_POST) {

            $validator = Validator::make($request->all(), [
//                    'name' => 'required'
            ], [
//                    'name.required' => 'Bắt buộc phải nhập tên',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            } else {
                $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                if ($request->has('image_extra')) {
                    $data['image_extra'] = implode('|', $request->image_extra);
                }

                if ($request->comment_content != null || $request->has('image_present')) {
                    $comment = new CommentLog();
                    $comment->content = $request->get('comment_content', '');
                    $comment->image_present = implode( '|', $request->get('image_present', []));
                    $comment->item_id = $item->id;
                    $comment->admin_id = \Auth::guard('admin')->user()->id;
                    $comment->save();

                    if (in_array(\App\Http\Helpers\CommonHelper::getRoleName($comment->admin_id, 'name'), ['customer',
                        'customer_ldp_vip'])) {
                        $data['status'] = 2;
                    } else {
                        $data['status'] = 1;
                    }
                }

                if (isset($data['send_report'])) {
                    unset($data['send_report']);
                }

                //  Tùy chỉnh dữ liệu insert
                foreach ($data as $k => $v) {
                    $item->$k = $v;
                }
                if ($item->save()) {

                    if ($request->has('customer_note') && is_object($item->bill)) {
                        $item->bill->customer_note = $request->customer_note;
                        $item->bill->save();
                    }

                    //  Nếu chọn gửi thông báo cho khách thì gửi thông báo
                    if ($request->has('send_report')) {
                        $this->sendMailReport($item->admin_id, 16);
                    }

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
//        } catch (\Exception $ex) {
//            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
////            CommonHelper::one_time_message('error', $ex->getMessage());
//            return redirect()->back()->withInput();
//        }
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

    public function deleteComment($ticket_id, $comment_id)
    {
        CommentLog::where('id', $comment_id)->delete();
        CommonHelper::one_time_message('success', 'Đã xóa bình luận!');
        return back();
    }

    public function report()
    {
        //  Tự động đóng các Ticket qua 3 ngày khách không phản hồi
        Ticket::where('status', 1)->where('updated_at', '<=', date('Y-m-d H:i:s', time() - 3 * 24 * 60 * 60))
            ->update([
                'status' => 3
            ]);

        // Lấy các ticket đang mở, chưa trả lời hoặc mới cập nhật ngày hôm nay => gọi ra danh sách id khách
        $customer_ids = Ticket::where(function ($query) {
//            $query->orWhereIn('status', [0, 2]);        //  đang mở, chưa trả lời
            $query->orWhere('updated_at', '>=', date('Y-m-d H:i:s', time() - 10 * 60 * 60));  //    mới cập nhật ngày hôm nay
        })->whereIn('status', [1, 3])->groupBy('admin_id')->pluck('admin_id')->toArray();
//        dd($customer_ids);
        $count = 0;
        foreach ($customer_ids as $customer_id) {
            $count++;
            $this->sendMailReport($customer_id);
        }
        CommonHelper::one_time_message('success', 'Đã gửi ' . $count . ' email báo cáo');
        return back();
    }

    public function sendMailReport($customer_id, $camp_id = 16, $ticket_ids = [])
    {
        $admin_emails = @Setting::where('name', 'admin_emails')->first()->value;
        //  Gửi mail cho khách
        $camp = MarketingMail::find($camp_id);
        $this->_mailSetting = Setting::whereIn('type', ['mail'])->pluck('value', 'name')->toArray();

        $tickets = Ticket::select('id', 'title', 'bill_id', 'updated_at', 'status')->where(function ($query) {
            $query->orWhereIn('status', [0, 2]);        //  lấy các ticket đang mở hoắc chưa trả lời
            $query->orWhere('updated_at', '>=', date('Y-m-d 00:00:00'));  //    lấy các ticket mới udpate ngày hôm nay
        })->where('admin_id', $customer_id)->orderBy('bill_id', 'desc')->orderBy('updated_at', 'asc');

        if (!empty($ticket_ids)) {
            $tickets = $tickets->whereIn('id', $ticket_ids);
        }

        $tickets = $tickets->get();

        $customer = Admin::select('name', 'email')->where('id', $customer_id)->first();

        $user = (object)[
            'email' => $customer->email,
            'name' => $customer->name,
            'id' => $customer->id
        ];
        $data = [
            'sender_account' => $camp->email_account,
            'user' => $user,
            'subject' => $camp->subject,
            'content' => $this->getContentEmailBanGiao($tickets, $camp),
            'cc' => explode(',', $admin_emails)
        ];
        \Mail::to($data['user'])->send(new MailServer($data));

        return true;
    }

    public function getContentEmailBanGiao($tickets, $camp)
    {
        return $this->processContentMail($camp->email_template->content, $tickets);
    }

    public function processContentMail($html, $tickets)
    {
        $ticket_done_html = '';
        $ticket_not_done_html = '';
        foreach ($tickets as $ticket) {
            if ($ticket->status == 1) {
                //  Ticket Đã hoàn thành
                $ticket_done_html .= '<tr>
                                            <td style="border: 1px #ccc dotted;padding: 5px;font-size: 14px;"><a href="//' . env('DOMAIN') . '/admin/ticket/' . $ticket->id . '" target="_blank">' . $ticket->title . '</a></td>
                                            <td style="border: 1px #ccc dotted;padding: 5px;font-size: 12px;">' . @$ticket->bill->domain . '</td>                                            
                                            <td style="border: 1px #ccc dotted;padding: 5px;font-size: 12px;">' . date('H:i', strtotime($ticket->updated_at)) . '</td>
                                        </tr>';
            } elseif($ticket->status == 3) {
                $text = strtotime($ticket->updated_at) > strtotime(date('Y-m-d 00:00:00')) ? 'Đã đóng' : 'Tự động đóng <br>do quá 3 ngày bạn không phản hồi';
                    //  Ticket tự động đóng
                $ticket_done_html .= '<tr>
                                            <td style="border: 1px #ccc dotted;padding: 5px;font-size: 14px;"><a href="//' . env('DOMAIN') . '/admin/ticket/' . $ticket->id . '" target="_blank">' . $ticket->title . '</a></td>
                                            <td style="border: 1px #ccc dotted;padding: 5px;font-size: 12px;">' . @$ticket->bill->domain . '</td>                                            
                                            <td style="border: 1px #ccc dotted;padding: 5px;font-size: 12px;">'.$text.'</td>
                                        </tr>';
            } else {
                //  Ticket chưa trả lời
                $ticket_not_done_html .= '<tr>
                                            <td style="border: 1px #ccc dotted;padding: 5px;font-size: 14px;"><a href="//' . env('DOMAIN') . '/admin/ticket/' . $ticket->id . '" target="_blank">' . $ticket->title . '</a></td>
                                            <td style="border: 1px #ccc dotted;padding: 5px;font-size: 12px;">' . @$ticket->bill->domain . '</td>
                                            <td style="border: 1px #ccc dotted;padding: 5px;font-size: 12px;">' . ($ticket->status == 0 ? 'Đang mở' : 'Chưa trả lời') . '</td>
                                        </tr>';
            }
        }
        $html = str_replace('{tasks_done}', $ticket_done_html, $html);
        $html = str_replace('{tasks_not_done}', $ticket_not_done_html, $html);
        $html = str_replace('{date_update_next}', date('H:i') . ' ngày mai', $html);

        return $html;
    }

}
