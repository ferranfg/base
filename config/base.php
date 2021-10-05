<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Meta Information
    |--------------------------------------------------------------------------
    |
    | Información que se incluirá en las cabeceras de la plantilla si se usa conjuntamente
    | el componente de blade @include('base::components.meta). Incluye información sobre
    | Open Graph y Twitter Cards
    |
    */

    'meta_title' => env('BASE_META_TITLE'),

    'meta_description' => env('BASE_META_DESCRIPTION'),

    'meta_image' => env('BASE_META_IMAGE'),

    /*
    |--------------------------------------------------------------------------
    | Footer Information
    |--------------------------------------------------------------------------
    |
    | Información que aparecerá en el footer de la web (descripción un poco más larga del site)
    | y los enlaces a las redes sociales y mail de contacto
    |
    */

    'description' => env('BASE_DESCRIPTION'),

    'twitter_username' => env('BASE_TWITTER_USERNAME'),

    /*
    |--------------------------------------------------------------------------
    | Incoming Slack Webhooks
    |--------------------------------------------------------------------------
    |
    | Incoming webhooks are a simple way to post messages from external sources into Slack.
    | They make use of normal HTTP requests with a JSON payload, which includes the message
    | and a few other optional details. You can include message attachments to display
    | richly-formatted messages.
    |
    */

    'slack_webhook' => env('BASE_SLACK_WEBHOOK')

];
