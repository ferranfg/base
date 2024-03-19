<?php

namespace Ferranfg\Base\Traits;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

trait HasVisits
{
    /**
     * Añade el método orderByVisits().
     */
    public function scopeOrderByVisits($query)
    {
        $model = $query->getModel();

        // Eliminamos del JSON la parte de {"es":" = 7
        return $query->select(
                DB::raw('cast(substring(metadata.value, 7) as unsigned) as visits'),
                "{$model->getTable()}.*"
            )
            ->leftJoin('metadata', function ($join) use ($model)
            {
                $join->on("{$model->getTable()}.id", 'metadata.parent_id');
                $join->where('metadata.name', '_visits');
                $join->where('metadata.parent_type', get_class($this));
            })
            ->orderBy('visits', 'desc');
    }

    /**
     * Devuelve el número de visitas de una página.
     *
     * return int
     */
    public function getVisitsAttribute()
    {
        return (int) $this->getMetadata('_visits');
    }

    /**
     * Increases the _visits metadata with 1 visit
     */
    public function trackVisit()
    {
        $crawlers = ['bot', 'crawl', 'slurp', 'spider', 'mediapartners'];

        if (Str::contains(request()->userAgent(), $crawlers)) return $this;

        if (config('base.tracking_api')) $this->trackVisitWithApi();

        $visits = $this->getMetadata('_visits');

        if (is_null($visits)) $visits = 0;

        $visits++;

        $this->setMetadata('_visits', $visits);

        return $this;
    }

    /**
     * Tracks the visit with an external API
     */
    public function trackVisitWithApi()
    {
        try
        {
            (new Client())->request('POST', config('base.tracking_api'), [
                RequestOptions::HEADERS => [
                    'User-Agent' => request()->userAgent(),
                    'X-Plausible-IP' => request()->ip(),
                ],
                RequestOptions::JSON => [
                    'domain' => config('base.tracking_domain'),
                    'name' => 'pageview',
                    'url' => request()->url(),
                    'referrer' => request()->header('referer'),
                    'props' => [
                        'site' => config('app.url'),
                    ]
                ]
            ]);
        }
        catch (Exception $e)
        {
            //
        }
    }
}