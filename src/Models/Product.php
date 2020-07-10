<?php

namespace Ferranfg\Base\Models;

use Spatie\Tags\HasTags;
use Spatie\Tags\HasSlug;
use Ferranfg\Base\Traits\HasMetadata;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasTags, HasTranslations, HasSlug, HasMetadata;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'excerpt', 'content'];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = ['name', 'slug', 'excerpt', 'content'];

    /**
     * The available status values.
     *
     * @var array
     */
    public static $status = [
        'available' => 'Available'
    ];

    /**
     * The available types values.
     *
     * @var array
     */
    public static $types = [
        'download' => 'Download'
    ];

    /**
     * The available product currencies.
     *
     * @var array
     */
    public static $currencies = [
        'eur' => 'Euro (€)'
    ];

    /**
     * Get the owner of the product.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
