<?php

namespace Ferranfg\Base\Repositories;

use Ferranfg\Base\Base;

class PostRepository
{
    public function withType($type)
    {
        return Base::post()->with('metadata')->withType($type)->paginate();
    }

    public function findBySlug(string $slug, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return Base::post()->where("slug->{$locale}", $slug)->firstOrFail();
    }
}