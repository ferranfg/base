<?php

namespace Ferranfg\Base\Repositories;

use Ferranfg\Base\Base;

class ProductRepository
{
    public function withType($type)
    {
        return Base::product()->with('metadata')->withType($type)->paginate();
    }

    public function findBySlug(string $slug, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return Base::product()->where("slug->{$locale}", $slug)->firstOrFail();
    }
}