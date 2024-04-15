<?php

namespace Modules\STBDProduct\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Auth;
use Illuminate\Http\Request;
use Modules\STBDProduct\Models\Category;
use Modules\STBDProduct\Models\Manufacturer;
use Modules\STBDProduct\Models\Post;
use Modules\STBDProduct\Models\Product;
use Modules\STBDProduct\Models\PropertieValue;
use Modules\STBDProduct\Models\PropertyValue;
use Validator;

class ManufacturerController extends CURDBaseController
{
    protected $module = [
        'code' => 'manufacturer',
        'table_name' => 'manufacturer',
        'label' => 'Thương hiệu',
        'modal' => '\Modules\STBDProduct\Models\Manufacturer',
        'list' => [
            ['name' => 'image', 'type' => 'image', 'foder' => 'manufacturer_files', 'label' => 'Ảnh', 'filter' => false],
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Tên'],
            ['name' => 'status', 'type' => 'status', 'label' => 'Trạng thái'],
            ['name' => 'name', 'route_name' => 'product', 'model' => 'Modules\STBDProduct\Models\Product', 'type' => 'custom',
                'td' => 'stbdproduct::list.td.manufacturer.count_product_by_manufacturer', 'label' => 'Số sản phẩm',],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Cập nhật'],
            ['name' => 'updated_at', 'type' => 'custom',
                'td' => 'stbdproduct::list.td.manufacturer.view_frontend','label' => 'Xem']
        ],
        'form' => [
            'general_tab' => [

                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên'],
                ['name' => 'filers_category', 'type' => 'select2_model', 'class' => '', 'label' => 'Danh mục', 'model' => Category::class, 'where' => 'type = 5',
                    'object' => 'category', 'display_field' => 'name', 'multiple' => true],
                ['name' => 'filers_post', 'type' => 'select2_model', 'class' => '', 'label' => 'Tin tức', 'model' => Post::class,
                    'object' => 'post', 'display_field' => 'name', 'multiple' => true],
                ['name' => 'intro', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.count_char_intro', 'class' => '', 'label' => 'Mô tả ngắn (Tối đa 120 ký tự)'],
                ['name' => 'discount', 'type' => 'number', 'class' => '',
                    'label' => 'Triết khấu cho các sản phẩm trong danh mục (%)', 'inner' => 'min=0 max=100', 'value' => 0, 'group_class' => 'col-md-6'],
                ['name' => 'discount_enable', 'type' => 'checkbox', 'label' => 'Thực thi triết khấu', 'value' => 0, 'group_class' => 'col-md-6'],
                ['name' => 'order_no', 'type' => 'number', 'class' => '', 'label' => 'Thứ tự', 'value' => 1, 'group_class' => 'col-md-6'],
                ['name' => 'status', 'type' => 'checkbox', 'class' => '', 'label' => 'Kích hoạt', 'value' => 1, 'group_class' => 'col-md-6'],

            ],

            'image_tab' => [
                ['name' => 'image', 'type' => 'file_editor', 'class' => '', 'label' => 'Ảnh mô tả'],
                ['name' => 'image_extra', 'type' => 'multiple_image_editor_no_limit', 'class' => '', 'label' => 'Ảnh khác'],
            ],

            'seo_tab' => [
                ['name' => 'slug', 'type' => 'slug', 'class' => 'required', 'label' => 'slug', 'des' => 'Đường dẫn thương hiệu trên thanh địa chỉ'],
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
        'label' => 'ID, tên',
        'fields' => 'id, name'
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

        return view('stbdproduct::manufacturer.list')->with($data);
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
                return view('stbdproduct::manufacturer.add')->with($data);
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
                    if ($request->has('multi_cat')) {
                        $data['multi_cat'] = '|' . implode('|', $request->multi_cat) . '|';
                        $data['category_id'] = $request->multi_cat[0];
                    }
                    if ($request->has('tags')) {
                        $data['tags'] = '|' . implode('|', $request->tags) . '|';
                    }
                    if ($request->has('image_extra')) {
                        $data['image_extra'] = implode('|', $request->image_extra);
                        $data['image_extra']  = str_replace('https://'.env('DOMAIN').'/public/filemanager/userfiles/', '', $data['image_extra']);
                    }
                    if ($request->has('input_image_extra')) {
                        $data['input_image_extra'] = implode('|', $request->input_image_extra);
                    }

//                    $data['company_id'] = \Auth::guard('admin')->user()->last_company_id;
                    #
//dd($data);
                    if ($request->has('filers_category')) {
                        $data['filers_category'] = '|' . implode('|', $request->filers_category) . '|';
                    }
                    if ($request->has('filers_post')) {
                        $data['filers_post'] = '|' . implode('|', $request->filers_post) . '|';
                    }
                    $data['discount_enable'] = 0;

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
            return view('stbdproduct::manufacturer.edit')->with($data);
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
                if ($request->has('multi_cat')) {
                    $data['multi_cat'] = '|' . implode('|', $request->multi_cat) . '|';
                    $data['category_id'] = $request->multi_cat[0];
                }
                if ($request->has('tags')) {
                    $data['tags'] = '|' . implode('|', $request->tags) . '|';
                }
                if ($request->has('image_extra')) {
                    $data['image_extra'] = implode('|', $request->image_extra);
                    $data['image_extra']  = str_replace('https://'.env('DOMAIN').'/public/filemanager/userfiles/', '', $data['image_extra']);
                }
                if ($request->has('input_image_extra')) {
                    $data['input_image_extra'] = implode('|', $request->input_image_extra);
                }
                #

                if ($request->has('filers_category')) {
                    $data['filers_category'] = '|' . implode('|', $request->filers_category) . '|';
                }
                if ($request->has('filers_post')) {
                    $data['filers_post'] = '|' . implode('|', $request->filers_post) . '|';
                }
                $data['discount_enable'] = 0;

                foreach ($data as $k => $v) {
                    $item->$k = $v;
                }
                if ($item->save()) {

                    //  Thực thi triết khấu
                    if ($request->has('discount_enable')) {
                        $products = Product::where('manufacture_id', $item->id)->orderBy('id', 'desc')->where('status', 1)->where('type', 1)->get();
                        if (!empty($products)){
                            foreach ($products as $update_val){
                                $product = Product::find($update_val->id);
                                $product->final_price = $update_val->base_price * (100-($request->discount))/100;
                                $product->save();
                            }
                        }
                        //  Thuc hien truy van tat cac SP trong danh muc va triet khau
                        //  $request->discount = % triet khau : gia tri 0 - 100
                    }

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
            CommonHelper::one_time_message('success', 'Xóa thành công!');
            CommonHelper::flushCache($this->module['table_name']);
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
