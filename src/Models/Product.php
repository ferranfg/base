<?php

namespace Ferranfg\Base\Models;

use Spatie\Tags\HasTags;
use Illuminate\Database\Eloquent\Model;

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