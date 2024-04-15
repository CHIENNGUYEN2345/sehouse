<?php

namespace App\Http\Controllers;

use App\Http\Helpers\CommonHelper;
use Artisan;
use Cache;
use App\Models\Error;

class CacheController extends Controller
{
    public function clearAll() {
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Cache::flush();
        CommonHelper::one_time_message('success', 'Đã xóa toàn bộ cache');
        return back();
    }

    public function clearView() {
        Artisan::call('view:clear');
        CommonHelper::one_time_message('success', 'Đã xóa cache giao diện');
        return back();
    }

    public function clearSetting() {
        Cache::flush();
        CommonHelper::one_time_message('success', 'Đã xóa cache cấu hình');
        return back();
    }

    public function clearRoute() {
        Artisan::call('route:clear');
        CommonHelper::one_time_message('success', 'Đã xóa cache đường dẫn');
        return back();
    }

    public function clearError() {
        Error::all()->delete();
        CommonHelper::one_time_message('success', 'Đã xóa toàn bộ lịch sử lỗi');
        return back();
    }
}


