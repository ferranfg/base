<?php

namespace Ferranfg\Base\Listeners;

use Notification;
use Carbon\Carbon;
use Ferranfg\Base\Base;
use Ferranfg\Base\Events\ExceptionReported;
use Ferranfg\Base\Notifications\SlackNotification;

class SlackEventSubscriber
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($name, $events)
    {
        $users = Base::user()->whereEmail(Base::$developers)->get();

        Notification::send($users, new SlackNotification($this->formatMessage($name, $events)));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen('Laravel\Spark\*', 'Ferranfg\Base\Listeners\SlackEventSubscriber@handle');
        $events->listen('Ferranfg\Base\*', 'Ferranfg\Base\Listeners\SlackEventSubscriber@handle');
        $events->listen('App\Events\*', 'Ferranfg\Base\Listeners\SlackEventSubscriber@handle');
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
        $record = ['[' . Carbon::now() . ']', $name];
        $event = reset($events);

        if ($event instanceof ExceptionReported and property_exists($event, 'exception'))
        {
            array_push($record, $event->exception->getMessage());
        }
        else
        {
            foreach ($event as $model)
            {
                if (method_exists($model, 'toJson')) array_push($record, $model->toJson());
            }
        }

        return implode(' ', $record);
    }

}
