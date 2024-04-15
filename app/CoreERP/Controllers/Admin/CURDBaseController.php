<?php
namespace App\CoreERP\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CommonHelper;
use Auth;
use Illuminate\Http\Request;
use Session;

class CURDBaseController extends Controller
{
    protected $model;
    protected $whereRaw = false;
    protected $orderByRaw = 'id desc';
    protected $limit_default = 20;
    protected $module = [];

    protected $filter = [];
    protected $quick_search = [
        'label' => 'ID',
        'fields' => 'id'
    ];

    public function __construct()
    {
        $this->model = new $this->module['modal'];
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

    public function filterSimple($request)
    {
        $where = '1=1 ';
        if (!is_null($request->id)) {
            $where .= " AND " . 'id' . " = " . $request->id;
        }
        #
        foreach ($this->filter as $filter_name => $filter_option) {
            if (!is_null($request->get($filter_name))) {
                if ($filter_option['query_type'] == 'like') {
                    $where .= " AND " . $filter_name . " LIKE '%" . $request->get($filter_name) . "%'";
                } elseif ($filter_option['query_type'] == 'from_to_date') {
//                    dd($request->all());
                    if (!is_null($request->get('from_date')) || $request->get('from_date') != '') {
                        $where .= " AND " . $filter_name . " >= '" . date('Y-m-d 00:00:00', strtotime($request->get('from_date'))) . "'";
                    }
                    if (!is_null($request->get('to_date')) || $request->get('to_date') != '') {
                        $where .= " AND " . $filter_name . " <= '" . date('Y-m-d 23:59:59', strtotime($request->get('to_date'))) . "'";
                    }
                } elseif ($filter_option['query_type'] == '=') {
                    $where .= " AND " . $filter_name . " = '" . $request->get($filter_name) . "'";
                }
            }
        }
        return $where;
    }

    public function quickSearch($listItem, $r) {
        if (@$r->quick_search != '') {
            $listItem = $listItem->where(function ($query) use ($r) {
                foreach (explode(',', $this->quick_search['fields']) as $field) {
                    $query->orWhere(trim($field), 'LIKE', '%' . $r->quick_search . '%');    //  truy vấn các tin thuộc các danh mục con của danh mục hiện tại
                }
            });

        }
        return $listItem;
    }

    public function sort($request, $model)
    {
        $dung_bo_loc = false;
        if ($request->sorts != null) {
            foreach ($request->sorts as $sort) {
                if ($sort != null) {
                    $dung_bo_loc = true;
                    $sort_data = explode('|', $sort);
                    $model = $model->orderBy($sort_data[0], $sort_data[1]);
                }
            }
        }

        if(!$dung_bo_loc) {
            $model = $model->orderByRaw($this->orderByRaw);
        }

        return $model;
    }

    public function exportExcel($request, $data)
    {
        \Excel::create(str_slug($this->module['label'], '_') . '_' . date('d_m_Y'), function ($excel) use ($data) {

            // Set the title
            $excel->setTitle($this->module['label'] . ' ' . date('d m Y'));

            $excel->sheet(str_slug($this->module['label'], '_') . '_' . date('d_m_Y'), function ($sheet) use ($data) {

                $field_name = ['ID'];
                foreach ($this->getAllFormFiled() as $field) {
                    if (!isset($field['no_export']) && isset($field['label'])) {
                        $field_name[] = $field['label'];
                    }
                }
                $field_name[] = 'Tạo lúc';
                $field_name[] = 'Cập nhập lần cuối';

                $sheet->row(1, $field_name);

                $k = 2;

                foreach ($data as $value) {
                    $data_export = [];
                    $data_export[] = $value->id;
                    foreach ($this->getAllFormFiled() as $field) {
                        if (!isset($field['no_export']) && isset($field['label'])) {
                            try {
                                if (in_array($field['type'], ['text', 'number', 'textarea', 'textarea_editor', 'date', 'datetime-local', 'email', 'hidden', 'checkbox', 'textarea_editor', 'textarea_editor2'])) {
                                    $data_export[] = $value->{$field['name']};
                                } elseif (in_array($field['type'], [
                                    'relation', 'select_model', 'select2_model', 'select2_ajax_model', 'select_model_tree',

                                ])) {
                                    $data_export[] = @$value->{$field['object']}->{$field['display_field']};
                                } elseif ($field['type'] == 'select') {
                                    $data_export[] = @$field['options'][$value->{$field['name']}];
                                } elseif (in_array($field['type'], ['file', 'file_editor2'])) {
                                    $data_export[] = \URL::asset('public/filemanager/userfiles/' . @$value->{$field['name']});
                                } elseif (in_array($field['type'], ['file_editor_extra'])) {
                                    $items = explode('|', @$value->{$field['name']});
                                    foreach ($items as $item) {
                                        $data_export[] = \URL::asset('public/filemanager/userfiles/' . @$item) . ' | ';
                                    }
                                } else {
                                    $data_export[] = $field['label'];
                                }
                            } catch (\Exception $ex) {
                                $data_export[] = $ex->getMessage();
                            }
                        }
                    }
                    $data_export[] = @$value->created_at;
                    $data_export[] = @$value->updated_at;
                    // dd($this->getAllFormFiled());
                    $sheet->row($k, $data_export);
                    $k++;
                }
            });
        })->download('xlsx');
    }

    public function getAllFormFiled()
    {
        $fields = [];
        foreach ($this->module['form'] as $tab) {
            foreach ($tab as $field) {
                if (strpos(@$field['inner'], 'disabled') === false) {
                    //  Nếu ko bị disabled thì lấy field này
                    $fields[] = $field;
                }
            } 
        }
        return $fields;
    }

    public function appendWhere($query, $request)
    {
        return $query;
    }

    public function getDataAdd(Request $request) {
        $data['module'] = $this->module;
        $data['page_title'] = 'Thêm ' . $this->module['label'];
        $data['page_type'] = 'add';
        return $data;
    }

    public function afterAddLog($request, $item)
    {
        try {
            if ($request->ajax()) {
                // $item->company_id = @$request->company_id;
                $item->admin_id = @$request->admin_id;
            } else {
                // $item->company_id = @\Auth::guard('admin')->user()->last_company_id;
                $item->admin_id = @\Auth::guard('admin')->user()->id;
            }
            $item->save();
        } catch (\Exception $ex) {
            return false;
        }
        return true;
    }

    public function processingValueInFields($request, $fields, $prefix = '')
    {
        $data = [];
        foreach ($fields as $field) {
            if (!in_array($field['type'], ['inner'])) {
                if (in_array($field['type'], ['checkbox', 'checkbox_check_permission'])) {
                    $data[$field['name']] = $request->has($prefix . $field['name']) ? 1 : 0;
                } elseif (in_array($field['type'], ['file_editor', 'file_editor2'])) {
                    if (strpos(@$request->get($prefix . $field['name']), 'filemanager')) {
                        $data[$field['name']] = @explode('filemanager/userfiles/', urldecode($request->get($prefix . $field['name'])))[1];
                    } else {
                        $data[$field['name']] = urldecode(@$request->get($prefix . $field['name']));
                    }
                } elseif (in_array($field['type'], ['file_editor_extra', 'multiple_image'])) {
                    $str_insert = '';
                    for ($i = 1; $i <= 6; $i++) {
                        $input_name = $prefix . $field['name'] . $i;
                        if ($request->{$input_name} != null && strpos($request->{$input_name}, 'filemanager')) {
                            $str_insert .= (@explode('filemanager/userfiles/', $request->{$input_name})[1] . '|');
                        } elseif ($request->{$input_name} != null) {
                            $str_insert .= $request->{$input_name} . '|';
                        }
                    }
                    $data[$field['name']] = $str_insert != '' ? substr($str_insert, 0, -1) : '';
                } elseif (in_array($field['type'], ['slug'])) {
                    $data[$field['name']] = $this->renderSlug(isset($request->id) ? $request->id : false, $request->{$field['name']});
                } elseif (in_array($field['type'], ['file_image'])) {
                    if ($request->get($field['name'] . '_delete', 0) == 0) {    //  If delete field file
                        if ($request->file($field['name']) != null) {
                            $data[$field['name']] = CommonHelper::saveFile($request->file($field['name']), $this->module['code']. date('/Y/m/d'));
                        }
                    } else {
                        $data[$field['name']] = '';
                    }
                } elseif (in_array($field['type'], ['datetimepicker']) && $request->get($prefix . $field['name']) != null) {
                    $data[$field['name']] = date('Y-m-d H:i:s', strtotime($request->get($prefix . $field['name'])));
                } elseif (!in_array($field['type'], ['re_password', 'dynamic', 'inner'])) {
                    $data[$field['name']] = $request->get($prefix . $field['name']);
                }
            }
        }
        return $data;
    }

    public function getDataUpdate(Request $request, $item) {
        $data['module'] = $this->module;
        $data['result'] = $item;
        $data['page_title'] = 'Chỉnh sửa ' . $this->module['label'];
        $data['page_type'] = 'update';
        return $data;
    }

    function renderSlug($id, $name, $field = 'slug')
    {
        $slug = str_slug($name, '-');
        $item = $this->model->where($field, '=', $slug);
        if ($id) $item = $item->where('id', '!=', $id);

        if ($item->count() > 0) {
            return $slug . '-' . time();
        }
        return $slug;
    }

    public function searchForSelect2(Request $request)
    {
        $col2 = $request->get('col2', '') == '' ? '' : ', ' . $request->get('col2');

        $data = $this->model->selectRaw('id, ' . $request->col . $col2)->where($request->col, 'like', '%' . $request->keyword . '%');

        if ($request->where != '') {
            $data = $data->whereRaw(urldecode(str_replace('&#039;', "'", $request->where)));
        }

        $data = $data->limit(10)->get();

        return response()->json([
            'status' => true,
            'items' => $data
        ]);
    }

    public function duplicate(Request $request, $id)
    {
        try {


            $item = $this->model->find($id);
            $new_item = $item->replicate();
            $new_item->save();
            CommonHelper::one_time_message('success', 'Nhân bản thành công! Bạn đang ở bản ghi mới');
            return redirect('/admin/'.$this->module['code'].'/' . $new_item->id);
        } catch (\Exception $ex) {
            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            return redirect()->withInput();
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
//            $this->adminLog($request,$item,'publish');
            CommonHelper::flushCache($this->module['table_name']);
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

    public function ajaxLoadTableBasicData(Request $request) {
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

        return view(config('core.admin_theme').".partials.table_basic_data_view")->with($data);
    }
}
