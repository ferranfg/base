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
     * Sube una imagen a la API de Facebook Graph.
     *
     * @return Response
     */
    public static function uploadMedia($consumer_key, $consumer_secret, $params)
    {
        $media = self::graphApi("{$consumer_key}/media", [
            'access_token' => $consumer_secret
        ], $params);

        if ( ! property_exists($media, 'id')) return false;

        return self::graphApi("{$consumer_key}/media_publish", [
            'access_token' => $consumer_secret
        ], [
            'creation_id' => $media->id
        ]);
    }
}