<?php

namespace Ferranfg\Base\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\ClientException;

class Facebook
{
    private static $base_api = "https://graph.facebook.com/v15.0/";

    /**
     * Llama a la API de Facebook Graph.
     *
     * @return Response
     */
    public static function graphApi($endpoint, $params = [], $form_params = [])
    {
        $graph_api = self::$base_api . "{$endpoint}?" . http_build_query($params);
        $method = count($form_params) ? 'post' : 'get';

        try
        {
            $request = (new Client)->request($method, $graph_api, [
                RequestOptions::FORM_PARAMS => $form_params
            ]);

            return json_decode((string) $request->getBody());
        }
        catch (ClientException $e)
        {
            return json_decode((string) $e->getResponse()->getBody());
        }
    }

    /**
     * Sube una imagen de Instagram mediante la API de Facebook Graph.
     *
     * @return Response
     */
    public static function uploadMedia($ig_user_id, $access_token, $params)
    {
        $media = self::graphApi("{$ig_user_id}/media", [
            'access_token' => $access_token
        ], $params);

        if ( ! property_exists($media, 'id')) return false;

        $pending = true;

        while ($pending)
        {
            $media = self::graphApi("{$media->id}", [
                'access_token' => $access_token,
                'fields' => 'id,status_code'
            ]);

            sleep(2);

            $pending = (property_exists($media, 'status_code') and $media->status_code != 'FINISHED');
        }

        return self::graphApi("{$ig_user_id}/media_publish", [
            'access_token' => $access_token
        ], [
            'creation_id' => $media->id
        ]);
    }

    /**
     * Suba una imagen a la Facebook Page mediante la API de Facebook Graph.
     */
    public static function uploadPost($page_id, $access_token, $params)
    {
        $page = self::graphApi("{$page_id}", [
            'access_token' => $access_token,
            'fields' => 'access_token'
        ]);

        if (array_key_exists('url', $params)) return self::graphApi("{$page_id}/photos", [
            'access_token' => $page->access_token
        ], $params);

        if (array_key_exists('file_url', $params)) return self::graphApi("{$page_id}/videos", [
            'access_token' => $page->access_token
        ], $params);

        return self::graphApi("{$page_id}/feed", [
            'access_token' => $page->access_token
        ], $params);
    }
}