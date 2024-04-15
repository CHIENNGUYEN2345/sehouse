<?php

namespace App\Http\Middleware;

use App\Http\Helpers\CommonHelper;
use Closure;
use Auth;

class Guest
{
    public function handle($request, Closure $next, $guard = null)
    {
        if ($guard == 'student') {
            if (!Auth::guard($guard)->check()) {
                return \Redirect::guest('/')->with(['openLogin' => true]);
            }
            if (@Auth::user()->status == -1) {
                CommonHelper::one_time_message('error', 'Tài khoản của bạn bị khóa');
                return redirect()->back();
            }
        } else if ($guard == 'admin') {
            if (!Auth::guard($guard)->check()) {
                //  nếu chưa đăng nhập chuyển sang login
                return \Redirect::guest('admin/login');
            } else {

                //  Bắt cập nhật profile
                //  Nếu đang không ở trang cập nhật mật khẩu
                if (env('CHANGE_PASSWORD_ADMIN') == true && strpos($request->url(), '/change-password') == false && strpos($request->url(), '/logout') == false) {

                    //  1 tháng ko đổi mật khẩu thì yêu cầu đổi mk
                    if (strtotime(Auth::guard($guard)->user()->updated_at) < (time() - 30 * 24 * 60 * 60)) {
                        CommonHelper::one_time_message('error', 'Mật khẩu quá cũ. Yêu cầu đổi mật khẩu');
                        return redirect('/admin/profile/change-password');
                    }
                }

                //  Bắt cập nhật ảnh đại diện
                //  Nếu đang không ở trang cập nhật profile
                if (strpos($request->url(), '/profile') == false && strpos($request->url(), '/logout') == false) {

                    //  1 tháng ko đổi mật khẩu thì yêu cầu đổi mk
                    if (Auth::guard('admin')->user()->image == null || Auth::guard('admin')->user()->image == '' || Auth::guard('admin')->user()->image == 'admin_default.png') {
                        CommonHelper::one_time_message('error', 'Yêu cầu cập nhật ảnh đại diện!');
                        return redirect('/admin/profile');
                    }
                }
            }
        }
        return $next($request);
    }
}
