<?php

namespace Ferranfg\Base;

use Laravel\Spark\Spark;
use Ferranfg\Base\Configuration\ManagesModelOptions;

class Base extends Spark
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
