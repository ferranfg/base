<?php

namespace Ferranfg\Base\Models;

use Ferranfg\Base\Base;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Ferranfg\Base\Traits\HasTags;
use Ferranfg\Base\Traits\HasSlug;
use Ferranfg\Base\Traits\HasMetadata;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Venturecraft\Revisionable\RevisionableTrait;

class Post extends Model implements Feedable
{
    use HasTags, HasTranslations, HasSlug, HasMetadata, RevisionableTrait;

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
        return $this->morphMany(Base::$commentModel, 'commentable')->where('type', 'comment');
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
     * Get the total chars on the excerpt (Used for meta-description).
     */
    public function getExcerptLengthAttribute()
    {
        return strlen($this->excerpt);
    }

    /**
     * Get the created at time in a human format.
     */
    public function getCreatedAtDiffAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Used to display the post content as a feed item.
     */
    public function toFeedItem(): FeedItem
    {
        return FeedItem::create([
            'id' => "?guid={$this->id}",
            'title' => $this->name,
            'summary' => $this->excerpt,
            'content' => $this->content,
            'updated' => $this->updated_at,
            'link' => $this->canonical_url,
            'author' => $this->author->name
        ]);
    }

    /**
     * Returns all the items that will be used to generate the feed.
     */
    public static function getAllFeedItems()
    {
        return self::all();
    }

    /**
     * Increases the _visits metadata with 1 visit
     */
    public function trackVisit()
    {
        $visits = $this->getMetadata('_visits');

        if (is_null($visits)) $visits = 0;

        $visits++;

        $this->setMetadata('_visits', $visits);

        return $this;
    }

}
