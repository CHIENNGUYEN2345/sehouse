<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CommonHelper;
use App\Models\Setting;
use Auth;
use Illuminate\Http\Request;

class SettingController extends Controller
{

    public function index(Request $request)
    {
        try {
            $settings = CommonHelper::getFromCache('settings', ['settings']);
            if (!$settings) {
                $settings = Setting::whereIn('type', ['general_tab'])->pluck('value', 'name')->toArray();
                CommonHelper::putToCache('settings', $settings, ['settings']);
            }

            $settings['logo'] = asset('public/filemanager/userfiles/'. $settings['logo']);

            return response()->json([
                'status' => true,
                'msg' => 'Thành công',
                'errors' => (object)[],
                'data' => $settings,
                'code' => 201
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'msg' => 'Thất bại',
                'errors' => [
                    'exception' => [
                        'Tài khoản không tồn tại'
                    ]
                ],
                'data' => null,
                'code' => 401
            ]);
        }
    }
}

