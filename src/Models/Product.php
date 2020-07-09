<?php

namespace Ferranfg\Base\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;

class Product extends Model
{
    use HasTags;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'posts';
}
