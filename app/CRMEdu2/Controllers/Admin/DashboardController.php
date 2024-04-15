<?php

namespace App\CRMEdu\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CommonHelper;
use App\Models\Setting;
use Auth;
use DB;
use Illuminate\Http\Request;
use Mail;
use App\CRMEdu\Controllers\Helpers\CRMEduHelper;

class DashboardController extends Controller
{

    public function dashboardSoftware()
    {
        $data['page_title'] = 'Thống kê';
        $data['page_type'] = 'list';

        if (CRMEduHelper::getRoleType(\Auth::guard('admin')->user()->id) == 'customer') {
            return view('CRMEdu.dashboard.dashboard_customer', $data);
        } else {
            if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'dashboard_view')) {
            
                if (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['ctv_sale'])) {
                    return redirect('/admin/lead');
                }

                return view('CRMEdu.dashboard.dashboard_blank', $data);
            }
            return view('CRMEdu.dashboard.dashboard', $data);
        }
    }

    public function dsKyThuat() {
        $data['page_title'] = 'Danh sách Giảng viên';
        $data['page_type'] = 'list';

        return view('CRMEdu.dashboard.ds_ky_thuat', $data);
    }
}
