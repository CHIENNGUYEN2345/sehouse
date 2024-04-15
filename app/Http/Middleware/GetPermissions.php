<?php

namespace App\Http\Middleware;

use App\Http\Helpers\CommonHelper;
use Closure;

class GetPermissions
{
    
    public function handle($request, Closure $next)
    {
        $per_check = \Eventy::filter('permission.check', [
            'setting',
            'dashboard',
            'admin_view', 'admin_edit', 'admin_add', 'admin_delete',
            'role_view', 'role_add', 'role_edit', 'role_delete',
            'user_view', 'user_edit', 'user_add', 'user_delete',
            'super_admin',
            'view_all_data',
        ]);
        $permissions = CommonHelper::has_permission(@\Auth::guard('admin')->user()->id, $per_check);
        \View::share('permissions', $permissions);
        return $next($request);
    }
}
