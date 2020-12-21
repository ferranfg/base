<?php

namespace Ferranfg\Base\Clients;

use Crew\Unsplash\Photo;
use Crew\Unsplash\HttpClient;

class Unsplash
{
    public static function random($params)
    {
        HttpClient::init([
            'applicationId' => config('services.unsplash.key'),
            'utmSource' => config('app.name')
        ]);

        return Photo::random($params);
    }

    public static function randomFromCollections()
    {
        $photos = cache()->remember('photos/random', 24 * 60 * 60, function ()
        {
            return self::random([
                'collections' => config('services.unsplash.collections'),
                'count' => 10
            ]);
        });

        return collect($photos->toArray());
    }
}