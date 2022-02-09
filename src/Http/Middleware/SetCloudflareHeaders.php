<?php

namespace Ferranfg\Base\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class SetCloudflareHeaders
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
        $response = $next($request);
        $content = $response->getContent();

        $max_age = config('base.cache_max_age');
        $options = [];

        // "null" para decir que desactivamos el cache
        if (is_null($max_age) or ! $request->isMethodCacheable() or ! $content)
        {
            return $response;
        }

        if (auth()->check() or $this->hasForms($content))
        {
            $options = ['private' => true, 'max_age' => 0, 's_maxage' => 0, 'no_store' => true];
        }
        else
        {
            $options = ['public' => true, 'max_age' => $max_age, 's_maxage' => $max_age];

            foreach ($response->headers->getCookies() as $cookie)
            {
                $response->headers->removeCookie($cookie->getName());
            }
        }

        $options['etag'] = md5($content);

        $response->setCache($options);
        $response->isNotModified($request);

        return $response;
    }

    protected function hasForms($content): bool
    {
        return Str::of(strtolower($content))->contains('<input type="hidden" name="_token"');
    }
}
