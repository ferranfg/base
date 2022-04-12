<?php

namespace Ferranfg\Base\Models;

use Carbon\Carbon;
use Ferranfg\Base\Base;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Ferranfg\Base\Traits\HasTags;
use Ferranfg\Base\Traits\HasSlug;
use Spatie\Activitylog\LogOptions;
use Ferranfg\Base\Traits\HasVisits;
use Ferranfg\Base\Traits\HasMetadata;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;
use Ferranfg\Base\Notifications\PostNewsletter;

class Post extends Model implements Feedable
{
    use HasTags, HasTranslations, HasSlug, HasMetadata, HasVisits, LogsActivity;

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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['canonical_url', 'horizontal_photo_url'];

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
        'scheduled' => 'Scheduled',
        'published' => 'Published',
        'private' => 'Private'
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
     * Configure the model activity logger.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
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
     * Get the updated at time in a human format.
     */
    public function getUpdatedAtDiffAttribute()
    {
        return $this->updated_at->diffForHumans();
    }

    /**
     * Get the Horizontal Photo URL for the product.
     */
    public function getHorizontalPhotoUrlAttribute()
    {
        return img_url($this->attached_url ?: $this->photo_url, [
            ['width' => 1200, 'height' => 630]
        ]);
    }

    /**
     * Used to display the post content as a feed item.
     */
    public function toFeedItem(): FeedItem
    {
        return FeedItem::create([
            'id' => "/blog?guid={$this->id}",
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
        $feed = self::where('status', 'published')->orderBy('updated_at', 'desc')->paginate(null, ['*'], 'paged');

        return collect($feed->items());
    }

    /**
     * Sends the post as a newsletter to all users.
     */
    public function sendNewsletter($test = false)
    {
        if ($test)
        {
            $users = Base::user()->whereIn('email', Base::$developers)->get();
        }
        else
        {
            $users = Base::user()->whereNull('unsubscribed_at')->get();

            $this->setMetadata('newslettered_at', Carbon::now());

            activity()->performedOn($this)->log('sent_newsletter');
        }

        foreach ($users as $user)
        {
            $user->notify(new PostNewsletter($this));
        }

        return $this;
    }

    /**
     * Publishes a post.
     */
    public function publish()
    {
        $this->setMetadata('published_at', Carbon::now());

        $this->status = 'published';
        $this->save();

        activity()->performedOn($this)->log('published');

        return $this;
    }

}
