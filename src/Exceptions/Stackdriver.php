<?php

namespace Ferranfg\Base\Exceptions;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use MarvinLabs\DiscordLogger\Converters\SimpleRecordConverter;
use MarvinLabs\DiscordLogger\Logger;

class Stackdriver extends Logger
{
    public function __construct(Container $container, Repository $config)
    {
        $config->set('discord-logger.emojis', null);
        $config->set('discord-logger.converter', SimpleRecordConverter::class);

        parent::__construct($container, $config);
    }
}
