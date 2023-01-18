<?php

namespace Ferranfg\Base\Repositories;

use Ferranfg\Base\Base;

class TagRepository
{
    public function whereType($type)
    {
        return Base::tag()->with('metadata')->whereType($type);
    }

    public function findById($id)
    {
        return Base::tag()->findOrFail($id);
    }

    public function findBySlug(string $slug)
    {
        return Base::tag()->where("slug", $slug)->firstOrFail();
    }
}