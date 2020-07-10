<?php

namespace Ferranfg\Base\Models;

use Ferranfg\Base\Base;
use Spatie\Tags\Tag as SpatieTag;
use Ferranfg\Base\Traits\HasMetadata;

class Tag extends SpatieTag
{
    use HasMetadata;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'type', 'description', 'photo_url'];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = ['name', 'slug', 'description'];

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
