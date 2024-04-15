<?php

namespace App\CRMEdu\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Auth;
use Illuminate\Http\Request;
use App\CRMEdu\Controllers\Admin\CURDBaseController;
use Validator;

class ReceiptPaymentController extends CURDBaseController
{
    protected $orderByRaw = 'status ASC, date desc';

    protected $module = [
        'code' => 'receipt_payment',
        'table_name' => 'bill_receipts',
        'label' => 'Thu - chi',
        'modal' => '\App\CRMEdu\Models\BillReceipts',
        'list' => [
            ['name' => 'date', 'type' => 'date_vi', 'label' => 'Ngày'],
            ['name' => 'price', 'type' => 'price_vi', 'label' => 'Số tiền'],
            ['name' => 'status', 'type' => 'status', 'label' => 'Duyệt phiếu'],
            ['name' => 'image', 'type' => 'image', 'label' => 'Ảnh CK'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'date', 'type' => 'date', 'class' => 'required', 'label' => 'Ngày', 'value' => ''],
                ['name' => 'receiving_account', 'type' => 'select', 'options' => [
                    'BIDV 21510001138644' => 'BIDV 21510001138644',
                    'ACB 4833097' => 'ACB 4833097',
                    'ACB 2288668668' => 'ACB 2288668668',
                ], 'class' => 'required', 'label' => 'Tài khoản'],
                ['name' => 'price', 'type' => 'price_vi', 'class' => 'required','label' => 'Số tiền giao dịch'],
                ['name' => 'note', 'type' => 'textarea', 'class' => 'required', 'label' => 'Lý do'],
                ['name' => 'image', 'type' => 'file_image', 'class' => '', 'label' => 'Ảnh bằng chứng'],
                ['name' => 'employees', 'type' => 'text', 'class' => '','label' => 'Tên người thực hiện'],
            ],
        ],
    ];

    protected $quick_search = [
        'label' => 'ID, giá trị',
        'fields' => 'id, date, price, note'
    ];

    protected $filter = [

    ];

    public function getIndex(Request $request)
    {
//        $price = BillReceipts::where('bill_id', 1)
//            ->where('status', 1)
//            ->sum('price');
//        Bill::where('id', 1)->update(['total_received' => $price]);
//        dd($price);

        $data = $this->getDataList($request);

        return view('CRMEdu.receipt_payment.list')->with($data);
    }

    public function getData($request) {
        //  Filter
        $where = $this->filterSimple($request);
        $listItem = $this->model->whereRaw($where);
        $listItem = $this->quickSearch($listItem, $request);
        if ($this->whereRaw) {
            $listItem = $listItem->whereRaw($this->whereRaw);
        }
        $listItem = $this->appendWhere($listItem, $request);

        //  Export
        if ($request->has('export')) {
            $this->exportExcel($request, $listItem->get());
        }

        //  Sort
        $listItem = $this->sort($request, $listItem);
        return $listItem;
    }

    public function getDataList(Request $request) {
        $listItem = $this->getData($request);

        $data['record_total'] = $listItem->count();

        $data['tong_thu'] = $this->getData($request)->where('price', '>', 0)->sum('price');
        $data['tong_chi'] = $this->getData($request)->where('price', '<', 0)->sum('price');

        if ($request->has('limit')) {
            $data['listItem'] = $listItem->paginate($request->limit);
            $data['limit'] = $request->limit;
        } else {
            $data['listItem'] = $listItem->paginate($this->limit_default);
            $data['limit'] = $this->limit_default;
        }
        $data['page'] = $request->get('page', 1);

        $data['param_url'] = $request->all();

        //  Get data default (param_url, filter, module) for return view
        $data['module'] = $this->module;
        $data['quick_search'] = $this->quick_search;
        $data['filter'] = $this->filter;

        //  Set data for seo
        $data['page_title'] = $this->module['label'];
        $data['page_type'] = 'list';
        return $data;
    }

    public function appendWhere($query, $request)
    {

        return $query;
    }

    public function add(Request $request)
    {
        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMEdu.receipt_payment.add')->with($data);
            } else if ($_POST) {
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
                    $data['admin_id'] = $data['saler_id'] = \Auth::guard('admin')->user()->id;

                    // if ($request->file('image') != null) {
                    //     $data['image'] = CommonHelper::saveFile($request->file('image'), $this->module['code']);

                    // } else {
                    //     unset($data['image']);
                    // }

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
        $item = $this->model->find($request->id);

        if (!is_object($item)) abort(404);
        if (!$_POST) {
            $data = $this->getDataUpdate($request, $item);
            return view('CRMEdu.receipt_payment.edit')->with($data);
        } else if ($_POST) {
            $validator = Validator::make($request->all(), [
                // 'value' => 'required'
            ], [
                // 'value.required' => 'Bắt buộc phải nhập tên',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            } else {
                $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                #

                foreach ($data as $k => $v) {
                    $item->$k = $v;
                }
                if ($item->save()) {

                    if ($item->bill_id != null) {
                        //  nếu là giao dịch của HĐ thì cập nhật tiền đã nhận của HĐ
                        $BillReceiptsController = new BillReceiptsController();
                        $BillReceiptsController->updateTienDaTraHD($item->bill_id);
                    }

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

                return redirect('admin/' . $this->module['code']);
            }
        }
    }

    public function getPublish(Request $request)
    {
        // dd($request->all());
        try {

            \DB::beginTransaction();
            $id = $request->get('id', 0);
            $item = $this->model->find($id);

            if (!is_object($item))
                return response()->json([
                    'status' => false,
                    'msg' => 'Không tìm thấy bản ghi'
                ]);

            if ($item->{$request->column} == 0) {
                //  duyệt phiếu thu
                $item->{$request->column} = 1;
            } else {
                //  huỷ duyệt phiếu thu
                $item->{$request->column} = 0;
            }

            $item->save();

            if ($item->bill_id != null) {
                //  nếu là giao dịch của HĐ thì cập nhật tiền đã nhận của HĐ
                $BillReceiptsController = new BillReceiptsController();
                $BillReceiptsController->updateTienDaTraHD($item->bill_id);
            }

            \DB::commit();

            return response()->json([
                'status' => true,
                'published' => $item->{$request->column} == 1 ? true : false
            ]);
        } catch (\Exception $ex) {
            \DB::rollback();
            return response()->json([
                'status' => false,
                'published' => null,
                'msg' => $ex->getMessage()
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {
            $item = $this->model->find($request->id);

            $bill_id = $item->bill_id;

            $item->delete();

            if ($bill_id != null) {
                //  nếu là giao dịch của HĐ thì cập nhật tiền đã nhận của HĐ
                $BillReceiptsController = new BillReceiptsController();
                $BillReceiptsController->updateTienDaTraHD($bill_id);
            }

            CommonHelper::flushCache();
            CommonHelper::one_time_message('success', 'Xóa thành công!');

            return redirect('admin/' . $this->module['code']);
        } catch (\Exception $ex) {
            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên viên.');
            return back();
        }
    }

    /*public function multiDelete(Request $request)
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
    }*/

}
