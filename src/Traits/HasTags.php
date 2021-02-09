<?php

namespace Ferranfg\Base\Traits;

use Ferranfg\Base\Base;
use Spatie\Tags\HasTags as SpatieHasTags;

trait HasTags {

    use SpatieHasTags {
        SpatieHasTags::getTagClassName as getTagClassName;
    }

    public static function getTagClassName(): string
    {
        return Base::$tagModel;
    }

}