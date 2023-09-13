<?php

namespace Ferranfg\Base\Listeners;

use Exception;
use Ferranfg\Base\Events\ExceptionReported;
use Mixpanel;

class MixpanelEventSubscriber
{
    /**
     * The events that should not be handled.
     *
     * @var array
     */
    private $ignore_events = [
        \Illuminate\Auth\Events\Attempting::class,
        \Illuminate\Auth\Events\Authenticated::class,
        \Illuminate\Auth\Events\Validated::class,
    ];

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($name, $events)
    {
        try
        {
            if ($name == ExceptionReported::class or app()->isLocal()) return;

            $mixpanel = Mixpanel::getInstance(config('services.mixpanel.key'), [
                'host' => 'api-eu.mixpanel.com'
            ]);

            $request_ip = request()->header('CF-Connecting-IP', request()->ip());

            if (auth()->check())
            {
                $props = [
                    '$first_name' => auth()->user()->name,
                    '$email' => auth()->user()->email,
                ];

                $mixpanel->people->set(auth()->user()->id, $props, $request_ip);
                $mixpanel->identify(auth()->user()->id);
            }

            $mixpanel->register('ip', $request_ip);

            foreach ($events as $event)
            {
                if (in_array(get_class($event), $this->ignore_events)) continue;

                $mixpanel->track(
                    (get_class($event)),
                    (property_exists($event, 'attr') ? (array) $event->attr : [])
                );
            }
        }
        catch (Exception $e)
        {
            //
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen('App\Events\*', [MixpanelEventSubscriber::class, 'handle']);
        $events->listen('Ferranfg\Base\*', [MixpanelEventSubscriber::class, 'handle']);
        $events->listen('Illuminate\Auth\Events\*', [MixpanelEventSubscriber::class, 'handle']);
        $events->listen('Laravel\Jetstream\Events\*', [MixpanelEventSubscriber::class, 'handle']);
        $events->listen('Laravel\Fortify\Events\*', [MixpanelEventSubscriber::class, 'handle']);
        $events->listen('Spark\Events\*', [MixpanelEventSubscriber::class, 'handle']);
    }
}