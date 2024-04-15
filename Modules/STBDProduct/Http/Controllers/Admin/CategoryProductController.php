<?php

namespace Modules\STBDProduct\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Auth;
use Illuminate\Http\Request;
use Modules\STBDProduct\Models\Category;
use Modules\STBDProduct\Models\Product;
use Modules\STBDProduct\Models\PropertieName;
use Validator;

class CategoryProductController extends CURDBaseController
{

    protected $whereRaw = 'type in (5)';

    protected $module = [
        'code' => 'category_product',
        'table_name' => 'categories',
        'label' => 'Chuyên mục sản phẩm',
        'modal' => '\Modules\STBDProduct\Models\Category',
        'list' => [

            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Tên',],
            ['name' => 'slug', 'type' => 'text', 'label' => 'Đường dẫn',],
            ['name' => 'parent_id', 'type' => 'custom', 'td' => 'stbdproduct::list.td.parent_id', 'label' => 'Danh mục cha'],
            ['name' => 'order_no', 'type' => 'text', 'label' => 'Thứ tự',],
            ['name' => 'gift_value_max', 'type' => 'number', 'label' => 'Giá trị lớn nhất quà tặng',],
            ['name' => 'option_show_cate', 'type' => 'select', 'options' =>
                [
                    0 => 'Danh sách',
                    1 => 'Luới',
                ], 'label' => 'Kiểu hiển thị SP'],
            ['name' => 'cate_show', 'type' => 'select', 'options' =>
                [
                    0 => 'Hiện full logo',
                    1 => 'Hiển thị danh mục con',
                    2 => 'Hiển thị tin tức',
                    3 => 'Hiển thị Banner + link',
                    4 => 'Hiển thị sản phẩm bán chạy'
                ], 'label' => 'Kiểu hiển thị header',],
            ['name' => 'name', 'route_name' => 'product', 'model' => 'Modules\STBDProduct\Models\Product', 'type' => 'custom', 'td' => 'stbdproduct::list.td.cate_product.count_product_by_category', 'label' => 'Số sản phẩm',],
            ['name' => 'color', 'type' => 'text', 'label' => 'Màu'],
            ['name' => 'status', 'type' => 'status', 'label' => 'Trạng thái',],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Cập nhật',],
            ['name' => 'id', 'type' => 'custom', 'td' => 'stbdproduct::list.td.view_cate_frontend', 'label' => 'Xem'],
        ],
        'form' => [

            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên'],
                ['name' => 'fields_id_product', 'class' => '', 'label' => 'Chọn sản phẩm', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.select_id_product', 'model' => Product::class,
                    'object' => 'category_post', 'where' => 'type in (5)', 'display_field' => 'name', 'display_field2' => 'id', 'multiple' => true],
                ['name' => 'parent_id', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.select_model_tree', 'class' => '', 'label' => 'Danh mục cha', 'model' => Category::class, 'where' => 'type = 5'],
                ['name' => 'properties_name_id', 'type' => 'select2_ajax_model', 'object' => 'properties_name', 'class' => '', 'label' => 'Thuộc tính', 'model' => PropertieName::class,
                    'display_field' => 'name', 'display_field2' => 'id', 'multiple' => true],
                ['name' => 'intro', 'type' => 'textarea', 'class' => '', 'label' => 'giới thiệu'],
                ['name' => 'content', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.editor2', 'class' => '', 'label' => 'Nội dung'],
                ['name' => 'color', 'type' => 'text', 'label' => 'Màu'],
                ['name' => 'title_banner_child', 'type' => 'text', 'class' => '', 'label' => 'Tiêu đề'],
                ['name' => 'featured_description', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.editor2', 'class' => '', 'label' => 'Nội dung (Danh mục con)'],
                ['name' => 'video', 'type' => 'text', 'class' => '', 'label' => 'Url video'],
                ['name' => 'discount', 'type' => 'number', 'class' => '',
                    'label' => 'Triết khấu cho các sản phẩm trong danh mục (%)', 'inner' => 'min=0 max=100', 'value' => 0],
                ['name' => 'discount_enable', 'type' => 'checkbox', 'label' => 'Thực thi triết khấu', 'value' => 0],
            ],
            'option_tab' => [
                ['name' => 'featured', 'type' => 'select', 'options' =>
                    [
                        0 => 'Ẩn',
                        1 => 'Hiển thị',
                    ], 'class' => '', 'label' => 'Hiển thị icon trên trang chủ'],
                ['name' => 'show_menu', 'type' => 'select', 'options' =>
                    [
                        0 => 'Ẩn',
                        1 => 'Hiển thị',
                    ], 'class' => '', 'label' => 'Hiển thị menu - trang chủ'],
                ['name' => 'show_homepage', 'type' => 'select', 'options' =>
                    [
                        0 => 'Ẩn',
                        1 => 'Hiển thị',
                    ], 'class' => '', 'label' => 'Hiển thị khối DANH MỤC SẢN PHẨM - trang chủ'],
                ['name' => 'status', 'type' => 'select', 'options' => [1 => 'Kích hoạt', 0 => 'Ẩn'], 'class' => 'required', 'label' => 'Trạng thái', 'value' => '1'],
                ['name' => 'order_no', 'type' => 'number', 'class' => '', 'label' => 'Thứ tự', 'value' => 1],


                ['name' => 'category_manufacturer_first_title', 'type' => 'text', 'class' => '', 'label' => 'Tiền tố meta title danh muc - thương hiệu'],
                ['name' => 'category_manufacturer_last_title', 'type' => 'text', 'class' => '', 'label' => 'Hậu tố meta title danh muc - thương hiệu'],
                ['name' => 'category_manufacturer_first_description', 'type' => 'text', 'class' => '', 'label' => 'Tiền tố meta description danh muc - thương hiệu'],
                ['name' => 'category_manufacturer_last_description', 'type' => 'text', 'class' => '', 'label' => 'Hậu tố meta description danh muc - thương hiệu'],
                ['name' => 'category_manufacturer_frontend_des_first', 'type' => 'text', 'class' => '', 'label' => 'Tiền tố mô tả danh muc - thương hiệu trên'],
                ['name' => 'category_manufacturer_frontend_des_last', 'type' => 'text', 'class' => '', 'label' => 'Hậu tố mô tả danh muc - thương hiệu trên'],
                ['name' => 'gift_value_max', 'type' => 'number', 'class' => '', 'label' => 'Giá trị lớn nhất quà tặng'],

                ['name' => 'category_manufacturer_frontend_des_first_bot', 'type' => 'text', 'class' => '', 'label' => 'Tiền tố mô tả danh muc - thương hiệu dưới'],
                ['name' => 'category_manufacturer_frontend_des_last_bot', 'type' => 'text', 'class' => '', 'label' => 'Hậu tố mô tả danh muc - thương hiệu dưới'],
            ],

            'image_tab' => [
                ['name' => 'icon', 'type' => 'file_editor', 'class' => '', 'label' => 'Icon'],
                ['name' => 'banner', 'type' => 'file_editor', 'class' => '', 'label' => 'Ảnh (Menu danh mục)'],
                ['name' => 'image', 'type' => 'file_editor', 'class' => '', 'label' => 'Ảnh (Menu trang chủ)'],
                ['name' => 'banner_sidebar', 'type' => 'file_editor', 'class' => '', 'label' => 'Ảnh (Bottom trang chủ)'],
                ['name' => 'banner_child', 'type' => 'file_editor', 'class' => '', 'label' => 'Ảnh (Danh mục con)'],
                ['name' => 'banner_child_left', 'type' => 'file_editor', 'class' => '', 'label' => 'Ảnh (Danh mục con - left)'],
            ],
            'banner_tab' => [
                ['type' => 'select', 'options' =>
                    [
                        1 => 'Lưới',
                        0 => 'Danh sách',
                    ], 'class' => '', 'label' => 'Chọn kiểu hiển thị danh mục sản phẩm', 'name' => "option_show_cate"],
                ['type' => 'select', 'options' =>
                    [
                        0 => 'Hiện full logo',
                        1 => 'Hiển thị danh mục con',
                        2 => 'Hiển thị tin tức',
                        3 => 'Hiển thị Banner + link',
                        4 => 'Hiển thị sản phẩm bán chạy'
                    ], 'class' => '', 'label' => 'Kiểu hiển thị', 'name' => "cate_show"],
                ['name' => 'cate_new_1', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.select_model_tree', 'class' => '', 'label' => 'Id Tin tức 1', 'model' => Category::class, 'where' => 'type = 1'],
                ['name' => 'cate_new_2', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.select_model_tree', 'class' => '', 'label' => 'Id Tin tức 2', 'model' => Category::class, 'where' => 'type = 1'],
                ['name' => 'cate_new_3', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.select_model_tree', 'class' => '', 'label' => 'Id Tin tức 3', 'model' => Category::class, 'where' => 'type = 1'],

                ['name' => 'banner_1', 'type' => 'file_editor', 'class' => '', 'label' => 'banner 1'],
                ['name' => 'link_banner_1', 'type' => 'text', 'class' => '', 'label' => 'link banner1'],
                ['name' => 'banner_2', 'type' => 'file_editor', 'class' => '', 'label' => 'banner 2'],
                ['name' => 'link_banner_2', 'type' => 'text', 'class' => '', 'label' => 'link banner 2'],
                ['name' => 'banner_3', 'type' => 'file_editor', 'class' => '', 'label' => 'banner 3'],
                ['name' => 'link_banner_3', 'type' => 'text', 'class' => '', 'label' => 'link banner 3'],
            ],

            'seo_tab' => [
                ['name' => 'slug', 'type' => 'slug', 'class' => 'required', 'label' => 'Slug', 'des' => 'Đường dẫn sản phẩm trên thanh địa chỉ'],
                ['name' => 'meta_title', 'type' => 'text', 'label' => 'Meta title'],
                ['name' => 'meta_description', 'type' => 'text', 'label' => 'Meta description'],
                ['name' => 'meta_keywords', 'type' => 'text', 'label' => 'Meta keywords'],
                ['name' => 'meta_robot', 'type' => 'select', 'options' =>
                    [
                        '' => 'Chọn Meta robots',
                        'noindex, nofollow' => 'noindex, nofollow',
                        'index, follow' => 'index, follow',
                        'index, nofollow' => 'index, nofollow',
                        'noindex, follow' => 'noindex, follow',
                    ],
                    'label' => 'Meta robots'],
            ],

        ],
    ];

    protected $quick_search = [
        'label' => 'ID, tên, đường dẫn, mô tả',
        'fields' => 'id, name, slug, intro'
    ];

    protected $filter = [
        'status' => [
            'label' => 'Trạng thái',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => 'Trạng thái',
                0 => 'Không kích hoạt',
                1 => 'Kích hoạt',
            ],
        ],
    ];

    public function getIndex(Request $request)
    {


        $data = $this->getDataList($request);

        return view('stbdproduct::category.list')->with($data);
    }

    public function appendWhere($query, $request)
    {
        //  Nếu không có quyền xem toàn bộ dữ liệu thì chỉ được xem các dữ liệu của công ty mình
//        if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'view_all_data')) {
//            $query = $query->where('company_id', \Auth::guard('admin')->user()->last_company_id);
//        }

//        if (!@$request->search == 'true') {
//            $query = $query->whereNull('parent_id');
//        }
        return $query;
    }

    public function add(Request $request)
    {

        if (!$_POST) {
            $data = $this->getDataAdd($request);
            return view('stbdproduct::category.add')->with($data);
        } else if ($_POST) {

            $validator = Validator::make($request->all(), [
                'name' => 'required'
            ], [
                'name.required' => 'Bắt buộc phải nhập tên',
            ]);
            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'status' => false,
                        'error' => $validator->errors()->all(),
                    ]);
                }
                return back()->withErrors($validator)->withInput();

            } else {
                $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                //  Tùy chỉnh dữ liệu insert

                if ($request->has('fields_id_product')) {
                    $data['fields_id_product'] = '|' . implode('|', $request->fields_id_product) . '|';
                }
                if ($request->has('properties_name_id')) {
                    $data['properties_name_id'] = '|' . implode('|', $request->properties_name_id) . '|';
                }
                $data['type'] = 5;
                $data['discount_enable'] = 0;
//                $data['company_id'] = \Auth::guard('admin')->user()->last_company_id;
                #
                if ($request->ajax()) {
                    unset($data['image']);
                    unset($data['meta_title']);
                    unset($data['meta_description']);
                    unset($data['meta_keywords']);
                }
                foreach ($data as $k => $v) {
                    $this->model->$k = $v;
                }

                if ($this->model->save()) {

                    $this->adminLog($request, $this->model, 'add');
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
                }

                return redirect('admin/' . $this->module['code']);
            }
        }
//        } catch (\Exception $ex) {
//            CommonHelper::one_time_message('error', $ex->getMessage());
//            return redirect()->back()->withInput();
//        }
    }

    public function update(Request $request)
    {
        try {

            $item = $this->model->find($request->id);

            //  Chỉ sửa được liệu công ty mình đang vào
//            if (strpos(\Auth::guard('admin')->user()->company_ids, '|' . $item->company_id . '|') === false) {
//                CommonHelper::one_time_message('error', 'Bạn không có quyền!');
//                return back();
//            }

            if (!is_object($item)) abort(404);
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('stbdproduct::category.edit')->with($data);
            } else if ($_POST) {


                $validator = Validator::make($request->all(), [
                    'name' => 'required'
                ], [
                    'name.required' => 'Bắt buộc phải nhập tên',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
//                    dd($data);
                    //  Tùy chỉnh dữ liệu insert

                    if ($request->has('fields_id_product')) {
                        $data['fields_id_product'] = '|' . implode('|', $request->fields_id_product) . '|';
                    }
                    if ($request->has('properties_name_id')) {
                        $data['properties_name_id'] = '|' . implode('|', $request->properties_name_id) . '|';
                    }
                    if ($request->has('contact_info_name')) {
                        $data['contact_info'] = json_encode($this->getContactInfo($request));
                    }
                    $data['discount_enable'] = 0;

                    foreach ($data as $k => $v) {
                        $item->$k = $v;
                    }
                    if ($item->save()) {

                        //  Thực thi triết khẩu
                        if ($request->has('discount_enable')) {
                            $item_id = [];
                            $product_all = Product::pluck('multi_cat', 'id');
                            if ($item->parent_id == 0) {
                                $cate_child = Category::where('parent_id', $item->id)->get();
                                $cate_child->push($item);
                                if (!empty($cate_child)) {
                                    foreach ($cate_child as $val) {
                                        foreach ($product_all as $key => $vl) {
                                            if (in_array($val->id, explode('|', $vl)) == true) {
                                                array_push($item_id, $key);
                                            }
                                        }
                                    }
                                }
                            } else {
                                foreach ($product_all as $key => $vl) {
                                    if (in_array($item->id, explode('|', $vl)) == true) {
                                        array_push($item_id, $key);
                                    }
                                }
                            }
                            $item_id = array_unique($item_id);
                            $products = Product::whereIn('id', $item_id)->orderBy('id', 'desc')->where('status', 1)->where('type', 1)->get();
                            foreach ($products as $update_val) {
                                $product = Product::find($update_val->id);
                                $product->final_price = $update_val->base_price * (100 - ($request->discount)) / 100;
                                $product->save();
                            }
                            //  Thuc hien truy van tat cac SP trong danh muc va triet khau
                            //  $request->discount = % triet khau : gia tri 0 - 100
                        }

                        $this->adminLog($request, $item, 'edit');
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
        } catch (\Exception $ex) {
            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
//            CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function getContactInfo($request)
    {
        $contact_info = [];
        if ($request->has('contact_info_name')) {
            foreach ($request->contact_info_name as $k => $key) {
                if ($key != null) {
                    $contact_info[] = [
                        'name' => $key,
                        'tel' => $request->contact_info_tel[$k],
                        'email' => $request->contact_info_email[$k],
                        'note' => $request->contact_info_note[$k],
                    ];
                }
            }
        }
        return $contact_info;
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

            $this->adminLog($request, $item, 'publish');
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

            $this->adminLog($request, $item, 'delete');
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
            $this->adminLog($request, $ids, 'multi_delete');
            if (is_array($ids)) {
                $this->model->whereIn('id', $ids)->delete();
            }
            CommonHelper::one_time_message('success', 'Xóa thành công!');
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

}
