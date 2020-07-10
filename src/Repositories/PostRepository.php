<?php

namespace Ferranfg\Base\Repositories;

use Ferranfg\Base\Base;

class PostRepository
{
    public function paginate()
    {
        return Base::post()->with('tags')->paginate();
    }

    public function findBySlug(string $slug, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return Base::post()->where("slug->{$locale}", $slug)->firstOrFail();
    }
}