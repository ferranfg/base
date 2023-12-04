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

        if (file_exists(storage_path('redirects.json')))
        {
            $redirects = collect(
                json_decode(file_get_contents(storage_path('redirects.json')))
            );

            $redirect = $redirects->first(function ($redirect) use ($request)
            {
                return $redirect->url == $request->url();
            });

            if ($redirect) return redirect($redirect->to, $redirect->status);
        }

        view()->share([
            'meta_title' => config('base.meta_title'),
            'meta_description' => config('base.meta_description'),
            'meta_url' => meta_url(),
            'meta_image' => config('base.meta_image'),
            'og_width' => 1200,
            'og_height' => 628
        ]);

        return $next($request);
    }
}
