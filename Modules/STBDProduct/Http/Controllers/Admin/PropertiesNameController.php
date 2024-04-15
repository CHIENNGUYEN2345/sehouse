<?php

namespace Modules\STBDProduct\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Auth;
use Illuminate\Http\Request;
use Modules\STBDProduct\Models\Category;
use Modules\STBDProduct\Models\Manufacturer;
use Modules\STBDProduct\Models\Product;
use Modules\STBDProduct\Models\PropertieValue;
use Modules\STBDProduct\Models\PropertyValue;
use Validator;

class PropertiesNameController extends CURDBaseController
{
    protected $module = [
        'code' => 'properties_name',
        'table_name' => 'properties_name',
        'label' => 'Thuộc tính',
        'modal' => '\Modules\STBDProduct\Models\PropertieName',
        'list' => [
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Tên'],
            ['name' => 'show_field_search', 'type' => 'status', 'label' => 'Hiển thị search'],
            ['name' => 'show_filed_filter', 'type' => 'status', 'label' => 'Hiển thị lọc'],
            ['name' => 'show_field_specifications', 'type' => 'status', 'label' => 'Hiển thị thông số kỹ thuật'],
            ['name' => 'count_properties_value', 'type' => 'custom','td' => 'stbdproduct::list.td.count_properties_value', 'label' => 'Số giá trị', 'sort' => false],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên'],
                ['name' => 'order_no', 'type' => 'number', 'class' => '', 'label' => 'Thứ tự', 'value' => 1],
                ['name' => 'show_field_search', 'type' => 'checkbox', 'class' => '', 'label' => 'Hiển thị search', 'value' => 1, 'group_class' => 'col-md-6'],
                ['name' => 'show_filed_filter', 'type' => 'checkbox', 'class' => '', 'label' => 'Hiển thị lọc', 'value' => 1, 'group_class' => 'col-md-6'],
                ['name' => 'show_field_specifications', 'type' => 'checkbox', 'class' => '', 'label' => 'Hiển thị thông số kỹ thuật', 'value' => 1, 'group_class' => 'col-md-6'],
                ['name' => 'price_option', 'type' => 'checkbox', 'class' => '', 'label' => 'Hiện thị trên báo giá', 'value' => 1, 'group_class' => 'col-md-6'],
            ],
            'properties_value_tab' => [
                ['name' => 'iframe', 'type' => 'iframe', 'class' => 'col-xs-12 col-md-8 padding-left', 'src' => '/admin/properties_value?properties_name_id={id}', 'inner' => 'style="min-height: 785px;"'],
            ],


        ],
    ];

    protected $quick_search = [
        'label' => 'ID, tên',
        'fields' => 'id, name'
    ];

    protected $filter = [
        'show_field_search' => [
            'label' => 'Hiển thị search',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => 'Chọn',
                0 => 'Ẩn',
                1 => 'Hiển thị',
            ],
        ],
        'show_filed_filter' => [
            'label' => 'Hiển thị lọc',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => 'Chọn',
                0 => 'Ẩn',
                1 => 'Hiển thị',
            ],
        ],
        'show_field_specifications' => [
            'label' => 'Hiển thị thông số kỹ thuật',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => 'Chọn',
                0 => 'Ẩn',
                1 => 'Hiển thị',
            ],
        ],
    ];

    public function getIndex(Request $request)
    {

        $data = $this->getDataList($request);

        return view('stbdproduct::properties_name.list')->with($data);
    }

    public function add(Request $request)
    {
        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('stbdproduct::properties_name.add')->with($data);
            } else if ($_POST) {
//                dd($request->all());
                $validator = Validator::make($request->all(), [
                    'name' => 'required'
                ], [
                    'name.required' => 'Bắt buộc phải nhập tên',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    //  Tùy chỉnh dữ liệu insert
                    unset($data['iframe']);
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


                    if ($request->return_direct == 'save_create') {
                        return redirect('admin/' . $this->module['code'] . '/add');
                    }
                    return redirect('admin/' . $this->module['code'] . '/' . $this->model->id);
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
            return view('stbdproduct::properties_name.edit')->with($data);
        } else if ($_POST) {
//            dd($request->all());
            $validator = Validator::make($request->all(), [
                'name' => 'required'
            ], [
                'name.required' => 'Bắt buộc phải nhập tên',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            } else {
                $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                //  Tùy chỉnh dữ liệu insert
                unset($data['iframe']);
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

                if ($request->return_direct == 'save_continue') {
                    return redirect('admin/' . $this->module['code'] . '/' . $item->id);
                } elseif ($request->return_direct == 'save_create') {
                    return redirect('admin/' . $this->module['code'] . '/add');
                }

                return redirect('admin/' . $this->module['code']);
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

            //  Không được xóa dữ liệu của cty khác, cty mình ko tham gia
//            if (strpos(Auth::guard('admin')->user()->company_ids, '|' . $item->company_id . '|') === false) {
//                CommonHelper::one_time_message('error', 'Bạn không có quyền xóa!');
//                return back();
//            }

            $item->delete();
            $this->adminLog($request,$item,'delete');
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

//    public function searchForSelect2(Request $request)
//    {
//        $data = $this->model->select([$request->col, 'id'])->where($request->col, 'like', '%' . $request->keyword . '%');
//
//        if ($request->where != '') {
//            $data = $data->whereRaw(urldecode(str_replace('&#039;', "'", $request->where)));
//        }
//        if (@$request->company_id != null) {
//            $data = $data->where('company_id', $request->company_id);
//        }
//        $data = $data->limit(5)->get();
//        return response()->json([
//            'status' => true,
//            'items' => $data
//        ]);
//    }
}
