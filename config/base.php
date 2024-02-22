<?php

use Ferranfg\Base\Notifications\WelcomeNewsletter;

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

    'headers_font_family' => env('BASE_HEADING_FONT_FAMILY'),

    'banner_path' => env('BASE_BANNER_PATH'),

    'tracking_api' => env('BASE_TRACKING_API'),

    'tracking_domain' => env('BASE_TRACKING_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Shop Information
    |--------------------------------------------------------------------------
    |
    | Información que se servirá para la plantilla del ecommerce.
    |
    */

    'shop_enabled' => false,

    'shop_title' => env('APP_NAME'),

    'shop_description' => env('BASE_SHOP_DESCRIPTION'),

    'shop_path' => '/shop',

    'shop_template' => 'layouts.web',

    'shop_currency' => 'EUR',

    /*
    |--------------------------------------------------------------------------
    | Blog Information
    |--------------------------------------------------------------------------
    |
    | Información que se incluirá en la plantilla del blog, en concreto "blog.list"
    |
    */

    'blog_title' => env('APP_NAME'),

    'blog_description' => env('BASE_BLOG_DESCRIPTION'),

    'blog_path' => '/blog',

    'blog_template' => 'layouts.web',

    'blog_dynamic_posts' => null,

    'blog_pinned_id' => null,

    'blog_keywords' => false,

    'blog_before_post' => null,

    'blog_after_post' => null,

    'blog_extended_post' => 'base::blog.post-halfway',

    /*
    |--------------------------------------------------------------------------
    | Guides Information
    |--------------------------------------------------------------------------
    |
    | Información sobre la configuración de la guides
    |
    */

    'guides_title' => env('APP_NAME'),

    /*
    |--------------------------------------------------------------------------
    | Newsletter Information
    |--------------------------------------------------------------------------
    |
    | Información sobre la configuración de la newsletter
    |
    */

    'newsletter_modal' => false,

    'newsletter_title' => env('APP_NAME'),

    'newsletter_description' => env('BASE_NEWSLETTER_DESCRIPTION'),

    'newsletter_notification' => WelcomeNewsletter::class,

    'newsletter_action' => env('BASE_NEWSLETTER_ACTION'),

    /*
    |--------------------------------------------------------------------------
    | Embeddings Information
    |--------------------------------------------------------------------------
    |
    | Función que se encarga de recoger los documentos que queremos convertir a embeddings
    | para enviarlo posteriormente a OpenAI.
    |
    | gcloud compute ssh --project=ferran-figueredo --zone=us-central1-a root@ferranfigueredo-vm
    |
    */

    'assistance_model' => 'gpt-4',

    'assistance_system' => null,

    'assistance_embeddings' => null,

    'assistance_enabled' => false,

    'assistance_docs_view' => null,

    /*
    |--------------------------------------------------------------------------
    | Notes Information
    |--------------------------------------------------------------------------
    |
    | Información que se incluirá en la plantilla de las notas
    |
    */

    'notes_view' => 'base::blog.post',

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

    'feedback_username' => env('BASE_FEEDBACK_USERNAME'),

    'copyrigth_url' => env('BASE_COPYRIGTH_URL', 'https://ferranfigueredo.com'),

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
