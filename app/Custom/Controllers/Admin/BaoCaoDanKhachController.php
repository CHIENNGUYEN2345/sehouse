<?php

namespace App\Custom\Controllers\Admin;

use App\CRMDV\Models\Codes;
use App\CRMDV\Models\CompanyProfile;
use App\Http\Helpers\CommonHelper;
use App\Models\Admin;
use App\Models\Province;
use App\Models\RoleAdmin;
use http\Env\Response;
use Illuminate\Http\Request;
use Validator;

class BaoCaoDanKhachController extends CURDBaseController
{

    protected $orderByRaw = 'id desc';

    protected $module = [
        'code' => 'bao_cao_dan_khach',
        'table_name' => 'bao_cao_dan_khach',
        'label' => 'Báo cáo dẫn khách',
        'modal' => '\App\Custom\Models\BaoCaoDanKhach',
        'list' => [
            ['name' => 'code_id', 'type' => 'custom', 'td' => 'Custom.bao_cao_dan_khach.list.td.ten_bang_hang', 'label' => 'Mã tin', 'object' => 'code', 'display_field' => 'id'],
            ['name' => 'code_id', 'type' => 'custom', 'td' => 'Custom.bao_cao_dan_khach.list.td.ten_bang_hang', 'label' => 'Sản phẩm', 'object' => 'code', 'display_field' => 'address'],
            ['name' => 'khach_name', 'type' => 'text', 'label' => 'Tên khách'],
            ['name' => 'khach_tel', 'type' => 'custom', 'td' => 'Custom.bao_cao_dan_khach.list.td.so_dien_thoai', 'label' => 'SĐT'],
            ['name' => 'note', 'type' => 'text', 'label' => 'Ghi chú'],
            ['name' => 'image_extra', 'type' => 'custom', 'td' => 'Custom.bao_cao_dan_khach.list.td.slide_anh', 'label' => 'Ảnh'],
            ['name' => 'created_at', 'type' => 'datetime_vi', 'label' => 'Ngày tạo'],
            ['name' => 'admin_id', 'type' => 'relation', 'label' => 'Người tạo', 'object' => 'admin', 'display_field' => 'name'],
            ['name' => 'hanh_dong', 'type' => 'custom', 'td' => 'Custom.bao_cao_dan_khach.list.td.hanh_dong', 'label' => 'Hành động'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'code_id', 'type' => 'custom', 'field' => 'Custom.bao_cao_dan_khach.list.form.select', 'label' => 'Bảng hàng', 'class' => 'required',
                    'model' => Codes::class, 'object' => 'codes', 'display_field' => 'address'],
//                ['name' => 'code_id', 'type' => 'custom', 'field' => 'Custom.bao_cao_dan_khach.list.form.select', 'label' => 'Bảng hàng', 'class' => 'required',
//                    'model' => Codes::class, 'object' => 'codes', 'display_field' => 'id'],
                ['name' => 'khach_name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên khách', 'group_class' => 'col-md-6'],
                ['name' => 'khach_tel', 'type' => 'text', 'class' => 'required', 'label' => 'SĐT', 'group_class' => 'col-md-6'],
                ['name' => 'note', 'type' => 'textarea', 'class' => 'required', 'label' => 'Ghi chú', 'group_class' => 'col-md-12'],
                ['name' => 'image_extra', 'type' => 'custom','field'=>'CRMDV.codes.list.form.multiple_image', 'class' => 'required', 'count' => '6', 'label' => 'Thêm nhiều ảnh khác'],

            ],
        ]
    ];

    protected $quick_search = [
        'label' => 'ID',
        'fields' => 'code_id','khach_tel, khach_name'
    ];

    protected $filter = [
        'admin_id' => [
            'label' => 'Người tạo',
            'type' => 'select2_model',
            'display_field' => 'name',
            'model' => Admin::class,
            'object' => 'admin',
            'query_type' => '='
        ],

        'code_id' => [
            'label' => 'Bảng hàng',
            'type' => 'select2_ajax_model',
            'display_field' => 'address',
            'model' => Codes::class,
            'object' => 'codes',
            'query_type' => '='
        ],
//        'khach_name' => [
//            'label' => 'Tên khách',
//            'type' => 'select2_model',
//            'model' => \App\Custom\Models\BaoCaoDanKhach::class,
//            'display_field' => 'khach_name',
////            'where' => 'type="project"',
//            'orderByRaw' => 'order_no desc',
//            'query_type' => 'like',
//        ],
//        'khach_tel' => [
//            'label' => 'Số điện thoại',
//            'type' => 'select2_model',
//            'model' => \App\Custom\Models\BaoCaoDanKhach::class,
//            'display_field' => 'khach_tel',
//            'orderByRaw' => 'order_no desc',
//            'query_type' => 'like',
//        ],

        'filter_date' => [
            'label' => 'Lọc theo',
            'type' => 'filter_date',
            'options' => [
                '' => '',
                'created_at' => 'Ngày tạo',
            ],
            'query_type' => 'filter_date'
        ],
    ];

    public function getIndex(Request $request)
    {

//        $codes = CompanyProfile::where('address', 'like', '%Hà Nội%')->whereNull('province_id')->update(['province_id' => 2]);

        $data = $this->getDataList($request);

        return view('Custom.bao_cao_dan_khach.list')->with($data);
    }

    public function appendWhere($query, $request)
    {

//        if (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['dau_chu'])) {
//            // nếu là quyền đầu chủ thì hiển thị các báo cáo dẫn khách về bảng hàng của mình tạo
//            $code_ids = Codes::where('admin_id', \Auth::guard('admin')->user()->id)->pluck('id')->toArray();
//            $query = $query->whereIn('code_id', $code_ids);
//        }


        if (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['cvkd_fulltime', 'cvkd_parttime'])) {

            // nếu là quyền nvkd full / part thì hiển thị các báo cáo dẫn khách do mình tạo
            $query = $query->where('admin_id', \Auth::guard('admin')->user()->id);
        }
//        if (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['truong_phong'])) {
//            // nếu là quyền đầu chủ thì hiển thị các báo cáo dẫn khách về bảng hàng của mình tạo
//            dd(1);
//            $code_ids = Codes::where('admin_id', \Auth::guard('admin')->user()->id)->pluck('id')->toArray();
//            $query = $query->whereIn('code_id', $code_ids);
//        }
////
        if (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['tpkd'])) {
            // nếu là quyền nvkd full / part thì hiển thị các báo cáo dẫn khách do mình tạo
            //  lấy id các thành viên trong phòng mình
            $admin_ids = Admin::select('id')->where('phong_ban_id', \Auth::guard('admin')->user()->phong_ban_id)->pluck('id')->toArray();
            $code_ids = Codes::where('admin_id', \Auth::guard('admin')->user()->id)->pluck('id')->toArray();

            $query = $query->where(function ($query) use ($admin_ids, $code_ids) {
                $query->whereIn('admin_id', $admin_ids)
                    ->orWhereIn('code_id', $code_ids);
            });
        }
        if (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['dau_chu'])) {
            // nếu là quyền nvkd full / part thì hiển thị các báo cáo dẫn khách do mình tạo
            //  lấy id các thành viên trong phòng mình
//            $admin_ids = Admin::select('id')->where('phong_ban_id', \Auth::guard('admin')->user()->phong_ban_id)->pluck('id')->toArray();
//
//            $query = $query->where(function ($query) use ($admin_ids) {
//                foreach ($admin_ids as $admin_id) {
//                    $query->orWhere('admin_id', $admin_id); //   xem duoc của thành viên trong phòng mình
//                }
//            });

            $code_ids = Codes::where('admin_id', \Auth::guard('admin')->user()->id)->pluck('id')->toArray();

            $query = $query->where(function ($query) use ($code_ids) {
                $query->where('admin_id', \Auth::guard('admin')->user()->id)
                    ->orWhereIn('code_id', $code_ids);
            });


        }


        return $query;
    }

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('Custom.bao_cao_dan_khach.add')->with($data);
            } else if ($_POST) {
                \DB::beginTransaction();

                $validator = Validator::make($request->all(), [
                    'image_extra' => 'required',
                    'khach_tel' => 'required|regex:/^[0-9]{10}$/',
                    'khach_name' => 'required',
                ], [
                    'image_extra.required' => 'Bắt buộc phải nhập ảnh',
                    'khach_name.required' => 'Tên khách không được để trống',
                    'khach_tel.required' => 'Số điện thoại khách không được để trống',
                    'khach_tel.regex' => 'Số điện thoại khách không đúng định dạng',
                ]);
                if ($validator->fails()) {
                    CommonHelper::one_time_message('error', 'Thêm mới thất bại, vui lòng kiểm tra lại.');

                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert

                    $data['admin_id'] = \Auth::guard('admin')->user()->id;
                    if ($request->has('image_extra')) {
                        $data['image_extra'] = implode('|', $request->image_extra);
                    }


                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
                        \DB::commit();

                        CommonHelper::one_time_message('success', 'Tạo mới thành công!');
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

                    return redirect('admin/' . $this->module['code']);
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

            if (!is_object($item)) abort(404);
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('Custom.bao_cao_dan_khach.edit')->with($data);
            } else if ($_POST) {
                \DB::beginTransaction();

                $validator = Validator::make($request->all(), [
                    'image_extra' => 'required',
                    'khach_tel' => 'required|regex:/^[0-9]{10}$/',
                    'khach_name' => 'required',
                ], [
                    'image_extra.required' => 'Bắt buộc phải nhập ảnh',
                    'khach_name.required' => 'Tên khách không được để trống',
                    'khach_tel.required' => 'Số điện thoại khách không được để trống',
                    'khach_tel.regex' => 'Số điện thoại khách không đúng định dạng',
                ]);


                if ($validator->fails()) {
                    CommonHelper::one_time_message('error', 'Cập nhật thất bại, vui lòng kiểm tra lại.');

                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    if ($request->has('image_extra')) {
                        $data['image_extra'] = implode('|', $request->image_extra);
                    }

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
            }
        } catch (\Exception $ex) {
            \DB::rollback();
//            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function getPublish(Request $request)
    {
        try {

            $item = $this->model->find($request->id);

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

//    public function exportExcel($request, $data)
//    {
//        \Excel::create(str_slug($this->module['label'], '_') . '_' . date('d_m_Y'), function ($excel) use ($data) {
//
//            // Set the title
//            $excel->setTitle($this->module['label'] . ' ' . date('d m Y'));
//
//            $excel->sheet(str_slug($this->module['label'], '_') . '_' . date('d_m_Y'), function ($sheet) use ($data) {
//
//                $field_name = ['ID'];
//                $field_name[] = 'Tên';
//                $field_name[] = 'Tên quốc tế';
//                $field_name[] = 'Tên ngắn';
//                $field_name[] = 'Mã số thuế';
//                $field_name[] = 'Địa chỉ';
//                $field_name[] = 'Người đại diện';
//                $field_name[] = 'Số điện thoại';
//                $field_name[] = 'Email';
//                $field_name[] = 'Ngày cấp';
//                $field_name[] = 'Ngành nghề';
//                $field_name[] = 'Tỉnh/thành';
//                $field_name[] = 'Trạng thái';
//
//                $sheet->row(1, $field_name);
//
//                $k = 2;
//
//                foreach ($data as $value) {
//                    $data_export = [];
//                    $data_export[] = $value->id;
//                    $data_export[] = $value->name;
//                    $data_export[] = $value->name_en;
//                    $data_export[] = $value->name_short;
//                    $data_export[] = $value->mst;
//                    $data_export[] = $value->address;
//                    $data_export[] = $value->dai_dien;
//                    $data_export[] = $value->tel;
//                    $data_export[] = $value->email;
//                    $data_export[] = date('d/m/Y', strtotime($value->ngay_cap));
//                    $data_export[] = @$value->career->name;
//                    $data_export[] = @$value->province->name;
//                    $data_export[] = $value->trang_thai	;
//
//                    // dd($this->getAllFormFiled());
//                    $sheet->row($k, $data_export);
//                    $k++;
//
//                    $value->export_ex = 1;
//                    $value->save();
//                }
//            });
//        })->download('xlsx');
//    }

    public function exportExcel($request, $data)
    {
        \Excel::create(str_slug($this->module['label'], '_') . '_' . date('d_m_Y'), function ($excel) use ($data) {

            // Set the title
            $excel->setTitle($this->module['label'] . ' ' . date('d m Y'));

            $excel->sheet(str_slug($this->module['label'], '_') . '_' . date('d_m_Y'), function ($sheet) use ($data) {

                $field_name = ['ID'];
                foreach ($this->getAllFormFiled() as $field) {
                    if (!isset($field['no_export']) && isset($field['label'])) {
                        $field_name[] = $field['label'];
                    }
                }
                $field_name[] = 'Tạo lúc';
                $field_name[] = 'Cập nhập lần cuối';

                $sheet->row(1, $field_name);

                $k = 2;

                foreach ($data as $value) {
                    $data_export = [];
                    $data_export[] = $value->id;
                    foreach ($this->getAllFormFiled() as $field) {
                        if (!isset($field['no_export']) && isset($field['label'])) {
                            try {
                                if (in_array($field['type'], ['text', 'number', 'textarea', 'textarea_editor', 'date', 'datetime-local', 'email', 'hidden', 'checkbox', 'textarea_editor', 'textarea_editor2'])) {
                                    if(in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['dau_chu', 'tpkd', 'hcns_195'])) {
                                        if($field['label'] == 'SĐT') {
                                            $phoneNumber = $value->{$field['name']};
                                            $visiblePart = substr($phoneNumber, 0, 4);
                                            if (strlen($phoneNumber) >= 4) {
                                                $maskedPart = str_repeat('*', strlen($phoneNumber) - 4);
                                                $maskedPhoneNumber = $visiblePart.$maskedPart;
                                            } else {
                                                $maskedPhoneNumber = $phoneNumber;
                                            }
                                            $data_export[] = $maskedPhoneNumber;
                                        } else {
                                            $data_export[] = $value->{$field['name']};
                                        }
                                    } else {
                                        $data_export[] = $value->{$field['name']};
                                    }
                                } elseif (in_array($field['type'], [
                                    'relation', 'select_model', 'select2_model', 'select2_ajax_model', 'select_model_tree',

                                ])) {
                                    $data_export[] = $value->{$field['object']}->{$field['display_field']};
                                } elseif ($field['type'] == 'select') {
                                    $data_export[] = @$field['options'][$value->{$field['name']}];
                                } elseif (in_array($field['type'], ['file', 'file_editor2'])) {
                                    $data_export[] = \URL::asset('public/filemanager/userfiles/' . @$value->{$field['name']});
                                } elseif (in_array($field['type'], ['file_editor_extra'])) {
                                    $items = explode('|', @$value->{$field['name']});
                                    foreach ($items as $item) {
                                        $data_export[] = \URL::asset('public/filemanager/userfiles/' . @$item) . ' | ';
                                    }
                                } else {
                                    $data_export[] = $field['label'];
                                }
                            } catch (\Exception $ex) {
                                $data_export[] = $ex->getMessage();
                            }
                        }
                    }
                    $data_export[] = @$value->created_at;
                    $data_export[] = @$value->updated_at;
                    // dd($this->getAllFormFiled());
                    $sheet->row($k, $data_export);
                    $k++;
                }
            });
        })->download('xlsx');
    }

    public function ajaxGetInfo($id)
    {
        $data = $this->model->find($id);
        $code = Codes::query()->where('id', $data->code_id)->first();
        $service = $code->service->name_vi;


        if (!is_object($data)) abort(404);
        $imagePath = asset('/filemanager/userfiles/' . $code->image);
        $imagePaths = explode('|', $code->image_extra);
        $fullPaths = array_map(function ($path) {
            return asset('/filemanager/userfiles/' . $path);
        }, $imagePaths);

// image sổ đỏ và hợp đồng với chủ nhà
        $imageRedBook = explode('|', $code->so_do_va_hop_dong_chu_nha);
        $imageRedBooks = array_map(function ($path) {
            return asset('/filemanager/userfiles/' . $path);
        }, $imageRedBook);


        // lấy thông tin đầu chủ
        $dauchu = \App\CRMDV\Models\Admin::query()->where('id', $code->admin_id)->first();
        $anhDauChu = asset('/filemanager/userfiles/' . $dauchu->image);

        //lay thong tin phong ban
        $phongban = \App\CRMDV\Models\Phong_ban::query()->where('id', $dauchu->phong_ban_id)->first();


        $show = true;
        if ($code->admin_id == \Auth::guard('admin')->user()->id || \Auth::guard('admin')->user()->super_admin == 1 || CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'hcns_195')) {
            $show = true;
        } else {
            $show = false;
        }
        return response()->json([
            'data' => $code,
            'imageRedBooks' => $imageRedBooks,
            'imagePaths' => $fullPaths,
            'service' => $service,
            'show' => $show,
            'dauchu' => $dauchu,
            'phongban' => $phongban,
            'anhDauChu' => $anhDauChu,
        ]);
    }

    public function ajaxGetImage($id)
    {
//        $data = $id;
        $data = $this->model->find($id);
        $imagePaths = explode('|', $data->image_extra);
        $fullPaths = array_map(function ($path) {
            return asset('/filemanager/userfiles/' . $path);
        }, $imagePaths);
        return Response()->json([
            'data' => $data,
            'fullPaths' => $fullPaths
        ]);
    }

}
