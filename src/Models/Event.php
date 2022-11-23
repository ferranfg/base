<?php

namespace Ferranfg\Base\Models;

use Ferranfg\Base\Base;
use Ferranfg\Base\Traits\HasTags;
use Ferranfg\Base\Traits\HasSlug;
use Ferranfg\Base\Traits\HasUuid;
use Ferranfg\Base\Traits\HasMetadata;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasTags, HasSlug, HasMetadata, HasUuid;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'events';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'description'];

    /**
     * Get the owner of the product.
     */
    public function owner()
    {
        return $this->belongsTo(Base::$userModel, 'owner_id');
    }

    /**
     * Get the comments of the product.
     */
    public function comments()
    {
        return $this->morphMany(Base::$commentModel, 'commentable');
    }
}