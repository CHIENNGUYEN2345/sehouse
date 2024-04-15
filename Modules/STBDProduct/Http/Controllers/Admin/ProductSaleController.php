<?php

namespace Modules\STBDProduct\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use MicrosoftAzure\Storage\Common\Internal\Validate;
use Modules\STBDProduct\Models\Category;
use Modules\STBDProduct\Models\Manufacturer;
use Modules\STBDProduct\Models\Product;
use Modules\STBDProduct\Models\PropertyValue;

class ProductSaleController extends CURDBaseController
{
    protected $module = [
        'code' => 'product_sale',
        'table_name' => 'product_sale',
        'label' => 'Sản phẩm khuyến mãi',
        'modal' => '\Modules\STBDProduct\Models\ProductSale',
        'list' => [
            ['name' => 'id', 'type' => 'text', 'label' => 'ID'],
            ['name' => 'id_product_sale', 'type' => 'custom', 'td' => 'stbdproduct::list.td.product_sale.multi_object', 'model' => Product::class, 'object' => 'product', 'display_field' => 'name', 'label' => 'Sản phẩm làm khuyến mãi'],
            ['name' => 'id_product', 'type' => 'custom', 'td' => 'stbdproduct::list.td.product_sale.multi_object', 'model' => Product::class, 'object' => 'product', 'display_field' => 'name', 'label' => 'Sản phẩm được khuyến mãi'],
            ['name' => 'category_ids', 'type' => 'custom', 'td' => 'stbdproduct::list.td.product_sale.multi_object', 'model' => Category::class, 'object' => 'category_product', 'display_field' => 'name', 'label' => 'Danh mục được khuyến mãi'],
            ['name' => 'manufacturer_ids', 'type' => 'custom', 'td' => 'stbdproduct::list.td.product_sale.multi_object', 'model' => Manufacturer::class, 'object' => 'product', 'display_field' => 'name', 'label' => 'Hãng được khuyến mãi'],
            ['name' => 'time_start', 'type' => 'datetime_vi', 'label' => 'Bắt đầu'],
            ['name' => 'time_end', 'type' => 'datetime_vi', 'label' => 'Kết thúc'],
            ['name' => 'id', 'type' => 'custom', 'td' => 'stbdproduct::list.td.action', 'class' => '', 'label' => 'Xem'],

        ],
        'form' => [
            'general_tab' => [
                ['name' => 'id_product_sale', 'type' => 'select2_model', 'class'=>'required', 'label' => 'Sản phẩm làm khuyến mãi', 'model' => Product::class,
                    'display_field' => 'name', 'display_field2' => 'id', 'multiple' => true],
                ['name' => 'category_ids', 'type' => 'select2_model', 'class' => '', 'label' => 'Danh mục được khuyến mãi', 'model' => Category::class,
                    'display_field' => 'name', 'display_field2' => 'id', 'multiple' => true, 'where' => 'type in (5)'],
                ['name' => 'manufacturer_ids', 'type' => 'select2_model', 'class' => '', 'label' => 'Hãng được khuyến mãi', 'model' => Manufacturer::class,
                    'display_field' => 'name', 'display_field2' => 'id', 'multiple' => true],
                ['name' => 'id_product', 'type' => 'select2_model', 'class'=>'', 'label' => 'Sản phẩm được khuyến mãi', 'model' => Product::class,
                    'display_field' => 'name', 'display_field2' => 'id', 'multiple' => true],
                ['name' => 'time_start', 'type' => 'custom','field'=>'stbdproduct::form.fields.input_datetime-local', 'label' => 'Thời gian bắt đầu'],
                ['name' => 'time_end',  'type' => 'custom','field'=>'stbdproduct::form.fields.input_datetime-local', 'label' => 'Thời gian kết thúc'],
                ['name' => 'gift_max',  'type' => 'number', 'label' => 'Số quà tối đa cho phép người dùng nhận'],
            ],
        ],
    ];

    protected $filter = [

    ];

    public function getIndex(Request $request)
    {

        $data = $this->getDataList($request);

        return view('stbdproduct::product_sale.list')->with($data);
    }

    public function appendWhere($query, $request)
    {
        //  Nếu không có quyền xem toàn bộ dữ liệu thì chỉ được xem các dữ liệu của công ty mình
//        if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'view_all_data')) {
//            $query = $query->where('company_id', \Auth::guard('admin')->user()->last_company_id);
//        }

        //  Lọc theo danh mục
        if (!is_null($request->get('category_id'))) {
            $category = Category::find($request->category_id);
            if (is_object($category)) {
                $query = $query->where(function ($query) use ($category) {
                    $cat_childs = $category->childs; //  Lấy các id của danh mục con
                    $query->orWhere('multi_cat', 'LIKE', '%|' . $category->id . '|%');  // truy vấn các tin thuộc danh mục hiện tại
                    foreach ($cat_childs as $cat_child) {
                        $query->orWhere('multi_cat', 'LIKE', '%|' . $cat_child->id . '|%');    //  truy vấn các tin thuộc các danh mục con của danh mục hiện tại
                    }
                });
            }
        }
        if (!is_null($request->get('tags'))) {
            $query = $query->where('multi_cat', 'LIKE', '%|' . $request->category_id . '|%');
        }
        return $query;
    }

    public function add(Request $request)
    {
        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('stbdproduct::product_sale.add')->with($data);
            } else if ($_POST) {
//                dd($request->all());
                $validator = Validator::make($request->all(), [
                    'id_product_sale' => 'required'
                ], [
                    'id_product_sale.required' => 'Bắt buộc phải nhập sản phẩm làm khuyến mãi',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    //  Tùy chỉnh dữ liệu insert
                if ($request->has('id_product_sale')) {
                    $data['id_product_sale'] = '|' . implode('|', $request->id_product_sale) . '|';
                }
                if ($request->has('category_ids')) {
                    $data['category_ids'] = '|' . implode('|', $request->category_ids) . '|';
                }
                if ($request->has('id_product')) {
                    $data['id_product'] = '|' . implode('|', $request->id_product) . '|';
                }
                if ($request->has('manufacturer_ids')) {
                    $data['manufacturer_ids'] = '|' . implode('|', $request->manufacturer_ids) . '|';
                }
//                    $data['company_id'] = \Auth::guard('admin')->user()->last_company_id;
                    #
//dd($data);
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

                    if ($request->return_direct == 'save_continue') {
                        return redirect('admin/' . $this->module['code'] . '/' . $this->model->id);
                    } elseif ($request->return_direct == 'save_create') {
                        return redirect('admin/' . $this->module['code'] . '/add');
                    } elseif ($request->return_direct == 'save_editor') {
                        return redirect('admin/' . $this->module['code'] . '/' . $this->model->id . '/editor');
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
        $item = $this->model->find($request->id);

        //  Chỉ sửa được liệu công ty mình đang vào
//            if (strpos(\Auth::guard('admin')->user()->company_ids, '|' . $item->company_id . '|') === false) {
//                CommonHelper::one_time_message('error', 'Bạn không có quyền!');
//                return back();
//            }

        if (!is_object($item)) abort(404);
        if (!$_POST) {
            $data = $this->getDataUpdate($request, $item);
            return view('stbdproduct::product_sale.edit')->with($data);
        } else if ($_POST) {
//            dd($request->all());
                $validator = Validator::make($request->all(), [
                    'id_product_sale' => 'required'
                ], [
                    'id_product_sale.required' => 'Bắt buộc phải nhập sản phẩm làm khuyến mãi',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                //  Tùy chỉnh dữ liệu insert
                if ($request->has('id_product_sale')) {
                    $data['id_product_sale'] = '|' . implode('|', $request->id_product_sale) . '|';
                }
                if ($request->has('category_ids')) {
                    $data['category_ids'] = '|' . implode('|', $request->category_ids) . '|';
                }
                if ($request->has('id_product')) {
                    $data['id_product'] = '|' . implode('|', $request->id_product) . '|';
                }
                if ($request->has('manufacturer_ids')) {
                    $data['manufacturer_ids'] = '|' . implode('|', $request->manufacturer_ids) . '|';
                }

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
}
