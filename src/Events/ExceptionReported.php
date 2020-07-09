<?php

namespace Ferranfg\Base\Events;

use Throwable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

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
