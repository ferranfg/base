<?php

namespace Ferranfg\Base\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;

trait IsShareable
{
    use HasMetadata;

    /**
     * Construye la URL de intent tweet para compartir directamente
     * https://developer.twitter.com/en/docs/twitter-for-websites/tweet-button/overview
     */
    public function intentTweetUrl()
    {
        return 'https://twitter.com/intent/tweet?' . http_build_query([
            'url' => $this->canonical_url,
        ]);
    }

    /*
     * Construye la URL de intent Facebook para compartir.
     * https://developers.facebook.com/docs/sharing/reference/share-dialog
     */
    public function intentFacebookUrl()
    {
        return 'https://www.facebook.com/dialog/share?' . http_build_query([
            'app_id' => config('services.facebook.client_id'),
            'display' => 'popup',
            'redirect_uri' => $this->canonical_url,
            'href' => $this->canonical_url,
        ]);
    }

    /**
     * Construye la URL de intent Pinterest para compartir.
     * https://developers.pinterest.com/docs/add-ons/save-button/
     */
    public function intentPinterestUrl()
    {
        return 'https://www.pinterest.com/pin/create/button?' . http_build_query([
            'url' => $this->canonical_url,
            'media' => img_url($this->photo_url),
            'description' => $this->name,
        ]);
    }

    /**
     * Construye la URL de intent WhatsApp para compartir.
     */
    public function intentWhatsAppUrl()
    {
        return 'https://api.whatsapp.com/send?' . http_build_query([
            'text' => $this->canonical_url,
        ]);
    }

    /**
     * Check if the model has a shortlink service.
     */
    public function hasShortlink()
    {
        return config('services.bitly.secret');
    }

    /**
     * Get the shortlink of the model or create a new one.
     */
    public function shortlink()
    {
        if ($shortlink = $this->getMetadata('_shortlink')) return Arr::get($shortlink, 'link');

        $request = (new Client)->post('https://api-ssl.bitly.com/v4/shorten', [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . config('services.bitly.secret'),
            ],
            RequestOptions::JSON => [
                'long_url' => $this->canonical_url,
            ]
        ]);

        $response = json_decode($request->getBody()->getContents(), true);

        $this->setMetadata('_shortlink', $response);

        return Arr::get($response, 'link');
    }
}