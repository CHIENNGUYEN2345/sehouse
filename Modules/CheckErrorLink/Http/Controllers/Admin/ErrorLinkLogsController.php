<?php

namespace Modules\CheckErrorLink\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Models\RoleAdmin;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\CheckErrorLink\Http\Controllers\Admin\CURDBaseController;
use Illuminate\Support\Facades\Validator;
use Modules\EworkingRole\Http\Controllers\Admin\RoleController;

class ErrorLinkLogsController extends CURDBaseController
{
    protected $_role;
    protected $orderByRaw = 'id desc';

    public function __construct()
    {
        parent::__construct();
        $this->_role = new RoleController();
    }

    protected $module = [
        'group_by' => 'domain_id',
        'code' => 'error_link_logs',
        'table_name' => 'check_error_link_logs',
        'label' => 'Lịch sử lỗi',
        'modal' => 'Modules\CheckErrorLink\Models\LinkErrorLogs',
        'list' => [
            ['name' => 'domain_id', 'type' => 'custom', 'td' => 'checkerrorlink::layouts.td.name_domain', 'label' => 'Tên trang web'],
            ['name' => 'links', 'type'=>'text_edit', 'label' => 'Đường dẫn', 'sort' => true],
            ['name' => 'error_code', 'type' => 'text', 'label' => 'Mã lỗi'],
            ['name' => 'error_messenger', 'type' => 'text', 'label' => 'Mô tả lỗi'],
            ['name' => 'time_scan', 'type' => 'text', 'label' => 'Thời gian quét'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'website', 'type' => 'text', 'label' => 'Tên trang web', 'class' => 'requir'],
                ['name' => 'links', 'type' => 'text', 'label' => 'Đường dẫn', 'class' => 'requir'],
                ['name' => 'status', 'type' => 'checkbox', 'label' => 'Trạng thái'],
            ],
        ]
    ];

    protected $filter = [
        'error_code' => [
            'label' => 'Mã lỗi',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'error_messenger' => [
            'label' => 'Mô tả lỗi',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'website' => [
            'label' => 'Tên trang web',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'links' => [
            'label' => 'Đường dẫn',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'time_scan' => [
            'label' => 'Thời gian quét',
            'type' => 'text',
            'query_type' => 'like'
        ]
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);
//        dd($data);
        return view('checkerrorlink::list')->with($data);
    }

    public function all(Request $request)
    {
        $data = $this->getDataListAll($request);

        return view('checkerrorlink::all')->with($data);
    }

    public function getDataListAll(Request $request)
    {
        //  Filter
        $where = $this->filterSimple($request);
        $listItem = $this->model->whereRaw($where);
        if ($this->whereRaw) {
            $listItem = $listItem->whereRaw($this->whereRaw);
        }

        //  Export
        if ($request->has('export')) {
            $this->exportExcel($request, $listItem->get());
        }

        //  Sort
        $listItem = $this->sort($request, $listItem);
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
        $data['filter'] = $this->filter;
        if ($this->whereRaw) {
            $data['record_total'] = $this->model->whereRaw($this->whereRaw);
        } else {
            $data['record_total'] = $this->model;
        }

        $data['record_total'] = $data['record_total']->count();

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
                return view('checkerrorlink::add')->with($data);
            } else if ($_POST) {
                $validator = Validator::make($request->all(), [
                    'website' => 'required',
                    'links' => 'required',
                ], [
                    'website.required' => 'Bắt buộc phải nhập tên',
                    'links.required' => 'Bắt buộc phải đường dẫn',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {

                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert
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
                    return redirect('admin/'.$this->module['code']);
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
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('checkerrorlink::edit')->with($data);
            } else if ($_POST) {


                if ($item->id == \Auth::guard('admin')->user()->id) {
                    $validator = Validator::make($request->all(), [
                        'website' => 'required',
                        'links' => 'required',
                    ], [
                        'website.required' => 'Bắt buộc phải nhập tên',
                        'links.required' => 'Bắt buộc phải đường dẫn',
                    ]);

                    if ($validator->fails()) {
                        return back()->withErrors($validator)->withInput();
                    }
                }
                $data = $this->processingValueInFields($request, $this->getAllFormFiled());
//                    dd($data);
                //  Tùy chỉnh dữ liệu insert
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
                return redirect('admin/'.$this->module['code'].'/'.$item->id);
            }
        } catch (\Exception $ex) {
//            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function getPublish(Request $request)
    {
        try {
            $id = $request->get('id', 0);
            $item = $this->model->find($id);

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

            $this->deleteAdminFromCompany([$item->id]);

            CommonHelper::one_time_message('success', 'Xóa thành công!');
            return redirect('admin/' . $this->module['code'] . '/all');
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
//                $this->deleteAdminFromCompany($ids);
                $this->model->whereIn('id', $ids)->delete();
            }
            return response()->json([
                'status' => true,
                'msg' => 'Xóa thành công!'
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'msg' => 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.'
            ]);
        }
    }
    public function allDelete(Request $request)
    {
        try {
            $ids=$this->model->all();
            if ($ids->count()>0){
                foreach ($ids as $k){
                    $this->model->find($k->id)->delete();
                }
                CommonHelper::one_time_message('success', 'Xóa thành công!');
                if ($request->ajax()) {
                    return response()->json([
                        'status' => true,
                        'msg' => 'Xóa thành công!'
                    ]);
                }
            }else{
                if ($request->ajax()){
                    return response()->json([
                        'status' => false,
                        'msg' => 'Không có bản ghi nào!'
                    ]);
                }
//                CommonHelper::one_time_message('error', 'Không có bản ghi nào!');
            }
            return redirect('admin/' . $this->module['code']);
        } catch (\Exception $ex) {
            if ($request->ajax()){
                return response()->json([
                    'status' => false,
                    'msg' => 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.'
                ]);
            }
//            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            return back();
        }
    }
    public function searchForSelect2(Request $request)
    {
        $data = $this->model->select([$request->col, 'id'])->where($request->col, 'like', '%' . $request->keyword . '%');
        if ($request->where != '') {
            $data = $data->whereRaw(urldecode(str_replace('&#039;', "'", $request->where)));
        }
        if (@$request->company_id != null) {
            $data = $data->where('company_ids', 'like', '%|' . $request->company_id . '|%');
        }
        $data = $data->limit(5)->get();
        return response()->json([
            'status' => true,
            'items' => $data
        ]);
    }
}
