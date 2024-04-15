<?php

namespace App\Http\Middleware;

use Closure;

class CheckApiTokenAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if ($request->is_web == 1) {
                return $next($request);
            } else {
                if (empty($request->api_token)) {
                    throw new \Exception('api_token is null');
                }

                if (!\Auth::guard('admin_api')->check()) {
                    throw new \Exception('Wrong api_token');
                }
            }

            return $next($request);

        } catch (\Exception $ex) {
            return response()->json([
                'result'  => false,
                'message' => $ex->getMessage()
            ]);
        }
    }
}
