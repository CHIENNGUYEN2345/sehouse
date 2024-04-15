<?php

namespace Modules\LandingPage\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LandingPage\Models\Landingpage;
use Validator;
use URL;

class LandingpageController extends Controller
{
    public function index(Request $r)
    {
        $url = $r->get('url', '');
        $url = @explode('?', $url)[0];
        $url = str_replace('www.', '', $url);
        if (strpos($url, '://') !== false) {
            $url = @explode('://', $url)[1];
        }
        if (substr($url, -1) == '/') {
            $url = substr($url, 0, -1);
        }
        $landingpage = Landingpage::select('name', 'ladi_link', 'domain', 'form_action', 'form_fields', 'updated_at', 'head_code', 'body_code')
            ->where('status', 1)->where('domain', $url)->first();
        if (!is_object($landingpage)) {
            return response()->json([
                'status' => false,
                'data' => null,
                'msg' => 'Không tìm thấy tên miền ' . $url
            ]);
        }
        $landingpage->form_fields = json_decode($landingpage->form_fields);
        return response()->json([
            'status' => true,
            'data' => $landingpage,
            'msg' => 'Thành công'
        ]);
    }
}
