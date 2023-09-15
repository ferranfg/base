<?php

namespace Ferranfg\Base\Models;

use Ferranfg\Base\Base;
use Ferranfg\Base\Traits\HasSlug;
use Ferranfg\Base\Traits\HasMetadata;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Tag extends Model implements Sortable
{
    use HasMetadata, HasSlug, SortableTrait;

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
        'guide-tag' => 'Guide Tag',
    ];

    /**
     * Get all of the products that are assigned this tag.
     */
    public function products()
    {
        return $this->morphedByMany(Base::product(), 'taggable')->orderBy('id', 'desc');
    }

    /**
     * Get all of the posts that are assigned this tag.
     */
    public function posts()
    {
        return $this->morphedByMany(Base::post(), 'taggable')->orderBy('id', 'asc');
    }

    /**
     * Get the product canonical URL.
     */
    public function getCanonicalUrlAttribute()
    {
        return url("{$this->type}/{$this->slug}");
    }

}
