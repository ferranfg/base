<?php

namespace Ferranfg\Base\Clients;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use GuzzleHttp\RequestOptions;
use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Twitter
{
    const BASE_URL = 'https://api.twitter.com/';

    private static $bearer_token;

    public static function bearerToken()
    {
        if ( ! is_null(self::$bearer_token)) return self::$bearer_token;

        $request = (new Client)->post(self::BASE_URL . 'oauth2/token', [
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
            ],
            RequestOptions::AUTH => [
                config('services.twitter.client_id'),
                config('services.twitter.client_secret')
            ],
            RequestOptions::FORM_PARAMS => [
                'grant_type' => 'client_credentials'
            ]
        ]);

        self::$bearer_token = json_decode((string) $request->getBody());

        return self::$bearer_token;
    }

    public static function get($uri, $params = [])
    {
        if (count($params)) $uri .= '?' . http_build_query($params);

        $request = (new Client)->get(self::BASE_URL . "2/{$uri}", [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . self::bearerToken()->access_token
            ]
        ]);

        return json_decode((string) $request->getBody());
    }

    public static function lookup($tweet_id)
    {
        if ( ! is_numeric($tweet_id))
        {
            $regex = '#https?://twitter\.com/(?:\#!/)?(\w+)/status(es)?/(\d+)#is';

            if (preg_match($regex, $tweet_id, $match)) $tweet_id = Arr::get($match, 3);
        }

        if (is_null($tweet_id)) throw new ModelNotFoundException;

        $response = cache()->remember("tweet-{$tweet_id}", 3600, function () use ($tweet_id)
        {
            return self::get("tweets/{$tweet_id}", [
                'tweet.fields' => 'public_metrics,referenced_tweets,in_reply_to_user_id,attachments,created_at',
                'user.fields' => 'profile_image_url,description,verified',
                'media.fields' => 'height,width,url',
                'expansions' => 'author_id,attachments.media_keys,in_reply_to_user_id'
            ]);
        });

        if (property_exists($response, 'errors')) throw new ModelNotFoundException;

        $tweet = $response->data;
        $includes = $response->includes;

        $author = collect($includes->users)->firstWhere('id', $tweet->author_id);
        $media = property_exists($includes, 'media') ? collect($includes->media)->firstWhere('type', 'photo') : null;

        return [$tweet, $author, $media];
    }

    public static function mentions($user_id)
    {
        $response = self::get("users/{$user_id}/mentions", [
            'tweet.fields' => 'referenced_tweets'
        ]);

        if (property_exists($response, 'errors')) throw new ModelNotFoundException;

        return $response;
    }

    public static function update($oauth_token, $oauth_secret, $params)
    {
        $twitter = new TwitterOAuth(
            config('services.twitter.client_id'),
            config('services.twitter.client_secret'),
            $oauth_token,
            $oauth_secret
        );

        $response = $twitter->post('statuses/update', $params);

        return $response;
    }
}
