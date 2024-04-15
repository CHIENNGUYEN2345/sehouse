<?php

namespace App\CRMWoo\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Mail\MailServer;
use App\Models\Admin;
use App\Models\Setting;
use Auth;
use Illuminate\Http\Request;
use App\CRMWoo\Controllers\Helpers\Helper;
use App\CRMWoo\Models\Bill;
use App\CRMWoo\Models\BillHistory;
use App\CRMWoo\Models\Service;
use Validator;

class GHBillController extends CURDBaseController
{

    protected $orderByRaw = 'status DESC, auto_extend DESC, expiry_date ASC, id DESC';

    protected $module = [
        'code' => 'gh-bill',
        'table_name' => 'bills',
        'label' => 'Gia hạn Hợp đồng',
        'modal' => '\App\CRMWoo\Models\Bill',
        'list' => [
            ['name' => 'domain', 'type' => 'text_edit', 'label' => 'Tên miền'],
            ['name' => 'customer_id', 'type' => 'relation', 'label' => 'Khách hàng', 'object' => 'admin', 'display_field' => 'name'],
            ['name' => 'service_id', 'type' => 'relation', 'label' => 'Sản phẩm', 'object' => 'service', 'display_field' => 'name_vi'],
//            ['name' => 'count_product', 'type' => 'custom', 'td' => 'CRMWoo.cskh_bill.list.td.count_product', 'label' => 'Tổng SP'],
            ['name' => 'exp_price', 'type' => 'price_vi', 'label' => 'Giá gia hạn'],
            ['name' => 'expiry_date', 'type' => 'date_vi', 'label' => 'Hết hạn'],
            ['name' => 'auto_extend', 'type' => 'custom', 'td' => 'CRMWoo.list.td.status', 'label' => 'Tự động gia hạn', 'options' => [
                    0 => 'Không kích hoạt',
                    1 => 'Kích hoạt',
                ], 'sort' => true
            ],
            ['name' => 'status', 'type' => 'custom', 'td' => 'CRMWoo.list.td.status', 'label' => 'Trạng thái', 'options' => [
                    0 => 'Không kích hoạt',
                    1 => 'Kích hoạt',
                ], 'sort' => true
            ],
            ['name' => 'contacted_log_last', 'type' => 'date_vi', 'label' => 'Chăm sóc lần cuối'],
            ['name' => 'saler_id', 'type' => 'relation', 'label' => 'Kinh doanh', 'object' => 'saler', 'display_field' => 'name'],
            ['name' => 'action', 'type' => 'custom', 'td' => 'CRMWoo.cskh_bill.list.td.action', 'label' => 'Hành động'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'total_price', 'type' => 'price_vi', 'class' => 'required', 'label' => 'Doanh số', 'group_class' => 'col-md-6'],
                ['name' => 'domain', 'type' => 'text', 'label' => 'Tên miền', 'group_class' => 'col-md-6'],
                ['name' => 'service_id', 'type' => 'select2_model', 'class' => ' required',  'label' => 'Gói DV', 'model' => Service::class, 'display_field' => 'name_vi', 'group_class' => 'col-md-3'],
                ['name' => 'registration_date', 'type' => 'date', 'label' => 'Ngày ký HĐ', 'class' => 'required', 'group_class' => 'col-md-3'],
                ['name' => 'contract_time', 'type' => 'number', 'label' => 'Thời gian sử dụng (tháng)', 'class' => 'required', 'group_class' => 'col-md-3'],
            ],
            'customer_tab' => [
                ['name' => 'marketer_ids', 'type' => 'select2_ajax_model', 'label' => 'Marketing', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'code', 'multiple' => true, 'group_class' => 'col-md-6'],
                ['name' => 'saler_id', 'type' => 'select2_ajax_model', 'label' => 'Sale', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'code', 'class' => 'required'],
                ['name' => 'customer_id', 'type' => 'custom', 'type_history' => 'relation_multiple', 'field' => 'CRMWoo.form.fields.select_customer',
                    'label' => 'Khách hàng', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'tel', 'class' => 'required'],
                ['name' => 'customer_legal_id', 'type' => 'custom', 'type_history' => 'relation_multiple', 'field' => 'CRMWoo.form.fields.select_customer',
                    'label' => 'Đại diện pháp lý', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'tel', 'class' => ''],
            ],
            'gia_han_tab' => [
                ['name' => 'expiry_date', 'type' => 'custom', 'field' => 'CRMWoo.form.fields.expiry_date', 'label' => 'Ngày hết hạn', 'class' => 'required', 'group_class' => 'col-md-6'],
                ['name' => 'exp_price', 'type' => 'price_vi', 'class' => ' required', 'label' => 'Giá gia hạn', 'group_class' => 'col-md-6'],
                ['name' => 'auto_extend', 'type' => 'checkbox', 'label' => 'Kích hoạt tự động gia hạn', 'value' => 1, 'group_class' => 'col-md-6'],
            ],
            'domain_tab' => [
            ],
            'hosting_tab' => [
            ],
            'ldp_tab' => [
            ],
            'wp_tab' => [
            ],
            'service_tab' => [
                ['name' => 'status', 'type' => 'checkbox', 'label' => 'Kích hoạt', 'value' => 1, ],
                ['name' => 'note', 'type' => 'textarea', 'label' => 'Ghi chú'],
                ['name' => 'customer_note', 'type' => 'textarea', 'label' => 'Ghi chú của khách'],
            ],
            'account_tab' => [
            ],
//            'histories_bill_tab' => [
//            ],
        ],
    ];

    protected $filter = [
        'customer_id' => [
            'label' => 'Tên khách hàng',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'model' => \App\Models\Admin::class,
            'object' => 'admin',
            'query_type' => '='
        ],
        'marketer_ids' => [
            'label' => 'Nguồn marketing',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'display_field2' => 'code',
            'model' => \App\Models\Admin::class,
            'object' => 'admin',
            'query_type' => 'custom'
        ],
        'service_id' => [
            'label' => 'Sản phẩm',
            'type' => 'select2_model',
            'display_field' => 'name_vi',
            'model' => Service::class,
            'query_type' => '='
        ],
        /*'total_price' => [
            'label' => 'Tổng tiền',
            'type' => 'number',
            'query_type' => 'like'
        ],*/
        'expiry_date' => [
            'label' => 'Hết hạn',
            'type' => 'date',
            'query_type' => '='
        ],
        'auto_extend' => [
            'label' => 'Tự động gia hạn',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => '',
                0 => 'Không kích hoạt',
                1 => 'Kích hoạt',
            ],
        ],
        'status' => [
            'label' => 'Trạng thái',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => 'Tất cả',
                0 => 'Không kích hoạt',
                1 => 'Kích hoạt',
            ],
        ],
        'registration_date' => [
            'label' => 'Ngày ký HĐ',
            'type' => 'from_to_date',
            'query_type' => 'from_to_date'
        ],
    ];

    protected $quick_search = [
        'label' => 'ID, tên miền, giá, note',
        'fields' => 'id, domain, exp_price, total_price, note'
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('CRMWoo.gh_bill.list')->with($data);
    }

    public function appendWhere($query, $request)
    {

        //  chỉ lấy các hợp đồng chính
        $query = $query->whereNull('bill_parent');
        
        if (@$request->marketer_ids != null) {
            $query = $query->where('marketer_ids', 'like', '%|' . $request->marketer_ids . '|%');
        }

        if (\Auth::guard('admin')->user()->super_admin != 1) {
            //  Nếu ko phải super_admin thì truy vấn theo dữ liệu cty đó
            // $query = $query->where('company_id', \Auth::guard('admin')->user()->last_company_id);
        }


        //	Truy vấn cứu các hợp đồng sắp hết hạn
     //    if (\Auth::guard('admin')->user()->super_admin != 1) {
	    //     //  Nếu ko phải super_admin thì truy vấn theo dữ liệu cty đó
	    //     $whereCompany = 'company_id = ' . \Auth::guard('admin')->user()->last_company_id;
	    // } else {
	    //     $whereCompany = '1 = 1';
	    // }

        $settings = Setting::whereIn('type', ['web_service'])->pluck('value', 'name')->toArray();
		$next_month = strftime('%m', strtotime(strtotime(date('m')) . " +1 month"));
		$last_month = strftime('%m', strtotime(strtotime(date('m')) . " -1 month"));

		// ====|Min-quá hạn|======|Now - hiện tại|=====|Closed - sắp hết hạn|======|Max - tối đa sắp hết hạn|======>
		$query = $query
            // ->whereRaw($whereCompany)
		    ->where('expiry_date', '<>', Null)->where('expiry_date', '>=', date('Y-m-d', strtotime('-' . $settings['min_day'] . ' day')))
		    ->where('expiry_date', '<=', date('Y-m-d', strtotime('+' . $settings['close_day'] . ' day')));


        return $query;
    }

    public function update(Request $request)
    {
        try {
            $item = $this->model->find($request->id);

            //  Khách hàng ko được xem bản ghi của người khác
            if (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['customer', 'customer_ldp_vip'])
                && $item->customer_id != \Auth::guard('admin')->user()->id) {
                CommonHelper::one_time_message('error', 'Bạn không có quyền!');
                return back();
            }

            if (!is_object($item)) abort(404);

            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);

                if ($item->service_id == 1) {
                    $camp = MarketingMail::find(1);
                    $data['email_content_ban_giao'] = $this->processContentMail(@$camp->email_template->content, $item);
                } elseif($item->service_id == 5) {
                    $camp = MarketingMail::find(5);
                    $data['email_content_ban_giao'] = $this->processContentMail(@$camp->email_template->content, $item);
                }

                return view('CRMWoo.gh_bill.edit')->with($data);
            }

            $data = $this->processingValueInFields($request, $this->getAllFormFiled());

            //  Tùy chỉnh dữ liệu insert
            //  Khách hàng tự sửa hóa đơn của mình
            if (Helper::getRoleType(\Auth::guard('admin')->user()->id) == 'customer') {
                unset($data['customer_id']);
            }

            // //  nếu thuê tên miền bên mình thì tạo 1 hóa đơn cho tên miền đó
            // if ($data['domain_owner'] == 'hobasoft') {
            //     $this->createBillForDomain($data, $item);
            // }

            // //  nếu thuê hosting bên mình thì tạo 1 hóa đơn cho hosting đó
            // if ($data['hosting_owner'] == 'hobasoft') {
            //     $this->createBillForHosting($data, $item);
            // }

            //  Lấy người phụ trách
            $data['curator_ids'] = '|' . implode('|', $request->get('curator_ids', [])) . '|';
            $data['staff_care'] = '|' . implode('|', $request->get('staff_care', [])) . '|';
            $data['marketer_ids'] = '|' . implode('|', $request->get('marketer_ids', [])) . '|';

            unset($data['file_ldp']);

            foreach ($data as $k => $v) {
                $item->$k = $v;
            }
            if ($item->save()) {


                /*$expiry_date_db = Bill::find($request->id)->expiry_date;
                $price_db = Bill::find($request->id)->exp_price;
                if ($expiry_date_db != $request->expiry_date) {
                    $billHistory = new BillHistory();
                    $billHistory->bill_id = $request->id;
                    $billHistory->price = $price_db;
                    $billHistory->expiry_date = $expiry_date_db;

                    $billHistory->save();
                }*/

                if ($request->return_direct == 'mail_ban_giao_ldp') {
                    //  Gửi mail bàn giao LDP
                    $this->sendMailBanGiao($item, 1);

                    $item->handover_landingpage ++;

                    $item->save();

                    CommonHelper::one_time_message('success', 'Bàn giao thành công!');
                    return redirect('admin/' . $this->module['code'] . '/edit/' . $item->id);
                }

                if ($request->return_direct == 'mail_ban_giao_wp') {
                    //  Gửi mail bàn giao LDP
                    $this->sendMailBanGiao($item, 5);

                    $item->handover_wp ++;

                    $item->save();

                    CommonHelper::one_time_message('success', 'Bàn giao thành công!');
                    return redirect('admin/' . $this->module['code'] . '/edit/' . $item->id);
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
                return redirect('admin/' . $this->module['code'] . '/edit/' . $item->id);
            } elseif ($request->return_direct == 'save_create') {
                return redirect('admin/' . $this->module['code'] . '/add');
            }

            return redirect('admin/' . $this->module['code']);

        } catch (\Exception $ex) {
            dd($ex->getMessage());
//            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
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

    public function searchForSelect2(Request $request)
    {
        $data = $this->model->select([$request->col, 'id'])->where($request->col, 'like', '%' . $request->keyword . '%');
        if ($request->where != '') {
            $data = $data->whereRaw(urldecode(str_replace('&#039;', "'", $request->where)));
        }
        if (@$request->company_id != null) {
            $data = $data->where('company_id', $request->company_id);
        }

        //  Khách hàng ko được xem hóa đơn người khác
        if (Helper::getRoleType(\Auth::guard('admin')->user()->id) == 'customer') {
            $data = $data->where('customer_id', \Auth::guard('admin')->user()->id);
        }

        $data = $data->limit(5)->get();
        return response()->json([
            'status' => true,
            'items' => $data
        ]);
    }

    public function processContentMail($html, $bill)
    {
        $html = str_replace('{service_name}', $bill->service_name == '' ? $bill->service->name_vi : $bill->service_name, $html);
        $html = str_replace('{domain}', '<a target="_blank" href="'.$bill->domain.'">'.$bill->domain.'</a>', $html);
        $html = str_replace('{guarantee}', $bill->guarantee, $html);
        $html = str_replace('{web_link}', '<a target="_blank" href="'.$bill->web_link.'">'.$bill->web_link.'</a>', $html);
        $html = str_replace('{web_username}', $bill->web_username, $html);
        $html = str_replace('{web_password}', $bill->web_password, $html);
        $html = str_replace('{hosting_link}', '<a target="_blank" href="'.$bill->hosting_link.'">'.$bill->hosting_link.'</a>', $html);
        $html = str_replace('{hosting_username}', $bill->hosting_username, $html);
        $html = str_replace('{hosting_password}', $bill->hosting_password, $html);
        $html = str_replace('{file_ldp}', '<a target="_blank" href="'.url('admin/landingpage/down-load-file/' . $bill->id . '/' . @$bill->ldp->id).'">Click để tải File thiết kế</a>', $html);
        $html = str_replace('{customer_link}', '<a target="_blank" href="'.@$bill->ldp->customer_link.'">Danh sách khách hàng</a>', $html);

        return $html;
    }
}
