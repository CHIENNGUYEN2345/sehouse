<?php

namespace App\Custom\Controllers\Admin;

use App\Custom\Controllers\Admin\CURDBaseController;
use App\Http\Helpers\CommonHelper;
use App\Mail\MailServer;
use App\Models\Admin;
use App\Models\Setting;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use App\Custom\Controllers\Helpers\CustomHelper;
use App\Custom\Models\Bill;
use Validator;
use Cache;

class BillController extends CURDBaseController
{

    protected $orderByRaw = 'status DESC, id DESC';

//    protected $whereRaw = 'service_id IN (1, 17, 18, 19, 20, 21)';

    protected $module = [
        'code' => 'bill',
        'table_name' => 'bills',
        'label' => 'Hợp đồng',
        'modal' => '\App\Custom\Models\Bill',
        'list' => [

        ],
        'form' => [

        ],
    ];

    protected $filter = [

    ];

    protected $quick_search = [
        'label' => 'ID, tên miền, giá, note',
        'fields' => 'id, domain, exp_price, total_price, note'
    ];


    public function giaHan(Request $request)
    {
        $data['page_title'] = 'Hợp đồng sắp hết hạn';
        $data['page_type'] = 'list';
        $data['module'] = $this->module;

        return view('Custom.bill.gia_han')->with($data);
    }

    public function koGiaHan(Request $request)
    {
        $data['page_title'] = 'Hợp đồng sắp hết hạn mà không gia hạn';
        $data['page_type'] = 'list';
        $data['module'] = $this->module;

        return view('Custom.bill.ko_gia_han')->with($data);
    }
}
