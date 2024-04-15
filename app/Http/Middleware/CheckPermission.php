<?php

namespace App\Http\Middleware;

use App\Http\Helpers\CommonHelper;
use Closure;

class CheckPermission
{
    
    public function handle($request, Closure $next, $permissions)
    {
        if(CommonHelper::has_permission(\Auth::guard('admin')->user()->id, $permissions)){
            return $next($request);
        }else{
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Bạn không có quyền!',
                ]);
            }
            CommonHelper::one_time_message('error', 'Bạn không có quyền!');
            return back();
        }
    }
}
