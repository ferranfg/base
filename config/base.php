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
    | Site Information
    |--------------------------------------------------------------------------
    |
    | Información que se servirá para la plantilla del site genérico
    |
    */

    'hero_image' => env('BASE_HERO_IMAGE'),

    'csrf_disabled' => false,

    /*
    |--------------------------------------------------------------------------
    | Blog Information
    |--------------------------------------------------------------------------
    |
    | Información que se incluirá en la plantilla del blog, en concreto "blog.list"
    |
    */

    'blog_title' => env('BASE_BLOG_TITLE'),

    'blog_description' => env('BASE_BLOG_DESCRIPTION'),

    'blog_path' => '/blog',

    'blog_substack_mode' => false,

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
    | Incoming Discord Webhooks
    |--------------------------------------------------------------------------
    |
    | Incoming webhooks are a simple way to post messages from external sources into Discord.
    | They make use of normal HTTP requests with a JSON payload, which includes the message
    | and a few other optional details. You can include message attachments to display
    | richly-formatted messages.
    |
    */

    'discord_webhook' => env('BASE_DISCORD_WEBHOOK')

];
