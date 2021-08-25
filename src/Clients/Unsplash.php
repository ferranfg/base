<?php

namespace Ferranfg\Base\Clients;

use Crew\Unsplash\Photo;
use Crew\Unsplash\HttpClient;

class Unsplash
{
    private static function init()
    {
        HttpClient::init([
            'applicationId' => config('services.unsplash.key'),
            'utmSource' => config('app.name')
        ]);
    }

    public static function get($id)
    {
        self::init();

        return Photo::find($id);
    }

    public static function random($params)
    {
        self::init();

        return Photo::random($params);
    }

    public static function randomFromCollections()
    {
        $photos = cache()->remember('photos/random', 24 * 60 * 60, function ()
        {
            return self::random([
                'collections' => config('services.unsplash.collections'),
                'count' => 30
            ]);
        });

        return collect($photos->toArray());
    }
}