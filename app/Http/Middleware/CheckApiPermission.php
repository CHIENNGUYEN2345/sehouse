<?php

namespace App\Http\Middleware;

use App\Http\Helpers\CommonHelper;
use Closure;

class CheckApiPermission
{
    
    public function handle($request, Closure $next, $permissions)
    {
        if(!CommonHelper::has_permission(\Auth::guard('admin_api')->user()->id, $permissions)){
            return response()->json([
                'status' => false,
                'msg' => 'Bạn không có quyền!',
            ]);
        }
        return $next($request);
    }
}
