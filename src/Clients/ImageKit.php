<?php

namespace Ferranfg\Base\Clients;

use ImageKit\ImageKit as ImageKitClient;

class ImageKit
{
    /**
     * Crea una nueva instancia de ImageKit\ImageKit client
     *
     * @return \ImageKit\ImageKit
     */
    public static function init()
    {
        return new ImageKitClient(
            config('services.imagekit.key'),
            config('services.imagekit.secret'),
            config('services.imagekit.endpoint')
        );
    }
}