<?php

namespace Modules\CheckErrorLink\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\CheckErrorLink\Models\DomainCheck;
use Modules\CheckErrorLink\Models\LinkCheck;
use Modules\EworkingRole\Http\Controllers\Admin\RoleController;

class DomainController extends CURDBaseController
{
    protected $_role;
    protected $orderByRaw = 'id desc';

    public function __construct()
    {
        parent::__construct();
        $this->_role = new RoleController();
    }

    protected $module = [
        'code' => 'domain',
        'table_name' => 'check_error_domain',
        'label' => 'Danh sách domain',
        'modal' => 'Modules\CheckErrorLink\Models\DomainCheck',
        'list' => [
            ['name' => 'name', 'type' => 'custom', 'td' => 'checkerrorlink::layouts.td.text', 'label' => 'Tên website', 'sort' => true],
            ['name' => 'status', 'type' => 'status', 'label' => 'Trạng thái'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'label' => 'Tên miền', 'class' => 'required'],
                ['name' => 'status', 'type' => 'checkbox', 'label' => 'Trạng thái'],
            ],
        ]
    ];

    protected $filter = [
        'website' => [
            'label' => 'Tên miền',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'links' => [
            'label' => 'Đường dẫn',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'status' => [
            'label' => 'Trạng thái',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => 'Trạng thái',
                1 => 'Kich hoạt',
                0 => 'Khóa',
            ]
        ],
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);
        return view('checkerrorlink::list')->with($data);
    }

    public function all(Request $request)
    {
        $data = $this->getDataListAll($request);

        return view('checkerrorlink::all')->with($data);
    }

    public function run(Request $request)
    {
        $list_domain = DomainCheck::where('status', 1)->pluck('id')->toArray();
        $linkchecks = LinkCheck::whereIn('domain_id', $list_domain)->where('status', 1)->get();
        $result = \Modules\CheckErrorLink\Http\Helpers\CommonHelper::check_link_run($linkchecks);
        if ($result['status']) {
            \App\Http\Helpers\CommonHelper::one_time_message('success', 'Quét lỗi thành công vui lòng kiểm tra email !');
        } else {
            \App\Http\Helpers\CommonHelper::one_time_message('error', $result['msg']);
        }
        return back();
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
                    'name' => 'required',
                ], [
                    'name.required' => 'Bắt buộc phải nhập tên',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {

                    if (DomainCheck::where('name', trim($request->name, '/'))->exists()) {
                        CommonHelper::one_time_message('error', 'Domain đã tồn tại');
                        return back();
                    }

                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    $data['name'] = trim($data['name'], '/');
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
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('checkerrorlink::edit')->with($data);
            } else if ($_POST) {


                if ($item->id == \Auth::guard('admin')->user()->id) {
                    $validator = Validator::make($request->all(), [
                        'name' => 'required',
                    ], [
                        'name.required' => 'Bắt buộc phải nhập tên',
                    ]);

                    if ($validator->fails()) {
                        return back()->withErrors($validator)->withInput();
                    }
                }
                if (DomainCheck::where('name', trim($request->name, '/'))->exists()) {
                    CommonHelper::one_time_message('error', 'Domain đã tồn tại');
                    return back();
                }

                $data = $this->processingValueInFields($request, $this->getAllFormFiled());
//                    dd($data);
                //  Tùy chỉnh dữ liệu insert
                #
                $data['name'] = trim($data['name'], '/');
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
                return redirect('admin/' . $this->module['code'] . '/' . $item->id);
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
//                $this->deleteAdminFromCompany($ids);
                $this->model->whereIn('id', $ids)->delete();
            }
            CommonHelper::one_time_message('success', 'Xóa thành công!');
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
            $ids = $this->model->all();
            if ($ids->count() > 0) {
                foreach ($ids as $k) {
                    $this->model->find($k->id)->delete();
                }
                CommonHelper::one_time_message('success', 'Xóa thành công!');
                if ($request->ajax()) {
                    return response()->json([
                        'status' => true,
                        'msg' => 'Xóa thành công!'
                    ]);
                }

            } else {
                if ($request->ajax()) {
                    return response()->json([
                        'status' => false,
                        'msg' => 'Không có bản ghi nào!'
                    ]);
                }
//                CommonHelper::one_time_message('error', 'Không có bản ghi nào!');
            }

            return redirect('admin/' . $this->module['code']);
        } catch (\Exception $ex) {
            if ($request->ajax()) {
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


    public function appendWhere($query, $request)
    {
//       $query = $query->where('parent_id',0);
        return $query;
    }
}
