<?php

namespace Ferranfg\Base\Models;

use Spatie\Tags\Tag as SpatieTag;
use Ferranfg\Base\Traits\HasSlug;
use Ferranfg\Base\Traits\HasMetadata;
use Venturecraft\Revisionable\RevisionableTrait;

class Tag extends SpatieTag
{
    use HasMetadata, HasSlug, RevisionableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'type', 'description', 'icon', 'photo_url'];

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
