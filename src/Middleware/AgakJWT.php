<?php

namespace Mhafizhasan\AgakCore\Middleware;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;


use DB;
use Closure;

/**
 *
 */
class AgakJWT extends BaseMiddleware
{
    public function handle($request, Closure $next)
    {

        if (! $token = $this->auth->setRequest($request)->getToken()) {
            return \Response::make('No token', 400);
        }

        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            return \Response::make('Token expired', 400);
        } catch (JWTException $e) {
            return \Response::make('Invalid token', 400);
        }

        if (! $user) {
            return \Response::make('Not a valid user', 404);
        }

        $this->events->fire('tymon.jwt.valid', $user);

        return $next($request);
    }
}
