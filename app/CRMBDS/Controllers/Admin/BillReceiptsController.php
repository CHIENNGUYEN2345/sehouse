<?php

namespace App\CRMBDS\Controllers\Admin;

use App\CRMBDS\Models\BillReceipts;
use App\Http\Helpers\CommonHelper;
use Auth;
use Illuminate\Http\Request;
use Validator;
use App\CRMBDS\Models\Bill;
use App\CRMBDS\Models\Tag;
use App\CRMBDS\Models\PostTag;

class BillReceiptsController extends CURDBaseController
{
    protected $orderByRaw = 'status ASC, date desc';

    protected $module = [
        'code' => 'bill_receipts',
        'table_name' => 'bill_receipts',
        'label' => 'Phiếu thu',
        'modal' => '\App\CRMBDS\Models\BillReceipts',
        'list' => [
            ['name' => 'date', 'type' => 'date_vi', 'label' => 'Ngày'],
            ['name' => 'price', 'type' => 'price_vi', 'label' => 'Số tiền'],
            ['name' => 'status', 'type' => 'status', 'label' => 'Duyệt phiếu'],
            ['name' => 'image', 'type' => 'image', 'label' => 'Ảnh CK'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'date', 'type' => 'date', 'class' => 'required', 'label' => 'Ngày', 'value' => '', 'group_class' => 'col-sm-4'],
                ['name' => 'receiving_account', 'class' => 'required', 'type' => 'select2_model_tag', 'label' => 'Tk nhận tiền', 'model' => Tag::class, 'where' => "type = 'bill_receipts'",
                    'group_class' => 'col-md-8', 'inner' => 'maxTags: 1,'],
                ['name' => 'price', 'type' => 'price_vi', 'class' => 'required','label' => 'Số tiền khách trả', 'group_class' => 'col-sm-6'],
                ['name' => 'image', 'type' => 'file_image', 'class' => 'required', 'label' => 'Ảnh chuyển khoản', 'group_class' => 'col-sm-6'],
                ['name' => 'note', 'type' => 'textarea', 'class' => 'required', 'label' => 'Nội dung chuyển khoản'],

            ],
        ],
    ];

    protected $quick_search = [
        'label' => 'ID, giá trị',
        'fields' => 'id, date, price, note'
    ];

    protected $filter = [


    ];

    public function getIndex(Request $request)
    {

        $data = $this->getDataList($request);

        return view('CRMBDS.bill_receipts.list')->with($data);
    }

    public function appendWhere($query, $request)
    {

        if (@$request->bill_id != null) {
            $query = $query->where('bill_id', $request->bill_id);
        }

        return $query;
    }

    public function add(Request $request)
    {
        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('CRMBDS.bill_receipts.add')->with($data);
            } else if ($_POST) {
                $validator = Validator::make($request->all(), [
//                    'value' => 'required'
                ], [
//                    'value.required' => 'Bắt buộc phải nhập giá trị',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    //  Tùy chỉnh dữ liệu insert
                    $data['bill_id'] = $request->bill_id;

                    $data['admin_id'] = $data['saler_id'] = \Auth::guard('admin')->user()->id;

                    // if ($request->file('image') != null) {
                    //     $data['image'] = CommonHelper::saveFile($request->file('image'), $this->module['code']);
                       
                    // } else {
                    //     unset($data['image']);
                    // }
                    
                    #

                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {

                        //  Xử lý tag
                        $this->xulyTag($this->model->id, $data);

                        CommonHelper::flushCache();
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

                    if ($request->has('bill_id')) {
                        return redirect('admin/' . $this->module['code'] . '?bill_id=' . $request->bill_id);
                    } else {
                        return redirect('admin/' . $this->module['code']);
                    }
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
            return view('CRMBDS.bill_receipts.edit')->with($data);
        } else if ($_POST) {
            $validator = Validator::make($request->all(), [
                // 'value' => 'required'
            ], [
                // 'value.required' => 'Bắt buộc phải nhập tên',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            } else {
                $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                #

                foreach ($data as $k => $v) {
                    $item->$k = $v;
                }
                if ($item->save()) {

                    $this->xulyTag($item->id, $data);

                    //  cập nhật tiền đã nhận vào HĐ
                    $this->updateTienDaTraHD($item->bill_id);

                    CommonHelper::flushCache();
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

                if ($request->has('bill_id')) {
                    return redirect('admin/' . $this->module['code'] . '?bill_id=' . $request->bill_id);
                } else {
                    return redirect('admin/' . $this->module['code']);
                }
            }
        }
    }

    public function getPublish(Request $request)
    {
        // dd($request->all());
        try {

            \DB::beginTransaction();
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

            if ($item->{$request->column} == 0) {
                //  duyệt phiếu thu
                $item->{$request->column} = 1;
            } else {
                //  huỷ duyệt phiếu thu
                $item->{$request->column} = 0;
            }

            $item->save();

            //  cập nhật tiền đã nhận vào HĐ
            $this->updateTienDaTraHD($item->bill_id);

            \DB::commit();

            return response()->json([
                'status' => true,
                'published' => $item->{$request->column} == 1 ? true : false
            ]);
        } catch (\Exception $ex) {
            \DB::rollback();
            return response()->json([
                'status' => false,
                'published' => null,
                'msg' => $ex->getMessage()
            ]);
        }
    }

    public function updateTienDaTraHD($bill_id)
    {
        $price = BillReceipts::where('bill_id', $bill_id)
            ->where('price', '>', 0)
            ->where('status', 1)
            ->sum('price');
//        dd($price);

        Bill::where('id', $bill_id)->update(['total_received' => $price]);

        return true;
    }

    public function delete(Request $request)
    {
        try {
            $item = $this->model->find($request->id);
            $bill_id = $item->bill_id;

            $item->delete();

            //  cập nhật lại tiền đã nhận cho hợp đồng
            if ($bill_id != null) {
                //  nếu là giao dịch của HĐ thì cập nhật tiền đã nhận của HĐ
                $this->updateTienDaTraHD($bill_id);
            }

            CommonHelper::one_time_message('success', 'Xóa thành công!');

            if ($request->has('bill_id')) {
                return redirect('admin/' . $this->module['code'] . '?bill_id=' . $request->bill_id);
            } else {
                return redirect('admin/' . $this->module['code']);
            }
        } catch (\Exception $ex) {
            CommonHelper::one_time_message('error', 'Lỗi hệ thống! Vui lòng liên hệ kỹ thuật viên.');
            return back();
        }
    }

    public function xulyTag($post_id, $data)
    {
        $id_updated = [];
        $tags = json_decode($data['receiving_account']);

        if (is_array($tags) && !empty($tags)) {
            foreach ($tags as $tag_name) {
                $tag_name = $tag_name->value;
                //  Tạo tag nếu chưa có
                $tag = Tag::where('name', $tag_name)->first();
                if (!is_object($tag)) {
                    $tag = new Tag();
                    $tag->name = $tag_name;
                    $tag->slug = str_slug($tag_name, '-');
                    $tag->type = 'bill_receipts';
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
    }

}
