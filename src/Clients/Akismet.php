<?php

namespace Ferranfg\Base\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class Akismet
{
    private static function getKey()
    {
        return config('services.akismet.key');
    }

    public static function isValidKey()
    {
        $res = (new Client)->post('https://rest.akismet.com/1.1/verify-key', [
            RequestOptions::FORM_PARAMS => [
                'key' => self::getKey(),
                'blog' => config('app.url')
            ]
        ]);

        return (string) $res->getBody() == 'valid';
    }

    public static function isSpam($user_ip, $permalink, $type, $author, $email, $content)
    {
        if ( ! self::isValidKey()) return null;

        $endpoint = 'https://' . self::getKey() . '.rest.akismet.com/1.1/comment-check';

        $res = (new Client())->post($endpoint, [
            RequestOptions::FORM_PARAMS => [
                'blog' => config('app.url'),
                'user_ip' => $user_ip,
                'permalink' => $permalink,
                'comment_type' => $type,
                'comment_author' => $author,
                'comment_author_email' => $email,
                'comment_content' => $content
            ]
        ]);

        return (string) $res->getBody() == "true";
    }

}