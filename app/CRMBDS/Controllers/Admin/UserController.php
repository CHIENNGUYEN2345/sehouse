<?php

namespace App\CRMBDS\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Models\{Admin, RoleAdmin, User};
use Auth;
use Illuminate\Http\Request;
use Mail;
use Session;
use Validator;
use App\CRMBDS\Models\Tag;

class UserController extends CURDBaseController
{

    protected $module = [
        'code' => 'user',
        'label' => 'Khách hàng',
        'modal' => '\App\Models\User',
        'list' => [
            ['name' => 'image', 'type' => 'image', 'label' => 'admin.image'],
            ['name' => 'name', 'type' => 'text_admin_edit', 'label' => 'admin.name'],
            ['name' => 'tel', 'type' => 'text', 'label' => 'admin.phone'],
            ['name' => 'email', 'type' => 'text', 'label' => 'admin.email'],
            ['name' => 'tick', 'type' => 'custom','td'=> 'CRMBDS.list.td.multi_tick', 'label' => 'Đánh dấu khách hàng', 'object' => 'tick', 'display_field' => 'name', 'sort' => true],
            ['name' => 'status', 'type' => 'status', 'label' => 'admin.status'],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'admin.update']

        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'admin.full_name', 'group_class' => 'col-md-6'],
                ['name' => 'short_name', 'type' => 'text', 'class' => '', 'label' => 'Tên ngắn gọn', 'group_class' => 'col-md-6'],
                ['name' => 'email', 'type' => 'custom', 'field' => 'CRMBDS.admin.form.email', 'class' => '', 'label' => 'admin.email', 'group_class' => 'col-md-4'],
                ['name' => 'tel', 'type' => 'custom', 'class' => 'required', 'field' => 'CRMBDS.admin.form.tel', 'label' => 'admin.phone', 'group_class' => 'col-md-4'],
                ['name' => 'code', 'type' => 'text', 'class' => '', 'field' => 'CRMBDS.admin.form.tel', 'label' => 'Mã', 'group_class' => 'col-md-4'],
                ['name' => 'address', 'type' => 'text', 'class' => '', 'label' => 'admin.address', 'group_class' => 'col-md-12'],
                ['name' => 'province_id', 'type' => 'select_location', 'label' => 'admin.choose_place', 'group_class' => 'col-md-9'],
                ['name' => 'intro', 'type' => 'textarea', 'class' => '', 'label' => 'admin.introduce'],
                ['name' => 'note', 'type' => 'textarea', 'class' => '', 'label' => 'admin.note', 'inner' => 'rows=10'],
                ['name' => 'tick', 'type' => 'select2_model', 'label' => 'Đánh dấu khách hàng', 'model' => Tag::class, 'where' => 'type="user_tick"'
                    , 'object' => 'tick','orderByRaw' => 'order_no desc', 'display_field' => 'name', 'class' => '','multiple' => true, 'group_class' => 'col-md-6'],

            ],
            'more_info_tab' => [
                ['name' => 'invite_by', 'type' => 'select2_ajax_model', 'label' => 'admin.presenter', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'tel'],
                ['name' => 'sale_id', 'type' => 'select2_ajax_model', 'label' => 'admin.sale', 'model' => Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'tel', 'class' => ''],
                
                ['name' => 'image', 'type' => 'file_editor', 'label' => 'Ảnh đại diện'],
                ['name' => 'facebook', 'type' => 'text', 'class' => '', 'label' => 'admin.facebook'],
                ['name' => 'skype', 'type' => 'text', 'class' => '', 'label' => 'admin.skype'],
                ['name' => 'zalo', 'type' => 'text', 'class' => '', 'label' => 'admin.zalo'],
                
            ],
        ]
    ];

    protected $filter = [
        'status' => [
            'label' => 'admin.status',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => 'admin.status',
                0 => 'admin.hidden',
                1 => 'admin.active'
            ]
        ],
        'tick' => [
            'label' => 'Đánh dấu khách hàng',
            'type' => 'select2_model',
            'model' => Tag::class,
            'display_field' => 'name',
            'where' => 'type="user_tick"',
            'orderByRaw' => 'order_no desc',

            'query_type' => 'like',
        ],
    ];

    protected $quick_search = [
        'label' => 'ID, tên, sđt, email, address',
        'fields' => 'id, name, tel, email, address'
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('CRMBDS.user.list')->with($data);
    }

    public function appendWhere($query, $request)
    {

        //  Chỉ truy vấn ra các đơn hàng của mình
        if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'view_all_data')) {
            $query = $query->where('sale_id', \Auth::guard('admin')->user()->id);
        }

        return $query;
    }

    public function add(Request $request)
    {
        try {

            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMBDS.user.add')->with($data);
            } else if ($_POST) {

                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    // 'email' => 'required',
                    'tel' => 'required',
                    // 'password' => 'required|min:5',
                    // 'password_confimation' => 'required|same:password',
                ], [
                    'name.required' => 'Bắt buộc phải nhập tên!',
//                    'email.required' => 'Bắt buộc phải nhập email!',
//                    'email.unique' => 'Địa chỉ email đã tồn tại!',
                    // 'password.required' => 'Bắt buộc phải nhập mật khẩu!',
                    // 'password.min' => 'Mật khẩu phải trên 5 ký tự!',
                    // 'password_confimation.required' => 'Bắt buộc nhập lại mật khẩu!',
                    // 'password_confimation.same' => 'Nhập lại sai mật khẩu!',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    \DB::beginTransaction();

                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    $data['api_token'] = base64_encode(rand(1, 100) . time());

                    //  Tùy chỉnh dữ liệu insert

//                    $data['role_id']=1;
                    
                    unset($data['role_id']);
                    

                    $data['district_id'] = $request->get('district_id', null);
                    $data['ward_id'] = $request->get('ward_id', null);

                    if ($request->has('tick')) {
                        $data['tick'] = '|' . implode('|', $request->tick) . '|';
                    }

                    #
                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
                        
                        \DB::commit();
                        CommonHelper::one_time_message('success', 'Tạo mới thành công!');

//                        return redirect('admin/user');
                    } else {
                        \DB::rollback();
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
                        return redirect('admin/' . $this->module['code'] . '/edit/' . $this->model->id);
                    } elseif ($request->return_direct == 'save_create') {
                        return redirect('admin/' . $this->module['code'] . '/add');
                    }

                    return redirect('admin/' . $this->module['code'] . '/edit/' . $this->model->id);
                }
            }
        } catch (\Exception $ex) {
            \DB::rollback();
            CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function update(Request $request)
    {
        try {
            $item = $this->model->find($request->id);

            if (!is_object($item)) {
                abort(404);
            }
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('CRMBDS.user.edit')->with($data);
            } else if ($_POST) {
                
                \DB::beginTransaction();

                if ($item->id == \Auth::guard('admin')->user()->id) {
                    $validator = Validator::make($request->all(), [
                        'name' => 'required'
                    ], [
                        'name.required' => 'Bắt buộc phải nhập tên',
                    ]);

                    if ($validator->fails()) {
                        return back()->withErrors($validator)->withInput();
                    }
                }
                $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                //  Tùy chỉnh dữ liệu edit
                if ($request->has('tick')) {
                    $data['tick'] = '|' . implode('|', $request->tick) . '|';
                }
               
                unset($data['role_id']);
                

                $data['district_id'] = $request->get('district_id', null);
                $data['ward_id'] = $request->get('ward_id', null);
                #
                foreach ($data as $k => $v) {
                    $item->$k = $v;
                }
                if ($item->save()) {
                    
                    \DB::commit();
                    CommonHelper::one_time_message('success', 'Cập nhật thành công!');
                } else {
                    \DB::rollback();
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
                    return redirect('admin/' . $this->module['code'] . '/edit/' . $item->id);
                } elseif ($request->return_direct == 'save_create') {
                    return redirect('admin/' . $this->module['code'] . '/add');
                }

                return redirect('admin/' . $this->module['code']);
            }
        } catch (\Exception $ex) {
            \DB::rollback();
            CommonHelper::one_time_message('error', $ex->getMessage());
//            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            return redirect()->back()->withInput();
        }
    }

    public function exportExcel($request, $data)
    {
        \Excel::create(str_slug($this->module['label'], '_') . '_' . date('d_m_Y'), function ($excel) use ($data) {

            // Set the title
            $excel->setTitle($this->module['label'] . ' ' . date('d m Y'));

            $excel->sheet(str_slug($this->module['label'], '_') . '_' . date('d_m_Y'), function ($sheet) use ($data) {

                $field_name = ['ID'];
                $field_name[] = 'Tên';
                $field_name[] = 'SĐT';
                $field_name[] = 'Mail';
                $field_name[] = 'Tạo lúc';
                $field_name[] = 'Cập nhập lần cuối';

                $sheet->row(1, $field_name);

                $k = 2;

                foreach ($data as $value) {
                    $data_export = [];
                    $data_export[] = $value->id;
                    $data_export[] = $value->name;
                    $data_export[] = $value->tel;
                    $data_export[] = $value->mail;
                    $data_export[] = @$value->created_at;
                    $data_export[] = @$value->updated_at;
                    // dd($this->getAllFormFiled());
                    $sheet->row($k, $data_export);
                    $k++;
                }
            });
        })->download('xlsx');
    }

    public function searchForSelect2(Request $request)
    {
        $col2 = $request->get('col2', '') == '' ? '' : ', ' . $request->get('col2');

        $data = $this->model->selectRaw('id, ' . $request->col . $col2)
            ->where(function ($query) use ($request) {
                $query->orWhere($request->col, 'like', '%' . $request->keyword . '%');
                $query->orWhere('name', 'like', '%' . $request->keyword . '%');
                $query->orWhere('tel', 'like', '%' . $request->keyword . '%');
                $query->orWhere('email', 'like', '%' . $request->keyword . '%');
            });

        if ($request->where != '') {
            $data = $data->whereRaw(urldecode(str_replace('&#039;', "'", $request->where)));
        }

        $data = $data->limit(10)->get();

        return response()->json([
            'status' => true,
            'items' => $data
        ]);
    }

    public function ajaxGetInfo(Request $r) {
        $user = User::select(['id', 'name', 'tel', 'image', 'email'])->where('id', $r->id)->first();
        if (!is_object($user)) {
            return response()->json([
                'status' => false,
                'msg' => 'Không tìm thấy bản ghi',
                'data' => $user
            ]);
        }
        $user->image = CommonHelper::getUrlImageThumb(@$user->image, 250, null);
        $user->province_name = @$user->province->name;
        $user->district_name = @$user->district->name;
        $user->ward_name = @$user->ward->name;

        return response()->json([
            'status' => true,
            'msg' => '',
            'data' => $user
        ]);
    }

    public function delete(Request $request)
    {
        try {

            $item = $this->model->find($request->id);

            $item->delete();

            CommonHelper::one_time_message('success', 'Xóa thành công!');
            return redirect('admin/' . $this->module['code']);
        } catch (\Exception $ex) {
            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            return back();
        }
    }

    public function checkExist(Request $request) {
        if ($request->has('email')) {
            $user = User::select('email', 'name', 'tel', 'id')->where('email', $request->email);
            if ($request->has('id')) {
                $user = $user->where('id', '!=', $request->id)->first();
            }
            $user = $user->first();
            if (is_object($user)) {
                return response()->json([
                    'status' => false,
                    'html' => 'Email này đã được tạo cho <a  target="_blank" href="/admin/user/edit/'.$user->id.'">'.$user->name .' - email: ' . $user->email.' - sđt: ' . $user->tel.'</a>'
                ]);
            }
        }
        if ($request->has('tel')) {
            $user = User::select('id', 'email', 'name', 'tel')->where('tel', $request->tel);
            if ($request->has('id')) {
                $user = $user->where('id', '!=', $request->id)->first();
            }
            $user = $user->first();
            if (is_object($user)) {
                return response()->json([
                    'status' => false,
                    'html' => 'SĐT này đã được tạo cho <a target="_blank" href="/admin/user/edit/'.$user->id.'">'.$user->name .' - email: ' . $user->email.' - sđt: ' . $user->tel.'</a>'
                ]);
            }
        }
        return response()->json([
            'status' => true,
            'html' => ''
        ]);
    }
}



