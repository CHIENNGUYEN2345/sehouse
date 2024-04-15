<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, $role = null)
    {
        if (Auth::guard($role)->check()) {
            if ($role == 'student') {
                return redirect('/');
            }
            else if ($role == 'admin') {
                return redirect('/admin/dashboard');
            }
        }

        return $next($request);
    }
}
