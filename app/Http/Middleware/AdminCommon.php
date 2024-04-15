<?php
namespace App\Http\Middleware;

use Closure;
use TorMorten\Eventy\Facades\Events as Eventy;

class AdminCommon
{

    public function handle($request, Closure $next)
    {
        /*$middleware = Eventy::filter('Middleware.AdminCommon', [
            'request' => $request,
            'next' => true,
            'link' => ''
        ]);
        if (!$middleware['next']) {
            return redirect($middleware['link']);
        }*/
        return $next($request);
    }
}
