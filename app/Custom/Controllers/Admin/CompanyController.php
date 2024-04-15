<?php

namespace App\Custom\Controllers\Admin;
use App\CRMDV\Models\CompanyProfile;
use App\Http\Helpers\CommonHelper;
use App\Models\Admin;
use App\Models\Province;
use Illuminate\Http\Request;
use Validator;

class CompanyController extends CURDBaseController
{

    protected $orderByRaw = 'ngay_cap desc';

    protected $module = [
        'code' => 'company',
        'table_name' => 'company_category',
        'label' => 'Hồ sơ công ty',
        'modal' => '\App\Custom\Models\Company',
        'list' => [
            ['name' => 'name', 'type' => 'text_edit', 'label' => 'Custom_admin.company_name'],
            ['name' => 'mst', 'type' => 'text', 'label' => 'Custom_admin.company_mst'],
            ['name' => 'address', 'type' => 'text', 'label' => 'Custom_admin.company_address'],
            ['name' => 'dai_dien', 'type' => 'text', 'label' => 'Custom_admin.company_representative'],
            ['name' => 'tel', 'type' => 'text', 'label' => 'Custom_admin.company_tel'],
            ['name' => 'career_id', 'type' => 'relation_filter', 'label' => 'Custom_admin.company_career', 'object' => 'career', 'display_field' => 'name', 'sort' => true],
            ['name' => 'ngay_cap', 'type' => 'date_vi', 'label' => 'Ngày cấp', 'sort' => true],
            ['name' => 'crawl_link', 'type' => 'a', 'label' => 'Link gốc', 'inner' => 'Xem', 'sort' => true],
//            ['name' => 'export_ex', 'type' => 'status', 'label' => 'Đã xuất EX', 'sort' => true],
        ],
        'form' => [
            'general_tab' => [
                ['name' => 'name', 'type' => 'text', 'class' => '', 'label' => 'Custom_admin.company_name', 'group_class' => 'col-md-6'],
                ['name' => 'mst', 'type' => 'text', 'class' => '', 'label' => 'Custom_admin.company_mst', 'group_class' => 'col-md-3'],
                ['name' => 'address', 'type' => 'text', 'class' => '', 'label' => 'Custom_admin.company_address', 'group_class' => 'col-md-3'],
                ['name' => 'tel', 'type' => 'text', 'class' => '', 'label' => 'Custom_admin.company_tel', 'group_class' => 'col-md-6'],
            ],
        ]
    ];

    protected $quick_search = [
        'label' => 'ID',
        'fields' => 'id, name, name_en, name_short, mst, address, tel, email, crawl_link'
    ];

    protected $filter = [
        'career_id' => [
            'label' => 'Ngành nghề',
            'type' => 'select2_model',
            'display_field' => 'name',
            'model' => \App\Custom\Models\CompanyCategory::class,
            'object' => 'company_category',
            'query_type' => '='
        ],
        'province_id' => [
            'label' => 'Tỉnh/thành',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'model' => Province::class,
            'object' => 'province',
            'query_type' => '='
        ],

        'filter_date' => [
            'label' => 'Lọc theo',
            'type' => 'filter_date',
            'options' => [
                '' => '',
                'created_at' => 'Ngày tạo',
                'ngay_cap' => 'Ngày cấp',
            ],
            'query_type' => 'filter_date'
        ],
    ];

    public function getIndex(Request $request)
    {

//        $codes = CompanyProfile::where('address', 'like', '%Hà Nội%')->whereNull('province_id')->update(['province_id' => 2]);

        $data = $this->getDataList($request);

        return view('Custom.company.list')->with($data);
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
                return view('Custom.company.add')->with($data);
            } else if ($_POST) {
                \DB::beginTransaction();

                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                ], [
                    'name.required' => 'Bắt buộc phải nhập tên',
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                    //  Tùy chỉnh dữ liệu insert

                    $data['admin_id'] = \Auth::guard('admin')->user()->id;

                    $data['bill_ids'] = '|' . implode('|', $request->get('bill_ids', [])) . '|';

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
                return view('Custom.company.edit')->with($data);
            } else if ($_POST) {
                \DB::beginTransaction();

                $validator = Validator::make($request->all(), [
//                    'name' => 'required',
//                    'link' => 'required',
                ], [
//                    'name.required' => 'Bắt buộc phải nhập tên gói',
//                    'link.unique' => 'Web này đã đăng!',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $data = $this->processingValueInFields($request, $this->getAllFormFiled());

                    $data['bill_ids'] = '|' . implode('|', $request->get('bill_ids', [])) . '|';

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

                $field_name = ['ID'];
                $field_name[] = 'Tên';
                $field_name[] = 'Tên quốc tế';
                $field_name[] = 'Tên ngắn';
                $field_name[] = 'Mã số thuế';
                $field_name[] = 'Địa chỉ';
                $field_name[] = 'Người đại diện';
                $field_name[] = 'Số điện thoại';
                $field_name[] = 'Email';
                $field_name[] = 'Ngày cấp';
                $field_name[] = 'Ngành nghề';
                $field_name[] = 'Tỉnh/thành';
                $field_name[] = 'Trạng thái';

                $sheet->row(1, $field_name);

                $k = 2;

                foreach ($data as $value) {
                    $data_export = [];
                    $data_export[] = $value->id;
                    $data_export[] = $value->name;
                    $data_export[] = $value->name_en;
                    $data_export[] = $value->name_short;
                    $data_export[] = $value->mst;
                    $data_export[] = $value->address;
                    $data_export[] = $value->dai_dien;
                    $data_export[] = $value->tel;
                    $data_export[] = $value->email;
                    $data_export[] = date('d/m/Y', strtotime($value->ngay_cap));
                    $data_export[] = @$value->career->name;
                    $data_export[] = @$value->province->name;
                    $data_export[] = $value->trang_thai	;

                    // dd($this->getAllFormFiled());
                    $sheet->row($k, $data_export);
                    $k++;

                    $value->export_ex = 1;
                    $value->save();
                }
            });
        })->download('xlsx');
    }
}
