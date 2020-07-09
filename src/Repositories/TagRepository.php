<?php

namespace Ferranfg\Base\Repositories;

use Ferranfg\Base\Base;

class TagRepository
{
    public function whereType($type)
    {
        return Base::tag()->whereType($type)->paginate(10);
    }
}