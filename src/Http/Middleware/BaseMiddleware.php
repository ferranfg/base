<?php

namespace Ferranfg\Base\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

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
            'meta_url' => meta_url(),
            'meta_image' => config('base.meta_image'),
            'og_width' => 1200,
            'og_height' => 628
        ]);

        $response = $next($request);
        $max_age = config('base.cache_max_age');

        // "null" para decir que desactivamos el cache
        if (is_null($max_age)) return $response;

        if (auth()->check() or $this->hasForms($response) or $request->method() != 'GET')
        {
            $response->setCache(['private' => true, 'max_age' => 0, 's_maxage' => 0, 'no_store' => true]);
        }
        else
        {
            $response->setCache(['public' => true, 'max_age' => $max_age, 's_maxage' => $max_age]);

            foreach ($response->headers->getCookies() as $cookie)
            {
                $response->headers->removeCookie($cookie->getName());
            }
        }

        return $response;
    }

    protected function hasForms($response): bool
    {
        $content = strtolower($response->getContent());

        return Str::of($content)->contains('<input type="hidden" name="_token"');
    }
}
