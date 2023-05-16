<?php

namespace Ferranfg\Base\Clients;

use OpenAI as OpenAIClient;

class OpenAI
{
    /**
     * The OpenAI client instance.
     *
     * @var \OpenAI
     */
    public function __construct()
    {
        $client = OpenAIClient::client(
            config('services.openai.secret'),
            config('services.openai.key')
        );

        return $client;
    }
}