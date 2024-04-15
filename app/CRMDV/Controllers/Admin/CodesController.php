<?php

namespace App\CRMDV\Controllers\Admin;

use App\CRMDV\Models\Bill;
use App\Http\Helpers\CommonHelper;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\CRMDV\Models\Category;
use App\CRMDV\Models\Codes;
use App\CRMDV\Models\Theme;
use App\CRMDV\Models\Tag;
use Validator;
use App\CRMDV\Models\PostTag;
use App\CRMDV\Models\BillProgress;

class CodesController extends CURDBaseController
{

    protected $module = [
        'code' => 'codes',
        'table_name' => 'codes',
        'label' => 'Bảng hàng',
        'modal' => '\App\CRMDV\Models\Codes',
        'list' => [
            ['name' => 'id', 'type' => 'custom', 'td' => 'CRMDV.codes.list.td.STT', 'label' => 'Mã tin'],
            ['name' => 'address', 'type' => 'custom', 'td' => 'CRMDV.codes.list.td.ten_bang_hang', 'label' => 'Địa chỉ'],
            ['name' => 'status', 'type' => 'custom', 'td' => 'CRMDV.codes.list.td.trang_thai', 'label' => 'Trạng thái'],
            ['name' => 'image_extra', 'type' => 'custom', 'td' => 'CRMDV.codes.list.td.slide_anh', 'label' => 'Ảnh'],
            ['name' => 'khoang_tang', 'type' => 'custom', 'td' => 'CRMDV.codes.list.td.khoang_tang', 'label' => 'Khoảng tầng'],
            ['name' => 'service_id', 'type' => 'custom', 'td' => 'CRMDV.codes.list.td.du_an', 'label' => 'Dự án', 'object' => 'service', 'display_field' => 'name_vi'],
            ['name' => 'gia_niem_yet', 'type' => 'price_vi', 'label' => 'Giá'],
            ['name' => 'dien_tich', 'type' => 'custom', 'td' => 'CRMDV.codes.list.td.dientich', 'label' => 'Diện tích'],
            ['name' => 'so_phong_ngu', 'type' => 'number', 'label' => 'Phòng ngủ'],
            ['name' => 'created_at', 'type' => 'date_vi', 'label' => 'Ngày tạo'],
            ['name' => 'admin_id', 'type' => 'relation', 'label' => 'Đầu chủ', 'object' => 'admin', 'display_field' => 'name'],
            ['name' => 'hanh_dong', 'type' => 'custom', 'td' => 'CRMDV.codes.list.td.hanh_dong', 'label' => 'Hành động'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'loai_hinh', 'class' => 'required', 'type' => 'select', 'options' => [
                    '' => '',
                    'Mua bán' => 'Mua bán',
                    'Cho thuê' => 'Cho thuê',
                ], 'label' => 'Loại hình', 'group_class' => 'col-md-6'],
                ['name' => 'loai_nha_dat', 'class' => 'required', 'type' => 'select', 'options' => [
                    '' => '',
                    'Nhà đất riêng lẻ' => 'Nhà đất riêng lẻ',
                    'Liền kề - biệt thự' => 'Liền kề - biệt thự',
                    'Chung cư' => 'Chung cư',
                ], 'label' => 'Loại nhà đất', 'group_class' => 'col-md-6'],
                ['name' => 'service_id', 'class' => 'required', 'type' => 'select2_model', 'label' => 'Dự án', 'model' => \App\CRMDV\Models\Service::class, 'object' => 'service', 'display_field' => 'name_vi', 'group_class' => 'col-md-6'],
//                ['name' => 'project_type_id', 'type' => 'select2_model', 'label' => 'Loại dự án', 'model' => \App\CRMDV\Models\Project_type::class,'where' => 'type="project"', 'object' => 'project_type', 'display_field' => 'name', 'group_class' => 'col-md-6'],
                ['name' => 'province_id', 'type' => 'custom', 'no_export' => true, 'field' => 'CRMDV.codes.list.form.select_location2', 'label' => 'Chọn địa điểm', 'group_class' => 'col-md-9'],
                ['name' => 'duong', 'type' => 'custom', 'field' => 'CRMDV.codes.list.form.text', 'label' => 'Đường', 'class' => 'required', 'group_class' => 'col-md-12'],
                ['name' => 'address', 'type' => 'custom', 'field' => 'CRMDV.codes.list.form.text', 'class' => 'required', 'label' => 'Địa chỉ chi tiết', 'group_class' => 'col-md-12'],
                ['name' => 'dien_tich', 'type' => 'custom', 'field' => 'CRMDV.codes.list.form.text', 'class' => 'required', 'label' => 'Diện tích', 'group_class' => 'col-md-4'],
                ['name' => 'so_phong_ngu', 'type' => 'custom', 'field' => 'CRMDV.codes.list.form.number', 'class' => 'required', 'label' => 'Số phòng ngủ', 'group_class' => 'col-md-4'],
                ['name' => 'khoang_tang', 'class' => 'required', 'type' => 'select', 'options' => [
                    '' => '',
                    'Tầng thấp' => 'Tầng thấp',
                    'Tầng trung' => 'Tầng trung',
                    'Tầng cao' => 'Tầng cao',
                ], 'label' => 'Khoảng tầng', 'group_class' => 'col-md-4'],
                ['name' => 'huong_cua_chinh', 'class' => '', 'type' => 'select', 'options' => [
                    '' => '',
                    'Đông' => 'Đông',
                    'Tây' => 'Tây',
                    'Nam' => 'Nam',
                    'Bắc' => 'Bắc',
                    'Đông Bắc' => 'Đông Bắc',
                    'Tây Bắc' => 'Tây Bắc',
                    'Tây Nam' => 'Tây Nam',
                    'Đông Nam' => 'Đông Nam',
                ], 'label' => 'Hướng cửa chính', 'group_class' => 'col-md-4'],
                ['name' => 'ban_cong', 'type' => 'custom', 'field' => 'CRMDV.codes.list.form.number', 'label' => 'Ban công / Lô gia', 'group_class' => 'col-md-4'],
                ['name' => 'so_nha_ve_sinh', 'type' => 'custom', 'field' => 'CRMDV.codes.list.form.number', 'label' => 'Số nhà vệ sinh', 'group_class' => 'col-md-4'],
//                ['name' => 'link', 'type' => 'text', 'class' => '', 'label' => 'Đường', 'group_class' => 'col-md-12'],
            ],

            'remind_tab' => [
//                ['name' => 'image', 'type' => 'file_image', 'label' => 'Ảnh đại diện'],
                ['name' => 'image_extra', 'type' => 'custom', 'field' => 'CRMDV.codes.list.form.multiple_image', 'class' => 'required', 'count' => '6', 'label' => 'Ảnh nhà chào bán'],
            ],
            'des_tab' => [
                ['name' => 'gia_niem_yet', 'type' => 'price_vi', 'class' => 'required', 'label' => 'Giá bán', 'group_class' => 'col-md-4'],
                ['name' => 'phi_moi_gioi', 'type' => 'price_vi', 'class' => 'required', 'label' => 'Phí môi giới', 'group_class' => 'col-md-4'],
                ['name' => 'content', 'type' => 'custom', 'field' => 'CRMDV.codes.list.form.textarea', 'label' => 'Mô tả chi tiết','class' => 'required' ],
//                ['name' => 'content', 'type' => 'textarea_editor', 'class' => '', 'label' => 'Mô tả chi tiết tính năng'],
                ['name' => 'intro', 'type' => 'custom', 'field' => 'CRMDV.codes.list.form.text', 'label' => 'Họ tên chủ nhà', 'class' => 'required', 'group_class' => 'col-md-6'],
                ['name' => 'dia_chi_tren_so', 'type' => 'custom', 'field' => 'CRMDV.codes.list.form.text', 'class' => '', 'label' => 'Địa chỉ chủ nhà', 'group_class' => 'col-md-6'],
                ['name' => 'sdt_chu_nha', 'type' => 'custom', 'field' => 'CRMDV.codes.list.form.text', 'label' => 'Số điện thoại chủ nhà', 'class' => 'required', 'group_class' => 'col-md-6'],

                ['name' => 'so_giay_chung_nhan', 'type' => 'custom', 'field' => 'CRMDV.codes.list.form.text', 'label' => 'Số seri sổ/GCN/HDMB', 'group_class' => 'col-md-6'],
//                ['name' => 'seri', 'type' => 'text', 'label' => 'Số hợp đồng mua bán', 'group_class' => 'col-md-12'],
                ['name' => 'so_do_va_hop_dong_chu_nha', 'type' => 'custom', 'field' => 'CRMDV.codes.list.form.multiple_image','class' => 'required', 'count' => '6', 'label' => 'Sổ đỏ và hợp đồng ký gửi với chủ nhà'],
//                ['name' => 'xac_thuc', 'type' => 'checkbox', 'label' => 'Trạng thái tin xác thực', 'value' => 1, 'group_class' => 'col-md-4'],
//                ['name' => 'trang_thai_2', 'type' => 'checkbox', 'label' => 'Trạng thái tin xác thực', 'value' => 1, 'group_class' => 'col-md-4'],
//                ['name' => 'da_ban', 'type' => 'checkbox', 'label' => 'Đã bán', 'value' => 1, 'group_class' => 'col-md-4'],
                ['name' => 'status', 'class' => '', 'type' => 'radio', 'value' => 'Đang bán', 'options' => [
                    'Đang bán' => 'Đang bán',
                    'Đã bán' => 'Đã bán',
                    'Tạm dừng' => 'Tạm dừng',
                ], 'label' => 'Trạng thái', 'group_class' => 'col-md-4'],

            ],
        ]
    ];

    protected $quick_search = [
        'label' => 'ID',
        'fields' => 'id, address'
    ];

    protected $filter = [

        'service_id' => [
            'label' => 'Dự án',
            'type' => 'select2_model',
            'model' => \App\CRMDV\Models\Service::class,
            'display_field' => 'name_vi',
            'orderByRaw' => 'order_no desc',
            'query_type' => 'like',
        ],

        'gia_niem_yet' => [
            'label' => 'Khoảng giá',
            'type' => 'custom',
            'field' => 'CRMDV.codes.list.filter.loc_khoang_gia',
            'options' => [
                'Chọn khoảng giá' => '',
                '1-2 tỷ' => '1-2 tỷ',
                '2-3 tỷ' => '2-3 tỷ',
                '3-4 tỷ' => '3-4 tỷ',
                'trên 4 tỷ' => 'trên 4 tỷ',
            ],
            'query_type' => 'custom',
        ],
        'dien_tich' => [
            'label' => 'Diện tích',
            'type' => 'custom',
            'field' => 'CRMDV.codes.list.filter.loc_khoang_gia',
            'options' => [
                'Chọn diện tích' => '',
                'Dưới 30m2' => 'Dưới 30m2',
                '30m2 - 50m2' => '30m2 - 50m2',
                '50m2 - 80m2' => '50m2 - 80m2',
                '80m2 - 100m2' => '80m2 - 100m2',
                '100m2 - 150m2' => '100m2 - 150m2',
                '150m2 - 200m2' => '150m2 - 200m2',
                '200m2 - 250m2' => '200m2 - 250m2',
                'trên 250m2' => 'trên 250m2',
            ],
            'query_type' => 'custom',
        ],
        'so_phong_ngu' => [
            'label' => 'Số phòng ngủ',
            'type' => 'custom',
            'field' => 'CRMDV.codes.list.filter.loc_khoang_gia',
            'options' => [
                'Chọn số phòng ngủ' => '',
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
            ],
            'query_type' => 'custom',
        ],
        'ban_cong' => [
            'label' => 'Ban công/ Lô gia',
            'type' => 'custom',
            'field' => 'CRMDV.codes.list.filter.loc_khoang_gia',
            'options' => [
                'Chọn ban công' => '',
                '1' => '1',
                '2' => '2',
                '3' => '3',
            ],
            'query_type' => 'custom',
        ],
        'huong_cua_chinh' => [
            'label' => 'Hướng cửa chính',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => '',
                'Đông' => 'Đông',
                'Tây' => 'Tây',
                'Nam' => 'Nam',
                'Bắc' => 'Bắc',
                'Đông Bắc' => 'Đông Bắc',
                'Tây Bắc' => 'Tây Bắc',
                'Tây Nam' => 'Tây Nam',
                'Đông Nam' => 'Đông Nam',
            ]
        ],
        'province_id' => [

            'type' => 'location',
            'field' => 'CRMDV.codes.list.filter.location',
//            'model' => \App\Models\Province::class,
            'query_type' => 'custom',
        ],
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('CRMDV.codes.list')->with($data);
    }

    public function view(Request $request)
    {
        $where = $this->filterSimple($request);
        $listItem = $this->model->whereRaw($where);
        $listItem = $this->quickSearch($listItem, $request);
        if ($this->whereRaw) {
            $listItem = $listItem->whereRaw($this->whereRaw);
        }
//        $listItem = $this->appendWhere($listItem, $request);

        //  Export
        if ($request->has('export')) {
            $this->exportExcel($request, $listItem->take(9000)->get());
        }

        //  Sort
        $listItem = $this->sort($request, $listItem);

        $data['record_total'] = $listItem->count();
        $data = $this->thongKe($data, $listItem, $request);

        if ($request->has('limit')) {
            $data['listItem'] = $listItem->paginate($request->limit);
            $data['limit'] = $request->limit;
        } else {
            $data['listItem'] = $listItem->paginate($this->limit_default);
            $data['limit'] = $this->limit_default;
        }
        $data['page'] = $request->get('page', 1);

        $data['param_url'] = $request->all();

        //  Get data default (param_url, filter, module) for return view
        $data['module'] = [
            'code' => 'codes',
            'table_name' => 'codes',
            'label' => 'Bảng hàng',
            'modal' => '\App\CRMDV\Models\Codes',
            'list' => [
//            ['name' => 'image', 'type' => 'image', 'label' => 'Ảnh'],
                ['name' => 'id', 'type' => 'text', 'label' => 'No'],
                ['name' => 'address', 'type' => 'custom', 'td' => 'CRMDV.codes.list.td.ten_bang_hang', 'label' => 'Địa chỉ'],
//            ['name' => 'link', 'type' => 'relation', 'object' => 'bill', 'display_field' => 'name_vi', 'label' => 'Dự án'],
//            ['name' => 'multi_cat', 'type' => 'custom', 'td' => 'CRMDV.list.td.multi_cat', 'label' => 'Danh mục'],
                ['name' => 'dien_tich', 'type' => 'text', 'label' => 'Diện tích'],
                ['name' => 'gia_niem_yet', 'type' => 'price_vi', 'label' => 'Giá'],
                ['name' => 'so_phong_ngu', 'type' => 'number', 'label' => 'Số phòng ngủ'],
                ['name' => 'phi_moi_gioi', 'type' => 'number', 'label' => 'Phí môi giới'],
                ['name' => 'luot_xem', 'type' => 'number', 'label' => 'Lượt xem',],
                ['name' => 'created_at', 'type' => 'datetime_vi', 'label' => 'Ngày tạo'],
                ['name' => 'admin_id', 'type' => 'relation', 'label' => 'Người tạo', 'object' => 'admin', 'display_field' => 'name'],
            ],
        ];
        $data['quick_search'] = $this->quick_search;
        $data['filter'] = $this->filter;

        //  Set data for seo
        $data['page_title'] = $this->module['label'];
        $data['page_type'] = 'list';
        return view('CRMDV.codes.view')->with($data);
    }

    public function appendWhere($query, $request)
    {
        //  Nếu không có quyền xem toàn bộ dữ liệu thì chỉ được xem các dữ liệu mình tạo
//        if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'view_all_data')) {
//            if (CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'cvkd_parttime')) {
//
//                //  lấy id các thành viên trong phòng mình
//                $admin_ids = Admin::select('id')->where('phong_ban_id', \Auth::guard('admin')->user()->phong_ban_id)->pluck('id')->toArray();
//
//                $query = $query->where(function ($query) use ($admin_ids) {
//                    foreach ($admin_ids as $admin_id) {
//                        $query->orWhere('admin_id', $admin_id); //   xem duoc của thành viên trong phòng mình
//                    }
//                });
//            } else {
//                $query = $query->where('admin_id', \Auth::guard('admin')->user()->id);
//            }
//        }

//        if (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['cvkd_parttime', 'cvkd_fulltime'])) {
//            $admin_ids = Admin::select('id')->where('phong_ban_id', \Auth::guard('admin')->user()->phong_ban_id)->pluck('id')->toArray();
//
//            $query = $query->where(function ($query) use ($admin_ids) {
//                foreach ($admin_ids as $admin_id) {
//                    $query->orWhere('admin_id', $admin_id); //   xem duoc của thành viên trong phòng mình
//                }
//            });
//        }


//        if (!in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['cvkd_fulltime', 'cvkd_parttime'])) {
////            if (CommonHelper::has_permission(\Auth::guard('admin')->user()->id, ['cvkd_fulltime', 'cvkd_parttime'])) {
//
//            // nếu là quyền nvkd full / part thì hiển thị các báo cáo dẫn khách do mình tạo
//            //  lấy id các thành viên trong phòng mình
//            $admin_ids = Admin::select('id')->where('phong_ban_id', \Auth::guard('admin')->user()->phong_ban_id)->pluck('id')->toArray();
//
//            $query = $query->where(function ($query) use ($admin_ids) {
//                foreach ($admin_ids as $admin_id) {
//                    $query->orWhere('admin_id', $admin_id); //   xem duoc của thành viên trong phòng mình
//                }
//            });
//        } else {
//            $query = $query->where('admin_id', \Auth::guard('admin')->user()->id);
//
//        }
        if (!is_null($request->get('province_id'))) {
            $query = $query->where('province_id', '=', $request->province_id);
        }
        if (!is_null($request->get('district_id'))) {
            $query = $query->where('district_id', '=', $request->district_id);
        }

        if (!is_null($request->get('multi_cat'))) {
            $query = $query->where('multi_cat', 'like', '%|' . $request->multi_cat . '|%');
        }

        if ($request->gia_niem_yet != null) {
            if ($request->gia_niem_yet == '1-2 tỷ') {
                $query = $query->where('gia_niem_yet', '>=', 1000000000)->where('gia_niem_yet', '<', 2000000000);
            } elseif ($request->gia_niem_yet == '2-3 tỷ') {
                $query = $query->where('gia_niem_yet', '>=', 2000000000)
                    ->where('gia_niem_yet', '<', 3000000000);
            } elseif ($request->gia_niem_yet == '3-4 tỷ') {
                $query = $query->where('gia_niem_yet', '>=', 3000000000)
                    ->where('gia_niem_yet', '<', 4000000000);
            } elseif ($request->gia_niem_yet == 'trên 4 tỷ') {
                $query = $query->where('gia_niem_yet', '>=', 4000000000);
            }
        }
        if ($request->dien_tich != null) {
            if ($request->dien_tich == 'Dưới 30m2') {
                $query = $query->where('dien_tich', '<', 30);
            } elseif ($request->dien_tich == '30m2 - 50m2') {
                $query = $query->where('dien_tich', '>=', 30)
                    ->where('dien_tich', '<', 50);
            } elseif ($request->dien_tich == '50m2 - 80m2') {
                $query = $query->where('dien_tich', '>=', 50)
                    ->where('dien_tich', '<', 80);
            } elseif ($request->dien_tich == '80m2 - 100m2') {
                $query = $query->where('dien_tich', '>=', 80)->where('dien_tich', '<', 100);
            } elseif ($request->dien_tich == '100m2 - 150m2') {
                $query = $query->where('dien_tich', '>=', 100)->where('dien_tich', '<', 150);
            } elseif ($request->dien_tich == '150m2 - 200m2') {
                $query = $query->where('dien_tich', '>=', 150)->where('dien_tich', '<', 200);
            } elseif ($request->dien_tich == '200m2 - 250m2') {
                $query = $query->where('dien_tich', '>=', 200)->where('dien_tich', '<', 250);
            } elseif ($request->dien_tich == 'trên 250m2') {
                $query = $query->where('dien_tich', '>=', 250);
            }
        }
        if ($request->so_phong_ngu != null) {
            if ($request->so_phong_ngu == '1') {
                $query = $query->where('so_phong_ngu', '=', 1);
            } elseif ($request->so_phong_ngu == '2') {
                $query = $query->where('so_phong_ngu', '=', 2);
            } elseif ($request->so_phong_ngu == '3') {
                $query = $query->where('so_phong_ngu', '=', 3);
            } elseif ($request->so_phong_ngu == '4') {
                $query = $query->where('so_phong_ngu', '=', 4);
            }
        }

        if ($request->ban_cong != null) {
            if ($request->ban_cong == '1') {
                $query = $query->where('ban_cong', '=', 1);
            } elseif ($request->ban_cong == '2') {
                $query = $query->where('ban_cong', '=', 2);
            } elseif ($request->ban_cong == '3') {
                $query = $query->where('ban_cong', '=', 3);
            }
        }
        if (strpos($request->url(), '/da-ban') !== false) {
//  nếu vào trang đã bán thì truy vấn trạng thái đã bán
            $query = $query->where(function ($query) {
                $query->orWhereIn('status', ['Đã bán']);
//                $query->orWhereRaw('status is NULL');
            });

        } elseif (strpos($request->url(), '/tam-dung') !== false) {

            //  Vào quan tâm mới
            $query = $query->where(function ($query) {
                $query->orWhereIn('status', ['Tạm dừng']);
//                $query->orWhereRaw('status is NULL');
            });
        } elseif (strpos($request->url(), '/tat-ca') !== false) {

        } elseif (strpos($request->url(), '/') !== false) {

            //  Vào quan tâm mới
            $query = $query->where(function ($query) {
                $query->orWhereIn('status', ['Đang bán']);
            });
        } else {


        }

        return $query;
    }

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMDV.codes.add')->with($data);
            } else if ($_POST) {

                \DB::beginTransaction();
                $validator = Validator::make($request->all(), [
                    'district_id' => 'required',
                    'province_id' => 'required',
                    'so_do_va_hop_dong_chu_nha' => 'required',
                    'ward_id' => 'required',
                    'image_extra' => 'required',
                    'content' => 'required',
                    'dien_tich' => ['required', 'regex:/^[0-9]+([,.][0-9]+)?$/u'],
//                    'gia_niem_yet' => ['required', 'regex:/^[0-9]+([,.][0-9]+)?$/'],
//                    'phi_moi_gioi' => ['required', 'regex:/^[0-9]+([,.][0-9]+)?$/'],
                    'sdt_chu_nha' => 'required|regex:/^[0-9]{10}$/',


                ], [
                    'so_do_va_hop_dong_chu_nha.required' => 'Bắt buộc phải sổ đỏ và hợp đồng với chủ nhà',
                    'image_extra.required' => 'Bắt buộc phải nhập ảnh',
                    'district_id.required' => 'Quận huyện bắt buộc chọn',
                    'ward_id.required' => 'Phường xã bắt buộc chọn',
                    'province_id.required' => 'Thành phố bắt buộc chọn',
                    'content.required' => 'Mô tả không được để trống',
                    'sdt_chu_nha.regex' => 'Số điện thoại khách không đúng định dạng',
//                    'dien_tich.regex' => 'Diện tích không đúng định dạng',
//                    'dien_tich.required' => 'Diện tích bắt buộc nhập',
//                    'dien_tich.numeric' => 'Diện tích bắt buộc là số'


                ]);
                if ($validator->fails()) {
                    CommonHelper::one_time_message('error', 'Cập nhật thất bại, vui lòng kiểm tra lại.');
                    return back()->withErrors($validator)->withInput();
                } else {
//                                    dd(1);

                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert

                    $data['admin_id'] = \Auth::guard('admin')->user()->id;
                    $data['district_id'] = $request->district_id;
                    $data['ward_id'] = $request->ward_id;
                    $data['gia_niem_yet'] = str_replace(".", "", $request->gia_niem_yet);
                    $data['phi_moi_gioi'] = str_replace(".", "", $request->phi_moi_gioi);

                    if ($request->has('image_extra')) {
                        $data['image_extra'] = implode('|', $request->image_extra);
                    }
//                    dd($data['image_extra']);

                    if ($request->has('so_do_va_hop_dong_chu_nha')) {
                        $data['so_do_va_hop_dong_chu_nha'] = implode('|', $request->so_do_va_hop_dong_chu_nha);
                    }


                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
                        $this->model->code_id = 'S' . $this->model->id;
                        $this->model->save();
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

    //  Xử lý tag
    public function xulyTag($post_id, $data)
    {
        $id_updated = [];
        $tags = json_decode($data['tags']);

        if (is_array($tags) && !empty($tags)) {
            foreach ($tags as $tag_name) {
                $tag_name = $tag_name->value;
                //  Tạo tag nếu chưa có
                $tag = Tag::where('name', $tag_name)->first();
                if (!is_object($tag)) {
                    $tag = new Tag();
                    $tag->name = $tag_name;
                    $tag->slug = str_slug($tag_name, '-');
                    $tag->type = 'code';
                    $tag->save();
                }


                $post_tag = PostTag::updateOrCreate([
                    'post_id' => $post_id,
                    'tag_id' => $tag->id,
                ], [
                    'multi_cat' => $data['multi_cat']
                ]);
                $id_updated[] = $post_tag->id;
            }
        }
        //  Xóa tag thừa
        PostTag::where('post_id', $post_id)->whereNotIn('id', $id_updated)->delete();

        return true;
    }

    public function update(Request $request)
    {
        try {


            $item = $this->model->find($request->id);

            if (!is_object($item)) abort(404);
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('CRMDV.codes.edit')->with($data);
            } else if ($_POST) {
                \DB::beginTransaction();

                $validator = Validator::make($request->all(), [
                    'district_id' => 'required',
                    'province_id' => 'required',
                    'so_do_va_hop_dong_chu_nha' => 'required',
                    'ward_id' => 'required',
                    'image_extra' => 'required',
                    'content' => 'required',
                    'dien_tich' => ['required', 'regex:/^[0-9]+([,.][0-9]+)?$/u'],
//                    'gia_niem_yet' => ['required', 'regex:/^[0-9]+([,.][0-9]+)?$/'],
//                    'phi_moi_gioi' => ['required', 'regex:/^[0-9]+([,.][0-9]+)?$/'],
                    'sdt_chu_nha' => 'required|regex:/^[0-9]{10}$/',


                ], [
                    'so_do_va_hop_dong_chu_nha.required' => 'Bắt buộc phải sổ đỏ và hợp đồng với chủ nhà',
                    'image_extra.required' => 'Bắt buộc phải nhập ảnh',
                    'district_id.required' => 'Quận huyện bắt buộc chọn',
                    'ward_id.required' => 'Phường xã bắt buộc chọn',
                    'province_id.required' => 'Thành phố bắt buộc chọn',
                    'content.required' => 'Mô tả không được để trống',
                    'sdt_chu_nha.regex' => 'Số điện thoại khách không đúng định dạng',
//                    'dien_tich.regex' => 'Diện tích không đúng định dạng',
//                    'dien_tich.required' => 'Diện tích bắt buộc nhập',
//                    'dien_tich.numeric' => 'Diện tích bắt buộc là số'


                ]);

                if ($validator->fails()) {
                    CommonHelper::one_time_message('error', 'Cập nhật thất bại, vui lòng kiểm tra lại.');
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    //  Tùy chỉnh dữ liệu insert
                    if ($request->has('image_extra')) {
                        $data['image_extra'] = implode('|', $request->image_extra);
                    }

//                    dd($data['image_extra']);
                    if ($request->has('so_do_va_hop_dong_chu_nha')) {
                        $data['so_do_va_hop_dong_chu_nha'] = implode('|', $request->so_do_va_hop_dong_chu_nha);
                    }
                    $data['district_id'] = $request->district_id;
                    $data['ward_id'] = $request->ward_id;
                    $data['gia_niem_yet'] = str_replace(".", "", $request->gia_niem_yet);
                    $data['phi_moi_gioi'] = str_replace(".", "", $request->phi_moi_gioi);
                    $item->phi_moi_gioi = $data['phi_moi_gioi'];
                    $item->save();

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

    public function exportExcel($request, $data)
    {
        \Excel::create(str_slug($this->module['label'], '_') . '_' . date('d_m_Y'), function ($excel) use ($data) {

            // Set the title
            $excel->setTitle($this->module['label'] . ' ' . date('d m Y'));

            $excel->sheet(str_slug($this->module['label'], '_') . '_' . date('d_m_Y'), function ($sheet) use ($data) {

                $field_name = ['Mã tin'];
                foreach ($this->getAllFormFiled() as $field) {
                    if (!isset($field['no_export']) && isset($field['label'])) {
                        $field_name[] = $field['label'];
                    }
                }

                //   thêm cột tỉnh / huyện / xã
                $field_name[] = 'Tỉnh';
                $field_name[] = 'Huyện';
                $field_name[] = 'Xã';


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
                                if ($field['label'] == 'Mô tả chi tiết') {
                                    $dataInput = $value->{$field['name']};
                                    $data_export[] = strip_tags($dataInput);
                                }
                                elseif (in_array($field['type'], ['text', 'number', 'textarea', 'textarea_editor', 'date', 'datetime-local', 'email', 'hidden', 'checkbox', 'textarea_editor', 'textarea_editor2', 'custom', 'radio', 'price_vi'])) {
                                    if(in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['cvkd_parttime'])) {
                                        if($field['label'] == 'Địa chỉ chi tiết') {
                                            $data_export[] = '--Đã ẩn đối với quyền cvkd parttime--';
                                        } else {
                                            $data_export[] = $value->{$field['name']};
                                        }
                                    }else {
                                        $data_export[] = $value->{$field['name']};
                                    }
                                } elseif (in_array($field['type'], [
                                    'relation', 'select_model', 'select2_model', 'select2_ajax_model', 'select_model_tree',

                                ])) {
                                    $data_export[] = @$value->{$field['object']}->{$field['display_field']};
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

                    //  xuất ra tỉnh / huyện / xã
                    $data_export[] = @$value->province->name;
                    $data_export[] = @$value->district->name;
                    $data_export[] = @$value->ward->name;

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
        if (!is_object($data)) abort(404);
        $service = $data->service->name_vi;
        // tăng số lượt xem thêm 1
        $data->luot_xem += 1;
        $data->save();

        // lấy thông tin đầu chủ
        $dauchu = \App\CRMDV\Models\Admin::query()->where('id', $data->admin_id)->first();
        $anhDauChu = asset('/filemanager/userfiles/' . $dauchu->image);


        //lay thong tin phong ban
        $phongban = \App\CRMDV\Models\Phong_ban::query()->where('id', $dauchu->phong_ban_id)->first();

        $imagePath = asset('/filemanager/userfiles/' . $data->image);
        $anhSoDo = asset('/filemanager/userfiles/' . $data->so_do_va_hop_dong_chu_nha);
        // image chi tiet
        $imagePaths = explode('|', $data->image_extra);
        $fullPaths = array_map(function ($path) {
            return asset('/filemanager/userfiles/' . $path);
        }, $imagePaths);

        // image sổ đỏ và hợp đồng với chủ nhà
        $imageRedBook = explode('|', $data->so_do_va_hop_dong_chu_nha);
        $imageRedBooks = array_map(function ($path) {
            return asset('/filemanager/userfiles/' . $path);
        }, $imageRedBook);


        $show = true;
        if ($data->admin_id == \Auth::guard('admin')->user()->id || \Auth::guard('admin')->user()->super_admin == 1 || CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'hcns_195')) {
            $show = true;
        } else {
            $show = false;
        }
        return response()->json([
            'status' => true,
            'data' => $data,
            'service' => $service,
            'imageRedBooks' => $imageRedBooks,
            'imagePaths' => $fullPaths,
            'dauchu' => $dauchu,
            'anhDauChu' => $anhDauChu,
            'phongban' => $phongban,
            'show' => $show
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
