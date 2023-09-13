<?php

namespace Ferranfg\Base\Listeners;

use Ferranfg\Base\Models\User;
use Ferranfg\Base\Notifications\DiscordNotification;
use Ferranfg\Base\Events\ExceptionReported;
use Illuminate\Support\Facades\Notification;

class DiscordEventSubscriber
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $exception = $event->exception;
        $record = [];

        array_push($record, '*' . request()->url() . '*');
        array_push($record, '`' . get_class($exception) . '`');
        array_push($record, '`' . $exception->getMessage() . '`');

        Notification::send(new User, new DiscordNotification(implode(' - ', $record)));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(ExceptionReported::class, [DiscordEventSubscriber::class, 'handle']);
    }
}