<?php

namespace App\CRMBDS\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\CRMBDS\Models\Category;
use App\CRMBDS\Models\Codes;
use App\CRMBDS\Models\Theme;
use App\CRMBDS\Models\Tag;
use Validator;
use App\CRMBDS\Models\PostTag;
use App\CRMBDS\Models\BillProgress;

class CodesController extends CURDBaseController
{

    protected $module = [
        'code' => 'codes',
        'table_name' => 'codes',
        'label' => 'Code ',
        'modal' => '\App\CRMBDS\Models\Codes',
        'list' => [
            ['name' => 'image', 'type' => 'image', 'label' => 'Ảnh'],
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Tên'],
            ['name' => 'link', 'type' => 'text', 'label' => 'Link'],
            ['name' => 'multi_cat', 'type' => 'custom', 'td' => 'CRMBDS.list.td.multi_cat', 'label' => 'Danh mục'],
            ['name' => 'tags', 'type' => 'text', 'label' => 'Sản phẩm',],
            ['name' => 'source', 'type' => 'text', 'label' => 'Mã nguồn',],
            ['name' => 'owned', 'type' => 'text', 'label' => 'Nguồn',],
            ['name' => 'status', 'type' => 'status', 'label' => 'Trang thái'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => '', 'label' => 'Tên', 'group_class' => 'col-md-6'],
                ['name' => 'link', 'type' => 'text', 'class' => 'required', 'label' => 'Link', 'des' => 'Link website. VD: https://dienmayxanh.vn', 'group_class' => 'col-md-6'],
                ['name' => 'multi_cat', 'type' => 'custom', 'field' => 'CRMBDS.form.fields.multi_cat', 'label' => 'Danh mục sản phẩm', 'model' => Category::class,
                    'object' => 'category_product', 'where' => 'type in (10)', 'display_field' => 'name', 'multiple' => true, 'group_class' => 'col-md-6', 'des' => 'Danh mục đầu tiên chọn là danh mục chính'],
                ['name' => 'tags', 'type' => 'tags', 'label' => 'Ngành hàng', 'model' => Tag::class, 'where' => "type = 'code'", 'group_class' => 'col-md-6'],
                ['name' => 'source', 'type' => 'checkbox_multiple', 'options' =>
                    [
                        'wordpress' => 'Wordpress',
                        'ladipage' => 'Ladipage',
                        'laravel' => 'Laravel',
                        // 'magento' => 'Magento',
                        'react native' => 'react native',
                        'native' => 'native',
                        'flutter' => 'flutter',
                        'khác' => 'Khác',
                    ], 'class' => 'required', 'multiple' => true, 'label' => 'Mã nguồn', 'group_class' => 'col-md-12',],
                ['name' => 'price_setup', 'type' => 'price_vi', 'class' => 'required', 'group_class' => 'col-md-4', 'label' => 'Giá bán code', 'des' => 'Bán code, setup lên hosting cho khách'],
                ['name' => 'price_interface_change', 'type' => 'price_vi', 'class' => '', 'group_class' => 'col-md-4', 'label' => 'Giá thay giao diện  1 chút', 'des' => 'Sửa màu, di chuyển khối, làm cho khác web cũ đi 1 chút'],
                ['name' => 'price_interface_change_all', 'type' => 'price_vi', 'class' => '', 'group_class' => 'col-md-4', 'label' => 'Giá thay giao diện toàn bộ', 'des' => 'Tìm theme trên mạng và thay vào'],
                ['name' => 'owned', 'type' => 'radio', 'class' => '', 'label' => 'Lưu trữ tại', 'value' => 'server mình', 'options' => [
                    'server mình' => 'Trên server mình',
                    'trên mạng' => 'Kiếm trên mạng',
                    'asite.vn' => 'asite.vn',
                    'bizhostvn.com' => 'bizhostvn.com',
                    'mauwebsitedep.net' => 'mauwebsitedep.net',
                    'webrt.vn' => 'webrt.vn',
                ]],
                ['name' => 'status', 'type' => 'checkbox', 'label' => 'admin.active', 'value' => 1, 'group_class' => 'col-md-4'],
                ['name' => 'created_by_name', 'type' => 'text', 'class' => '', 'label' => 'Tác giả', 'group_class' => 'col-md-4'],
                ['name' => 'link_ios', 'type' => 'text', 'class' => '', 'label' => 'Link ios', 'group_class' => 'col-md-4'],        
            ],
            'remind_tab' => [
                ['name' => 'image', 'type' => 'file_image', 'label' => 'Ảnh đại diện'],
                ['name' => 'image_extra', 'type' => 'multiple_image_dropzone', 'count' => '6', 'label' => 'Thêm nhiều ảnh khác'],
            ],
            'des_tab' => [
                ['name' => 'intro', 'type' => 'textarea_editor', 'class' => '', 'label' => 'Mô tả qua về tính năng'],
                ['name' => 'content', 'type' => 'textarea_editor', 'class' => '', 'label' => 'Mô tả chi tiết tính năng'],
            ],
        ]
    ];

    protected $quick_search = [
        'label' => 'ID',
        'fields' => 'id, name, link, source, owned'
    ];

    protected $filter = [
        'multi_cat' => [
            'label' => 'Thể loại',
            'type' => 'select2_model',
            'display_field' => 'name',
            'object' => 'category',
            'model' => Category::class,
            'query_type' => 'custom',
        ],
    ];

    public function getIndex(Request $request)
    {
        
        $data = $this->getDataList($request);

        return view('CRMBDS.codes.list')->with($data);
    }

    public function appendWhere($query, $request)
    {
        //  Nếu không có quyền xem toàn bộ dữ liệu thì chỉ được xem các dữ liệu mình tạo
        if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'view_all_data')) {
            // $query = $query->where('admin_id', \Auth::guard('admin')->user()->id);
        }

        if (!is_null($request->get('multi_cat'))) {
            $query = $query->where('multi_cat', 'like', '%|'.$request->multi_cat.'|%');
        }

        return $query;
    }

    public function add(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMBDS.codes.add')->with($data);
            } else if ($_POST) {
                \DB::beginTransaction();

                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'link' => 'required|unique:codes,link',
                ], [
                    'name.required' => 'Bắt buộc phải nhập tên',
                    'link.unique' => 'Web này đã đăng!',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert

                    $data['admin_id'] = \Auth::guard('admin')->user()->id;
                    if ($request->has('multi_cat')) {
                        $data['multi_cat'] = '|' . implode('|', $request->multi_cat) . '|';
                    }

                    if ($request->has('image_extra')) {
                        $data['image_extra'] = implode('|', $request->image_extra);
                    }


                    if ($request->has('source')) {
                        $data['source'] = '|' . implode('|', $data['source']) . '|';
                    } else {
                        $data['source'] = '';
                    }

                    if ($request->has('type')) {
                        $data['type'] = '|' . implode('|', $data['type']) . '|';
                    } else {
                        $data['type'] = '';
                    }

                    $data['link'] = preg_replace('/\s+/', '', $data['link']);
                    if (substr($data['link'], -1) != '/') {
                        //  Nếu cuối chuỗi không có dấu '/' thì nối thêm
                        $data['link'] .= '/';
                    }

                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
                        \DB::commit();

                        $this->afterAddLog($request, $this->model);

                        //  Xử lý tag
                        $this->xulyTag($this->model->id, $data);

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

    public function quickAdd(Request $request)
    {
        try {


            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMBDS.codes.quick_add')->with($data);
            } else if ($_POST) {
                $validator = Validator::make($request->all(), [
//                    'name' => 'required',
//                    'link' => 'required|unique:codes,link',
                ], [
//                    'name.required' => 'Bắt buộc phải nhập tên',
//                    'link.unique' => 'Web này đã đăng!',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
//                    dd($request->all());
                    //  Lưu ladipage của mình
                    $code = trim($request->ladipage);
                    $lines = explode("\n", $code);

                    //  Tùy chỉnh dữ liệu insert
                    $datainsert = [];
                    foreach ($lines as $v) {
                        $v = preg_replace('/\s+/', '', $v);
                        if ($v != '') {
                            if (substr($v, -1) != '/') {
                                //  Nếu cuối chuỗi không có dấu '/' thì nối thêm
                                $v .= '/';
                            }
                            if (!empty(trim($v)) && Codes::where('link', $v)->count() == 0 && !in_array($v, $datainsert)) {
                                $datainsert[] = [
                                    'link' => $v,
                                    'price_setup' => 800000,
                                    'source' => 'ladipage',
                                    'admin_id' => @\Auth::guard('admin')->user()->id
                                ];
                            }
                        }
                    }
                    if (!empty($datainsert)) {
                        Codes::insert($datainsert);
                    }


                    //  Lưu ladipage kiếm mạng
                    $code = trim($request->ladipage_mang);
                    $lines = explode("\n", $code);

                    //  Tùy chỉnh dữ liệu insert
                    $datainsert = [];
                    foreach ($lines as $v) {
                        $v = preg_replace('/\s+/', '', $v);
                        if ($v != '') {
                            if (substr($v, -1) != '/') {
                                //  Nếu cuối chuỗi không có dấu '/' thì nối thêm
                                $v .= '/';
                            }
                            if (!empty(trim($v)) && Codes::where('link', $v)->count() == 0 && !in_array($v, $datainsert)) {
                                $datainsert[] = [
                                    'link' => $v,
                                    'price_setup' => 800000,
                                    'source' => 'ladipage',
                                    'admin_id' => @\Auth::guard('admin')->user()->id,
                                    'owned' => 2,
                                ];
                            }
                        }
                    }
                    if (!empty($datainsert)) {
                        Codes::insert($datainsert);
                    }


                    //  Lưu wordpress
                    $code = trim($request->wordpress);
                    $lines = explode("\n", $code);

                    //  Tùy chỉnh dữ liệu insert
                    $datainsert = [];

                    foreach ($lines as $v) {
                        $v = preg_replace('/\s+/', '', $v);
                        if ($v != '') {
                            if (substr($v, -1) != '/') {
                                //  Nếu cuối chuỗi không có dấu '/' thì nối thêm
                                $v .= '/';
                            }
                            if (!empty(trim($v)) && Codes::where('link', $v)->count() == 0 && !in_array($v, $datainsert)) {
                                $datainsert[] = [
                                    'link' => $v,
                                    'source' => 'wordpress',
                                    'admin_id' => @\Auth::guard('admin')->user()->id
                                ];
                            }
                        }
                    }
                    if (!empty($datainsert)) {
                        Codes::insert($datainsert);
                    }


                    //  Lưu wordpress kiếm mạng
                    $code = trim($request->wordpress_mang);
                    $lines = explode("\n", $code);

                    //  Tùy chỉnh dữ liệu insert
                    $datainsert = [];

                    foreach ($lines as $v) {
                        $v = preg_replace('/\s+/', '', $v);
                        if ($v != '') {
                            if (substr($v, -1) != '/') {
                                //  Nếu cuối chuỗi không có dấu '/' thì nối thêm
                                $v .= '/';
                            }
                            if (!empty(trim($v)) && Codes::where('link', $v)->count() == 0 && !in_array($v, $datainsert)) {
                                $datainsert[] = [
                                    'link' => $v,
                                    'source' => 'wordpress',
                                    'admin_id' => @\Auth::guard('admin')->user()->id,
                                    'owned' => 2
                                ];
                            }
                        }
                    }
                    if (!empty($datainsert)) {
                        Codes::insert($datainsert);
                    }

                    CommonHelper::one_time_message('success', 'Tạo mới thành công!');

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
                return view('CRMBDS.codes.edit')->with($data);
            } else if ($_POST) {
                \DB::beginTransaction();

                $validator = Validator::make($request->all(), [
//                    'name' => 'required',
                    'link' => 'required',
                ], [
//                    'name.required' => 'Bắt buộc phải nhập tên gói',
//                    'link.unique' => 'Web này đã đăng!',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    //  Tùy chỉnh dữ liệu insert
                    if ($request->has('image_extra')) {
                        $data['image_extra'] = implode('|', $request->image_extra);
                    }
                    if ($request->has('source')) {
                        $data['source'] = '|' . implode('|', $data['source']) . '|';
                    } else {
                        $data['source'] = '';
                    }
                    if ($request->has('multi_cat')) {
                        $data['multi_cat'] = '|' . implode('|', $request->multi_cat) . '|';
                    }

                    if ($request->has('type')) {
                        $data['type'] = '|' . implode('|', $data['type']) . '|';
                    } else {
                        $data['type'] = '';
                    }

                    foreach ($data as $k => $v) {
                        $item->$k = $v;
                    }
                    if ($item->save()) {
                        //  Xử lý tag
                        $this->xulyTag($item->id, $data);
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

    public function updateBillToCode()
    {
        //  lấy id các đơn hàng đang làm
        $bill_ids_dang_lam = BillProgress::whereNotIn('status', ['Kết thúc', 'Tạm dừng', 'Khách xác nhận xong', '', null])->pluck('bill_id')->toArray();


        //  Lấy các bill chua duoc update sang codes
        $count_bill_create = 0;
        $bills = \App\CRMBDS\Models\Bill::select('id', 'domain', 'service_id', 'update_to_codes')
                                ->where('update_to_codes', 0)
                                ->whereNotIn('id', $bill_ids_dang_lam)  //  không lấy các đơn đang làm
                                ->get();
        foreach ($bills as $bill) {
            if (Codes::where('link', 'like', '%' . $bill->domain . '%')->count() == 0) {
                //  Nếu chưa có thì update sang
                $code = new Codes();
                $code->link = 'https://' . $bill->domain . '/';
                if ($bill->service_id == 1) {
                    $code->source = '|ladipage|';
                } elseif ($bill->service_id == 5) {
                    $code->source = '|wordpress|';
                }
                $code->multi_cat = '|' .@$bill->ldp->career_id. '|';
                $code->bil_id = $bill->id;
                $code->save();
                $count_bill_create++;
            }

            $bill->update_to_codes = 1;
            $bill->save();
        }

        CommonHelper::one_time_message('success', number_format($count_bill_create) . ' code được tạo');
        return back();
    }

    public function backupToHtml()
    {
        $count = 0;
        $codes = Codes::select('id', 'backup_to_html', 'link', 'source')->where('source', 'like', '%ladipage%')->where('backup_to_html', 0)->get();
        foreach ($codes as $code) {
            if (strpos($code->source, 'ladipage') !== false) {

                //  Nếu là sources ladipage thì lưu vào thành .html
                if (strpos($code->link, '//') !== false) {
                    $file_name = explode('//', $code->link)[1];
                    $file_name = str_replace('/', '', $file_name);
                    $file_name = str_replace('.', '_', $file_name);
    //                dd($file_name);
                    if (!file_exists(base_path() . '/public_html/ldp-template/' . $file_name . '.html')) {

                        //  Nếu chưa có lưu file .html thì lưu lại
                        try {
                            $filename = base_path() . '/public_html/ldp-template/' . $file_name . '.html'; // whatever name you want.
                            $myfile = fopen($filename, "w") or die("Unable to open file!");
                            $txt = $this->httpPost($code->link, ""); //<url> replace by url you want.
                            $txt = str_replace('href="/', 'href="' . $code->link, $txt);
                            $txt = str_replace('src="/', 'src="' . $code->link, $txt);
                            fwrite($myfile, $txt);
                            fclose($myfile);

    //                        $v = file_get_contents($code->link);
    //                        file_put_contents(base_path() . '/public_html/ldp-template/' . $file_name . '.html', $v);
                            $count ++;
                        } catch (\Exception $ex) {
                            dd($ex->getMessage());
                        }
                    }
                }
                
            } else {
//                dd($code);
            }
            $code->backup_to_html = 1;
            $code->save();
        }
        CommonHelper::one_time_message('success', number_format($count) . ' bản backup được tạo');
        return back();
    }

    function httpPost($url, $data)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "postman-token: d6c19d4c-1d67-1ed2-6ee4-4378c5a64dc2"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    //  Xử lý tag
    /*public function xulyTag($post_id, $tags_data)
    {
        $id_updated = [];
        $tags = json_decode($tags_data);

        if (is_array($tags) && !empty($tags)) {
            foreach ($tags as $tag_name) {
                $tag_name = $tag_name->value;
                //  Tạo tag nếu chưa có
                $tag = Tag::where('name', $tag_name)->first();
                if (!is_object($tag)) {
                    $tag = new Tag();
                    $tag->name = $tag_name;
                    $tag->slug = str_slug($tag_name, '-');
                    $tag->type = 'post';
                    $tag->save();
                }

                $post_tag = PostTag::updateOrCreate([
                    'post_id' => $post_id,
                    'tag_id' => $tag->id,
                ], [

                ]);
                $id_updated[] = $post_tag->id;
            }
        }
        //  Xóa tag thừa
        PostTag::where('post_id', $post_id)->whereNotIn('id', $id_updated)->delete();

        return true;
    }*/
}
