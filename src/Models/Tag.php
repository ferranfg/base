<?php

namespace Ferranfg\Base\Models;

use Spatie\Tags\Tag as SpatieTag;

class Tag extends SpatieTag
{
    /**
     * The available types values.
     *
     * @var array
     */
    public static $types = [
        'tag' => 'Tag',
        'category' => 'Category',
    ];
}
