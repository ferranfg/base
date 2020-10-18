<?php

namespace Ferranfg\Base\Models;

use Ferranfg\Base\Base;
use Spatie\Tags\HasTags;
use Spatie\Tags\HasSlug;
use Ferranfg\Base\Traits\HasMetadata;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasTags, HasTranslations, HasSlug, HasMetadata;

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
        'draft' => 'Draft',
        'published' => 'Published',
    ];

    /**
     * The available types values.
     *
     * @var array
     */
    public static $types = [
        'entry' => 'Entry',
        'page' => 'Page',
        'newsletter' => 'Newsletter',
    ];

    /**
     * Get the author of the post.
     */
    public function author()
    {
        return $this->belongsTo(Base::$userModel, 'author_id');
    }

    /**
     * Get the comments of the post.
     */
    public function comments()
    {
        return $this->morphMany(Base::$commentModel, 'commentable');
    }

    /**
     * Get the post canonical URL.
     */
    public function getCanonicalUrlAttribute()
    {
        return url("blog/{$this->slug}");
    }

    /**
     * Get the total words of this post.
     */
    public function getWordCountAttribute()
    {
        return str_word_count(strip_tags($this->content));
    }

    /**
     * Get the total reading time needed for this post.
     */
    public function getReadingTimeAttribute()
    {
        return floor(bcdiv($this->word_count, 200));
    }

    /**
     * Get the created at time in a human format.
     */
    public function getCreatedAtDiffAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
