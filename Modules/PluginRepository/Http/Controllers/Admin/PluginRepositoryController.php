<?php

namespace Modules\PluginRepository\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Auth;
use Illuminate\Http\Request;
use App\Http\Helpers\CommonHelper;
use Modules\ThemeSemicolonwebJdes\Models\Company;
use Validator;

class PluginRepositoryController extends CURDBaseController
{

    protected $module = [
        'code' => 'plugin_repository',
        'table_name' => 'plugins',
        'label' => 'Plugin Repository',
        'modal' => '\Modules\PluginRepository\Models\PluginRepository',
        'list' => [
            ['name' => 'image', 'type' => 'image', 'label' => 'Ảnh', 'sort' => true],
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Tên', 'sort' => true],
            ['name' => 'code', 'type' => 'text', 'label' => 'Mã', 'sort' => true],
            ['name' => 'intro', 'type' => 'text', 'label' => 'Mô tả', 'sort' => true],
            ['name' => 'author', 'type' => 'text', 'label' => 'Tác giả', 'sort' => true],
//            ['name' => 'actived', 'type' => 'number', 'label' => 'Số lần kích hoạt', 'sort' => true],
//            ['name' => 'version_required', 'type' => 'text', 'label' => 'Phiên bản', 'sort' => true],
//            ['name' => 'path', 'type' => 'text', 'label' => 'Đường dẫn', 'sort' => true],
//            ['name' => 'link_detail', 'type' => 'text', 'label' => 'Đường link', 'sort' => true],
//            ['name' => 'author_link', 'type' => 'text', 'label' => 'Link profile tác giả', 'sort' => true],
//            ['name' => 'review_count', 'type' => 'number', 'label' => 'Số lượt review', 'sort' => true],
//            ['name' => 'review', 'type' => 'number', 'label' => 'Số review', 'sort' => true],
            ['name' => 'status', 'type' => 'status', 'label' => 'Trạng thái', 'sort' => true],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên', 'group_class' => 'col-md-6'],
                ['name' => 'code', 'type' => 'text', 'class' => 'required', 'label' => 'Mã', 'group_class' => 'col-md-6'],
                ['name' => 'intro', 'type' => 'textarea_editor', 'class' => 'required', 'label' => 'Giới thiệu'],
                ['name' => 'author', 'type' => 'text','label' => 'Tác giả', 'group_class' => 'col-md-6'],
                ['name' => 'actived', 'type' => 'number', 'label' => 'Số lần kích hoạt', 'group_class' => 'col-md-6'],
                ['name' => 'min_version_required', 'type' => 'text', 'label' => 'Phiên bản tối thiểu', 'group_class' => 'col-md-6'],
                ['name' => 'max_version_required', 'type' => 'text', 'label' => 'Phiên bản tối đa', 'group_class' => 'col-md-6'],
                ['name' => 'path', 'type' => 'text', 'label' => 'Đường dẫn', 'group_class' => 'col-md-6'],
                ['name' => 'link_detail', 'type' => 'text', 'label' => 'Đường link', 'group_class' => 'col-md-6'],
                ['name' => 'author_link', 'type' => 'text', 'label' => 'Link profile tác giả'],
                ['name' => 'review_count', 'type' => 'number', 'label' => 'Số lượt review', 'group_class' => 'col-md-6'],
                ['name' => 'review', 'type' => 'number', 'label' => 'Số review', 'group_class' => 'col-md-6'],
                ['name' => 'status', 'type' => 'checkbox', 'label' => 'Kích hoạt','value' => 1, 'group_class' => 'col-md-6'],

            ],
            'image_tab' => [
                ['name' => 'image', 'type' => 'file_editor', 'label' => 'Ảnh'],
            ],
//            'company_info' => [
//                ['name' => 'company_id', 'type' => 'select2_ajax_model', 'label' => 'Công ty', 'object' => 'company', 'model' => Company::class, 'display_field' => 'short_name'],
//            ]
        ],
    ];

    protected $filter = [
        'name' => [
            'label' => 'Tên',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'code' => [
            'label' => 'Mã',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'author' => [
            'label' => 'Tác giả',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'min_version_required' => [
            'label' => 'Phiên bản tối thiểu',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'max_version_required' => [
            'label' => 'Phiên bản tối đa',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'path' => [
            'label' => 'Đường dẫn',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'link_detail' => [
            'label' => 'Đường link',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'author_link' => [
            'label' => 'Link profile tác giả',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'review_count' => [
            'label' => 'Số lượt review',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'review' => [
            'label' => 'Số review',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'status' => [
            'label' => 'Trạng thái',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => 'Trạng thái',
                0 => 'Khóa',
                1 => 'Đã kích hoạt',
            ]
            ],
    ];

    public function getIndex(Request $request)
    {


        $data = $this->getDataList($request);

        return view('pluginrepository::list')->with($data);
    }

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('pluginrepository::add')->with($data);
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
//                    $data['status'] = $request->has('status') ? 1 : 0;

                    if ($request->has('contact_info_name')) {
                        $data['contact_info'] = json_encode($this->getContactInfo($request));
                    }
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
                return view('pluginrepository::edit')->with($data);
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
//                    $data['status'] = $request->has('status') ? 1 : 0;

//                    if ($request->has('contact_info_name')) {
//                        $data['contact_info'] = json_encode($this->getContactInfo($request));
//                    }
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
        } catch (\Exception $ex) {
            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
//            CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
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

}
