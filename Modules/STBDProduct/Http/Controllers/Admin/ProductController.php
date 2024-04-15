<?php

namespace Modules\STBDProduct\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Models\Setting;
use Auth;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use Modules\STBDBill\Models\Order;
use Modules\STBDProduct\Models\Category;
use Modules\STBDProduct\Models\Guarantees;
use Modules\STBDProduct\Models\Manufacturer;
use Modules\STBDProduct\Models\Origin;
use Modules\STBDProduct\Models\Post;
use Modules\STBDProduct\Models\Product;
use Modules\STBDProduct\Models\ProductAttribute;
use Modules\STBDProduct\Models\PropertieName;
use Modules\STBDProduct\Models\PropertieValue;
use Modules\STBDProduct\Models\PropertyValue;
use Validator;
use DB;
class ProductController extends CURDBaseController
{
    protected $module = [
        'code' => 'product',
        'table_name' => 'products',
        'label' => 'Sản phẩm',
        'modal' => '\Modules\STBDProduct\Models\Product',
        'list' => [
            ['name' => 'image', 'type' => 'image', 'label' => 'Ảnh', 'foder' => 'product_files'],
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Tên'],
            ['name' => 'wss_stt', 'type' => 'text', 'label' => 'Thứ tự WSS'],
            ['name' => 'wss_top1_amount', 'type' => 'price', 'label' => 'Giá top 1'],
            ['name' => 'wss_top1_website', 'type' => 'text', 'label' => 'Web top 1'],
            ['name' => 'code', 'type' => 'text', 'label' => 'Mã'],
            ['name' => 'multi_cat', 'type' => 'category',  'object' => 'category', 'display_field' => 'name', 'display_field2' => 'id', 'label' => 'Danh mục'],
            ['name' => 'manufacture_id', 'type' => 'manufacture', 'object' => 'manufacture', 'display_field' => 'name', 'display_field2' => 'id', 'label' => 'Hãng'],
            // ['name' => 'tags', 'type' => 'custom', 'td' => 'stbdproduct::list.td.multi_cat', 'label' => 'Tags'],
            ['name' => 'base_price', 'type' => 'input', 'label' => 'Giá gốc'],
            ['name' => 'final_price', 'type' => 'input', 'label' => 'Giá bán'],
            ['name' => 'sale', 'type' => 'custom', 'td' => 'stbdproduct::list.td.sale', 'label' => 'Chiết khấu'],
            ['name' => 'guarantee', 'type' => 'guarantees', 'object' => 'guarantees', 'display_field' => 'name', 'display_field2' => 'id', 'label' => 'Bảo hành'],
            ['name' => 'origin_id', 'type' => 'origins', 'object' => 'origin', 'display_field' => 'name_origin', 'label' => 'Xuất xứ'],
            ['name' => 'status', 'type' => 'status', 'label' => 'Trạng thái'],
            ['name' => 'featured', 'type' => 'custom', 'td' => 'stbdproduct::list.td.featured', 'label' => 'Bán chạy'],
            ['name' => 'id', 'type' => 'custom', 'td' => 'stbdproduct::list.td.count_buy_product', 'label' => 'Lượt mua'],
            ['name' => 'order_no', 'type' => 'input', 'label' => 'Thứ tự'],
            ['name' => 'gift_max', 'type' => 'number', 'label' => 'Giá trị quà tặng'],
            ['name' => 'admin_id', 'type' => 'relation', 'object' => 'admin', 'display_field' => 'name', 'label' => 'Người tạo'],
            // ['name' => 'view', 'type' => 'custom', 'td' => 'stbdproduct::list.td.view_frontend', 'label' => 'Xem'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên sản phẩm'],
                ['name' => 'code', 'type' => 'text', 'label' => 'Mã sản phẩm', 'group_class' => 'col-md-6'],
                ['name' => 'international_Code', 'type' => 'text', 'class' => '', 'label' => 'Mã quốc tế', 'group_class' => 'col-md-6'],

                ['name' => 'order_no', 'type' => 'number', 'class' => '', 'label' => 'Thứ tự', 'value' => 1, 'group_class' => 'col-md-4'],
                ['name' => 'base_price', 'type' => 'text', 'label' => 'Giá ban đầu', 'class' => 'number_price', 'group_class' => 'col-md-4'],
                ['name' => 'final_price', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.number_sale', 'label' => 'Giá bán', 'class' => 'number_price', 'group_class' => 'col-md-4'],

                ['name' => 'multi_cat', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.multi_cat', 'label' => 'Danh mục sản phẩm', 'model' => Category::class,
                    'object' => 'category_product', 'display_field' => 'name', 'multiple' => true, 'where' => 'type=5', 'des' => 'Danh mục đầu tiên chọn là danh mục chính'],


                ['name' => 'guarantee', 'type' => 'select2_ajax_model', 'object' => 'guarantees', 'class' => '', 'group_class' => 'col-md-4',
                    'label' => 'Thời gian bảo hành', 'model' => Guarantees::class, 'display_field' => 'name', 'display_field2' => 'id'],
                ['name' => 'origin_id', 'type' => 'select2_ajax_model', 'label' => 'Xuất xứ', 'model' => Origin::class,
                    'object' => 'origin', 'display_field' => 'name_origin', 'group_class' => 'col-md-4'],
                ['name' => 'manufacture_id', 'type' => 'select2_ajax_model', 'object' => 'manufacturer', 'class' => '',
                    'label' => 'Nhà phân phối', 'model' => Manufacturer::class, 'display_field' => 'name', 'where' => 'status=1', 'display_field2' => 'id', 'group_class' => 'col-md-4'],

                ['name' => 'proprerties_id', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.proprerties_id', 'object' => 'properties_value', 'multiple' => true, 'class' => '', 'label' => 'Tất cả thuộc tính',
                    'model' => PropertyValue::class, 'display_field' => 'value', 'display_field2' => 'id'],

                ['name' => 'tags', 'type' => 'select2_ajax_model', 'label' => 'Từ khóa sản phẩm', 'model' => Category::class,
                    'object' => 'tag_product', 'display_field' => 'name', 'multiple' => true, 'where' => 'type=6'],

                ['name' => 'intro', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.count_char_intro', 'class' => '', 'label' => 'Mô tả ngắn (Tối đa 120 ký tự)'],
                ['name' => 'content', 'type' => 'textarea_editor', 'class' => '', 'label' => 'Nội dung'],
                ['name' => 'highlight', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.editor2', 'class' => '', 'label' => 'Đặc điểm nổi bật'],
//                ['name' => 'highlight', 'type' => 'textarea_editor2', 'class' => '', 'label' => 'Đặc điểm nổi bật'],
                ['name' => 'review_detail', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.editor2', 'class' => '', 'label' => 'Đánh giá chi tiết'],

                ['name' => 'status', 'type' => 'checkbox', 'class' => '', 'label' => 'Kích hoạt', 'value' => 1, 'group_class' => 'col-md-4'],
                ['name' => 'quantity', 'type' => 'number', 'class' => '', 'label' => 'Số lượng', 'value' => 1, 'group_class' => 'col-md-4'],
                ['name' => 'stock', 'type' => 'checkbox', 'class' => '', 'label' => 'Còn hàng', 'value' => 1, 'group_class' => 'col-md-4'],

                ['name' => 'featured', 'type' => 'checkbox', 'class' => '', 'label' => 'Hiển thị trang chủ', 'value' => 0, 'group_class' => 'col-md-4'],
                ['name' => 'hot', 'type' => 'checkbox', 'class' => '', 'label' => 'SP Hot', 'value' => 0, 'group_class' => 'col-md-4'],
                ['name' => 'new', 'type' => 'checkbox', 'class' => '', 'label' => 'SP mới', 'value' => 0, 'group_class' => 'col-md-4'],
//                ['name' => 'link_wss', 'type' => 'text', 'label' => 'Link bên websosanh', 'group_class' => 'col-md-12'],

//                ['name' => 'gift', 'type' => 'checkbox', 'label' => 'Dạng quà tặng', 'value' => 0, 'group_class' => 'col-md-4'],
//                ['name' => 'popular', 'type' => 'checkbox', 'label' => 'Nổi bật', 'group_class' => 'col-md-3'],
            ],

            'image_tab' => [
                ['name' => 'image', 'type' => 'file_editor', 'label' => 'Ảnh sản phẩm'],
                ['name' => 'image_extra', 'type' => 'multiple_image_editor_no_limit', 'label' => 'Ảnh khác'],
            ],
            'attribute_tab' => [
                ['name' => 'price_options',
                    'type' => 'custom',  'field' => 'stbdproduct::form.fields.price_options', 'class' => '',
                    'label' => 'Tùy chỉnh giá sản phẩm',],
            ],
            'related_products_tab' => [

                ['name' => 'related_products',
                    'type' => 'select2_ajax_model', 'class' => '',
                    'label' => 'Sản phẩm liên quan',
                    'object' => 'product',
                    'model' => Product::class,
                    'multiple' => true,
                    'display_field' => 'name',
                    'display_field2' => 'id'],
                ['name' => 'related_post',
                    'type' => 'custom',  'field' => 'stbdproduct::form.fields.related_post', 'class' => '',
                    'label' => 'Tin tức liên quan',
                    'display_field' => 'name',
                    'display_field2' => 'id'],

                ['name' => 'gift_list', 'type' => 'text', 'class' => '', 'label' => 'Quà tặng'],
                ['name' => 'gift_max', 'type' => 'text', 'class' => '', 'label' => 'Giới hạn giá trị quà'],
//                ['name' => 'gift_list',
//                    'type' => 'select2_ajax_model', 'class' => '',
//                    'label' => 'Quà tặng',
//                    'object' => 'product',
//                    'model' => Product::class,
//                    'multiple' => true,
////                    'where' => 'gift=1',
//                    'display_field' => 'name',
//                    'display_field2' => 'id'],
//

//                ['name' => 'gift_max', 'type' => 'number', 'label' => 'Số quà cho phép'],

            ],
            'seo_tab' => [
                ['name' => 'slug', 'type' => 'slug', 'class' => 'required', 'label' => 'slug', 'des' => 'Đường dẫn sản phẩm trên thanh địa chỉ'],
                ['name' => 'meta_title', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.count_char_intro', 'class' => '', 'label' => 'Meta title', 'max_char' => 1000],
                ['name' => 'meta_description', 'type' => 'custom', 'field' => 'stbdproduct::form.fields.count_char_intro', 'class' => '', 'label' => 'Meta description', 'max_char' => 1000],
                ['name' => 'meta_keywords', 'type' => 'text', 'label' => 'Meta keywords'],
                ['name' => 'star_number', 'type' => 'text', 'label' => 'Số sao'],
                ['name' => 'review_number', 'type' => 'text', 'label' => 'số đánh giá'],
            ],
        ],
    ];

    protected $quick_search = [
        'label' => 'ID, tên, mã, giá',
        'fields' => 'id, name, code, final_price, base_price'
    ];

    protected $filter = [
        /*'name' => [
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
        ],*/
//        'count_buy_product' => [
//            'label' => 'Lượt mua',
//            'type' => 'number',
//            'query_type' => 'custom'
//        ],

//        'category_id' => [
//            'label' => 'Danh mục',
//            'type' => 'select2_ajax_model',
//            'display_field' => 'name',
//            'object' => 'category_product',
//            'model' => \Modules\STBDProduct\Models\Category::class,
//            'query_type' => '='
//        ],

        'multi_cat' => [
            'label' => 'Danh mục',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'object' => 'category_product',
            'model' => \Modules\STBDProduct\Models\Category::class,
            'query_type' => 'custom'
        ],

        'manufacture_id' => [
            'label' => 'Thương hiệu',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'object' => 'manufacturer',
            'model' => \Modules\STBDProduct\Models\Manufacturer::class,
            'query_type' => '='
        ],

        'tags' => [
            'label' => 'Từ khóa',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'object' => 'tag_product',
            'model' => \Modules\STBDProduct\Models\Category::class,
            'query_type' => '='
        ],
        'featured' => [
            'label' => 'Nổi bật',
            'type' => 'select',
            'query_type' => '=',
            'options' => [
                '' => 'Trạng thái',
                0 => 'Không kích hoạt',
                1 => 'Kích hoạt',
            ],
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
        'assign_proprerties' => [
            'label' => 'Thuộc tính',
            'type' => 'select',
            'query_type' => 'custom',
            'options' => [
                '' => 'Thuộc tính',
                0 => 'Chưa gán',
                1 => 'Đã gán',
            ],
        ],
        'proprerties_id' => [
            'label' => 'GT thuộc tính',
            'type' => 'select2_ajax_model',
            'display_field' => 'value',
            'object' => 'properties_value',
            'model' => \Modules\STBDProduct\Models\PropertieValue::class,
            'query_type' => 'custom'
        ],
        'link_wss' => [
            'label' => 'WSS LINK',
            'type' => 'select',
            'query_type' => 'custom',
            'options' => [
                '' => 'Chọn loại',
                0 => 'Không có link WSS',
                1 => 'Đã có link WSS',
            ],
        ],
    ];

    public function getIndex(Request $request)
    {

        $guarantees = Guarantees::all();
        $origins = Origin::all();
        $manufacture = Manufacturer::all();
        $category = Category::all();
        $data = $this->getDataList($request);
        $query="SELECT wss_updated_at FROM products ORDER BY wss_updated_at DESC LIMIT 1";
        $resp = DB::select(DB::raw($query));
        $wss_updated_at = $resp[0]->wss_updated_at;
        $query="SELECT count(id) AS total FROM products WHERE link_wss IS NULL OR link_wss =''";
        $resp = DB::select(DB::raw($query));
        $wss_link_count_null = $resp[0]->total;

        return view('stbdproduct::list',compact("guarantees" , "origins","manufacture","category","wss_updated_at","wss_link_count_null"))->with($data);
    }

    public function appendWhere($query, $request)
    {
//        dd($request);
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


        if (!is_null($request->get('multi_cat'))) {
            $query = $query->where('multi_cat', 'LIKE', '%|' . $request->multi_cat . '|%');
        }
        if (!is_null($request->get('tags'))) {
            $query = $query->where('multi_cat', 'LIKE', '%|' . $request->category_id . '|%');
        }
        if (!is_null($request->get('assign_proprerties'))) {
            if ($request->assign_proprerties == 0) {
                $query = $query->where('proprerties_id', null);
            } else {
                $query = $query->where('proprerties_id', '!=', null);
            }
        }
        if (!is_null($request->get('proprerties_id'))) {
            $query = $query->where('proprerties_id', 'like', '%|'.$request->proprerties_id.'|%');
        }
        if (!is_null($request->get('link_wss'))) {
            if ($request->link_wss == 0) {
                $query = $query->where('link_wss', null);
            } else {
                $query = $query->where('link_wss', '!=', null);
            }
        }
//        dd($request->sorts[13]);
//        if (!is_null($request->get('count_buy_product'))) {
//            $prdIds = Product::selectRaw('product_id, sum(quantity) AS luot_mua' )->join('orders', 'products.id', '=', 'orders.product_id')->groupBy('product_id')->pluck('product_id','luot_mua')->toArray();
////            $prdIds = Order::where('product_id',$item->id)->get()->sum('quantity');
////dd($prdIds);
//            $query = $query->where('id', $prdIds[$request->get('count_buy_product')]);
//        }
//


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
                    unset($data['price_options']);

                    //  Tùy chỉnh dữ liệu insert
                    if ($request->has('multi_cat')) {
                        $data['multi_cat'] = '|' . implode('|', $request->multi_cat) . '|';
                        $data['category_id'] = $request->multi_cat[0];
                    }

                    if ($request->has('related_products')) {
                        $data['related_products'] = '|' . implode('|', $request->related_products) . '|';

                    }
//
//                    if ($request->has('manufacture_id')) {
//                        $data['manufacture_id'] = '|' . implode('|', $request->manufacture_id) . '|';
//                    }
                    if ($request->has('related_post')) {
                        $data['related_post'] = '|' . implode('|', $request->related_post) . '|';
                    }

                    $data['admin_id'] = \Auth::guard('admin')->user()->id;
                    if ($request->has('tags')) {
                        $data['tags'] = '|' . implode('|', $request->tags) . '|';
                    }
                    if ($request->has('image_extra')) {
                        $img_extra = '';
                        foreach ($request->image_extra as $image_extra) {
                            $img_extra .= (@explode('filemanager/userfiles/', $image_extra)[1] . '|');
                        }
                        $data['image_extra'] = $img_extra;
                    }
                    $data = $this->appendData($request, $data);
                    if (isset($data['error'])) {
                        return $this->returnError($data, $request);
                    }
                    if ($request->has('input_image_extra')) {
                        $data['input_image_extra'] = implode('|', $request->input_image_extra);
                    }

                    if ($request->has('proprerties_id')) {
                        $data['proprerties_id'] = '|' . implode('|', $request->proprerties_id) . '|';
                    }
                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if (!empty($data['final_price']) && !empty($data['base_price'])) {
                        $data['sale'] = ceil(($data['base_price'] - $data['final_price']) * 100 / $data['base_price']) . '%';
                    } elseif (empty($data['final_price']) || empty($data['base_price'])) {
                        $data['sale'] = '0%';
                    } elseif (empty($data['final_price']) && empty($data['base_price'])) {
                        $data['sale'] = '0%';
                    }
                    if ($this->model->save()) {
                        if (\Schema::hasTable('product_attributes')) {
                            //  Cập nhật attribute cho sản phẩm
                            $product_attribute_updated = [];
                            foreach ($request->all() as $k => $v) {
                                if (strpos($k, 'attributes') !== false) {
                                    $key = str_replace('attributes', '', $k);
                                    $properties_value_ids = '|' . implode('|', $request->get('attributes'.$key)) . '|';
                                    if (strpos(@$request->get('image'.$key), 'filemanager')) {
                                        $image = @explode('filemanager/userfiles/', urldecode($request->get('image'.$key)))[1];
                                    } else {
                                        $image = urldecode(@$request->get('image'.$key));
                                    }
                                    $productAttr = ProductAttribute::updateOrCreate([
                                        'product_id' => $this->model->id,
                                        'properties_value_ids' => $properties_value_ids
                                    ], [
                                        'image' => $image,
                                        'final_price' => str_replace(',', '', $request->get('final_price'.$key))
                                    ]);
                                    $product_attribute_updated[] = $productAttr->id;
                                }
                            }
                            ProductAttribute::where('product_id', $this->model->id)->whereNotIn('id', $product_attribute_updated)->delete();
                        }

                        //  Lưu log
                        $this->adminLog($request, $this->model, 'add');
                        $this->afterAddLog($request, $this->model);

                        //  Update sản phẩm liên quan
                        if (!empty($request->related_products)) {
                            foreach ($request->related_products as $related_product) {
                                $prd = Product::find($related_product);
                                if (empty($prd->related_products)) {

                                    $prd->related_products = '|' . $this->model->id . '|';
                                } else {
                                    $prd->related_products .= $this->model->id . '|';
                                }
                                $prd->save();
                            }
                        }

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

    public function appendData($request, $data)
    {
        $check_square_image = @Setting::where('name', 'check_square_image')->first()->value;

        $domain = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        try {
            if (!empty($data['image'])) {
                if (strpos($data['image'], $domain . '/public/filemanager/userfiles/') === 0) {
                    list($width, $height) = @getimagesize($data['image']);
                    $image = get_headers($data['image'], 1);
                    $bytes_image = $image["Content-Length"];
                    if ($bytes_image > 200000) {
                        return [
                            'error' => true,
                            'msg' => 'Ảnh sản phẩm phải nhỏ hơn 200kb !'
                        ];
                    }
                } else {
                    $file_headers = @get_headers($domain . '/public/filemanager/userfiles/' . $data['image']);
                    if ($file_headers[0] == 'HTTP/1.0 500 Internal Server Error') {
                        return [
                            'error' => true,
                            'msg' => 'Ảnh sản phẩm không hợp lệ !'
                        ];
                    } else {
                        list($width, $height) = @getimagesize($domain . '/public/filemanager/userfiles/' . $data['image']);
                    }
                }

                if ($check_square_image == 1 && $width != $height) {    //  Check kick co anh ko vuong thi bao loi
                    return [
                        'error' => true,
                        'msg' => 'Ảnh sản phẩm phải là ảnh vuông!'
                    ];
                }
            }
        } catch (\Exception $ex) {

        }

        $data['image'] = str_replace($domain . '/public/filemanager/userfiles/', '', $data['image']);
        $img_extra = '';
        for ($i = 1; $i <= 9; $i++) {
            $image_extras = [];
            try {
                if (!empty($data['image_extra' . $i])) {
                    if (strpos($data['image_extra' . $i], $domain . '/public/filemanager/userfiles/') === 0) {
                        list($width, $height) = @getimagesize($data['image_extra' . $i]);
                        $image_extras[$i] = get_headers($data['image_extra' . $i], 1);
                        $bytes_image_extra = $image_extras[$i]["Content-Length"];
                        if ($bytes_image_extra > 200000) {
                            $getI = $i;
                            return [
                                'error' => true,
                                'msg' => 'Ảnh sản thumb thứ ' . $getI . ' phải nhỏ hơn 200kb !'
                            ];

                        }
                    } else {
                        $file_headers = @get_headers($domain . '/public/filemanager/userfiles/' . $data['image_extra' . $i]);
                        if ($file_headers[0] == 'HTTP/1.0 500 Internal Server Error') {
                            return [
                                'error' => true,
                                'msg' => 'Ảnh thumb sản phẩm thứ ' . $i . ' không hợp lệ !'
                            ];
                        } else {
                            list($width, $height) = @getimagesize($domain . '/public/filemanager/userfiles/' . $data['image_extra' . $i]);
                        }
                    }
                    if ($check_square_image == 1 && $width != $height) {
                        return [
                            'error' => true,
                            'msg' => 'Ảnh thunbail sản phẩm phải là ảnh vuông!'
                        ];
                    }
                    $img_extra .= (str_replace($domain . '/public/filemanager/userfiles/', '', $data['image_extra' . $i]) . '|');
                }
            } catch (\Exception $ex) {

            }
            unset($data['image_extra' . $i]);

        }

        return $data;
    }

    public function update(Request $request)
    {
        $item = $this->model->find($request->id);
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
                unset($data['price_options']);

                if ($request->has('related_products')) {
                    $data['related_products'] = '|' . implode('|', $request->related_products) . '|';
                    foreach ($request->related_products as $related_products) {
                        $prd = Product::find($related_products);
                        if (empty($prd->related_products)) {
                            $prd->related_products = '|' . $item->id . '|';
                        } else {
                            $prd->related_products .= $item->id . '|';
                        }
                        $prd->save();
                    }
                }

                if ($request->has('multi_cat')) {
                    $data['multi_cat'] = '|' . implode('|', $request->multi_cat) . '|';
                    $data['category_id'] = $request->multi_cat[0];
                }

                if ($request->has('related_products')) {
                    $data['related_products'] = '|' . implode('|', $request->related_products) . '|';

                }
//
//                    if ($request->has('manufacture_id')) {
//                        $data['manufacture_id'] = '|' . implode('|', $request->manufacture_id) . '|';
//                    }
                if ($request->has('related_post')) {
                    $data['related_post'] = '|' . implode('|', $request->related_post) . '|';
                }

                if ($request->has('tags')) {
                    $data['tags'] = '|' . implode('|', $request->tags) . '|';
                }
                if ($request->has('image_extra')) {
                    $img_extra = '';
                    foreach ($request->image_extra as $img) {
                        if ($img != null && strpos($img, 'filemanager')) {
                            $img_extra .= (@explode('filemanager/userfiles/', $img)[1] . '|');
                        } elseif ($img != null) {
                            $img_extra .= $img . '|';
                        }
                    }
                    $data['image_extra'] = $img_extra;
                }
                $data = $this->appendData($request, $data);
                if (isset($data['error'])) {
                    return $this->returnError($data, $request);
                }
                if ($request->has('input_image_extra')) {
                    $data['input_image_extra'] = implode('|', $request->input_image_extra);
                }
                if ($request->has('proprerties_id')) {
                    $data['proprerties_id'] = '|' . implode('|', $request->proprerties_id) . '|';
                }
                #
                if (!empty($data['final_price']) && !empty($data['base_price'])) {
                    $data['sale'] = CEIL(($data['base_price'] - $data['final_price']) * 100 / $data['base_price']) . '%';
                } elseif (empty($data['final_price']) || empty($data['base_price'])) {
                    $data['sale'] = '0%';
                } elseif (empty($data['final_price']) && empty($data['base_price'])) {
                    $data['sale'] = '0%';
                }
                foreach ($data as $k => $v) {
                    $item->$k = $v;
                }
                $item->link_wss = $request->link_wss;
                if ($item->save()) {
                    if (\Schema::hasTable('product_attributes')) {
                        //  Cập nhật attribute cho sản phẩm
                        $product_attribute_updated = [];
                        foreach ($request->all() as $k => $v) {
                            if (strpos($k, 'attributes') !== false) {
                                $key = str_replace('attributes', '', $k);
                                $properties_value_ids = '|' . implode('|', $request->get('attributes'.$key)) . '|';
                                if (strpos(@$request->get('image'.$key), 'filemanager')) {
                                    $image = @explode('filemanager/userfiles/', urldecode($request->get('image'.$key)))[1];
                                } else {
                                    $image = urldecode(@$request->get('image'.$key));
                                }
                                $productAttr = ProductAttribute::updateOrCreate([
                                    'product_id' => $item->id,
                                    'properties_value_ids' => $properties_value_ids
                                ], [
                                    'image' => $image,
                                    'final_price' => str_replace(',', '', $request->get('final_price'.$key))
                                ]);
                                $product_attribute_updated[] = $productAttr->id;
                            }
                        }
                        ProductAttribute::where('product_id', $item->id)->whereNotIn('id', $product_attribute_updated)->delete();
                    }

                    // admin log
                    $this->adminLog($request, $item, 'edit');
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

    public function returnError($data, $request)
    {
        CommonHelper::one_time_message('error', $data['msg']);
        return redirect()->back();
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

            $this->adminLog($request, $item, 'publish');
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
            $this->adminLog($request, $item, 'delete');
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
            $this->adminLog($request, $ids, 'multi_delete');
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
                foreach ($ids as $product) {
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
                foreach ($ids as $product) {
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

    public function ajaxGetHtmlPriceOption(Request $r) {
        return view('stbdproduct::partials.ajax_html_price_option')->render();
    }
    public function getTop1Wss(Request $request){
        $product_info = $this->model->find($request->product_id);
        $data=file_get_contents($product_info->link_wss);
        $doc = new DOMDocument();                        
        $doc->loadHTML($data,LIBXML_NOERROR);
        $lis = $doc->getElementsByTagName('li');
        $nodes = [];
        $wss_stt = 100;
        $i=0;
        foreach ($lis as $li) {
            $classes = $li->getAttribute('class');
            if ($classes == 'compare-item') {
                $tmp_domain = '';
                $tmp_price = '';
                $spans = $li->getElementsByTagName('span');
                foreach($spans AS $span){
                    $span_classes = $span->getAttribute('class');
                    if($span_classes=='compare-name'){
                        $tmp_domain = $span->nodeValue;
                    }
                }
                $divs = $li->getElementsByTagName('div');
                foreach($divs AS $div){
                    $div_classes = $div->getAttribute('class');
                    if($div_classes=='compare-product-price'){
                        $tmp_price = $div->nodeValue;
                        $tmp_price = str_replace("Lưu ý: Giá sản phẩm thấp hơn đáng kể so với mặt bằng chung. Vui lòng kiểm tra kỹ để tránh mua phải hàng nhái, hàng kém chất lượng.","",$tmp_price);
                        $tmp_price = str_replace(".","",$tmp_price);
                        $tmp_price = str_replace("đ","",$tmp_price);
                        $tmp_price = (int)$tmp_price;
                    }
                }
                $i++;
                $nodes[] = array(
                    'domain'=> $tmp_domain,
                    'price'=> $tmp_price,
                );
                if ($tmp_domain == 'khobepchauau.com') {
                    $wss_stt = $i;
                }  
            }
        }
        if(isset($nodes[0])){
            $res = array(
                'status'=>1,
                'wss_stt'=>$wss_stt,
                'wss_top1_amount'=>number_format($nodes[0]['price']),
                'wss_top1_website'=>$nodes[0]['domain'],
                'wss_top1_amount_int'=>$nodes[0]['price']
            );
            $product_info->wss_stt = $wss_stt;
            $product_info->wss_top1_amount = $nodes[0]['price'];
            $product_info->wss_top1_website = $nodes[0]['domain'];
            $product_info->save();
        }else{
            $res = array(
                'status'=>0,
                'message'=>'Không lấy được dữ liệu',
            );
        }

        return response()->json( $res);
    }
    public function updateTop1Wss(Request $request){
        $product_info = $this->model->find($request->product_id);
        $setting=DB::table('settings')->where('name', 'ignore')->where('type','update_price_product_tab')->get();
        $text_ignore = $setting[0]->value."\r\n".$product_info->code;
        DB::table('settings')->where('name', 'ignore')->where('type','update_price_product_tab')->update(['value'=> $text_ignore]);
        return response()->json( "ok");
    }
    public function updatePrice(Request $request)
    {
        $status =  200;
        $message   =  "Thành công";
        $product_id  = $request->product_id  ?? '';
        $price  = $request->price  ?? '';
        $price_column  = $request->price_column  ?? '';
        try {
            $product = Product::find($product_id);
            $price = str_replace(',', '', $price);
            $product->$price_column =  $price   ;
             
            if (!empty($product['final_price']) && !empty($product['base_price'])) {
                $product->sale = ceil(($product['base_price'] - $product['final_price']) * 100 / $product['base_price']) . '%';
            } elseif (empty($product['final_price']) || empty($product['base_price'])) {
                $product->sale = '0%';
            } elseif (empty($product['final_price']) && empty($product['base_price'])) {
                $product->sale = '0%';
            }
            $product->save();
            
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            $status =  201;
            $message   =  $e->getMessage();
        }
        $result = array(
            'status'    => $status,
            'message'   => $message,
        );
        return response()->json($result);
    }
}
