<?php

namespace Ferranfg\Base\Models;

use Ferranfg\Base\Base;
use Spatie\Tags\Tag as SpatieTag;
use Ferranfg\Base\Traits\HasSlug;
use Ferranfg\Base\Traits\HasMetadata;

class Tag extends SpatieTag
{
    use HasMetadata, HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'type', 'description', 'icon', 'photo_url'];

    /**
     * The available types values.
     *
     * @var array
     */
    public static $types = [
        'tag' => 'Tag',
        'category' => 'Category',
    ];

    /**
     * Get all of the products that are assigned this tag.
     */
    public function products()
    {
        return $this->morphedByMany(Base::product(), 'taggable')->orderBy('id', 'desc');
    }

    /**
     * Get the product canonical URL.
     */
    public function getCanonicalUrlAttribute()
    {
        return url("{$this->type}/{$this->slug}");
    }

}
