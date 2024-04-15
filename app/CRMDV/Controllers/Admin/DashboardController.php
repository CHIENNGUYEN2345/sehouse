<?php

namespace App\CRMDV\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CommonHelper;
use App\Models\Setting;
use Auth;
use DB;
use Illuminate\Http\Request;
use Mail;
use App\CRMDV\Controllers\Helpers\CRMDVHelper;

class DashboardController extends Controller
{

    public function dashboardSoftware()
    {
        $data['page_title'] = 'Thống  kê';
        $data['page_type'] = 'list';

        if (CRMDVHelper::getRoleType(\Auth::guard('admin')->user()->id) == 'customer') {
            return view('CRMDV.dashboard.dashboard_customer', $data);
        } else {
            return view('CRMDV.dashboard.dashboard', $data);
        }
    }

    public function dsKyThuat() {
        $data['page_title'] = 'Danh sách kỹ thuật';
        $data['page_type'] = 'list';

        return view('CRMDV.dashboard.ds_ky_thuat', $data);
    }
}
