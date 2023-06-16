<?php

namespace Ferranfg\Base\Nova\Actions;

use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Nova\Http\Requests\NovaRequest;

class PostNewsletter extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * The type of newsletter we are sending.
     *
     * @var string
     */
    public $newsletter_type = 'test';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        if ($models->count() > 1)
        {
            return Action::danger('Please run this on only one user resource.');
        }

        $models->first()->sendNewsletter($this->newsletter_type);
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}
