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
        $max_age = config('base.cache_max_age');

        // "null" para decir que desactivamos el cache
        if (is_null($max_age)) return $response;

        if ($request->isMethodCacheable() and $response->getContent() and ! auth()->check() and ! $this->hasForms($response))
        {
            $response->setCache(['public' => true, 'max_age' => $max_age, 's_maxage' => $max_age]);
        }
        else
        {
            $response->setCache(['private' => true, 'max_age' => 0, 's_maxage' => 0, 'no_store' => true]);
        }

        return $response;
    }

    protected function hasForms($response): bool
    {
        $content = strtolower($response->getContent());

        return Str::of($content)->contains('<input type="hidden" name="_token"');
    }
}
