<?php

namespace Ferranfg\Base\Clients;

use GuzzleHttp\Client;

class Pexels
{
    /**
     * Make a request to the Pexels API.
     *
     * @param  string  $url
     * @return array
     */
    public static function request($url)
    {
        $client = new Client();

        $response = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => config('services.pexels.secret')
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Make a request to the Pexels API.
     *
     * @param  string  $url
     * 
     */
    public static function searchVideo($query, $page = 1, $per_page = 10, $orientation = null)
    {
        $params = ['query' => $query, 'per_page' => $per_page, 'page' => $page];

        if ($orientation) $params['orientation'] = $orientation;

        $response = self::request('https://api.pexels.com/videos/search?' . http_build_query($params));

        return collect($response['videos']);
    }
}