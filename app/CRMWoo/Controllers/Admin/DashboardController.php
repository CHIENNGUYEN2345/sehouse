<?php

namespace App\CRMWoo\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CommonHelper;
use App\Models\Setting;
use Auth;
use DB;
use Illuminate\Http\Request;
use Mail;
use App\CRMWoo\Controllers\Helpers\Helper;

class DashboardController extends Controller
{

    public function dashboardSoftware()
    {
        $data['page_title'] = 'Thống kê';
        $data['page_type'] = 'list';

        if (Helper::getRoleType(\Auth::guard('admin')->user()->id) == 'customer') {
            return view('CRMWoo.dashboard.dashboard_customer', $data);
        } else {
            if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'dashboard_view')) {
            
                if (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['ctv_sale'])) {
                    return redirect('/admin/lead');
                }

                return view('CRMWoo.dashboard.dashboard_blank', $data);
            }
            return view('CRMWoo.dashboard.dashboard', $data);
        }
    }

    public function dsKyThuat() {
        $data['page_title'] = 'Danh sách kỹ thuật';
        $data['page_type'] = 'list';

        return view('CRMWoo.dashboard.ds_ky_thuat', $data);
    }
}
