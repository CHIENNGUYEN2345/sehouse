<?php

namespace App\CRMWoo\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Illuminate\Http\Request;
use App\CRMWoo\Models\plan;
use App\Models\Admin;
use Validator;

class PlanController extends CURDBaseController
{

    protected $module = [
        'code' => 'plan',
        'table_name' => 'plans',
        'label' => 'Mục tiêu',
        'modal' => '\App\CRMWoo\Models\Plan',
        'list' => [
            ['name' => 'admin_id', 'type' => 'relation_edit', 'label' => 'Thành viên', 'object' => 'admin', 'display_field' => 'name'],
            ['name' => 'khqt', 'type' => 'text', 'label' => 'KH quan tâm', 'sort' => true],
//            ['name' => 'expiry_date', 'type' => 'date_vi', 'label' => 'Hết hạn'],
            ['name' => 'khqt_cao', 'type' => 'text', 'label' => 'KH quan tâm cao', 'sort' => true],
            ['name' => 'co_hoi', 'type' => 'text', 'label' => 'Cơ hội', 'sort' => true],
            ['name' => 'hd', 'type' => 'text', 'label' => 'Hợp đồng', 'sort' => true],
            ['name' => 'ds_tuan', 'type' => 'text', 'label' => 'Doanh số tuần', 'sort' => true],
            ['name' => 'ds_thang', 'type' => 'text', 'label' => 'Doanh số tháng', 'sort' => true],
            ['name' => 'created_at', 'type' => 'date_vi', 'label' => 'Ngày tạo', 'sort' => true],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'inner', 'type' => 'inner', 'class' => '', 'html' => '<a href="https://docs.google.com/spreadsheets/d/19LOLFUfPEXd0QDK4ifAHUL_FGWx_3rCFy3tIZ7yV4nk/edit#gid=336024191" target="_blank">Giải thích thuật ngữ</a>', 'label' => '', 'class' => 'col-md-12'],

                ['name' => 'admin_id', 'type' => 'select2_ajax_model', 'label' => 'Thành viên', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'class' => 'col-md-12'],
                ['name' => 'inner', 'type' => 'inner', 'class' => '', 'html' => '<strong>Điền vào mục tiêu công việc của bạn</strong>', 'label' => '', 'class' => 'col-md-12'],
                ['name' => 'ds_thang', 'type' => 'number', 'class' => 'required', 'label' => 'Doanh số tháng (đv: triệu)', 'class' => 'col-md-6'],
                ['name' => 'ds_tuan', 'type' => 'number', 'class' => 'required', 'label' => 'Doanh số tuần (đv: triệu)', 'class' => 'col-md-6'],
                ['name' => 'hd', 'type' => 'number', 'class' => 'required', 'label' => 'Tổng Hợp đồng/tuần', 'class' => 'col-md-6'],
                ['name' => 'co_hoi', 'type' => 'number', 'class' => 'required', 'label' => 'Tổng Cơ hội', 'class' => 'col-md-6'],
                ['name' => 'khqt_cao', 'type' => 'number', 'class' => 'required', 'label' => 'Tổng KH quan tâm cao', 'class' => 'col-md-6'],
                ['name' => 'khqt', 'type' => 'number', 'class' => 'required', 'label' => 'Tổng KH quan tâm', 'class' => 'col-md-6'],
                ['name' => 'khqt_moi', 'type' => 'number', 'class' => 'required', 'label' => 'KH quan tâm mới / ngày', 'class' => 'col-md-6'],
                
            ],
            'remind_tab' => [],
            'des_tab' => [],
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
        //  Chuyển hướng luôn đến trang chỉnh sửa kế hoạch
        $plan = Plan::where('admin_id', \Auth::guard('admin')->user()->id)->first();
        if (!is_object($plan)) {
            $plan = new Plan();
            $plan->admin_id = \Auth::guard('admin')->user()->id;
            $plan->save();
            return redirect('/admin/plan/edit/' . $plan->id);
        }
        return redirect('/admin/plan/edit/' . $plan->id);


        $data = $this->getDataList($request);

        return view('CRMWoo.plan.list')->with($data);
    }

    public function appendWhere($query, $request)
    {
        //  Nếu không có quyền xem toàn bộ dữ liệu thì chỉ được xem các dữ liệu mình tạo
        if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'view_all_data')) {
            $query = $query->where('admin_id', \Auth::guard('admin')->user()->id);
        }

        if (!is_null($request->get('multi_cat'))) {
            $query = $query->where('multi_cat', 'like', '%|'.$request->multi_cat.'|%');
        }

        return $query;
    }

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMWoo.plan.add')->with($data);
            } else if ($_POST) {
                \DB::beginTransaction();

                $validator = Validator::make($request->all(), [
                    // 'name' => 'required',
                    // 'link' => 'required|unique:codes,link',
                ], [
                    // 'name.required' => 'Bắt buộc phải nhập tên',
                    // 'link.unique' => 'Web này đã đăng!',
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
                        \DB::commit();


                        CommonHelper::one_time_message('success', 'Tạo mới thành công!');
                    } else {
                        \DB::rollback();
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
            \DB::rollback();
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
                return view('CRMWoo.plan.edit')->with($data);
            } else if ($_POST) {
                \DB::beginTransaction();

                $validator = Validator::make($request->all(), [
//                    'name' => 'required',
                    // 'link' => 'required',
                ], [
//                    'name.required' => 'Bắt buộc phải nhập tên gói',
//                    'link.unique' => 'Web này đã đăng!',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    foreach ($data as $k => $v) {
                        $item->$k = $v;
                    }
                    if ($item->save()) {

                        \DB::commit();
                        CommonHelper::one_time_message('success', 'Cập nhật thành công!');
                    } else {
                        \DB::rollback();
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
            \DB::rollback();
//            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            CommonHelper::one_time_message('error', $ex->getMessage());
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
}
