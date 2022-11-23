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
        $user = auth()->user();

        return [
            'state' => [
                'user' => $user,
                'teams' => auth()->check() ? $user->teams : [],
                'currentTeam' => auth()->check() ? $user->currentTeam : null,
            ],
            'locale' => config('app.locale')
        ];
    }

}
