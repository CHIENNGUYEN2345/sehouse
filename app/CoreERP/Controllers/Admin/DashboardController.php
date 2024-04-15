<?php

namespace App\CoreERP\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CommonHelper;
use App\Models\Setting;
use Auth;
use DB;
use Illuminate\Http\Request;
use Mail;

class DashboardController extends Controller
{

    public function ajaxLoadKhoi(Request $r) {
        return view($r->file);
    }
}
