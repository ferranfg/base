<?php

namespace Ferranfg\Base;

use Ferranfg\Base\Configuration\ManagesModelOptions;

class Base
{
    use ManagesModelOptions;

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
}
