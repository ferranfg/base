<?php

namespace Ferranfg\Base\Repositories;

use Ferranfg\Base\Base;

class ProductRepository
{
    public function paginate()
    {
        return Base::product()->paginate();
    }
}