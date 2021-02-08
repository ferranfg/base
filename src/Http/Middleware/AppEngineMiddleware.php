<?php

namespace Ferranfg\Base\Http\Middleware;

use Closure;

class AppEngineMiddleware
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
        if ($request->server->has('X-Appengine-User-IP'))
        {
            $request->server->set('REMOTE_ADDR', $request->server->get('X-Appengine-User-IP'));
        }

        return $next($request);
    }
}
