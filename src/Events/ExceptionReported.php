<?php

namespace Ferranfg\Base\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ExceptionReported
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $exception;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }
}
