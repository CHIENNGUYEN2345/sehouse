<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Models\Category;
use Illuminate\Http\Request;

class ExampleControllerController extends CURDBaseController
{
    protected $whereRaw = false;    //  Tuy chinh where. Mac dinh false
    protected $orderByRaw = 'id desc';  //  Tuy chinh order. Mac dinh : id desc
    protected $limit_default = 15;  //  limit so phan tu truy van. Mac dinh = 15
     // Module can nhung quyen nao thi de quyen do vao. mac dinh ko phan quyen
    protected $join_table = [       // Su dung join bang cho trang view
        [
            'table' => 'users',
            'ownerKey' => 'users.id',
            'character' => '=',
            'foreignKey' => 'bookings.customer_id',
            'type' => 'right',
        ],
        [
            'table' => 'tours',
            'ownerKey' => 'tours.id',
            'character' => '=',
            'foreignKey' => 'bookings.customer_id',
            'type' => 'right',
        ],
    ];

    protected $module = [
        'code' => 'example',    // Mã module
        'table_name' => 'examples',   //  Ten bang
        'label' => 'Ví dụ',     // Tên hiển thị của  module
        'modal' => '\App\Models\Example',   // Đường dẫn  file model của bảng
        'list' => [     //   Cấu hình hiển thị các cột ra trang danh khóa học. name:tên trường trong db.  type:loại dữ liệu hiển thị.  label: tên cột
            'view' => 'list.simple',    //   simple | datatable | tree
            'td' => [
                ['name' => 'image', 'type' => 'image', 'label' => 'Ảnh'],
                ['name' => 'name_vi', 'type' => 'text', 'label' => 'Tên'],
                ['name' => 'category_id', 'type' => 'belongsTo', 'label' => 'Loại', 'object' => 'category_product', 'display_field' => 'name', 'sort_name' => 'tours.title',
                    'tooltip_info' => [
                        ['name' => 'name_vi', 'type' => 'text', 'label' => 'Tên'],
                        ['name' => 'user_id', 'type' => 'relation', 'label' => 'Tên khách hàng', 'object' => 'user', 'display_field' => 'name'],
                    ]],
                ['name' => 'category_id', 'type' => 'hasOne', 'label' => 'Loại', 'hasOne' => 'category_product', 'display_field' => 'name', 'sort_name' => 'tours.title'],
                ['name' => 'filter', 'type' => 'filter', 'route' => 'tour', 'key' => 'user_id', 'label' => 'Lịch sử tour', 'where' => ['user_id' => 1]],
                ['name' => 'status', 'type' => 'status', 'label' => 'Trạng thái'],
            ],
            'include_footer_script' => [
                'public/libs/select2/js/select2.min.js',
            ],
            'include_header_script' => [
                'public/libs/select2/css/select2.min.css'
            ],
        ],
        'form' => [     // Cấu hình các ô nhập liệu trong trang thêm/sửa dữ liệu.
            'view' => 'form.simple',        //  simple | tab | multi_box
            'tabs' => [
                'general_tab' => [
                    'label' => 'Thông tin chung',
                    'td' => [
                        //  Input thông thường
                        ['name' => 'name_vi', 'type' => 'text', 'class' => 'require', 'label' => 'Tên'],
                        ['name' => 'name_vi', 'type' => 'slug', 'class' => 'require', 'label' => 'Slug'],
                        ['name' => 'base_price', 'type' => 'number', 'label' => 'Giá cũ'],
                        ['name' => 'intro', 'type' => 'textarea', 'label' => 'Mô tả ngắn'],
                        ['name' => 'status', 'type' => 'select', 'options' =>
                            [
                                0 => 'Inactive',
                                1 => 'Active',
                            ], 'label' => 'Trạng thái', 'value' => 1],
                        ['name' => 'status', 'type' => 'checkbox', 'label' => 'Kích hoạt', 'value' => 1, 'no_export' => true],       //  no_export là không xuất field này khi xuất excel

                        //  Input sử dụng editor
                        ['name' => 'content', 'type' => 'textarea_editor', 'label' => 'Nội dung'],
                        ['name' => 'image', 'type' => 'file_editor', 'label' => 'Ảnh mô tả'],
                        ['name' => 'image_extra', 'type' => 'file_editor_extra', 'label' => 'Ảnh khác', 'count' => 4],

                        //  Input truy vấn  dữ liệu DB
                        ['name' => 'manufacture_id', 'type' => 'select2_model', 'label' => 'Nhà phân phối', 'model' => \App\Models\Company::class, 'display_field' => 'name'],
                        ['name' => 'ward_id', 'type' => 'select2_model', 'label' => 'Phường/Xã', 'model' => \App\Models\Ward::class, 'display_field' => 'name', 'where_attr' => 'district_id'],
                        ['name' => 'manufacture_id', 'type' => 'select2_ajax_model', 'label' => 'Nhà phân phối', 'object' => 'company', 'display_field' => 'name'],
                        ['name' => 'multi_cat', 'type' => 'select_multiple_category', 'label' => 'Danh mục', 'options' => [5]],
                        ['name' => 'parent_id', 'type' => 'select_model_tree', 'label' => 'Danh mục cha', 'model' => \App\Models\Category::class, 'where' => "type = 'category_product'"],

                        //  Input relation
                        ['name' => 'phone', 'type' => 'belongs_to_text', 'label' => 'Số điện thoại', 'object' => 'details'],  //  liên kết đến bảng khác. field = text
                        ['name' => 'birthday', 'type' => 'belongs_to_date', 'label' => 'Ngày sinh', 'object' => 'details'],
                        ['name' => 'font_photo', 'type' => 'belongs_to_file', 'label' => 'Ảnh mặt trước CMND', 'object' => 'passport'],
                        ['name' => 'font_photo', 'type' => 'belongs_to_file_image', 'label' => 'Ảnh mặt trước CMND', 'object' => 'passport'], //  liên kết đến bảng khác. field = file ảnh
                        ['name' => 'gender', 'type' => 'belongs_to_select', 'object' => 'details', 'options' =>
                            [
                                0 => 'Nam',
                                1 => 'Nữ',
                                2 => 'Chưa rõ'
                            ], 'label' => 'Giới tính'],

                        //  Input không  nhận giá trị
                        ['name' => 'view_more', 'type' => 'inner', 'label' => 'Xem thêm'],
                        ['name' => 'id', 'type' => 'hidden', 'label' => 'ID'],

                        //  Input khác
                        ['name' => 'dac_diem_khac', 'type' => 'checkbox_multiple', 'label' => 'Đặc điểm khác', 'options' => [
                            1 => 'Tiện để ở',
                            2 => 'Gần trường',
                            3 => 'Khu nội bộ',
                        ]],
                        ['name' => 'photos', 'type' => 'multiple_image', 'object' => 'tours', 'label' => 'Hình ảnh tour', 'count' => 5],
                        ['name' => 'price_options', 'type' => 'dynamic', 'label' => 'Thời gian & tiền'],
                    ],
                ],
            ],
            'include_footer_script' => [    //  Chèn thêm các file js vào footer
                'public/libs/select2/js/select2.min.js',
            ],
            'include_header_script' => [    //  Chèn  thêm các file css vào header
                'public/libs/select2/css/select2.min.css'
            ],
        ],
    ];

    protected $filter = [   // Cấu hình bộ lọc
        'name_vi' => [
            'label' => 'Tên',   //  Tên hiển thị
            'type' => 'text',   // Loại input
            'query_type' => 'like',  // Kiểu truy vấn
        ],
        'category_id' => [
            'label' => 'Loại hình',
            'type' => 'select2_model',
            'display_field' => 'name',
            'model' => \App\Models\Category::class,
            'where' => "type = 'category_product'",
            'query_type' => '='
        ],
        'final_price' => [
            'label' => 'Giá bán',
            'type' => 'number',
            'query_type' => '='
        ],
        'status' => [
            'label' => 'Trạng thái',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => 'Trạng thái',
                0 => 'Ẩn',
                1 => 'Duyệt'
            ]
        ],
    ];

    protected $validate = [     // Cấu hình xác thực dữ liệu khi thêm /sửa
        'request' => [
            'fullname' => 'required',
            'email' => 'required',
        ],
        'label' => [
            'fullname' => 'Họ tên',
            'email' => 'Email',
        ]
    ];

    protected $validate_add = [     // Cấu hình xác thực dữ liệu khi thêm (cộng thêm với cái validate ở trên)
        'request' => [
            'email' => 'required|unique:users',
            'password' => 'required',
        ],
        'label' => [
            'password' => 'Mật khẩu',
        ]
    ];

    public function editColumn($dataTable)      //  Chỉnh sửa cột ở trang danh khóa học. Chỉ  dùng cho view=datatable
    {
        $dataTable = $dataTable->addColumn('multi_cat', function ($item) {
            if ($item->multi_cat == null) return '';
            $cat_id_arr = [];
            foreach (explode('|', $item->multi_cat) as $v) {
                if ($v != '') $cat_id_arr[] = $v;
            }
            $categories = Category::whereIn('id', $cat_id_arr)->pluck('name');
            $html = '';
            foreach ($categories as $v) {
                $html .= $v . ' | ';
            }

            return substr($html, 0, -3);
        });
        return $dataTable;
    }

    public function appendData($request, $data, $item = false)     // Xử lý các input phức tạp truyền vào trong hành động thêm/sửa
    {
        if ($request->has('multi_cat')) {
            $multi_cat = '';
            foreach ($request->get('multi_cat') as $k => $v) {
                if ($k == 0)
                    $multi_cat .= '|' . $v . '|';
                else
                    $multi_cat .= $v . '|';
            }
            $data['multi_cat'] = $multi_cat;
        }
        return $data;
    }

    public function afterAdd($request, $item)      // Sau khi thêm mới thành công sẽ thực hiện các lệnh này
    {
        if ($request->has('font_photo')) {
            $file_name = $request->file('font_photo')->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $file_name_insert = date('s_i_') . $file_name;
            $request->file('font_photo')->move(base_path() . '/public/uploads/passport/', $file_name_insert);
            $passport['font_photo'] = $file_name_insert;
        }
        if (isset($passport)) {
            Passport::updateOrCreate(['user_id' => $item->id], $passport);
        }
        return true;
    }

    public function afterUpdate($request, $item)       // Sau khi cập nhật thành công sẽ thực hiện các lệnh này
    {
        if ($request->has('font_photo')) {
            $file_name = $request->file('font_photo')->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $file_name_insert = date('s_i_') . $file_name;
            $request->file('font_photo')->move(base_path() . '/public/uploads/passport/', $file_name_insert);
            $passport['font_photo'] = $file_name_insert;
        }
        if (isset($passport)) {
            Passport::updateOrCreate(['user_id' => $item->id], $passport);
        }
        return true;
    }

    public function appendWhere($query, $request)    // Thêm đoạn truy vấn cho bộ lọc ở trang danh khóa học
    {
        $query = $query->where('type_login', 0);
        return $query;
    }

    public function updateAttributes($request, $item)
    {
        if ($request->has('price_options_key')) {
            $key_update = [];
            foreach ($request->price_options_key as $k => $key) {
                if ($key != null && $request->price_options_value[$k] != null) {
                    $key_update[] = $key;
                    Attribute::updateOrCreate([
                        'key' => $key,
                        'table' => $this->module['table_name'],
                        'type' => 'price_options',
                        'item_id' => $item->id
                    ], [
                        'value' => $request->price_options_value[$k]
                    ]);
                }
            }
            if (!empty($key_update)) {
                Attribute::where([
                    'table' => $this->module['table_name'],
                    'type' => 'price_options',
                    'item_id' => $item->id
                ])->whereNotIn('key', $key_update)->delete();
            }
        } else {
            Attribute::where([
                'table' => $this->module['table_name'],
                'type' => 'price_options',
                'item_id' => $item->id
            ])->delete();
        }
        return true;
    }
}
