<?php

namespace App\CRMWoo\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use Illuminate\Http\Request;
use Modules\EworkingCompany\Models\Company;
use App\CRMWoo\Models\Service;
use App\CRMWoo\Models\ServiceHistory;
use Validator;
use App\CRMWoo\Models\LeadContactedLog;
use App\CRMWoo\Models\Lead;
use App\Models\Admin;
use App\CRMWoo\Models\Bill;
use DB;

class MKTLeadController extends CURDBaseController
{

    protected $module = [
        'code' => 'lead',
        'table_name' => 'leads',
        'label' => 'Đầu mối',
        'modal' => '\App\CRMWoo\Models\Lead',
        'list' => [
            ['name' => 'id', 'type' => 'text', 'label' => 'ID', 'sort' => true],
            ['name' => 'name', 'type' => 'custom', 'td' => 'CRMWoo.lead.list.name', 'label' => 'Tên', 'sort' => true],
            ['name' => 'product', 'type' => 'text', 'label' => 'Lĩnh vực KD', 'sort' => true],
            ['name' => 'tinh', 'type' => 'text', 'label' => 'Tỉnh', 'sort' => true],
            ['name' => 'tel', 'type' => 'custom', 'td' => 'CRMWoo.lead.list.tel', 'label' => 'SĐT', 'sort' => true],
            ['name' => 'created_at', 'type' => 'date_vi', 'label' => 'Ngày tạo', 'sort' => true],
            ['name' => 'rate', 'type' => 'custom', 'td' => 'CRMWoo.lead.list.rate', 'label' => 'Đánh giá', 'sort' => true],
            ['name' => 'contacted_log_last', 'type' => 'custom', 'td' => 'CRMWoo.lead.list.contacted_log_last', 'label' => 'TT lần cuối', 'sort' => true],
            ['name' => 'admin_id', 'type' => 'relation', 'label' => 'Người tạo', 'object' => 'admin', 'display_field' => 'name'],
        ],
        'form' => [
            'general_tab' => [
                
                
            ],
            'tab_2' => [
                

            ],
            'tab_3' => [
                
            ],
        ]
    ];

    protected $quick_search = [
        'label' => 'ID, tên, sđt, đánh giá, mô tả',
        'fields' => 'id, name, tel, rate, profile, need, product'
    ];

    protected $filter = [
        'saler_ids' => [
            'label' => 'Sale',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'display_field2' => 'code',
            'model' => \App\Models\Admin::class,
            'object' => 'admin',
            'query_type' => 'custom'
        ],
        'marketer_ids' => [
            'label' => 'Nguồn marketing',
            'type' => 'select2_ajax_model',
            'display_field' => 'name',
            'display_field2' => 'code',
            'model' => \App\Models\Admin::class,
            'object' => 'admin',
            'query_type' => 'custom'
        ],
        'source' => [
            'label' => 'Nguồn khách',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'tinh' => [
            'label' => 'Tỉnh / thành',
            'type' => 'text',
            'query_type' => 'like'
        ],
        'service' => [
            'label' => 'Sản phẩm',
            'type' => 'select',
            'options' => [
                '' => 'Tất cả',
                'landingpage' => 'landingpage',
                'wordpress' => 'wordpress',
                'laravel' => 'laravel',
                'web khác' => 'web khác',
                'app' => 'app',
                'marketing tổng thể' => 'Marketing tổng thể',
                'ads' => 'ads',
                'seo' => 'seo',
                'content' => 'content',
                'logo' => 'logo',
                'banner' => 'banner',
                'design khác' => 'design khác',
                'game' => 'game',
            ],
            'query_type' => 'like'
        ],
        'status' => [
            'label' => 'Trạng thái',
            'type' => 'select',
            'options' => [
                '' => 'Tất cả',
                'Đang chăm sóc' => 'Đang chăm sóc',
                'Tạm dừng' => 'Tạm dừng',
                'Thả nổi' => 'Thả nổi',
                'Đã ký HĐ' => 'Đã ký HĐ',
            ],
            'query_type' => '='
        ],
        'rate' => [
            'label' => 'Đánh giá',
            'type' => 'select',
            'options' => [
                '' => 'Tất cả',
                'Chưa đánh giá' => 'Chưa đánh giá',
                'Không liên lạc được' => 'Không liên lạc được',
                'Không có nhu cầu' => 'Không có nhu cầu',
                'Đang tìm hiểu / Care dài' => 'Đang tìm hiểu / Care dài',
                'Quan tâm cao' => 'Quan tâm cao',
                'Đã ký HĐ' => 'Đã ký HĐ',
            ],
            'query_type' => 'custom'
        ],
        'sale_status' => [
            'label' => 'Tình trạng sale',
            'type' => 'select',
            'options' => [
                '' => 'Tất cả',
                'Chưa có sale' => 'Chưa có sale',
                'Đã có sale' => 'Đã có sale',
            ],
            'query_type' => 'custom'
        ],
        'lead_status' => [
            'label' => 'Sắp xếp',
            'type' => 'select',
            'options' => [
                '' => 'Không',
                'Sắp thả nổi' => 'Sắp thả nổi',
                'Ngày tạo: Mới -> cũ' => 'Ngày tạo: Mới -> cũ',
                'Ngày nhận: Mới -> cũ' => 'Ngày nhận: Mới -> cũ',
                'Đến ngày TT' => 'Đến ngày TT',
            ],
            'query_type' => 'custom'
        ],
        'contacted_log_last' => [
            'label' => 'Ngày TT',
            'type' => 'from_to_date',
            'query_type' => 'from_to_date'
        ],
        'check_tel' => [
            'label' => 'Check SĐT',
            'type' => 'textarea',
            'query_type' => 'custom'
        ],
    ];

    public function appendWhere($query, $request)
    {
        if($request->rate != null) {
            if ($request->rate == 'Đang tìm hiểu / Care dài') {
                $query = $query->where(function ($query) use ($request) {
                    $query->orWhere('rate', 'Đang tìm hiểu');
                    $query->orWhere('rate', 'Care dài');
                });
            } elseif ($request->rate == 'Chưa đánh giá') {
                $query = $query->where(function ($query) use ($request) {
                    $query->orWhere('rate', '');
                    $query->orWhere('rate', null);
                });
            } elseif ($request->rate == 'Quan tâm cao') {
                $query = $query->where(function ($query) use ($request) {
                    $query->orWhere('rate', 'Quan tâm cao');
                    $query->orWhere('rate', 'Cơ hội');
                });
            } else {
                $query = $query->where('rate', $request->rate);
            }
        }

        if (@$request->marketer_ids != null) {
            $query = $query->where(function ($query) use ($request) {
                // $query->orWhere('marketer_ids', 'like', '%|' . $request->marketer_ids . '|%');
                $query->orWhere('admin_id', $request->marketer_ids);
            });
        }
        
        if (@$request->saler_ids != null) {
            $query = $query->where('saler_ids', 'like', '%|' . $request->saler_ids . '|%');
        }
        if (@$request->sale_status != null) {
            if ($request->sale_status == 'Chưa có sale') {
                $query = $query->where(function ($query) use ($request) {
                    $query->orWhere('saler_ids', '|');
                    $query->orWhere('saler_ids', '||');
                    $query->orWhere('saler_ids', '');
                    $query->orWhere('saler_ids', null);
                });
            } else if ($request->sale_status == 'Đã có sale') {
                $query = $query->where('saler_ids', '!=', '|')->where('saler_ids', '!=', '||')
                ->where('saler_ids', '!=', '')
                ->where('saler_ids', '!=', null);
            }
        }
        if (@$request->lead_status != null) {
            if ($request->lead_status == 'Sắp thả nổi') {
                $query = $query->where('saler_ids', '!=', '|')->where('saler_ids', '!=', '||')
                ->where('saler_ids', '!=', '')
                ->where('saler_ids', '!=', null)
                ->where('status', 'Đang chăm sóc');
            }
        }

        //  Kiểm tra list sđt
        if (@$request->check_tel != null) {
            $check_tel = $request->check_tel;
            $tels = preg_split('/\r\n|[\r\n]/',$check_tel);
            foreach($tels as $k => $v) {
                $v = trim($v);
                $v = str_replace(' ', '', $v);
                $v = str_replace('.', '', $v);
                $v = str_replace(',', '', $v);
                if ($v != '' && mb_substr($v, 0, 1) != '0') {
                    $v = '0' . $v;
                }
                $tels[$k] = $v;
            }
            $query = $query->whereIn('tel', $tels);
        }

        //  Truy vấn ra khách mình tạo

        if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'view_all_data')) {
            //  Nếu ko có quyền xem toàn bộ dữ liệu thì chỉ truy vấn ra các lead của mình tạo
            $query = $query->where(function ($query) use ($request) {
                if (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['marketing'])) {
                    //  Nếu là marketing thì chỉ truy vấn ra khách của mình tạo
                    $query->orWhere('admin_id', \Auth::guard('admin')->user()->id);
                }
                
                
                // $query->orWhere('admin_id', \Auth::guard('admin')->user()->id);
            });
        }
        
        return $query;
    }

    public function sort($request, $model)
    {
        if (@$request->lead_status != null) {
            if ($request->lead_status == 'Sắp thả nổi') {
                $model = $model->orderBy('contacted_log_last', 'asc');
            } elseif ($request->lead_status == 'Ngày tạo: Mới -> cũ') {
                $model = $model->orderBy('id', 'desc');
            } elseif ($request->lead_status == 'Ngày nhận: Mới -> cũ') {
                $model = $model->orderBy('received_date', 'desc');
            } elseif ($request->lead_status == 'Đến ngày TT') {
                $model = $model->orderBy('dating', 'asc')
                ->where('dating', '<=', date('Y-m-d 23:59:59'));
            }
        }
        if ($request->sorts != null) {
            foreach ($request->sorts as $sort) {
                if ($sort != null) {
                    $sort_data = explode('|', $sort);
                    $model = $model->orderBy($sort_data[0], $sort_data[1]);
                }
            }
        } else {
            $model = $model->orderByRaw($this->orderByRaw);
        }
        return $model;
    }

    public function quickSearch($listItem, $r) {
        if (@$r->quick_search != '') {
            $listItem = $listItem->where(function ($query) use ($r) {
                foreach (explode(',', $this->quick_search['fields']) as $field) {
                    $query->orWhere(trim($field), 'LIKE', '%' . $r->quick_search . '%');    //  truy vấn các tin thuộc các danh mục con của danh mục hiện tại
                }

                //  Tìm theo sđt 
                $search_tel = str_replace('.', '', $r->quick_search);
                $search_tel = str_replace(',', '', $search_tel);
                $search_tel = trim($search_tel);
                $query->orWhere('tel', 'LIKE', '%' . $search_tel . '%');
            });

        }
        return $listItem;
    }

    public function getIndex(Request $request)
    {

        $data = $this->getDataList($request);

        return view('CRMWoo.mktlead.list')->with($data);
    }

}
