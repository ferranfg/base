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

    /**
     * Get the default JavaScript variables for Spark.
     *
     * @return array
     */
    public static function scriptVariables()
    {
        return [
            'locale' => config('app.locale')
        ];
    }

}
