<?php

namespace Ferranfg\Base\Nova;

use Ferranfg\Base\Base;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\MorphToMany;
use Ferranfg\Base\Nova\Filters\PostType;
use Spatie\NovaTranslatable\Translatable;
use Ferranfg\Base\Nova\Filters\PostStatus;

class Post extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Ferranfg\Base\Models\Post';

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
        'id', 'name',
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

                Textarea::make('Excerpt')
                    ->rules('required'),

                Markdown::make('Content'),
            ]),

            Text::make('Keywords')
                ->rules('required'),

            Boolean::make('Featured')
                ->hideFromIndex(),

            BelongsTo::make('Author', 'author', User::class)
                ->sortable()
                ->rules('required'),

            Image::make('Photo Url')
                ->onlyOnForms(),

            Text::make('Video Url')
                ->onlyOnForms(),

            Select::make('Type')
                ->rules('required')
                ->options(Base::post()::$types)
                ->displayUsingLabels(),

            Select::make('Status')
                ->rules('required')
                ->options(Base::post()::$status)
                ->displayUsingLabels(),

            Number::make('Comments', function ()
            {
                return $this->comments()->count();
            }),

            Number::make('Excerpt Length')
                ->onlyOnIndex(),

            Number::make('Word Count')
                ->onlyOnIndex(),

            MorphToMany::make('Tags'),
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
            new PostType,
            new PostStatus,
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
        return [
            new Actions\PostPublish,
            new Actions\PostTestNewsletter,
            new Actions\PostAllNewsletter,
            new Actions\PostCustomersNewsletter,
            new Actions\PostNonCustomersNewsletter,
        ];
    }
}
