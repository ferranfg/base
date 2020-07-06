<?php

namespace Ferranfg\Base;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ferranfg\Base\Base
 */
class BaseFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'base';
    }
}
