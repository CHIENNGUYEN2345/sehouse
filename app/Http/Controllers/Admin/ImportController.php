<?php
namespace App\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Models\Attribute;
use App\Models\Import;
use Illuminate\Http\Request;
use Validator;
use DB;

class ImportController extends CURDBaseController
{
    protected $table_import = '';

    protected $module = [
        'code' => 'import',
        'table_name' => 'imports',
        'label' => 'Import',
        'modal' => '\App\Models\Import',
        'list' => [
            ['name' => 'module', 'type' => 'select', 'label' => 'Module', 'options' => [
                'user' => 'Khách hàng / đối tác',
                'admin' => 'Thành viên quản lý',
            ]],
//            ['name' => 'record_total', 'type' => 'custom', 'td' => 'import.list.td.count_record', 'label' => 'Thành công/Tổng số'],
//            ['name' => 'record_success', 'type' => 'number', 'label' => 'Bản ghi thành công'],
            ['name' => 'file', 'type' => 'file', 'label' => 'File import'],
            ['name' => 'created_at', 'type' => 'datetime_vi', 'label' => 'Thời gian'],
            ['name' => 'action', 'type' => 'action', 'class' => '', 'label' => '#'],
        ],
        'form' => [
            'general_tab' => [
                /*['name' => 'module', 'type' => 'select', 'options' => [
                    'user' => 'Khách hàng / đối tác',
                    'admin' => 'Thành viên quản lý',
                ], 'class' => 'required', 'label' => 'Chọn module', 'des' => 'Khu vực mà bạn muốn đẩy dữ liệu vào'],*/
                ['name' => 'module', 'type' => 'custom', 'field' => 'import.partials.select_module', 'options' => [
                    'user' => 'Khách hàng / đối tác',
                    'admin' => 'Thành viên quản lý',
                ], 'class' => 'required', 'label' => 'Chọn module', 'des' => 'Khu vực mà bạn muốn đẩy dữ liệu vào'],
                ['name' => 'btn_download_excel_demo', 'type' => 'inner', 'class' => '', 'label' => 'Tải file Excel mẫu', 'html' => '<button type="button" onclick="downloadExcelDemo();" class="btn btn-brand">
                                        <i class="la la-download"></i>
                                        <span class="kt-hidden-mobile">Tải về file mẫu</span>
                                    </button>'],
                ['name' => 'file', 'type' => 'file', 'class' => 'required', 'label' => 'Nhập file Excel', 'des' => 'Nhập vào file excel mà bạn muốn import dữ liệu. Lưu ý: hệ thống chỉ nhận dữ liệu ở các cột đã được khai báo trong file mẫu'],
                ['name' => 'field_options', 'type' => 'dynamic', 'label' => 'Tham số mặc định', 'cols' => ['Trường', 'Giá trị'], 'des' => 'Các trường dữ liệu mà bạn muốn set cứng vào các bản ghi khi import dữ liệu vào'],
                ['name' => 'note', 'type' => 'textarea', 'label' => 'Ghi chú'],
            ],
        ]
    ];

    protected $import = [
        'users' => [
            'fields' => [
            ],
            'modal' => '\App\Models\User',
            'field_require' => 'tel',
            'unique' => 'tel'
        ],
        'admin' => [
            'fields' => [
            ],
            'modal' => '\App\Models\Admin',
            'field_require' => 'tel',
            'unique' => 'tel'
        ]
    ];

    protected $filter = [
        'module' => [
            'label' => 'Module',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => 'Module',
                'user' => 'Khách hàng / đối tác',
                'admin' => 'Thành viên quản lý',
            ]
        ],
    ];

    public function appendData($request, $data)
    {
        if ($request->has('file')) {
            $file_name = $request->file('file')->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $file_name_insert = date('s_i_') . $file_name;
            $request->file('file')->move(base_path() . '/public_html/filemanager/userfiles/imports/', $file_name_insert);
            $data['file'] = 'imports/' . $file_name_insert;
        }

        unset($data['field_options_key']);
        unset($data['field_options_value']);
        return $data;
    }

    public function getIndex(Request $request)
    {

//        if (in_array('view', $this->permission)) {
//            if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, $this->module['name'] . '_view')) {
//                CommonHelper::one_time_message('error', 'Bạn không có quyền sử dụng chức năng này!');
//                return redirect()->back();
//            }
//        }
        #
        $data = $this->getDataList($request);

        return view('admin.themes.metronic1.'.$this->module['code'].'.list')->with($data);
    }

    public function add(Request $request)
    {
        try {

            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('admin.themes.metronic1.' . $this->module['code'] . '.add')->with($data);
            } else if ($_POST) {
                $validator = Validator::make($request->all(), [
                    'module' => 'required',
                ], [
                    'module.required' => 'Bắt buộc phải nhập module!',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert

                    $data = $this->appendData($request, $data);
                    #
                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
                        $this->afterAdd($request, $this->model);
                        CommonHelper::flushCache($this->module['table_name']);
                        CommonHelper::one_time_message('success', 'Tạo mới thành công!');
                        DB::commit();
                        return redirect('/admin/import');
                    } else {
                        DB::rollback();
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
            DB::rollback();
            CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function update(Request $request)
    {
        try {

            $item = $this->model->find($request->id);

            if (!is_object($item)) {
                abort(404);
            }
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('admin.themes.metronic1.' . $this->module['code'] . '.edit')->with($data);
            } else if ($_POST) {
                if ($item->id == \Auth::guard('admin')->user()->id) {
                    $validator = Validator::make($request->all(), [
                        'module' => 'required'
                    ], [
                        'module.required' => 'Bắt buộc phải nhập module',
                    ]);

                    if ($validator->fails()) {
                        return back()->withErrors($validator)->withInput();
                    }
                }
                $data = $this->processingValueInFields($request, $this->getAllFormFiled());
//                    dd($data);
                //  Tùy chỉnh dữ liệu edit

                #
                foreach ($data as $k => $v) {
                    $item->$k = $v;
                }
                if ($item->save()) {
                    $this->afterUpdate($request, $item);
                    CommonHelper::flushCache($this->module['table_name']);
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
        } catch (\Exception $ex) {
            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
//            CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function delete(Request $request)
    {
        try {
            $item = $this->model->find($request->id);

            $item->delete();
            CommonHelper::flushCache($this->module['table_name']);
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
            CommonHelper::flushCache($this->module['table_name']);
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

    public function afterAdd($request, $item)
    {
        $this->updateAttributes($request, $item);
        $this->importExcel($request, $item);
        return true; // TODO: Change the autogenerated stub
    }

    public function afterUpdate($request, $item)
    {
        $this->updateAttributes($request, $item);
        $this->importExcel($request, $item);
        return true; // TODO: Change the autogenerated stub
    }

    /*public function importExcel($request, $item)
    {
        $table_import = $r->has('table') ? $r->table : $this->module['table_name'];
        $record_total = $record_success = 0;
        $dataInsertFix = Attribute::where('table', $table_import)->where('type', 'field_options')->where('item_id', @$item->id)->pluck('value', 'key')->toArray();
        \Excel::load('public_html/filemanager/userfiles/' . $item->file, function ($reader) use ($request, $dataInsertFix, &$model, &$record_total, &$record_success) {
            $reader->each(function ($sheet) use ($request, $reader, $dataInsertFix, &$model, &$record_total, &$record_success) {

                if ($reader->getSheetCount() == 1) {

                    $result = $this->importItem($sheet, $request, $dataInsertFix);
                    if ($result) {
                        $record_total++;
                        $record_success++;
                    }
                } else {
                    $sheet->each(function ($row) use ($request, $dataInsertFix, &$model, &$record_total, &$record_success) {
                        $result = $this->importItem($row, $request, $dataInsertFix);
                        if ($result) {
                            $record_total++;
                            $record_success++;
                        }
                    });
                }
            });
        });
        $item->record_total = $record_total;
        $item->record_success = $record_total;
        $item->save();
        return true;
    }*/

    //  Xử lý import 1 dòng excel
//    public function importItem($row, $request, $dataInsertFix)
//    {
//        try {
//            if (!isset($row->{$this->import[$request->module]['field_require']}) || $row->{$this->import[$request->module]['field_require']} == '' || $row->{$this->import[$request->module]['field_require']} == null) {
//                return false;
//            }
//
//            /*if ($this->import[$request->module]['unique']) {
//                $field_name = $this->import[$request->module]['fields'][$this->import[$request->module]['unique']];
//                $model_new = new $this->import[$request->module]['modal'];
//                $model = $model_new->where($field_name, $row->{$this->import[$request->module]['unique']})->first();
//            }*/
//            if (!isset($model) || !is_object($model)) {
//                $model = new $this->import[$request->module]['modal'];
//            }
//
//            //  Xử lý các data set cứng
//            foreach ($dataInsertFix as $k => $v) {
//                $model->{$k} = $v;
//            }
//            $fields = $this->import[$request->module]['fields'];
//
//
//            foreach ($row->all() as $key => $value) {
//                if (\Schema::hasColumn($model->getTable(), $key)) {
//                    $model->{$key} = $value;
//                }
//            }
//            if ($model->save()) {
//                return true;
//            }
//        } catch (\Exception $ex) {
//            return false;
//        }
//        return false;
//    }

    /**
     * Tải về file_mau_importexcel
    */
    public function downloadExcelDemo(Request $r) {
        $zipFileName = 'excel_default/'.@$r->module.'.xlsx';

        $filetopath = base_path() . '/public_html/filemanager/userfiles/' . $zipFileName;

        //  Nếu có sẵn file thì tải về
        if (file_exists($filetopath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename='.basename($filetopath));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filetopath));
            readfile($filetopath);
            exit();
        }

        //  Nếu không có sẵn file thì lấy tất cả các cột của bảng đó ra cho vào file excel rồi cho tải về
        \Excel::create('file_mau_import_' . $r->module, function ($excel) use ($r) {

            // Set the title
            $excel->setTitle('file_mau_import_' . $r->module);

            $excel->sheet($r->module, function ($sheet) use ($r) {

                foreach (\Schema::getColumnListing($r->module) as $column) {
                    if (!in_array($column, ['id', 'created_at', 'updated_at']))
                    $field_name[] = $column;
                }

                $sheet->row(1, $field_name);
            });
        })->download('xls');
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
            $data = $this->processingValueInFields($r, $importController->getAllFormFiled());
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
                $this->updateAttributes($r, $item);

                $this->processingImport($r, $item);

                CommonHelper::flushCache($table_import);
                CommonHelper::one_time_message('success', 'Tạo mới thành công!');
//                return redirect('/admin/import');
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

//            return redirect('/admin/import');
        }
    }

    public function updateAttributes($r, $item)
    {
        $table_import = $r->has('table') ? $r->table : $this->module['table_name'];
        if ($r->has('field_options_key')) {
            $key_update = [];
            foreach ($r->field_options_key as $k => $key) {
                if ($key != null && $r->field_options_value[$k] != null) {
                    $key_update[] = $key;
                    \App\Models\Attribute::updateOrCreate([
                        'key' => $key,
                        'table' => $table_import,
                        'type' => 'field_options',
                        'item_id' => $item->id
                    ], [
                        'value' => $r->field_options_value[$k]
                    ]);
                }
            }
            if (!empty($key_update)) {
                \App\Models\Attribute::where([
                    'table' => $table_import,
                    'type' => 'field_options',
                    'item_id' => $item->id
                ])->whereNotIn('key', $key_update)->delete();
            }
        } else {
            \App\Models\Attribute::where([
                'table' => $table_import,
                'type' => 'field_options',
                'item_id' => $item->id
            ])->delete();
        }
        return true;
    }

    public function processingImport($r, $item)
    {


// echo phpinfo();

// // Show just the module information.
// // phpinfo(8) yields identical results.
// echo phpinfo(INFO_MODULES);
// die;
        $table_import = $r->has('table') ? $r->table : $this->module['table_name'];
        $record_total = $record_success = 0;
        $dataInsertFix =\App\Models\Attribute::where('table', $table_import)->where('type', 'field_options')->where('item_id', @$item->id)->pluck('value', 'key')->toArray();

        // dd('public_html/filemanager/userfiles/' . $item->file, $item->file, $r, $dataInsertFix, $record_total, $record_success);



        echo '<a style="padding: 20px; background-color: blue; color: #FFF; font-weight: bold;" href="javascript:history.back()">Quay lại</a><br>';

        \Excel::load('public_html/filemanager/userfiles/' . $item->file, function ($reader) use ($r, $dataInsertFix, &$record_total, &$record_success) {
            
            $reader->each(function ($sheet) use ($r, $reader, $dataInsertFix, &$record_total, &$record_success) {
                
                if ($reader->getSheetCount() == 1) {
                    echo 'bắt đầu import <br>';
                    $result = $this->importItem($sheet, $r, $dataInsertFix);
                    if (isset($result['msg'])) {
                        echo '&nbsp;&nbsp;&nbsp;&nbsp; => '.$result['msg'].'<br>';
                    }

                    if ($result['status']) {
                        $record_total++;
                    }
                    if ($result['import']) {
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
            /*$item_model = new $this->module['modal'];
            $item = $item_model->where('tel', $row->all()['tel'])->first();
            if (!is_object($item)) {
                $item = $item_model;
            }*/

            /*if ($this->import[$request->module]['unique']) {
                $field_name = $this->import[$request->module]['fields'][$this->import[$request->module]['unique']];
                $model_new = new $this->import[$request->module]['modal'];
                $model = $model_new->where($field_name, $row->{$this->import[$request->module]['unique']})->first();
            }*/

            if (!$row_empty) {
                $data = [];

                //  Gán các dữ liệu được fix cứng từ view
                foreach ($dataInsertFix as $k => $v) {
                    $data[$k] = $v;
                }

                //  Chèn các dữ liệu lấy vào từ excel
                foreach ($row->all() as $key => $value) {
                    switch ($key) {
                        case 'password': {
                            $data['password'] = bcrypt($value);
                            break;
                        }
                        default: {
                            if (\Schema::hasColumn($r->table, $key)) {
                                $data[$key] = $value;
                            }
                        }
                    }
                }
                if (DB::table($r->table)->insert($data)) {
                    return [
                        'status' => true,
                        'import' => true
                    ];
                }
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
