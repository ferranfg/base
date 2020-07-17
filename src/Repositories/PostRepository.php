<?php

namespace Ferranfg\Base\Repositories;

use Ferranfg\Base\Base;

class PostRepository
{
    public function whereType($type)
    {
        return Base::post()->with('metadata')->whereType($type);
    }

    public function findBySlug(string $slug, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return Base::post()->where("slug->{$locale}", $slug)->firstOrFail();
    }
}