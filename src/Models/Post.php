<?php

namespace Ferranfg\Base\Models;

use Spatie\Tags\HasTags;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasTags;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'slug', 'content'];

    /**
     * The available status values.
     *
     * @var array
     */
    public static $status = [
        'draft' => 'Draft',
        'published' => 'Published'
    ];

    /**
     * The available types values.
     *
     * @var array
     */
    public static $types = [
        'post' => 'Post',
        'page' => 'Page'
    ];

    /**
     * Get the author of the post.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
