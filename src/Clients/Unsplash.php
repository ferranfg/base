<?php

namespace Ferranfg\Base\Clients;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Intervention\Image\ImageManagerStatic;
use Unsplash\Photo;
use Unsplash\Search;
use Unsplash\Exception;
use Unsplash\HttpClient;

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

    public static function search($query, $page = 1, $per_page = 10, $orientation = null)
    {
        self::init();

        $search = Search::photos($query, $page, $per_page, $orientation);

        return collect($search->getResults());
    }

    public static function random($params)
    {
        self::init();

        return Photo::random($params);
    }

    public static function randomFromCollections()
    {
        try
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
        catch (Exception $e)
        {
            return collect([
                ['urls' => ['regular' => config('base.hero_image')]]
            ]);
        }
    }

    /**
     * Utilizado como endpoint para obtener imágenes aleatorias
     *
     * @param Request $request
     * @param $method
     * @param null $param
     * @return array|\Illuminate\Http\Response
     */
    public static function request(Request $request, $method, $param = null)
    {
        if ( ! method_exists(self::class, $method)) return abort(404);

        $response = self::$method($param);

        if ($request->type == 'image')
        {
            if ($response instanceof Collection) $response = $response->random();
            if ($response instanceof Photo) $response = $response->toArray();

            return ImageManagerStatic::make($response['urls']['regular'])->response();
        }

        return $response->toArray();
    }
}