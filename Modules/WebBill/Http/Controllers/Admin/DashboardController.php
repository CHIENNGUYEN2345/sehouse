<?php

namespace Modules\WebBill\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CommonHelper;
use App\Models\Setting;
use Auth;
use DB;
use Illuminate\Http\Request;
use Mail;
use Modules\WebBill\Http\Helpers\WebBillHelper;

class DashboardController extends Controller
{

    public function dashboardSoftware()
    {
        $data['page_title'] = 'Thống kê';
        $data['page_type'] = 'list';

        if (WebBillHelper::getRoleType(\Auth::guard('admin')->user()->id) == 'customer') {
            return view('webbill::dashboard.dashboard_customer', $data);
        } else {
            if (!CommonHelper::has_permission(\Auth::guard('admin')->user()->id, 'dashboard_view')) {
            
                if (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['ctv_sale'])) {
                    return redirect('/admin/lead');
                }

                return view('webbill::dashboard.dashboard_blank', $data);
            }
            return view('webbill::dashboard.dashboard', $data);
        }
    }

    public function dsKyThuat() {
        $data['page_title'] = 'Danh sách kỹ thuật';
        $data['page_type'] = 'list';

        return view('webbill::dashboard.ds_ky_thuat', $data);
    }
}
