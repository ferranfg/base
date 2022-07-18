<?php

namespace Ferranfg\Base;

use Laravel\Spark\Spark;
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
        return array_merge(Spark::scriptVariables(), [
            'csrfToken' => base_csrf_token(),
            'locale' => config('app.locale')
        ]);
    }

}
