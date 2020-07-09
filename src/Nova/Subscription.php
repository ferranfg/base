<?php

namespace Ferranfg\Base\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Stripe\Subscription as StripeSubscription;

class Subscription extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Laravel\Spark\Subscription';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'stripe_id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('User'),

            Text::make('Stripe_Id'),

            Text::make('ProviderPlan'),

            Badge::make('Stripe_Status')->map([
                StripeSubscription::STATUS_ACTIVE => 'success',
                StripeSubscription::STATUS_CANCELED => 'info',
                StripeSubscription::STATUS_PAST_DUE => 'danger',
                StripeSubscription::STATUS_TRIALING => 'info',
                StripeSubscription::STATUS_UNPAID => 'warning',
                StripeSubscription::STATUS_INCOMPLETE => 'danger',
                StripeSubscription::STATUS_INCOMPLETE_EXPIRED => 'danger',
            ]),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
