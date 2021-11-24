<?php

namespace Ferranfg\Base\Http\Middleware;

use Closure;

class BaseMiddleware
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
        if ($request->server->has('HTTP_CF_CONNECTING_IP'))
        {
            $request->server->set('REMOTE_ADDR', $request->server->get('HTTP_CF_CONNECTING_IP'));
        }

        view()->share([
            'meta_title' => config('base.meta_title'),
            'meta_description' => config('base.meta_description'),
            'meta_url' => url()->current(),
            'meta_image' => config('base.meta_image'),
            'og_width' => 1200,
            'og_height' => 628
        ]);

        return $next($request);
    }
}
