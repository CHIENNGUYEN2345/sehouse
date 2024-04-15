<?php

namespace Modules\WebBill\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Models\Admin;
use Illuminate\Http\Request;
use Modules\WebBill\Models\Timekeeper;
use Validator;

class TimekeeperController extends CURDBaseController
{

    protected $limit_default = 100;

    protected $orderByRaw = 'time ASC';

    protected $module = [
        'code' => 'timekeeper',
        'table_name' => 'timekeeper',
        'label' => 'Dữ liệu chấm công',
        'modal' => '\Modules\WebBill\Models\Timekeeper',
        'list' => [
            ['name' => 'admin_id', 'type' => 'relation_edit', 'label' => 'Họ tên', 'object' => 'admin', 'display_field' => 'name'],
            ['name' => 'admin_id', 'type' => 'relation', 'label' => 'Mã NV', 'object' => 'admin', 'display_field' => 'code'],
            ['name' => 'may_cham_cong_id', 'type' => 'text', 'label' => 'ID máy chấm công'],
            ['name' => 'time', 'type' => 'datetime_vi', 'label' => 'Thời gian chấm'],
            ['name' => 'thoi_gian_muon', 'type' => 'custom', 'td' => 'webbill::timekeeper.list.td.thoi_gian_muon', 'label' => 'Thời gian muộn'],
            ['name' => 'status', 'type' => 'custom', 'td' => 'webbill::timekeeper.list.td.ly_do_muon', 'label' => 'Lý do muộn'],
        ],
        'form' => [
            'general_tab' => [
                // ['name' => 'may_cham_cong_id', 'type' => 'number', 'class' => '', 'label' => 'ID máy chấm công', 'group_class' => 'col-md-6'],
                ['name' => 'admin_id', 'type' => 'select2_ajax_model', 'class' => 'required',  'label' => 'Thành viên', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'code', 'group_class' => 'col-md-6'],
                ['name' => 'time', 'type' => 'datetime-local', 'class' => '', 'label' => 'Thời gian chấm công', 'group_class' => 'col-md-6'],
                ['name' => 'ly_do_muon', 'type' => 'text', 'class' => '', 'label' => 'Lý do đi muộn', 'group_class' => 'col-md-8'],        
                ['name' => 'status', 'type' => 'checkbox', 'label' => 'Chấp nhận lý do muộn', 'value' => 1, 'group_class' => 'col-md-4'],
            ],
           
        ]
    ];

    protected $quick_search = [
        'label' => 'ID',
        'fields' => 'id, admin_id, may_cham_cong_id, time'
    ];

    protected $filter = [
        'choose_time' => [
            'label' => 'Thời gian chấm',
            'type' => 'select',
            'options' => [
                'thang_truoc' => 'Tháng trước',
                'thang_nay' => 'Tháng này',
                'khong' => 'Không lọc',
            ],
            'query_type' => 'custom',
        ],
        'time' => [
            'label' => 'Khoảng thời gian',
            'type' => 'from_to_date',
            'query_type' => 'from_to_date'
        ],
        'may_cham_cong_id' => [
            'label' => 'Thành viên',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'display_field2' => 'code',
            'model' => \App\Models\Admin::class,
            'object' => 'admin',
            'query_type' => 'custom'
        ],
    ];

    public function getIndex(Request $request)
    {
        
        $data = $this->getDataList($request);

        return view('webbill::timekeeper.list')->with($data);
    }

    public function appendWhere($query, $request)
    {
        if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'timekeeper')) {
            $query = $query->where('may_cham_cong_id', \Auth::guard('admin')->user()->may_cham_cong_id);
        }

        if (@$request->choose_time != null) {
            if ($request->choose_time == 'thang_truoc') {
                $query = $query->where('time', '>', date('Y-m-01 00:00:00', strtotime(date('Y-m')." -1 month")))
                                ->where('time', '<', date('Y-m-t 23:59:00', strtotime(date('Y-m')." -1 month")));
            } elseif ($request->choose_time == 'thang_nay') {
                $query = $query->where('time', '>', date('Y-m-01 00:00:00'))
                                ->where('time', '<', date('Y-m-t 23:59:00'));
            }
        }

        if (@$request->may_cham_cong_id != null) {
            $query = $query->where('may_cham_cong_id', Admin::find($request->may_cham_cong_id)->may_cham_cong_id);
        }
        
        if (@$request->choose_time == null) {
            //  Nếu không lọc theo thời gian thì mặc định chọn tháng trước
            $query = $query->where('time', '>', date('Y-m-01 00:00:00', strtotime(date('Y-m')." -1 month")))
                                ->where('time', '<', date('Y-m-t 23:59:00', strtotime(date('Y-m')." -1 month")));
        }

        return $query;
    }

    public function baoCao(Request $request) {
        $data = $this->getDataList($request);
        
        return view('webbill::timekeeper.bao_cao')->with($data);
    }

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('webbill::timekeeper.add')->with($data);
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
                    $admin = Admin::find($data['admin_id']);
                    $data['may_cham_cong_id'] = @$admin->may_cham_cong_id;


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
                        return redirect('admin/' . $this->module['code'] . '/' . $this->model->id);
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
                return view('webbill::timekeeper.edit')->with($data);
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

                    //  tùy chỉnh dữ liệu insert
                    $admin = Admin::find($data['admin_id']);
                    $data['may_cham_cong_id'] = @$admin->may_cham_cong_id;

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
                        return redirect('admin/' . $this->module['code'] . '/' . $item->id);
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

    /**
     * Tối đa import được 999 dòng
     */
    public function importExcel(Request $r)
    {

        $table_import = $r->has('table') ? $r->table : $this->module['table_name'];
        $validator = Validator::make($r->all(), [
            'module' => 'required',
        ], [
            'module.required' => 'Bắt buộc phải nhập module!',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {

            $importController = new \App\Http\Controllers\Admin\ImportController();
            $data = $importController->processingValueInFields($r, $importController->getAllFormFiled());
            //  Tùy chỉnh dữ liệu insert

            if ($r->has('file')) {
                $file_name = $r->file('file')->getClientOriginalName();
                $file_name = str_replace(' ', '', $file_name);
                $file_name_insert = date('s_i_') . $file_name;
                $r->file('file')->move(base_path() . '/public_html/filemanager/userfiles/imports/', $file_name_insert);
                $data['file'] = 'imports/' . $file_name_insert;
            }

            unset($data['field_options_key']);
            unset($data['field_options_value']);
            #

            $item = new \App\Models\Import();
            foreach ($data as $k => $v) {
                $item->$k = $v;
            }
            if ($item->save()) {

                //  Import dữ liệu vào
                $importController->updateAttributes($r, $item);

                $this->processingImport($r, $item);

                // CommonHelper::flushCache($table_import);
                CommonHelper::one_time_message('success', 'Tạo mới thành công!');
                return redirect('/admin/import');
            } else {
                CommonHelper::one_time_message('error', 'Lỗi tao mới. Vui lòng load lại trang và thử lại!');
            }

            if ($r->ajax()) {
                return response()->json([
                    'status' => true,
                    'msg' => '',
                    'data' => $item
                ]);
            }

            return redirect('/admin/import');
        }
    }

    public function processingImport($r, $item)
    {

        $table_import = $r->has('table') ? $r->table : $this->module['table_name'];
        $record_total = $record_success = 0;
        $dataInsertFix =\App\Models\Attribute::where('table', $table_import)->where('type', 'field_options')->where('item_id', @$item->id)->pluck('value', 'key')->toArray();

        echo '<a style="padding: 20px; background-color: blue; color: #FFF; font-weight: bold;" href="/admin/timekeeper">Quay lại</a><br>';

        \Excel::load('public_html/filemanager/userfiles/' . $item->file, function ($reader) use ($r, $dataInsertFix, &$record_total, &$record_success) {

            $reader->each(function ($sheet) use ($r, $reader, $dataInsertFix, &$record_total, &$record_success) {
                
                if ($reader->getSheetCount() == 1) {

                    $result = $this->importItem($sheet, $r, $dataInsertFix);
                    if (isset($result['msg'])) {
                        echo '&nbsp;&nbsp;&nbsp;&nbsp; => '.$result['msg'].'<br>';
                    }
                    
                    if (@$result['status'] == true) {
                        $record_total++;
                    }
                    if (@$result['import'] == true) {
                        $record_success++;
                        echo '&nbsp;&nbsp;&nbsp;&nbsp;=> Import thành công<br>';
                    }
                } else {

                    $sheet->each(function ($row) use ($r, $dataInsertFix, &$model, &$record_total, &$record_success) {
                        $result = $this->importItem($row, $r, $dataInsertFix);
                        if ($result['status']) {
                            $record_total++;
                        }
                        if ($result['import']) {
                            $record_success++;
                        }
                    });
                }
                
            });
        });
        $item->record_total = $record_total;
        $item->record_success = $record_total;
        $item->save();

        //  Xoá các log cũ
        print "Xoá log từ 7 ngày trước\n";
        Timekeeper::where('time', '<', date('Y-m-d', strtotime(" -7 days")))->delete();

        return true;
    }

    //  Xử lý import 1 dòng excel
    public function importItem($row, $r, $dataInsertFix)
    {
        try {
            
            //  Kiểm tra trường dữ liêu bắt buộc có
            /*$fields_require = ['tel'];
            foreach ($fields_require as $field_require) {
                if (!isset($row->{$field_require}) || $row->{$field_require} == '' || $row->{$field_require} == null) {
                    return false;
                }
            }*/

            $row_empty = true;
            foreach ($row->all() as $key => $value) {
                if ($value != null) {
                    $row_empty = false;
                }
            }

            //  Các trường không được trùng
            $item_model = new $this->module['modal'];
            $item = $item_model->where('may_cham_cong_id', $row->all()['may_cham_cong_id'])->where('time', $row->all()['time'])->first();

            if (is_object($item)) {
                //  nếu đã tồn tại đầu mối này
                $row_empty = true;
                return [
                    'status' => false,
                    'import' => false,
                    'msg' => 'Đã tồn tại',
                ];
            }

            /*if ($this->import[$request->module]['unique']) {
                $field_name = $this->import[$request->module]['fields'][$this->import[$request->module]['unique']];
                $model_new = new $this->import[$request->module]['modal'];
                $model = $model_new->where($field_name, $row->{$this->import[$request->module]['unique']})->first();
            }*/

            if (!$row_empty) {
                echo '__bắt đầu chèn dữ liệu:' . $row->all()['may_cham_cong_id'];
                $data = [];

                $data['create_by'] = \Auth::guard('admin')->user()->id.'|';

                //  Chèn các dữ liệu lấy vào từ excel
                foreach ($row->all() as $key => $value) {
                    switch ($key) {
                        
                        default: {
                            if (\Schema::hasColumn($r->table, $key)) {
                                $data[$key] = $value;
                            }
                        }
                    }
                }

                //  Gán các dữ liệu được fix cứng từ view
                foreach ($dataInsertFix as $k => $v) {
                    $data[$k] = $v;
                }

                $admin = Admin::select('id')->where('may_cham_cong_id', $data['may_cham_cong_id'])->first();
                $data['admin_id'] = @$admin->id;

                $timekeeper = new Timekeeper();
                foreach ($data as $k => $v) {
                    $timekeeper->$k = $v;
                }
            
                if ($timekeeper->save()) {
                    return [
                        'status' => true,
                        'import' => true,
                        'msg' => 'import thành công: ' . $row->all()['may_cham_cong_id'],
                    ];
                }
            } else {
                return [
                    'status' => false,
                    'import' => false,
                    'msg' => 'Dòng trống',
                ];
            }
        } catch (\Exception $ex) {
            return [
                'status' => true,
                'import' => false,
                'msg' => $ex->getMessage()
            ];
        }
    }
}
