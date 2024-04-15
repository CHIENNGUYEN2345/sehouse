<?php

namespace App\CRMEdu\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Auth;
use Illuminate\Http\Request;
use Validator;
use App\CRMEdu\Models\Bill;

class BillProgressHistoryController extends CURDBaseController
{

    protected $orderByRaw = 'id asc';

    protected $module = [
        'code' => 'bill_progress_history',
        'table_name' => 'bill_progress_history',
        'label' => 'Lịch sử',
        'modal' => '\App\CRMEdu\Models\BillProgressHistory',
        'list' => [
            // ['name' => 'bill_id', 'type' => 'relation', 'label' => 'Hợp đồng', 'object' => 'bill', 'display_field' => 'domain', 'sort' => true],
            ['name' => 'old_value', 'type' => 'text', 'label' => 'Cũ'],
            ['name' => 'new_value', 'type' => 'text', 'label' => 'Chuyển thành'],
            ['name' => 'admin_id', 'type' => 'relation', 'label' => 'Người thực hiện', 'object' => 'admin', 'display_field' => 'name', 'sort' => true],
            ['name' => 'created_at', 'type' => 'datetime_vi', 'label' => 'Thời gian'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'bill_id', 'type' => 'select2_ajax_model', 'label' => 'Hợp đồng', 'model' => Bill::class, 'object' => 'bill', 'display_field' => 'domain', 'display_field2' => 'registration_date', 'class' => 'required'],
                ['name' => 'admin_id', 'type' => 'select2_ajax_model', 'label' => 'Người thực hiện', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'code', 'class' => 'required'],
                ['name' => 'old_value', 'type' => 'text', 'class' => 'required', 'label' => 'Giá trị cũ'],
                ['name' => 'new_value', 'type' => 'text', 'class' => 'required', 'label' => 'Giá trị mới'],
                ['name' => 'note', 'type' => 'text', 'class' => '', 'label' => 'Ghi chú'],
                ['name' => 'type', 'type' => 'text', 'class' => 'required', 'label' => 'Loại bản ghi'],
            ],
        ],
    ];

    protected $quick_search = [
        'label' => 'ID, giá trị',
        'fields' => 'id, bill_id, admin_id, old_value, new_value, note, type'
    ];

    protected $filter = [
        
    ];

    public function getIndex(Request $request)
    {

        $data = $this->getDataList($request);

        return view('CRMEdu.bill_progress_history.list')->with($data);
    }

    public function add(Request $request)
    {
        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMEdu.bill_progress_history.add')->with($data);
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
            return view('CRMEdu.bill_progress_history.edit')->with($data);
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

    public function ajaxLichSuTrangThai(Request $request) {
        $where = $this->filterSimple($request);
        $listItem = $this->model->whereRaw($where);
        $listItem = $this->quickSearch($listItem, $request);
        if ($this->whereRaw) {
            $listItem = $listItem->whereRaw($this->whereRaw);
        }
        $listItem = $this->appendWhere($listItem, $request);

        if($request->has('bill_id')) {
            $listItem = $listItem->where('bill_id', $request->bill_id);
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

        $data['bill'] = Bill::select('id', 'registration_date')->where('id', @$request->bill_id)->first();
        return view("CRMEdu.dhbill.partials.lich_su_trang_thai_view")->with($data);
    }
}
