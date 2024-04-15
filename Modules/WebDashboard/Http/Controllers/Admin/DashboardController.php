<?php

namespace Modules\WebDashboard\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CommonHelper;
use App\Models\Setting;
use Auth;
use DB;
use Illuminate\Http\Request;
use Mail;

class DashboardController extends Controller
{
    protected $module = [
    ];

    public function dashboardSoftware()
    {
        $data['page_title'] = 'Thống kê';
        $data['page_type'] = 'list';

        if (CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name') == 'customer') {
            return view('webdashboard::dashboard_customer', $data);
        } else {
            return view('webdashboard::dashboard', $data);
        }
    }

//    public function dashboardCompany() {
//
//        $data['page_title'] = 'Thống kê';
//        $data['page_type'] = 'list';
//        $data['total_bill'] = Bill::select('total_price')->where('company_id',\Auth::guard('admin')->user()->last_company_id)->get();
//        $total_price=0;
//        foreach ($data['total_bill'] as $total_price_bill){
//            $total_price += $total_price_bill->total_price;
//        }
//        $data['total_price_bill'] = $total_price;
//
//        $data['total_product'] = Product::select('id')->where('company_id',\Auth::guard('admin')->user()->last_company_id)
//            ->where('status',1)->get()->count();
//        $data['bill_news'] = Bill::select('id','user_name','user_tel','user_email','created_at','updated_at','total_price')
//            ->where('company_id',\Auth::guard('admin')->user()->last_company_id)->orderBy('id','desc')
//            ->where('status',0)->paginate(10);
//        $data['total_bill_waitting'] = Bill::select('id')->where('company_id',\Auth::guard('admin')->user()->last_company_id)
//            ->where('status',1)->get()->count();
//        $data['total_bill_doing'] = Bill::select('id')->where('company_id',\Auth::guard('admin')->user()->last_company_id)
//            ->where('status',2)->get()->count();
//        $data['total_bill_done'] = Bill::select('id')->where('company_id',\Auth::guard('admin')->user()->last_company_id)
//            ->where('status',3)->get()->count();
//
//
//        $data['product_news'] = Product::select('id','name','image','code','final_price','base_price','status','multi_cat')
//            ->where('company_id',\Auth::guard('admin')->user()->last_company_id)->where('status',1)->orderBy('id','desc')->take(10)->get();
//        return view('webdashboard::dashboard_company', $data);
//    }


    public function tooltipInfo(Request $request)
    {
        $modal = new $request->modal;
        $data['item'] = $modal->find($request->id);
        $data['tooltip_info'] = $request->tooltip_info;

        return view('admin.common.modal.tooltip_info', $data);
    }

    public function ajax_up_file(Request $request)
    {
        if ($request->has('file')) {
            $file = CommonHelper::saveFile($request->file('file'));
        }
        return $file;
    }
}
