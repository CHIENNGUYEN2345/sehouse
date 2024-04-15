<?php

namespace Modules\STBDAutoUpdatePriceWSS\Http\Controllers;

use App\Http\Helpers\CommonHelper;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\STBDAutoUpdatePriceWSS\Entities\Category;
use Modules\STBDAutoUpdatePriceWSS\Entities\DoomProduct;
use Modules\STBDAutoUpdatePriceWSS\Entities\Website;
use Validator;
use Excel;
use Storage;
use ZipArchive;

class ProductController extends CURDBaseController
{
    protected $module = [
        'code' => 'doom-product',
        'table_name' => 'doom_product',
        'label' => 'Sản phẩm',
        'modal' => '\Modules\STBDAutoUpdatePriceWSS\Entities\DoomProduct',
        'list' => [
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Tên'],
            ['name' => 'product_link', 'type' => 'text', 'label' => 'Link'],
//            ['name' => 'image_extra', 'type' => 'text', 'label' => 'Ảnh'],
            ['name' => 'website_id', 'type' => 'relation', 'label' => 'Website', 'object' => 'website', 'display_field' => 'domain'],
            ['name' => 'multi_cat', 'type' => 'relation', 'label' => 'Chuyên mục', 'object' => 'category', 'display_field' => 'name'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'label' => 'Tên'],
                ['name' => 'product_link', 'type' => 'text', 'label' => 'Link'],
                ['name' => 'website_id', 'type' => 'select2_model', 'class' => '', 'label' => 'Website', 'model' => Website::class, 'display_field' => 'domain'],
                ['name' => 'multi_cat', 'type' => 'select2_model', 'class' => '', 'label' => 'Chuyên mục', 'model' => Category::class, 'display_field' => 'name'],
            ],
            'image_tab' => [
//                ['name' => 'size_regular_price', 'type' => 'custom', 'field' => 'stbdautoupdatepricewss::form.fields.dynamic4', 'object' => 'tours', 'class' => 'required', 'label' => 'Địa chỉ'],
                ['name' => 'image_extra', 'type' => 'custom', 'field' => 'stbdautoupdatepricewss::form.fields.dynamic_image', 'label' => 'Ảnh'],

            ],
        ],
    ];

    protected $filter = [
        'name' => [
            'label' => 'Tên',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'product_link' => [
            'label' => 'Link',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'website_id' => [
            'label' => 'Website',
            'type' => 'select2_model',
            'query_type' => '=',
            'model' => Website::class,
            'display_field' => 'domain'

        ],
        'multi_cat' => [
            'label' => 'Chuyên mục',
            'query_type' => '=',
            'type' => 'select2_model',
            'model' => Category::class,
            'display_field' => 'name'
        ],
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('stbdautoupdatepricewss::product.list')->with($data);
    }

    public function appendWhere($query, $request)
    {
        return $query;
    }

    public function add(Request $request)
    {
        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('stbdautoupdatepricewss::product.add')->with($data);
            } else if ($_POST) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',

                ], [
                    'name.required' => 'Bắt buộc phải nhập tên doom',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    //  Tùy chỉnh dữ liệu insert

                    if ($request->has('image_extra')) {
                        $data['image_extra'] = implode('|', $request->image_extra);
                    }

                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }
                    if ($this->model->save()) {
//                        $this->afterAddLog($request, $this->model);
//                        CommonHelper::flushCache($this->module['table_name']);
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
        if (!is_object($item)) abort(404);
        if (!$_POST) {
            $data = $this->getDataUpdate($request, $item);
            return view('stbdautoupdatepricewss::product.edit')->with($data);
        } else if ($_POST) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',

            ], [
                'name.required' => 'Bắt buộc phải nhập tên doom',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            } else {
                $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                //  Tùy chỉnh dữ liệu insert
                if ($request->has('image_extra')) {
                    $data['image_extra'] = implode('|', $request->image_extra);
                }
                foreach ($data as $k => $v) {
                    $item->$k = $v;
                }
                if ($item->save()) {
//                    CommonHelper::flushCache($this->module['table_name']);
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
//            CommonHelper::flushCache($this->module['table_name']);
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
//            CommonHelper::flushCache($this->module['table_name']);
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

//            CommonHelper::flushCache($this->module['table_name']);
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
    public function allDelete(Request $request)
    {
        try {
            DoomProduct::truncate();
            CommonHelper::one_time_message('success', 'Xóa thành công!');
            return redirect('/admin/doom-product');
        } catch (\Exception $ex) {

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
//            CommonHelper::flushCache($this->module['table_name']);
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

    public function getDataExportImage(Request $request,$product = false)
    {
        //  Tạo folder ảnh
        \File::deleteDirectory(public_path('filemanager/userfiles/_thumbs/draf'));
        if (is_file(public_path('filemanager/userfiles/_thumbs/spreadshirt.zip'))) {
            unlink(public_path('filemanager/userfiles/_thumbs/spreadshirt.zip'));
        }

        mkdir(base_path() . '/public/filemanager/userfiles/_thumbs/draf/Design', 0755, true);
        mkdir(base_path() . '/public/filemanager/userfiles/_thumbs/draf/Imagine_1', 0755, true);
        mkdir(base_path() . '/public/filemanager/userfiles/_thumbs/draf/Imagine_2', 0755, true);

        if ($product == false){
            $products = DoomProduct::whereIn('id', explode(',', $request->ids))->get();
        }else{
            $products = $product;
        }

        //  Di chuyển ảnh sản phẩm vào folder nháp
        foreach ($products as $k1=>$product) {
            \Modules\STBDAutoUpdatePriceWSS\Helpers\STBDCrawllerHelper::saveFileSTBD(asset('/public/filemanager/userfiles/' . $product->img_design), '_thumbs/draf/Design',$product->id,$product->name);
            \Modules\STBDAutoUpdatePriceWSS\Helpers\STBDCrawllerHelper::saveFileSTBD(asset('/public/filemanager/userfiles/' . $product->img_magine1), '_thumbs/draf/Imagine_1',$product->id,$product->name);
            \Modules\STBDAutoUpdatePriceWSS\Helpers\STBDCrawllerHelper::saveFileSTBD(asset('/public/filemanager/userfiles/' . $product->img_magine2), '_thumbs/draf/Imagine_2',$product->id,$product->name);
        }
        $img_designs = glob(base_path() . '/public/filemanager/userfiles/_thumbs/draf/Design/*');
        $img_magine1s = glob(base_path() . '/public/filemanager/userfiles/_thumbs/draf/Imagine_1/*');
        $img_magine2s = glob(base_path() . '/public/filemanager/userfiles/_thumbs/draf/Imagine_2/*');

        //  Nén folder nháp
        $zipFileName = 'spreadshirt.zip';
        $zip = new ZipArchive;
        if ($zip->open(base_path() . '/public/filemanager/userfiles/_thumbs/spreadshirt.zip', ZipArchive::CREATE) === TRUE) {

            $zip->addEmptyDir('Design');
            $zip->addEmptyDir('Imagine_1');
            $zip->addEmptyDir('Imagine_2');

            foreach ($img_designs as $h=>$vs) {
                $name_img = pathinfo($vs, PATHINFO_FILENAME);
                $ex_img = pathinfo($vs, PATHINFO_EXTENSION);
                $zip->addFile($vs, 'Design/'.$name_img . '.' . $ex_img);
            }
            foreach ($img_magine1s as $h1=>$vs1) {
                $name_img1 = pathinfo($vs1, PATHINFO_FILENAME);
                $ex_img1 = pathinfo($vs1, PATHINFO_EXTENSION);
                $zip->addFile($vs1, 'Imagine_1/'.$name_img1 . '.' . $ex_img1);
            }
            foreach ($img_magine2s as $h2=>$vs2) {
                $name_img2 = pathinfo($vs2, PATHINFO_FILENAME);
                $ex_img2 = pathinfo($vs2, PATHINFO_EXTENSION);
                $zip->addFile($vs2, 'Imagine_2/'.$name_img2 . '.' . $ex_img2);
            }
            $zip->close();
        }

        $filetopath = base_path() . '/public/filemanager/userfiles/_thumbs/' . $zipFileName;

        if (!file_exists($filetopath)) {
            CommonHelper::one_time_message('error', 'Không nén được file!');
            return back();
        }
//Thực hiện download
        header("Content-type: application/xlsx");
        header("Content-Disposition: attachment; filename=$zipFileName");
        header("Content-length: " . filesize($filetopath));
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile("$filetopath");
    }

    public function getDataExport(Request $request)
    {

        $ids = explode(',', trim($request->ids, ','));
        $datas = DoomProduct::whereIn('id', $ids)->get();
        $this->export($request, $datas);
    }

    public function export(Request $request, $datas)
    {
        $config = [
            'excel.csv' => [
                'use_bom' => true,
                'delimiter' => ',',
            ],
        ];
        config($config);

        \Excel::create(str_slug($this->module['label'], '_') . '_' . date('d_m_Y'), function ($excel) use ($datas) {
            // Set the title
            $excel->setTitle(str_slug($this->module['label'], '_') . ' ' . date('d m Y'));
            $excel->sheet($this->module['label'] . '_' . date('d_m_Y'), function ($sheet) use ($datas) {

                $field_name[] = 'Product Name';
                $field_name[] = 'Product link';
                $field_name[] = 'Link image 1';
                $field_name[] = 'Link image 2';
                $field_name[] = 'Link image 3';
                $sheet->row(1, $field_name);
                $k = 2;

                foreach ($datas as $data) {

                    $images = explode('|', $data['image_extra']);

                    $data_export = [];
                    $data_export[] = $data['name'];
                    $data_export[] = $data['product_link'];
                    $data_export[] = @$images[0];
                    $data_export[] = @$images[1];
                    $data_export[] = @$images[2];
                    $sheet->row($k, $data_export);
                    $k++;
                }
            });
        })->download('csv');

    }

//$string: chuỗi truyền vào
//$delimiter: ký tự ngăn cách các chuỗi cần random
//$wrap: Ký tự bao bọc chuỗi muốn random.
//VD: {a|b|c} => $wrap1 = { , $wrap2 = } , $delimiter= |
    public function getRanDomString($string, $wrap1, $wrap2, $delimiter)
    {
        $descriptions = explode($wrap1, $string);

        $str = $descriptions[0];
        unset($descriptions[0]);
        foreach ($descriptions as $description) {
            $arr = explode($wrap2, $description);
            $a = explode($delimiter, $arr[0]);

            $b = array_rand($a, 1);

            $str .= $a[$b] . $arr[1];

        }
        return $str;
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
//            CommonHelper::flushCache($this->module['table_name']);
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
}
