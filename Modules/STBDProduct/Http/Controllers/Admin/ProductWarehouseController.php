<?php

namespace Modules\STBDProduct\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Auth;
use Illuminate\Http\Request;
use Modules\STBDProduct\Models\Category;
use Modules\STBDProduct\Models\Product;
use Validator;

class ProductWarehouseController extends CURDBaseController
{
    protected $module = [
        'code' => 'product_warehouse',
        'table_name' => 'products',
        'label' => 'Sản phẩm',
        'modal' => '\Modules\STBDProduct\Models\Product',
        'list' => [
            ['name' => 'image', 'type' => 'image', 'label' => 'Ảnh', 'width' => '60px'],
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Tên sản phẩm'],
            ['name' => 'code', 'type' => 'text', 'label' => 'Mã'],
            ['name' => 'final_price', 'type' => 'price_vi', 'label' => 'Giá bán'],
            ['name' => 'multi_cat', 'type' => 'custom', 'td' => 'stbdproduct::list.td.multi_cat', 'label' => 'Danh mục', 'object' => 'category_product'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên sản phẩm'],
                ['name' => 'code', 'type' => 'text', 'label' => 'Mã sản phẩm', 'group_class' => 'col-md-4'],
                ['name' => 'base_price', 'type' => 'text', 'label' => 'Giá ban đầu', 'class' => 'number_price', 'group_class' => 'col-md-4'],
                ['name' => 'final_price', 'type' => 'text', 'label' => 'Giá bán', 'class' => 'number_price', 'group_class' => 'col-md-4'],
                ['name' => 'multi_cat', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.multi_cat', 'label' => 'Danh mục sản phẩm', 'model' => \Modules\STBDProduct\Models\Category::class,
                    'object' => 'category_product', 'display_field' => 'name', 'multiple' => true, 'where' => 'type=5', 'des' => 'Danh mục đầu tiên chọn là danh mục chính'],
                ['name' => 'tags', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.tags', 'label' => 'Từ khóa sản phẩm', 'model' => \Modules\STBDProduct\Models\Category::class,
                    'object' => 'tag_product', 'display_field' => 'name', 'multiple' => true, 'where' => 'type=6'],
                ['name' => 'intro', 'type' => 'textarea', 'label' => 'Mô tả ngắn'],
                ['name' => 'content', 'type' => 'textarea_editor', 'label' => 'Nội dung'],
                ['name' => 'status', 'type' => 'checkbox', 'label' => 'Kích hoạt', 'value' => 1, 'group_class' => 'col-md-6'],
                ['name' => 'order_no', 'type' => 'number', 'label' => 'Thứ tự ưu tiên', 'value' => 0, 'group_class' => 'col-md-6', 'des' => 'Số to ưu tiên hiển thị trước'],
            ],

            'image_tab' => [
                ['name' => 'image', 'type' => 'file_editor', 'label' => 'Ảnh sản phẩm'],
                ['name' => 'image_extra', 'type' => 'multiple_image', 'count' => '6', 'label' => 'Ảnh khác'],
            ],

            'seo_tab' => [
                ['name' => 'slug', 'type' => 'slug', 'class' => 'required', 'label' => 'Slug', 'des' => 'Đường dẫn sản phẩm trên thanh địa chỉ'],
                ['name' => 'meta_title', 'type' => 'text', 'label' => 'Meta title'],
                ['name' => 'meta_description', 'type' => 'text', 'label' => 'Meta description'],
                ['name' => 'meta_keywords', 'type' => 'text', 'label' => 'Meta keywords'],
            ],
        ],
    ];

    protected $filter = [
        'name' => [
            'label' => 'Tên sản phẩm',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'code' => [
            'label' => 'Mã sản phẩm',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'final_price' => [
            'label' => 'Giá bán',
            'type' => 'number',
            'query_type' => 'like'
        ],
        'category_id' => [
            'label' => 'Danh mục',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'object' => 'category_product',
            'model' => \Modules\STBDProduct\Models\Category::class,
            'query_type' => 'custom'
        ],
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('stbdproduct::list')->with($data);
    }

    public function appendWhere($query, $request)
    {
        //  Lấy các sản phẩm trong kho
//        $query = $query->where('company_id', null);

        //  Lọc theo danh mục
        if (!is_null($request->get('category_id'))) {
            $query = $query->where('multi_cat', 'LIKE', '%|' . $request->category_id . '|%');
        }

        return $query;
    }

    public function add(Request $request)
    {
        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('stbdproduct::add')->with($data);
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
                    }
//                    $data['company_id'] = \Auth::guard('admin')->user()->last_company_id;
                    #

                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
                        $this->afterAddLog($request, $this->model);

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
            return view('stbdproduct::edit')->with($data);
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
                }
                #

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

    public function getToMyCompany(Request $request, $id)
    {
        $product_new = $this->duplicate($request, $id);
        $order = \Modules\JdesSetting\Models\Editor::where('product_ids', 'like', '%|'.$id.'|%')->first();
        $order->product_ids = $order->product_ids . $product_new->id . '|';
        $order->save();
        CommonHelper::one_time_message('success', 'Đã lấy sản phẩm về kho. Sản phẩm mới đã được tạo.');
        return redirect('/admin/product/' . $product_new->id);
    }

    public function duplicate(Request $request, $id)
    {
        $poduct = Product::find($id);
        $poduct_new = $poduct->replicate();
//        $poduct_new->slug = $poduct->slug . $poduct_new->company_id;
//        $poduct_new->company_id = \Auth::guard('admin')->user()->last_company_id;
        $poduct_new->admin_id = \Auth::guard('admin')->user()->id;
        $poduct_new->save();
        return $poduct_new;
    }
}
