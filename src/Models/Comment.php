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
    protected $fillable = [
        'author_name',
        'author_email',
        'author_url',
        'author_IP',
        'content',
        'rating',
        'type'
    ];

    /**
     * The available types values.
     *
     * @var array
     */
    public static $types = [
        'spam' => 'Spam',
        'comment' => 'Comment',
        'review' => 'Review',
    ];

    /**
     * Get the commentable object.
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Get the author of the commnent.
     */
    public function author()
    {
        return $this->belongsTo(Base::$userModel, 'author_id');
    }

    /**
     * Get the created at time in a human format.
     */
    public function getCreatedAtDiffAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the user gravatar image attribute.
     */
    public function getAuthorPhotoUrlAttribute()
    {
        return 'https://secure.gravatar.com/avatar/' . md5($this->author_email);
    }
}
