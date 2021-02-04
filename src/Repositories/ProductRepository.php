<?php

namespace Ferranfg\Base\Repositories;

use Ferranfg\Base\Base;

class ProductRepository
{
    public function whereType($type)
    {
        return Base::product()->with('metadata')->whereType($type);
    }

    public function whereIn($column, $values)
    {
        return Base::product()->whereIn($column, $values);
    }

    public function findBySlug(string $slug, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return Base::product()->where("slug->{$locale}", $slug)->firstOrFail();
    }
}