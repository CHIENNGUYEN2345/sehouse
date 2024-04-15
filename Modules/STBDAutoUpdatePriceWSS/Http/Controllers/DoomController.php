<?php

namespace Modules\STBDAutoUpdatePriceWSS\Http\Controllers;

use App\Http\Helpers\CommonHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Models\Setting;
use Modules\STBDAutoUpdatePriceWSS\Console\CrawlProduct;
use Modules\STBDAutoUpdatePriceWSS\Entities\Category;
use Modules\STBDAutoUpdatePriceWSS\Entities\DoomCategory;
use Validator;
use Excel;
use Storage;

class DoomController extends CURDBaseController
{
    protected $module = [
        'code' => 'doom',
        'table_name' => 'website',
        'label' => 'doom',
        'modal' => '\Modules\STBDAutoUpdatePriceWSS\Entities\Website',
        'list' => [
        ],
        'form' => [
            'category_tab' => [
                ['name' => 'domain', 'type' => 'custom', 'field' => 'stbdautoupdatepricewss::form.fields.dynamic', 'class' => 'required', 'label' => 'Chuyên mục cần lấy'],
            ],
            'category_target_tab' => [
//                ['name' => 'url_pagination', 'type' => 'text', 'class' => 'required', 'label' => 'URL phân trang'],
                ['name' => 'category_pagination', 'type' => 'text', 'class' => 'required', 'label' => 'Link phân trang', 'value' => '?page={i}', 'des' => 'Ví dụ: ?page={i}'],
                ['name' => 'category_pagination_repeat', 'type' => 'checkbox', 'label' => 'Phân trang bị lặp (VD: Trang max = 10 khi click vào page 11 thì sang page 1)'],
                ['name' => 'target', 'type' => 'text', 'class' => 'required', 'label' => 'Target Mỗi Block Sản Phẩm'],
                ['name' => 'link', 'type' => 'text', 'class' => 'required', 'label' => 'Target Link Mỗi Sản Phẩm (Lấy từ thẻ con của Block ở trên)'],
            ],
            'product_doom_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Target Tên Sản Phẩm'],
                ['name' => 'name_prev', 'type' => 'text', 'class' => '', 'label' => 'Tiền tố tên SP'],
                ['name' => 'name_last', 'type' => 'text', 'class' => '', 'label' => 'Hậu tố tên SP'],
                ['name' => 'image', 'type' => 'text', 'class' => '', 'label' => 'Target Ảnh Đại Diện'],
                ['name' => 'check_image_square', 'type' => 'checkbox', 'value' => '1', 'label' => 'Chỉ lấy ảnh vuông'],
                ['name' => 'image_extra', 'type' => 'text', 'class' => '', 'label' => 'Target Ảnh thêm'],
                ['name' => 'code', 'type' => 'text', 'class' => '', 'label' => 'Mã SP'],
                ['name' => 'manufacturer_name', 'type' => 'text', 'class' => '', 'label' => 'Thương hiệu'],
                ['name' => 'origin_name', 'type' => 'text', 'class' => '', 'label' => 'Xuất sứ'],
                ['name' => 'base_price', 'type' => 'text', 'class' => '', 'label' => 'Giá cũ'],
                ['name' => 'final_price', 'type' => 'text', 'class' => '', 'label' => 'Giá bán'],
                ['name' => 'intro', 'type' => 'text', 'class' => '', 'label' => 'Mô tả ngắn'],
                ['name' => 'content', 'type' => 'text', 'class' => '', 'label' => 'Mô tả chi tiết'],
                ['name' => 'content_prev', 'type' => 'text', 'class' => '', 'label' => 'Tiền tố mô tả chi tiết'],
                ['name' => 'content_last', 'type' => 'text', 'class' => '', 'label' => 'Hậu tố mô tả chi tiết'],
                ['name' => 'attributes', 'type' => 'text', 'class' => '', 'label' => 'Thuộc tính'],
            ],
        ],
    ];

    protected $filter = [

    ];

    public function update(Request $request, $website_id)
    {
        try {
            $item = $this->model->find($website_id);

            if (!is_object($item)) abort(404);
            if (!$_POST) {

                $data = $this->getDataUpdate($request, $item);
                return view('stbdautoupdatepricewss::doom.edit')->with($data);
            } else if ($_POST) {


                $validator = Validator::make($request->all(), [
//                    'domain' => 'required'
                ], [
//                    'domain.required' => 'Bắt buộc phải nhập domain',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert

                    $category_updated = [];
                    foreach ($request->all() as $k => $v) {
                        if (strpos($k, 'link_crawl') !== false) {
                            $key = str_replace('link_crawl', '', $k);
                            $doom_cat = DoomCategory::updateOrCreate([
                                'website_id' => $item->id,
                                'category_id'=> $request->get('category_id' . $key),
                            ],[
                                'link_crawl' => $request->get('link_crawl' . $key),
                            ]);
                            $category_updated[] = @$doom_cat->id;
//                            dd($category_updated);
                        }
                    }
                    DoomCategory::where('website_id', $item->id)->whereNotIn('id', $category_updated)->delete();

                    $item->doom = json_encode($data);

                    if ($item->save()) {
//                        CommonHelper::flushCache($this->module['table_name']);
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

                    /*if ($request->return_direct == 'save_continue') {
                        return redirect('admin/' . $this->module['code'] . '/' . $item->id);
                    } elseif ($request->return_direct == 'save_create') {
                        return redirect('admin/' . $this->module['code'] . '/add');
                    }*/
                    return back();
                }
            }

        } catch
        (\Exception $ex) {
//            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function delete(Request $request)
    {
        try {
            $item = $this->model->find($request->id);

            $item->delete();
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

    public function ajaxGetHtmlSelectCategory(Request $r)
    {
        return view('stbdautoupdatepricewss::form.partials.ajax_html_select_category')->render();
    }

    public function test($website_id) {
        $crawlProduct = new CrawlProduct();
//        dd('12');
        return $crawlProduct->handle($website_id, true);
    }
}