<?php

namespace Mhafizhasan\AgakCore\Middleware;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Support\Facades\Session;

use DB;
use Closure;

/**
 *
 */
class AgakSession extends BaseMiddleware
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
        // If no Session redirect to logut page
        if(!Session::get('uid')) {
            return redirect('/');
        }
        return $next($request);
    }
}
