<?php

namespace Ferranfg\Base\Models;

use Illuminate\Database\Eloquent\Model;

class Metadata extends Model
{
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
     * Get the parent of the field.
     */
    public function parent()
    {
        return $this->morphTo();
    }
}