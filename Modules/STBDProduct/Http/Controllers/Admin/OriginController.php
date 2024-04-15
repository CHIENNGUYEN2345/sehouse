<?php

namespace Modules\STBDProduct\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Auth;
use Illuminate\Http\Request;
use Modules\STBDProduct\Models\Category;
use Modules\STBDProduct\Models\Manufacturer;
use Modules\STBDProduct\Models\Origin;
use Modules\STBDProduct\Models\Post;
use Modules\STBDProduct\Models\Product;
use Modules\STBDProduct\Models\PropertieValue;
use Modules\STBDProduct\Models\PropertyValue;
use Validator;

class OriginController extends CURDBaseController
{
    protected $module = [
        'code' => 'origin',
        'table_name' => 'origins',
        'label' => 'Sản phẩm',
        'modal' => '\Modules\STBDProduct\Models\Origin',
        'list' => [
            ['name' => 'name_origin', 'type' => 'text_edit', 'label' => 'Tên'],
            ['name' => 'status', 'type' => 'status', 'label' => 'Trạng thái'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name_origin', 'type' => 'text', 'class' => 'required', 'label' => 'Tên sản phẩm'],
                ['name' => 'status', 'type' => 'checkbox', 'class' => '', 'label' => 'Kích hoạt', 'value' => 1,  'group_class' => 'col-md-4'],
            ],
        ],
    ];

    protected $filter = [
        'name_origin' => [
            'label' => 'Tên sản phẩm',
            'type' => 'text',
            'query_type' => 'like'
        ],
    ];

    public function getIndex(Request $request)
    {

        $data = $this->getDataList($request);

        return view('stbdproduct::origin.list')->with($data);
    }

    public function appendWhere($query, $request)
    {
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
        if (!is_null($request->get('proprerties_id'))) {
            if ($request->proprerties_id == 0) {
                $query = $query->where('proprerties_id', null);
            } else {
                $query = $query->where('proprerties_id', '!=', null);
            }
        }
        return $query;
    }

    public function add(Request $request)
    {
        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('stbdproduct::origin.add')->with($data);
            } else if ($_POST) {
                $validator = Validator::make($request->all(), [
                    'name_origin' => 'required'
                ], [
                    'name_origin.required' => 'Bắt buộc phải nhập tên',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    //  Tùy chỉnh dữ liệu insert

                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if (!empty($data['final_price']) && !empty($data['base_price'])){
                        $data['sale']  = ($data['base_price'] - $data['final_price'])*100/ $data['base_price'] . '%' ;
                    }elseif (empty($data['final_price']) || empty($data['base_price'])){
                        $data['sale'] = '0%';
                    }elseif (empty($data['final_price']) && empty($data['base_price'])){
                        $data['sale'] = '0%';
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
            return view('stbdproduct::origin.edit')->with($data);
        } else if ($_POST) {
            $validator = Validator::make($request->all(), [
                'name_origin' => 'required'
            ], [
                'name_origin.required' => 'Bắt buộc phải nhập tên',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            } else {
                $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                //  Tùy chỉnh dữ liệu insert

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
    public function enabledStatus(Request $request)
    {
        try {
            $ids = $request->ids;
            if (is_array($ids)) {
                foreach ($ids as $product){
                    $product = $this->model->find($product);
                    $product->status = 1;
                    $product->save();
                }
            }
            CommonHelper::flushCache($this->module['table_name']);
            CommonHelper::one_time_message('success', 'Đổi trang thái sang kích hoạt thành công!');
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
    public function disabledStatus(Request $request)
    {
        try {
            $ids = $request->ids;
            if (is_array($ids)) {
                foreach ($ids as $product){
                    $product = $this->model->find($product);
                    $product->status = 0;
                    $product->save();
                }
            }
            CommonHelper::flushCache($this->module['table_name']);
            CommonHelper::one_time_message('success', 'Đổi trạng thái sang hủy kích hoạt thành công!');
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
