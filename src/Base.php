<?php

namespace Ferranfg\Base;

class Base
{
    /**
     * The post model class name.
     *
     * @var string
     */
    public static $postModel = 'Ferranfg\Base\Models\Post';

    /**
     * Get the post model class name.
     *
     * @return string
     */
    public static function postModel()
    {
        return static::$postModel;
    }

}
