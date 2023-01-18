<?php

namespace Ferranfg\Base;

use Ferranfg\Base\Configuration\ManagesModelOptions;

class Base
{
    use ManagesModelOptions;

    /**
     * All of the application developer e-mail addresses.
     *
     * @var array
     */
    public static $developers = [
        //
    ];

    /**
     * The user model class name.
     *
     * @var string
     */
    public static $userModel = 'Ferranfg\Base\Models\User';

    /**
     * The team model class name.
     *
     * @var string
     */
    public static $teamModel = 'Ferranfg\Base\Models\Team';

    /**
     * Set the user model class name.
     *
     * @param  string  $userModel
     * @return void
     */
    public static function useUserModel($userModel)
    {
        static::$userModel = $userModel;
    }

    /**
     * Get a new user model instance.
     *
     * @return \Ferranfg\Base\Models\User
     */
    public static function user()
    {
        return new static::$userModel;
    }
}
