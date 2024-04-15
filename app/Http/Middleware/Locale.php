<?php

namespace App\Http\Middleware;

use App;
use Closure;
use DB;
use Session;

class Locale
{
    public function handle($request, Closure $next)
    {
        $present_language = !isset($_COOKIE['language']) ? 'vi' : $_COOKIE['language'];
        App::setLocale($present_language);
        /*if(Auth::guard('admin')->check())
            return redirect('admin/dashboard');*/

        return $next($request);
    }
}
