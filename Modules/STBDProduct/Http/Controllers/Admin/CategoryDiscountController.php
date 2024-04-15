<?php

namespace Modules\STBDProduct\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\STBDProduct\Models\Category;
use Modules\STBDProduct\Models\Product;
use Modules\STBDProduct\Models\PropertieName;
use Validator;

class CategoryDiscountController extends CURDBaseController
{
    protected $whereRaw = 'type in (7)';

    protected $module = [
        'code' => 'category_discount',
        'table_name' => 'categories',
        'label' => 'Chuyên mục khuyến mãi',
        'modal' => '\Modules\STBDProduct\Models\Category',
        'list' => [

            ['name' => 'id', 'type' => 'text', 'label' => 'ID'],
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Tên'],
            ['name' => 'slug', 'type' => 'text', 'label' => 'URL'],
            ['name' => 'order_no', 'type' => 'text', 'label' => 'Thứ tự'],
            ['name' => 'status', 'type' => 'status', 'label' => 'Trạng thái'],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Cập nhật']
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên',],
                ['name' => 'parent_id', 'type' => 'custom','field' => 'stbdproduct::form.fields.select_model_tree', 'class' => '', 'label' => 'Danh mục cha', 'model' => Category::class, 'where' => 'type = 7'],
                ['name' => 'fields_id_product', 'class' => '', 'label' => 'Chọn sản phẩm', 'type' => 'custom','field'=>'stbdproduct::form.fields.select_id_product', 'model' => Product::class,
                    'object' => 'category_post', 'where' => 'type in (7)', 'display_field' => 'name', 'display_field2' => 'id', 'multiple' => true],
                ['name' => 'banner', 'type' => 'file_editor', 'class' => 'col-md-6', 'label' => 'Ảnh banner'],
                ['name' => 'image', 'type' => 'file_editor', 'class' => 'col-md-6', 'label' => 'Ảnh đại diện'],
                ['name' => 'intro', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.editor2', 'class' => '', 'label' => 'Mô tả'],
                ['name' => 'allows_users_create', 'type' => 'select', 'options' => [1 => 'Cho phép', 0 => 'Không cho phép'], 'class' => '', 'label' => 'Cho phép người dùng tạo DL', 'value' => '0'],
                ['name' => 'style', 'type' => 'select', 'options' =>
                    [
                        0 => 'Nhỏ / Có giá',
                        1 => 'To / không có giá',
                        2 => 'Dự án',
                    ], 'class' => '', 'label' => 'Kiểu giao diện'],
                ['name' => 'status', 'type' => 'select', 'options' => [1 => 'Kích hoạt', 0 => 'Ẩn'], 'class' => '', 'label' => 'Trạng thái', 'value' => '1'],
                ['name' => 'order_no', 'type' => 'number', 'class' => '', 'label' => 'Thứ tự', 'value' => 0]
            ],
            'seo_tab' => [
                ['name' => 'slug', 'type' => 'slug', 'class' => 'required', 'label' => 'Slug', 'des' => 'Đường dẫn sản phẩm trên thanh địa chỉ'],
                ['name' => 'meta_title', 'type' => 'text', 'label' => 'Meta title'],
                ['name' => 'meta_description', 'type' => 'text', 'label' => 'Meta description'],
                ['name' => 'meta_keywords', 'type' => 'text', 'label' => 'Meta keywords'],
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

        return view('stbdproduct::category_discount.list')->with($data);
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
            return view('stbdproduct::category_discount.add')->with($data);
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
                $data['type'] = 7;
                #

                if ($request->has('fields_id_product')) {
                    $data['fields_id_product'] = '|' . implode('|', $request->fields_id_product) . '|';
                }
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


            if (!is_object($item)) abort(404);
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('stbdproduct::category_discount.edit')->with($data);
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
