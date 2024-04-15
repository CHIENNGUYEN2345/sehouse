<?php

namespace App\CRMBDS\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Illuminate\Http\Request;
use App\CRMBDS\Models\PenaltyTicket;
use App\Models\Admin;
use Validator;

class PenaltyTicketController extends CURDBaseController
{

    protected $module = [
        'code' => 'penalty_ticket',
        'table_name' => 'penalty_ticket',
        'label' => 'Phiếu phạt',
        'modal' => '\App\CRMBDS\Models\PenaltyTicket',
        'list' => [
        	['name' => 'image', 'type' => 'image', 'label' => 'Ảnh'],
            ['name' => 'staff_id', 'type' => 'relation_edit', 'label' => 'Tên', 'object' => 'staff', 'display_field' => 'name'],
           	['name' => 'date', 'type' => 'date_vi', 'label' => 'Ngày'],
           	['name' => 'money', 'type' => 'price_vi', 'label' => 'Số tiền'],
            ['name' => 'regulations', 'type' => 'text', 'label' => 'Vi phạm quy định', 'sort' => true],
            ['name' => 'staff_status', 'type' => 'status', 'label' => 'Xác nhận'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'staff_id', 'type' => 'select2_ajax_model', 'class' => ' required',  'label' => 'Thành viên', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'code', 'group_class' => 'col-md-3'],
                ['name' => 'regulations', 'type' => 'text', 'class' => 'required', 'label' => 'Vi phạm quy định gì?'],
                ['name' => 'date', 'type' => 'date_vi', 'class' => 'required', 'label' => 'Ngày vi phạm'],
                ['name' => 'money', 'type' => 'number', 'class' => 'required', 'label' => 'Số tiền phạt'],
                ['name' => 'image', 'type' => 'file_image', 'class' => 'required', 'label' => 'Ảnh bằng chứng'],
            ],
        ]
    ];

    protected $filter = [
        'staff_id' => [
            'label' => 'Thành viên',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'display_field2' => 'code',
            'model' => \App\Models\Admin::class,
            'object' => 'admin',
            'query_type' => '='
        ],
        'date' => [
            'label' => 'Ngày phạt',
            'type' => 'from_to_date',
            'query_type' => 'from_to_date'
        ],
    ];

    public function appendWhere($query, $request)
    {
        //  Nếu không có quyền quản lý phiếu phạt thì chỉ xem được phiếu của mình
        if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'penalty_ticket')) {
            $query = $query->where('staff_id', \Auth::guard('admin')->user()->id);
        }


        return $query;
    }

    public function getIndex(Request $request)
    {

        $data = $this->getDataList($request);

        return view('CRMBDS.penalty_ticket.list')->with($data);
    }

    public function getDataList(Request $request) {
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

        $data['tong_tien'] = $listItem->sum('money');

        $data['record_total'] = $listItem->count();

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

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMBDS.penalty_ticket.add')->with($data);
            } else if ($_POST) {
                $validator = Validator::make($request->all(), [
                    // 'name_vi' => 'required',
                ], [
                    // 'name_vi.required' => 'Bắt buộc phải nhập tên',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert
                    $data['admin_id'] = \Auth::guard('admin')->user()->id;
                    
                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {

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

    public function update(Request $request)
    {
        try {


            $item = $this->model->find($request->id);

            if (!is_object($item)) abort(404);
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('CRMBDS.penalty_ticket.edit')->with($data);
            } else if ($_POST) {


                $validator = Validator::make($request->all(), [
                    // 'name_vi' => 'required',
                ], [
                    // 'name_vi.required' => 'Bắt buộc phải nhập tên gói',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert
                    
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

            if ($item->{$request->column} == 0) {
            	$item->{$request->column} = 1;
            }
            else {
            	if (\Auth::guard('admin')->user()->id != $item->staff_id) {
            		//	người không bị phạt mới sửa được trạng thái
            		$item->{$request->column} = 0;
            	}
            }

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

}
