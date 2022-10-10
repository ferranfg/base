<?php

namespace Ferranfg\Base\Listeners;

use Notification;
use Carbon\Carbon;
use Ferranfg\Base\Base;
use Ferranfg\Base\Events\DiscordMessage;
use Ferranfg\Base\Events\ExceptionReported;
use Ferranfg\Base\Notifications\DiscordNotification;

class DiscordEventSubscriber
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($name, $events)
    {
        Notification::send(Base::user(), new DiscordNotification($this->formatMessage($events)));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen('Laravel\Spark\*', 'Ferranfg\Base\Listeners\DiscordEventSubscriber@handle');
        $events->listen('Ferranfg\Base\*', 'Ferranfg\Base\Listeners\DiscordEventSubscriber@handle');
        $events->listen('App\Events\*', 'Ferranfg\Base\Listeners\DiscordEventSubscriber@handle');
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  string  $name
     * @param  object  $events
     * @return string
     */
    private function formatMessage($events)
    {
        $record = [];
        $event = reset($events);

        if ($event instanceof ExceptionReported and property_exists($event, 'exception'))
        {
            $exception = $event->exception;

            array_push($record, '**' . request()->url() . '**');
            array_push($record, '`' . get_class($exception) . '`');
            array_push($record, '`' . $exception->getMessage() . '`');

            if (method_exists($exception, 'getFile') and method_exists($exception, 'getLine'))
            {
                array_push($record, '`' . $exception->getFile() . ':' . $exception->getLine() . '`');
            }
        }
        else if ($event instanceof DiscordMessage)
        {
            array_push($record, '**' . $event->name . '**');
            array_push($record, '`' . json_encode($event->attr) . '`');
        }
        else
        {
            array_push($record, '**' . get_class($event) . '**');
        }

        return implode(' - ', $record);
    }

}
