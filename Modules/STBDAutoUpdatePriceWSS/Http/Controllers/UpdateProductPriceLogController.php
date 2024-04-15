<?php

namespace Modules\STBDAutoUpdatePriceWSS\Http\Controllers;

use App\Http\Helpers\CommonHelper;
use Illuminate\Http\Request;
use Modules\STBDAutoUpdatePriceWSS\Console\UpdatePriceWss;
use Modules\STBDAutoUpdatePriceWSS\Entities\LogUpdateProductPrice;
use Validator;
use Excel;
use Storage;

class UpdateProductPriceLogController extends CURDBaseController
{
    protected $module = [
        'code' => 'update-product-price',
        'table_name' => 'log_update_product_price',
        'label' => 'Lịch sử cập nhật giá SP',
        'modal' => '\Modules\STBDAutoUpdatePriceWSS\Entities\LogUpdateProductPrice',
        'list' => [
            ['name' => 'created_at', 'type' => 'custom', 'td' => 'stbdautoupdatepricewss::list.td.crawl_started_at', 'label' => 'Bắt đầu'],
            ['name' => 'end', 'type' => 'datetime_vi', 'label' => 'Kết thúc'],
            ['name' => 'links_error', 'type' => 'custom', 'td' => 'stbdautoupdatepricewss::list.td.links_error', 'label' => 'Link lỗi'],
            ['name' => 'log_price', 'type' => 'custom', 'td' => 'stbdautoupdatepricewss::list.td.log_price', 'label' => 'Lịch sử update giá'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'links_error', 'type' => 'textarea', 'class' => 'required', 'label' => 'Link lỗi'],
            ],
        ],
    ];

    protected $filter = [
        'links_error' => [
            'label' => 'Link lỗi',
            'type' => 'text',
            'query_type' => 'like'
        ],
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);
        return view('stbdautoupdatepricewss::list')->with($data);
    }

    public function add(Request $request)
    {
        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('stbdautoupdatepricewss::add')->with($data);
            } else if ($_POST) {

                $validator = Validator::make($request->all(), [
                    'domain' => 'required'
                ], [
                    'domain.required' => 'Bắt buộc phải nhập domain',
                ]);
                if ($validator->fails()) {
                    if ($request->ajax()) {
                        return response()->json([
                            'status' => false,
                            'error' => $validator->errors()->all(),
                        ]);
                    }
                    return back()->withErrors($validator)->withInput();

                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    //  Tùy chỉnh dữ liệu insert
                    #
                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
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
                    }

                    return redirect('admin/' . $this->module['code']);
                }
            }
        } catch (\Exception $ex) {
            CommonHelper::one_time_message('error', $ex->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $item = $this->model->find($id);
            if (!is_object($item)) abort(404);
            if (!$_POST) {
                $data = $this->getDataUpdate($request, $item);
                return view('stbdautoupdatepricewss::edit')->with($data);
            } else if ($_POST) {


                $validator = Validator::make($request->all(), [
                    'domain' => 'required'
                ], [
                    'domain.required' => 'Bắt buộc phải nhập domain',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert

                    foreach ($data as $k => $v) {
                        $item->$k = $v;
                    }
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

                    if ($request->return_direct == 'save_continue') {
                        return redirect('admin/' . $this->module['code'] . '/' . $item->id);
                    } elseif ($request->return_direct == 'save_create') {
                        return redirect('admin/' . $this->module['code'] . '/add');
                    } elseif ($request->return_direct == 'save_test') {
                        return redirect('admin/' . $this->module['code'] . '/' . $item->id . '/test');
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
            $item->delete();
            CommonHelper::flushCache($this->module['table_name']);
            CommonHelper::one_time_message('success', 'Xóa thành công!');
            return redirect('admin/' . $this->module['code']);
        } catch (\Exception $ex) {
//            CommonHelper::one_time_message('error', $ex->getMessage());
            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            return back();
        }
    }

    public function crawl(){
        try {
            $crawlProduct = new UpdatePriceWss();
            $crawlProduct->handle();

            CommonHelper::one_time_message('success', 'Crawl thành công!');
            return back();
        } catch (\Exception $ex) {
            CommonHelper::one_time_message('success', 'Crawl thất bại!');
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

    public function deleteLinksError() {
        LogUpdateProductPrice::whereRaw('1=1')->update([
            'links_error' => '',
            'log_price' => '',
        ]);
        CommonHelper::one_time_message('success', 'Đã xóa hết lịch sử lôi');
        return back();
    }
}
