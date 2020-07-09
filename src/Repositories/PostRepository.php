<?php

namespace Ferranfg\Base\Repositories;

use Ferranfg\Base\Base;

class PostRepository
{
    public function paginate()
    {
        return Base::post()->paginate();
    }
}