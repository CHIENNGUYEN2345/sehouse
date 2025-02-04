<?php

namespace App\CRMDV\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Illuminate\Http\Request;
use Modules\EworkingCompany\Models\Company;
use App\CRMDV\Models\Service;
use App\CRMDV\Models\ServiceHistory;
use Validator;

class ServiceController extends CURDBaseController
{

    protected $module = [
        'code' => 'service',
        'table_name' => 'services',
        'label' => 'Dự án',
        'modal' => '\App\CRMDV\Models\Service',
        'list' => [
            ['name' => 'name_vi', 'type' => 'text_edit', 'label' => 'Tên', 'sort' => true],
//            ['name' => 'expiry_date', 'type' => 'date_vi', 'label' => 'Hết hạn'],
            ['name' => 'intro', 'type' => 'text', 'label' => 'Mô tả', 'sort' => true],
//            ['name' => 'type', 'type' => 'select', 'options' => [
//                'bill_receipts' => 'Tài khoản tiền',
//                'lead_rate' => 'Đánh giá đầu mối',
//                'lead_source' => 'Nguồn khách',
//                'user_tick' => 'Đánh dấu khách hàng',
//            ], 'label' => 'Loại dự án',],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name_vi', 'type' => 'text', 'class' => 'required', 'label' => 'Tên'],
//                ['name' => 'code', 'type' => 'text', 'class' => 'required', 'label' => 'Mã'],
//                ['name' => 'expiry_date', 'type' => 'number', 'label' => 'Hết hạn'],
                ['name' => 'intro', 'type' => 'textarea', 'label' => 'Mô tả'],
//                ['name' => 'checkbox', 'type' => 'checkbox_multiple', 'label' => 'Chọn thứ', 'options' => [
//                    'Tuesday' => 3,
//                    'Friday' => 6
//                ]],
//                ['name' => 'type', 'type' => 'select', 'model' => \App\Models\Service::class,
//                    'options' => [
//                        'bill_receipts' => 'Tài khoản tiền',
//                        'lead_rate' => 'Đánh giá đầu mối',
//                        'lead_source' => 'Nguồn khách',
//                        'user_tick' => 'Đánh dấu khách hàng',
//                    ],'field' => 'type', 'class' => '', 'label' => 'loại dự án'],
            ],
        ]
    ];

    protected $filter = [
        /*'name_vi' => [
            'label' => 'Tên',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'price_vi' => [
            'label' => 'Giá',
            'type' => 'number',
            'query_type' => '='
        ],*/
    ];

    public function getIndex(Request $request)
    {

        $data = $this->getDataList($request);

        return view('CRMDV.service.list')->with($data);
    }

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMDV.service.add')->with($data);
            } else if ($_POST) {
                $validator = Validator::make($request->all(), [
                    'name_vi' => 'required',
                ], [
                    'name_vi.required' => 'Bắt buộc phải nhập tên',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert
                    if ($request->has('price_day')) {
                        $data['price'] = json_encode($this->getPriceInfo($request));
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

    public function getTimePrice($request)
    {
        $time_price = [];
        if ($request->has('time_price_use_date_max')) {
            foreach ($request->time_price_use_date_max as $k => $key) {
                if ($key != null) {
                    $time_price[] = [
                        'use_date_max' => $key,
                        'price' => $request->time_price_price[$k],
                    ];
                }
            }
        }
        return $time_price;
    }

    public function update(Request $request)
    {
        try {


            $item = $this->model->find($request->id);

            if (!is_object($item)) abort(404);
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('CRMDV.service.edit')->with($data);
            } else if ($_POST) {


                $validator = Validator::make($request->all(), [
                    'name_vi' => 'required',
                ], [
                    'name_vi.required' => 'Bắt buộc phải nhập tên gói',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert
                    if ($request->has('price_day')) {
                        $data['price'] = json_encode($this->getPriceInfo($request));
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

    public function getPriceInfo($request)
    {
        $price = [];
        if ($request->has('price_day')) {
            foreach ($request->price_day as $k => $key) {
                if ($key != null) {
                    $price[] = [
                        'day' => $key,
                        'price' => str_replace(',', '', str_replace('.', '', $request->price_price[$k])),
                    ];
                }
            }
        }
        return $price;
    }

    public function show()
    {
        $data['page_title'] = 'Các gói dịch vụ';
        $data['page_type'] = 'list';
        return view('CRMDV.service.show')->with($data);
    }

    public function get_info(Request $r) {
        $service = Service::find($r->service_id);
        $configPrice = json_decode($service->price);
        foreach ($configPrice as $v) {
            if ($v->day == 'start') {
                $priceStart = $v->price;
            }
            if ($v->day == '365') {
                $priceExpiry = $v->price;
            }
        }
        return response()->json([
            'total_price' => $priceStart,
            'total_price_format' => number_format($priceStart),
            'exp_price' => $priceExpiry,
            'exp_price_format' => number_format($priceExpiry)
        ]);
    }
}
