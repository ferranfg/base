<?php

namespace Ferranfg\Base\Repositories;

use Ferranfg\Base\Base;

class TagRepository
{
    public function whereType($type)
    {
        return Base::tag()->with('metadata')->whereType($type)->paginate();
    }

    public function findBySlug(string $slug, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return Base::tag()->where("slug->{$locale}", $slug)->firstOrFail();
    }
}