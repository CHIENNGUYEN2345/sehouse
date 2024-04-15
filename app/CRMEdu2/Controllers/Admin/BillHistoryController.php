<?php

namespace App\CRMEdu\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Auth;
use Illuminate\Http\Request;
use Validator;
use App\CRMEdu\Models\Bill;

class BillHistoryController extends CURDBaseController
{
    protected $orderByRaw = 'registration_date desc';

    protected $module = [
        'code' => 'bill_histories',
        'table_name' => 'bills',
        'label' => 'Lịch sử gia hạn',
        'modal' => '\App\CRMEdu\Models\Bill',
        'list' => [
            ['name' => 'registration_date', 'type' => 'date_vi', 'label' => 'Ngày'],
            ['name' => 'service_id', 'type' => 'relation', 'label' => 'Dịch vụ', 'object' => 'service', 'display_field' => 'name_vi', 'sort' => true],
            ['name' => 'total_price', 'type' => 'price_vi', 'label' => 'Số tiền'],
            ['name' => 'note', 'type' => 'text', 'label' => 'Ghi chú'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'registration_date', 'type' => 'date', 'class' => 'required', 'label' => 'Ngày gia hạn'],
                ['name' => 'total_price', 'type' => 'price_vi','label' => 'Số tiền khách trả'],
                ['name' => 'service_id', 'type' => 'select', 'options' =>
                    [
                        '' => 'Chọn dịch vụ',
                        7 => 'Duy trì website',
                        8 => 'Nâng cấp hosting',
                        9 => 'Nâng cấp web',
                        6 => 'Khác',                        
                    ], 'class' => '', 'label' => 'Dịch vụ', 'value' => 5],
                ['name' => 'note', 'type' => 'textarea', 'class' => '', 'label' => 'Ghi chú'],
            ],
        ],
    ];

    protected $quick_search = [
        'label' => 'ID, giá trị',
        'fields' => 'id, registration_date, total_price, note'
    ];

    protected $filter = [
        'bill_parent' => [
            'label' => '',
            'type' => 'text',
            'query_type' => '=',
            'class' => 'hidden',
        ],
    ];

    public function getIndex(Request $request)
    {

        $data = $this->getDataList($request);

        return view('CRMEdu.bill_history.list')->with($data);
    }

    public function add(Request $request)
    {
        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMEdu.bill_history.add')->with($data);
            } else if ($_POST) {
//                dd($request->all());
                $validator = Validator::make($request->all(), [
//                    'value' => 'required'
                ], [
//                    'value.required' => 'Bắt buộc phải nhập giá trị',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    //  Tùy chỉnh dữ liệu insert
                    $data['bill_parent'] = $request->bill_parent;

                    $bill_parent = Bill::find($data['bill_parent']);
                    $data['customer_id'] = $bill_parent->customer_id;
                    // $data['customer_tel'] = $bill_parent->customer_tel;
                    // $data['customer_name'] = $bill_parent->customer_name;
                    $data['status'] = 1;
                    $data['service_id'] = 7;    //  mặc định chọn dịch vụ gia hạn
                    $data['domain'] = $bill_parent->domain;
                    $data['saler_id'] = $bill_parent->saler_id;

                    #
                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
                        CommonHelper::flushCache();
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

                    if ($request->has('bill_parent')) {
                        return redirect('admin/' . $this->module['code'] . '?bill_parent=' . $request->bill_parent);
                    } else {
                        return redirect('admin/' . $this->module['code']);
                    }
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
            return view('CRMEdu.bill_history.edit')->with($data);
        } else if ($_POST) {
            $validator = Validator::make($request->all(), [
                'value' => 'required'
            ], [
                'value.required' => 'Bắt buộc phải nhập tên',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            } else {
                $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                //  Tùy chỉnh dữ liệu insert
//                $data['bill_parent'] = $request->bill_parent;
                #

                foreach ($data as $k => $v) {
                    $item->$k = $v;
                }
                if ($item->save()) {
                    CommonHelper::flushCache();
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

                if ($request->has('bill_parent')) {
                    return redirect('admin/' . $this->module['code'] . '?bill_parent=' . $request->bill_parent);
                } else {
                    return redirect('admin/' . $this->module['code']);
                }
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
            CommonHelper::flushCache();
            return response()->json([
                'status' => true,
                'published' => $item->{$request->column} == 1 ? true : false
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'published' => null,
                'msg' => 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên viên.'
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {
            $item = $this->model->find($request->id);
            $bill_parent = $item->bill_parent;

            //  Không được xóa dữ liệu của cty khác, cty mình ko tham gia
//            if (strpos(Auth::guard('admin')->user()->company_ids, '|' . $item->company_id . '|') === false) {
//                CommonHelper::one_time_message('error', 'Bạn không có quyền xóa!');
//                return back();
//            }

            $item->delete();
            CommonHelper::flushCache();
            CommonHelper::one_time_message('success', 'Xóa thành công!');

            return redirect('admin/' . $this->module['code'] . '?bill_parent=' . $bill_parent);
        } catch (\Exception $ex) {
            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên viên.');
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
            CommonHelper::flushCache();
            CommonHelper::one_time_message('success', 'Xóa thành công!');
            return response()->json([
                'status' => true,
                'msg' => ''
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'msg' => 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên viên'
            ]);
        }
    }

    public function duplicate(Request $request, $id)
    {
        $poduct = Product::find($id);
        $poduct_new = $poduct->replicate();
//        $poduct_new->company_id = \Auth::guard('admin')->user()->last_company_id;
        $poduct_new->admin_id = \Auth::guard('admin')->user()->id;
        $poduct_new->save();
        return $poduct_new;
    }

    public function searchForSelect2(Request $request)
    {

        $data = $this->model->join('properties_name', 'properties_name.id', '=', 'properties_value.bill_parent')
            ->select(['properties_value.' . $request->col, 'properties_value.id', 'properties_name.name as properties_name'])->where($request->col, 'like', '%' . $request->keyword . '%');
        if ($request->where != '') {
            $data = $data->whereRaw(urldecode(str_replace('&#039;', "'", $request->where)));
        }
//        if (@$request->company_id != null) {
//            $data = $data->where('company_id', $request->company_id);
//        }
        $data = $data->limit(5)->get();
        return response()->json([
            'status' => true,
            'items' => $data
        ]);
    }

    public function ajaxLichSuTrangThai(Request $request) {
        $where = $this->filterSimple($request);
        $listItem = $this->model->whereRaw($where);
        $listItem = $this->quickSearch($listItem, $request);
        if ($this->whereRaw) {
            $listItem = $listItem->whereRaw($this->whereRaw);
        }
        $listItem = $this->appendWhere($listItem, $request);

        if($request->has('where')) {
            $listItem = $listItem->whereRaw($request->where);
        }

        //  Sort
        $listItem = $this->sort($request, $listItem);

        if ($request->has('limit')) {
            $data['listItem'] = $listItem->paginate($request->limit);
        } else {
            $data['listItem'] = $listItem->paginate($this->limit_default);
        }

        //  Get data default (param_url, filter, module) for return view
        $data['module'] = $this->module;

        //  Set data for seo
        $data['page_title'] = $this->module['label'];

        return view("CRMEdu.dhbill.partials.lich_su_trang_thai_view")->with($data);
    }
}
