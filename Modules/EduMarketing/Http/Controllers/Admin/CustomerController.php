<?php

namespace Modules\EduMarketing\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Auth;
use Illuminate\Http\Request;
use Modules\EduMarketing\Models\Classs;
use Modules\EduMarketing\Models\Admin;
use Modules\EduMarketing\Models\Category;
use Modules\EduMarketing\Models\Lesson;
use Modules\EduMarketing\Models\MaketingMail;
use Modules\EduMarketing\Models\MarketingMailLog;
use Modules\EduMarketing\Models\Tag;
use Modules\EduSettings\Entities\Register;
use Modules\ThemeEdu\Models\Center;
use Modules\ThemeEdu\Models\Course;
use Modules\ThemeEdu\Models\Student;
use Validator;

class CustomerController extends CURDBaseController
{
    protected $module = [
        'code' => 'customer',
        'table_name' => 'customer',
        'label' => 'Khách hàng tiềm năng',
        'modal' => '\Modules\EduMarketing\Models\Customer',
        'list' => [
            ['name' => 'image', 'type' => 'image', 'label' => 'Ảnh'],
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Tên'],
            ['name' => 'email', 'type' => 'text', 'label' => 'Email'],
            ['name' => 'phone', 'type' => 'text', 'label' => 'Sđt'],
            ['name' => 'tags', 'type' => 'custom','td' => 'edumarketing::list.td.multi_tag', 'label' => 'Tags', 'object' => 'tag', 'display_field' => 'name'],
            ['name' => 'marketing_id', 'type' => 'custom','td' => 'edumarketing::list.td.multi_marketing', 'label' => 'Tags', 'object' => 'marketing-mail', 'display_field' => 'name'],
            ['name' => 'admin_id', 'type' => 'custom','td' => 'edumarketing::list.td.multi_admin', 'label' => 'Tags', 'object' => 'admin', 'display_field' => 'name'],
            ['name' => 'status', 'type' => 'status', 'label' => 'Trạng thái'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text','class' => 'required', 'label' => 'Tên'],
                ['name' => 'birthday', 'type' => 'datetimepicker','class' => '', 'label' => 'Ngày sinh','group_class' => 'col-md-6'],
                ['name' => 'gender', 'type' => 'select','class' => '', 'label' => 'Giới tính', 'options' => [
                    '' => 'Chọn giới tính',
                    1 => 'Nam',
                    2 => 'Nữ'
                ],'group_class' => 'col-md-6'],
                ['name' => 'school', 'type' => 'text','class' => '', 'label' => 'Trường học','group_class' => 'col-md-6'],
                ['name' => 'level', 'type' => 'text','class' => '', 'label' => 'Cấp độ','group_class' => 'col-md-6'],
                ['name' => 'tags', 'type' => 'select2_ajax_model', 'multiple' => true,  'label' => 'Tags', 'object' => 'tag', 'model' => Tag::class,
                    'display_field' => 'name','group_class' => 'col-md-6'],
                ['name' => 'source', 'type' => 'select', 'class' => '', 'label' => 'Nguồn','group_class' => 'col-md-6', 'options' => [
                    '' => '',
                    'Bạn bè' => 'Bạn bè',
                    'Facebook' => 'Facebook',
                    'Website' => 'Website',
                    'Zalo' => 'Zalo',
                    'Youtube' => 'Youtube'
                ]],
                ['name' => 'status', 'type' => 'checkbox', 'class' => '', 'label' => 'Kích hoạt', 'value' => 1, 'group_class' => 'col-md-3'],
                ['name' => 'note', 'type' => 'textarea','class' => '', 'label' => 'Ghi chú'],
            ],
            'image_tab' => [
                ['name' => 'image', 'type' => 'file_editor','class' => '', 'label' => 'Ảnh đại diện'],
            ],
            'info_tab' => [
                ['name' => 'marketing_id', 'type' => 'select2_ajax_model', 'multiple' => true,  'label' => 'Chiến dịch','object' => 'marketing-mail', 'model' => MaketingMail::class, 'display_field' => 'name'],
                ['name' => 'admin_id', 'type' => 'select2_ajax_model', 'multiple' => true,  'label' => 'Người phụ trách','object' => 'admin', 'model' => \Modules\ThemeEdu\Models\Admin::class, 'display_field' => 'name','where' => 'type=1'],
                ['name' => 'center_id', 'type' => 'select2_model', 'multiple' => true,  'label' => 'Chi nhánh','object' => 'center', 'model' => Center::class, 'display_field' => 'name'],
                ['name' => 'course_id', 'type' => 'select2_ajax_model', 'multiple' => true,  'label' => 'Khóa học mong muốn','object' => 'course', 'model' => Course::class, 'display_field' => 'name'],
            ],
            'contact_tab' => [
                ['name' => 'phone', 'type' => 'text','class' => '', 'label' => 'Sđt'],
                ['name' => 'email', 'type' => 'text','class' => '', 'label' => 'Email'],
                ['name' => 'facebook', 'type' => 'text','class' => '', 'label' => 'Link facebook cá nhân'],
            ],
        ],
    ];

    protected $filter = [
        'name' => [
            'label' => 'Tên',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'phone' => [
            'label' => 'Sđt',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'email' => [
            'label' => 'Email',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'tags' => [
            'label' => 'Tags',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'object' => 'tag',
            'model' => Tag::class,
            'query_type' => 'like'
        ],
        'marketing_id' => [
            'label' => 'Chiến dịch',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'object' => 'marketing-mail',
            'model' => MaketingMail::class,
            'query_type' => 'like'
        ],
        'admin_id' => [
            'label' => 'Người phụ trách',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'object' => 'admin',
            'model' => \Modules\ThemeEdu\Models\Admin::class,
            'query_type' => 'like'
        ],
        'course_id' => [
            'label' => 'Khóa học mong muốn',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'object' => 'course',
            'model' => Course::class,
            'query_type' => 'like'
        ],
        'center_id' => [
            'label' => 'Chi nhánh',
            'type' => 'select2_model',
            'display_field' => 'name',
            'object' => 'center',
            'model' => Tag::class,
            'query_type' => 'like'
        ],
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

        return view('edumarketing::customer.list')->with($data);
    }

    public function appendWhere($query, $request)
    {
        //  Nếu không có quyền xem toàn bộ dữ liệu thì chỉ được xem các dữ liệu của công ty mình
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
//        if (!is_null($request->get('tags'))) {
//            $query = $query->where('multi_cat', 'LIKE', '%|' . $request->category_id . '|%');
//        }
        return $query;
    }

    public function add(Request $request)
    {

        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('edumarketing::customer.add')->with($data);
            } else if ($_POST) {
//                dd($request);
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
                    if ($request->has('center_id')) {
                        $data['center_id'] = '|' . implode('|', $request->center_id) . '|';
                    }
                    if ($request->has('marketing_id')) {
                        $data['marketing_id'] = '|' . implode('|', $request->marketing_id) . '|';
                    }
                    if ($request->has('course_id')) {
                        $data['course_id'] = '|' . implode('|', $request->course_id) . '|';
                    }
                    if ($request->has('admin_id')) {
                        $data['admin_id'] = '|' . implode('|', $request->admin_id) . '|';
                    }

                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
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
            return view('edumarketing::customer.edit')->with($data);
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

//                  Tùy chỉnh dữ liệu insert
                if ($request->has('multi_cat')) {
                    $data['multi_cat'] = '|' . implode('|', $request->multi_cat) . '|';
                    $data['category_id'] = $request->multi_cat[0];
                }
                if ($request->has('tags')) {
                    $data['tags'] = '|' . implode('|', $request->tags) . '|';
                }
                if ($request->has('center_id')) {
                    $data['center_id'] = '|' . implode('|', $request->center_id) . '|';
                }
                if ($request->has('marketing_id')) {
                    $data['marketing_id'] = '|' . implode('|', $request->marketing_id) . '|';
                }
                if ($request->has('course_id')) {
                    $data['course_id'] = '|' . implode('|', $request->course_id) . '|';
                }
                if ($request->has('admin_id')) {
                    $data['admin_id'] = '|' . implode('|', $request->admin_id) . '|';
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

    public function duplicate(Request $request, $id)
    {
        $poduct = Lesson::find($id);
        $poduct_new = $poduct->replicate();
        $poduct_new->company_id = \Auth::guard('admin')->user()->last_company_id;
        $poduct_new->admin_id = \Auth::guard('admin')->user()->id;
        $poduct_new->save();
        return $poduct_new;
    }
}
