<?php

namespace Ferranfg\Base\Models;

use Ferranfg\Base\Base;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['*'];

    /**
     * The available types values.
     *
     * @var array
     */
    public static $types = [
        'reply' => 'Reply',
        'review' => 'Review',
    ];

    /**
     * Get the author of the commnent.
     */
    public function author()
    {
        return $this->belongsTo(Base::$userModel, 'author_id');
    }

}
