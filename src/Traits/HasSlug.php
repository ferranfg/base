<?php

namespace Ferranfg\Base\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasSlug
{
    public static function bootHasSlug()
    {
        static::saving(function (Model $model)
        {
            collect($model->getTranslatedLocales('name'))->each(function (string $locale) use ($model)
            {
                $model->setTranslation('slug', $locale, $model->generateSlug($locale));
            });
        });
    }

    protected function generateSlug(string $locale): string
    {
        $slug = $this->getTranslation('slug', $locale);

        if ( ! empty($slug)) return $slug;

        $slugger = config('tags.slugger');

        $slugger = $slugger ?: '\Illuminate\Support\Str::slug';

        return call_user_func($slugger, $this->getTranslation('name', $locale));
    }
}
