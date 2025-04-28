<?php

namespace Ferranfg\Base\Nova;

use Ferranfg\Base\Base;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\MorphToMany;
use Spatie\NovaTranslatable\Translatable;
use Ferranfg\Base\Nova\Filters\ProductType;
use Ferranfg\Base\Nova\Filters\ProductStatus;

class Product extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Ferranfg\Base\Models\Product';

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
        'id', 'name', 'stripe_id'
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

            Translatable::make([
                Text::make('Name')
                    ->sortable()
                    ->rules('required'),

                Markdown::make('Description'),
            ]),

            Text::make('Stripe Id')
                ->onlyOnDetail(),

            Number::make('Amount')
                ->step(0.01)
                ->sortable()
                ->rules('required'),

            Number::make('Sale Amount')
                ->step(0.01)
                ->sortable(),

            Select::make('Currency')
                ->options(Base::product()::$currencies)
                ->rules('required')
                ->onlyOnForms()
                ->displayUsingLabels(),

            Image::make('Photo Url')
                ->onlyOnForms(),

            Text::make('Video Url')
                ->onlyOnForms(),

            Text::make('Attached Url')
                ->onlyOnForms(),

            BelongsTo::make('Owner', 'owner', User::class)
                ->searchable()
                ->rules('required')
                ->onlyOnForms(),

            Select::make('Type')
                ->rules('required')
                ->options(Base::product()::$types)
                ->displayUsingLabels(),

            Select::make('Status')
                ->rules('required')
                ->options(Base::product()::$status)
                ->displayUsingLabels(),

            Number::make('Comments', function ()
            {
                return $this->comments()->count();
            }),

            MorphToMany::make('Tags'),

            MorphMany::make('Metadata'),
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
        return [
            new ProductType,
            new ProductStatus,
        ];
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
