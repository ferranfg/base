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
        $script_variables = array_merge(Spark::scriptVariables(), [
            'locale' => config('app.locale')
        ]);

        if (array_key_exists('csrfToken', $script_variables) and config('base.disable_csrf_token'))
        {
            unset($script_variables['csrfToken']);
        }

        return $script_variables;
    }

}
