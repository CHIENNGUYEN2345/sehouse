<?php

namespace Modules\WebBill\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Models\Admin;
use Illuminate\Http\Request;
use Modules\LandingPage\Models\Bill;
use Modules\LandingPage\Models\Landingpage;
use Modules\LandingPage\Models\Service;
use Modules\WebBill\Models\Category;
use Modules\WebBill\Models\Codes;
use Modules\WebBill\Models\Theme;
use Modules\WebBill\Models\Tag;
use Validator;
use Modules\WebBill\Models\PostTag;
use Modules\WebBill\Models\BillProgress;

class RoomController extends CURDBaseController
{
    protected $module = [
        'code' => 'rooms',
        'table_name' => 'phong_ban',
        'label' => 'Phòng ban',
        'modal' => '\Modules\WebBill\Models\Phong_ban',
        'list' => [
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Tên phòng ban', 'sort' => true],
            ['name' => 'luong_co_ban', 'type' => 'price', 'label' => 'Lương cơ bản', 'sort' => true],
            ['name' => 'kpi', 'type' => 'number', 'label' => 'KPI', 'sort' => true],
            ['name' => 'name', 'type' => 'custom', 'td' => 'kitcarebooking::phong_ban.list.td.so_thanh_vien', 'label' => 'Số thành viên', 'sort' => true],
            ['name' => 'tong_luong', 'type' => 'custom', 'td' => 'kitcarebooking::phong_ban.list.td.tong_luong', 'label' => 'Tổng lương', 'sort' => true],
            ['name' => 'note', 'type' => 'text', 'label' => 'Ghi chú', 'sort' => true],
            ['name' => 'name', 'type' => 'custom', 'td' => 'kitcarebooking::phong_ban.list.td.so_su_kien', 'label' => 'Số sự kiện tham gia', 'sort' => true],
            ['name' => 'tong_diem', 'type' => 'number', 'td' => 'kitcarebooking::phong_ban.list.td.tong_diem', 'label' => 'Tổng điểm', 'sort' => true],
            ['name' => 'status', 'type' => 'status', 'label' => 'Trạng thái', 'sort' => true],
            ['name' => 'truong_phong', 'type' => 'custom', 'td' => 'kitcarebooking::list.td.ds_admin', 'label' => 'Trưởng phòng', 'object' => 'admin', 'sort' => true],
            ['name' => 'admin_id', 'type' => 'relation_filter', 'label' => 'Người tạo', 'object' => 'admin', 'display_field' => 'name'],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => 'required', 'label' => 'Tên phòng ban'],
                ['name' => 'truong_phong', 'type' => 'select2_ajax_model_thanh_vien', 'object' => 'admin', 'type_history' => 'relation_multiple', 'field' => 'kitcarebooking::form.fields.select2_model', 'multiple' => true, 'label' => 'Trưởng phòng', 'model' => Admin::class, 'display_field' => 'name'],
                ['name' => 'luong_co_ban', 'type' => 'number', 'class' => '', 'label' => 'Lương cơ bản','group_class' => 'col-md-6'],
                ['name' => 'kpi', 'type' => 'number', 'class' => '', 'label' => 'KPI','group_class' => 'col-md-6'],
                ['name' => 'status', 'type' => 'checkbox', 'label' => 'Kich hoạt', 'value' => 1, 'group_class' => 'col-md-6'],
                ['name' => 'note', 'type' => 'text', 'class' => '', 'label' => 'Ghi chú',],
                ['name' => 'content', 'type' => 'textarea', 'class' => '', 'label' => 'Mô tả', 'inner' => 'rows=15'],
                ['name' => 'notification', 'type' => 'textarea', 'class' => '', 'label' => 'Thông báo',],

            ],

            'image_tab' => [
                ['name' => 'image', 'type' => 'file_editor', 'label' => 'Ảnh'],
            ],
        ]

    ];

    protected $filter = [

    ];

    protected $quick_search = [
        'label' => 'ID, Tên',
        'fields' => 'id, name'
    ];

    public function getIndex(Request $request)
    {
        $data = $this->getDataList($request);

        return view('webbill::phong_ban.list')->with($data);
    }

    public function getDataList(Request $request) {
        //  Filter
        $where = $this->filterSimple($request);
        $listItem = $this->model->whereRaw($where);
        $listItem = $this->quickSearch($listItem, $request);
        if ($this->whereRaw) {
            $listItem = $listItem->whereRaw($this->whereRaw);
        }
        $listItem = $this->appendWhere($listItem, $request);

        //  Export
        if ($request->has('export')) {
            $this->exportExcel($request, $listItem->get());
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
        $data['module'] = $this->module;
        $data['quick_search'] = $this->quick_search;
        $data['filter'] = $this->filter;

        //  Set data for seo
        $data['page_title'] = $this->module['label'];
        $data['page_type'] = 'list';
        return $data;
    }

    public function thongKe($data, $listItem, $request) {
        $data['luong_co_ban'] = $listItem->sum('luong_co_ban');
        $data['kpi'] = $listItem->sum('kpi');
        $listItem = $listItem->get();

        $data['so_thanh_vien'] = 0;
        $data['tong_luong'] = 0;
        $data['so_su_kien'] = 0;
        $data['tong_diem'] = 0;
        foreach ($listItem as $item) {
            $data['so_thanh_vien'] += \App\Models\Admin::where('status', 1)->where('phong_ban_id', $item->id)->count();
            $data['tong_luong'] += ((int) $item->luong_co_ban + (int) $item->kpi) * \App\Models\Admin::where('status', 1)->where('phong_ban_id', $item->id)->count();

            $thanh_vien_ids = \App\Models\Admin::where('phong_ban_id', $item->id)->pluck('id')->toArray();
            $data['so_su_kien'] += count(\Modules\KitCareBooking\Models\SuKienThanhVien::rightJoin('su_kien', 'su_kien.id', '=', 'su_kien_thanh_vien_tham_gia.su_kien_id')
                ->whereIn('su_kien_thanh_vien_tham_gia.admin_id', $thanh_vien_ids)->groupBy('su_kien_thanh_vien_tham_gia.su_kien_id')->pluck('su_kien_thanh_vien_tham_gia.su_kien_id')->toArray());

            $data['tong_diem'] += \Modules\KitCareBooking\Models\SuKienThanhVien::whereIn('admin_id', $thanh_vien_ids)->sum('diem');
            if (\Modules\KitCareBooking\Models\SuKienThanhVien::whereIn('admin_id', $thanh_vien_ids)->sum('diem') > 0) {
//                dd($thanh_vien_ids, $item);
            }

        }

        return $data;
    }

    public function appendWhere($query, $request)
    {
//        //  Nếu không có quyền xem toàn bộ dữ liệu thì chỉ được xem các dữ liệu của công ty mình
//        if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'view_all_data')) {
//            $query = $query->where('company_id', \Auth::guard('admin')->user()->last_company_id);
//        }

        if (!is_null($request->get('su_kien'))) {
            $thanh_vien_ids = \Modules\KitCareBooking\Models\SuKienThanhVien::
            where('su_kien_id', $request->su_kien)->pluck('admin_id')->toArray();


            $ids = \Modules\KitCareBooking\Models\PhongBan::leftJoin('admin', 'admin.phong_ban_id', '=', 'phong_ban.id')
                ->whereIn('admin.id', $thanh_vien_ids)->groupBy('phong_ban.id')->pluck('phong_ban.id')->toArray();

            $query = $query->whereIn('id', $ids);
        }


        return $query;
    }

    public function add(Request $request)
    {
        try {
            if (!$_POST) {
                $data = $this->getDataAdd($request);
                return view('kitcarebooking::phong_ban.add')->with($data);
            } else if ($_POST) {
//                dd($request->all());
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
                    if ($request->has('truong_phong')) {
                        $data['truong_phong'] = '|' . implode('|', $request->truong_phong) . '|';
                    }


                    foreach ($data as $k => $v) {
                        $this->model->$k = $v;
                    }

                    if ($this->model->save()) {
                        $this->afterAddLog($request, $this->model);

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
                        return redirect('admin/' . $this->module['rooms'] . '/' . $this->model->id);
                    } elseif ($request->return_direct == 'save_create') {
                        return redirect('admin/' . $this->module['rooms'] . '/add');
                    } elseif ($request->return_direct == 'save_editor') {
                        return redirect('admin/' . $this->module['rooms'] . '/' . $this->model->id . '/editor');
                    }

                    return redirect('admin/' . $this->module['rooms']);
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

        //  Chỉ sửa được liệu công ty mình đang vào
//            if (strpos(\Auth::guard('admin')->user()->company_ids, '|' . $item->company_id . '|') === false) {
//                CommonHelper::one_time_message('error', 'Bạn không có quyền!');
//                return back();
//            }

        if (!is_object($item)) abort(404);
        if (!$_POST) {
            $data = $this->getDataUpdate($request, $item);
            return view('kitcarebooking::phong_ban.edit')->with($data);
        } else if ($_POST) {
//            dd($request->all());
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
                if ($request->has('truong_phong')) {
                    $data['truong_phong'] = '|' . implode('|', $request->truong_phong) . '|';
                }


                foreach ($data as $k => $v) {
                    $item->$k = $v;
                }
                if ($item->save()) {
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


}
