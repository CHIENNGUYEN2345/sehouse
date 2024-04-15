<?php

namespace Modules\STBDProduct\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Auth;
use Illuminate\Http\Request;
use Modules\STBDProduct\Models\Category;
use Modules\STBDProduct\Models\Manufacturer;
use Modules\STBDProduct\Models\Product;
use Modules\STBDProduct\Models\PropertieName;
use Modules\STBDProduct\Models\PropertieValue;
use Modules\STBDProduct\Models\PropertyValue;
use Validator;

class PropertiesValueController extends CURDBaseController
{
    protected $module = [
        'code' => 'properties_value',
        'table_name' => 'properties_value',
        'label' => 'Giá trị thuộc tính',
        'modal' => '\Modules\STBDProduct\Models\PropertieValue',
        'list' => [
            ['name' => 'value', 'type' => 'text_edit', 'label' => 'Giá trị'],
            ['name' => 'link', 'type' => 'custom','td' => 'stbdproduct::list.td.link_property_value',  'label' => 'Link'],
            ['name' => 'so_sp',  'type' => 'custom','td' => 'stbdproduct::list.td.count_product_by_property_value', 'label' => 'Số sản phẩm'],
            ['name' => 'properties_name_id', 'type' => 'custom','td' => 'stbdproduct::list.td.belongsTo', 'label' => 'Thuộc tính', 'belongsTo' => 'property_name',
                'display_field' => 'name'],
            ['name' => 'status', 'type' => 'status', 'label' => 'Trạng thái'],
        ],
        'form' => [
            'general_tab' => [
                /*['name' => 'properties_name_id', 'type' => 'select2_model', 'class' => '', 'label' => 'Thuộc tính', 'model' => PropertieName::class,
                    'object' => 'property_name', 'display_field' => 'name', 'display_field2' => 'id'],*/
                ['name' => 'value', 'type' => 'text', 'class' => 'required', 'label' => 'Giá trị'],
                ['name' => 'link', 'type' => 'text','label' => 'Link'],
                /*['name' => 'properties_name_id',
                    'type' => 'select2_ajax_model', 'class' => '',
                    'label' => 'Thuộc tính',
                    'object' => 'properties_name',
                    'model' => PropertieName::class,
                    'display_field' => 'name',
                    'display_field2' => 'id'],*/

                ['name' => 'description', 'type' => 'textarea', 'class' => '', 'label' => 'Mô tả'],
                ['name' => 'order_no', 'type' => 'number', 'class' => '', 'label' => 'Thứ tự', 'value' => 1],
                ['name' => 'status', 'type' => 'checkbox', 'class' => '', 'label' => 'Kích hoạt', 'value' => 1],
            ],

        ],
    ];

    protected $quick_search = [
        'label' => 'ID, giá trị',
        'fields' => 'id, value'
    ];

    protected $filter = [
        'properties_name_id' => [
            'label' => '',
            'type' => 'text',
            'query_type' => '=',
            'class' => 'hidden',
        ],
    ];

    public function getIndex(Request $request)
    {

        $data = $this->getDataList($request);

        return view('stbdproduct::property_value.list')->with($data);
    }

    public function add(Request $request)
    {
        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('stbdproduct::property_value.add')->with($data);
            } else if ($_POST) {
//                dd($request->all());
                $validator = Validator::make($request->all(), [
                    'value' => 'required'
                ], [
                    'value.required' => 'Bắt buộc phải nhập giá trị',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    //  Tùy chỉnh dữ liệu insert
                    $data['properties_name_id'] = $request->properties_name_id;
                    #
                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
                        $this->adminLog($request,$this->model,'add');
                        $this->afterAddLog($request, $this->model);
                        CommonHelper::flushCache($this->module['table_name']);
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

                    if ($request->has('properties_name_id')) {
                        return redirect('admin/' . $this->module['code'] . '?properties_name_id=' . $request->properties_name_id);
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
            return view('stbdproduct::property_value.edit')->with($data);
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
//                $data['properties_name_id'] = $request->properties_name_id;
                #

                foreach ($data as $k => $v) {
                    $item->$k = $v;
                }
                if ($item->save()) {
                    $this->adminLog($request,$item,'edit');
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

                if ($request->has('properties_name_id')) {
                    return redirect('admin/' . $this->module['code'] . '?properties_name_id=' . $request->properties_name_id);
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
            $this->adminLog($request,$item,'publish');
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

    public function delete(Request $request)
    {
        try {
            $item = $this->model->find($request->id);
            $properties_name_id = $item->properties_name_id;

            //  Không được xóa dữ liệu của cty khác, cty mình ko tham gia
//            if (strpos(Auth::guard('admin')->user()->company_ids, '|' . $item->company_id . '|') === false) {
//                CommonHelper::one_time_message('error', 'Bạn không có quyền xóa!');
//                return back();
//            }

            $item->delete();
            $this->adminLog($request,$item,'delete');
            CommonHelper::flushCache($this->module['table_name']);
            CommonHelper::one_time_message('success', 'Xóa thành công!');

            return redirect('admin/' . $this->module['code'] . '?properties_name_id=' . $properties_name_id);
        } catch (\Exception $ex) {
            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            return back();
        }
    }

    public function multiDelete(Request $request)
    {
        try {
            $ids = $request->ids;
            $this->adminLog($request,$ids,'multi_delete');
            if (is_array($ids)) {
                $this->model->whereIn('id', $ids)->delete();
            }
            CommonHelper::flushCache($this->module['table_name']);
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

        $data = $this->model->join('properties_name', 'properties_name.id', '=', 'properties_value.properties_name_id')
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
}
