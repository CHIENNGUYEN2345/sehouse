<?php

namespace Modules\EduMarketing\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Auth;
use Illuminate\Http\Request;
use Modules\EduMarketing\Models\Classs;
use Modules\EduMarketing\Models\Admin;
use Modules\EduMarketing\Models\Category;
use Modules\EduMarketing\Models\Lesson;
use Modules\EduMarketing\Models\MarketingMailLog;
use Modules\EduSettings\Entities\Register;
use Modules\ThemeEdu\Models\Student;
use Validator;

class TagController extends CURDBaseController
{
    protected $module = [
        'code' => 'tag',
        'table_name' => 'tags',
        'label' => 'Tag',
        'modal' => '\Modules\EduMarketing\Models\Tag',
        'list' => [
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Tên'],
            ['name' => 'slug', 'type' => 'text', 'label' => 'Đường dẫn tĩnh'],
            ['name' => 'status', 'type' => 'status', 'label' => 'Trạng thái'],
//            ['name' => 'tags', 'type' => 'custom', 'td' => 'themeeduadmin::list.td.count_item', 'label' => 'Chứa số bản ghi', 'model' => \Modules\ThemeEduAdmin\Models\Post::class],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên'],
                ['name' => 'status', 'type' => 'checkbox', 'label' => 'Kích hoạt','value' => 1, 'group_class' => 'col-md-6'],

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
            'label' => 'Tên',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'slug' => [
            'label' => 'Đường dẫn',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'intro' => [
            'label' => 'Chứa số bản ghi',
            'type' => 'text',
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

        return view('edumarketing::tag.list')->with($data);
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
                return view('edumarketing::tag.add')->with($data);
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

                    return redirect('admin/' . $this->module['code'].'/'.$this->model->id);
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
            return view('edumarketing::tag.edit')->with($data);
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
                if ($request->has('student_ids')) {
                        $data['student_ids'] = '|' . implode('|', $request->student_ids) . '|';
                    }
                if ($request->has('class_id')) {
                    $data['class_id'] = '|' . implode('|', $request->class_id) . '|';
                }
//                if ($request->has('input_image_extra')) {
//                    $data['input_image_extra'] = implode('|', $request->input_image_extra);
//                }
                #

                foreach ($data as $k => $v) {
                    $item->$k = $v;
                }
                if ($item->save()) {
                    $student_ids = Register::whereIn('class_id', $request->get('class_id'))->pluck('student_id');
                    foreach ($student_ids as $student_id) {
                        $marketingmaillog = new MarketingMailLog();
                        $marketingmaillog->marketing_mail_id = $item->id;
                        $marketingmaillog->student_id = $student_id;
                        $marketingmaillog->date_send = $request->date_send;
                        $marketingmaillog->error = $request->status;
                        $marketingmaillog->sent = $request->status;
                        $marketingmaillog->save();
                    }
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
