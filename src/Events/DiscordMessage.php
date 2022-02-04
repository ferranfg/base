<?php

namespace Ferranfg\Base\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class DiscordMessage
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $name;

    public $attr = [];

    public function __construct($name, $attr)
    {
        $this->name = $name;
        $this->attr = $attr;
    }
}
