<?php

namespace Ferranfg\Base\Listeners;

use Notification;
use Carbon\Carbon;
use Ferranfg\Base\Base;
use Illuminate\Database\Eloquent\Model;
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
        Notification::send(Base::user(), new DiscordNotification($this->formatMessage($name, $events)));
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
    private function formatMessage($name, $events)
    {
        $record = ['[' . Carbon::now() . ']', "{$name}:"];
        $event = reset($events);

        if ($event instanceof ExceptionReported and property_exists($event, 'exception'))
        {
            array_push($record, $event->exception->getMessage());
        }
        else
        {
            foreach ($event as $model)
            {
                if (method_exists($model, 'toMessage'))
                {
                    array_push($record, $model->toMessage());
                }
                else if ($model instanceof Model)
                {
                    array_push($record, "#{$model->id}");
                }
            }
        }

        return implode(' ', $record);
    }

}
