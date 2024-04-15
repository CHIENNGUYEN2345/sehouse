<?php

namespace App\Http\Middleware;

use App\Http\Helpers\CommonHelper;
use Closure;
use Illuminate\Support\Facades\Input;
use Auth;

class LoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            return $next($request);
        } else {
            if ($request->path() == 'admin') {

                //  Bắt cập nhật ảnh đại diện
                //  Nếu đang không ở trang cập nhật profile
                if (strpos($request->url(), '/profile') == false && strpos($request->url(), '/logout') == false) {

                    //  1 tháng ko đổi mật khẩu thì yêu cầu đổi mk
                    if (Auth::guard('admin')->user()->image == null || Auth::guard('admin')->user()->image == '' || Auth::guard('admin')->user()->image == 'admin_default.png') {
                        CommonHelper::one_time_message('error', 'Yêu cầu cập nhật ảnh đại diện!');
                        return redirect('/admin/profile');
                    }
                }


                return redirect('admin/login');
            } else {
                return redirect()->route('login')->with('flash_message', 'Đăng nhập thất bại!');
            }
        }
    }
}
