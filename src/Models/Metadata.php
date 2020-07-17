<?php

namespace Ferranfg\Base\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Metadata extends Model
{
    use HasTranslations;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'metadata';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'value'];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = ['value'];

    /**
     * Get the parent of the field.
     */
    public function parent()
    {
        return $this->morphTo();
    }
}