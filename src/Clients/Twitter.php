<?php

namespace Ferranfg\Base\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class Twitter
{
    const BASE_URL = 'https://api.twitter.com/';

    public static function token()
    {
        $request = (new Client)->post(self::BASE_URL . 'oauth2/token', [
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
            ],
            RequestOptions::AUTH => [
                config('services.twitter.key'),
                config('services.twitter.secret')
            ],
            RequestOptions::FORM_PARAMS => [
                'grant_type' => 'client_credentials'
            ]
        ]);

        return json_decode((string) $request->getBody());
    }

    private static function options()
    {
        return [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . self::token()->access_token
            ]
        ];
    }

    public static function get($uri, $params = [])
    {
        if (count($params)) $uri .= '?' . http_build_query($params);

        $request = (new Client)->get(self::BASE_URL . "2/{$uri}", self::options());

        return json_decode((string) $request->getBody());
    }

    public static function lookup($id, $params = [])
    {
        return self::get("tweets/{$id}", array_merge($params, [
            'tweet.fields' => 'public_metrics',
            'user.fields' => 'profile_image_url,verified',
            'expansions' => 'author_id'
        ]));
    }
}
