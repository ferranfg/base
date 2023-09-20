<?php

namespace Ferranfg\Base\Models;

use Carbon\Carbon;
use Ferranfg\Base\Base;
use Ferranfg\Base\Clients\Unsplash;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Ferranfg\Base\Traits\HasTags;
use Ferranfg\Base\Traits\HasSlug;
use Ferranfg\Base\Traits\HasVisits;
use Ferranfg\Base\Traits\HasMetadata;
use Illuminate\Database\Eloquent\Model;
use Ferranfg\Base\Notifications\PostNewsletter;

class Post extends Model implements Feedable
{
    use HasTags, HasSlug, HasMetadata, HasVisits;

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
     * The available status values.
     *
     * @var array
     */
    public static $status = [
        'draft' => 'Draft',
        'pending' => 'Pending',
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
        'guide' => 'Guide',
        'dynamic' => 'Dynamic',
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
        if ($this->type == 'guide')
        {
            return $this->status == 'draft' ? url("guides/{$this->id}") : url("guides/{$this->slug}");
        }

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
     * Get the photo URL for the product.
     */
    public function getPhotoUrlAttribute($value)
    {
        if ($value) return $value;

        if ($this->exists and is_null($value) and config('services.unsplash.collections'))
        {
            return Unsplash::randomFromCollections()->pluck('urls.regular')->random();
        }

        return null;
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
            'title' => (string) $this->name,
            'summary' => (string) $this->excerpt,
            'content' => (string) $this->content,
            'updated' => $this->updated_at,
            'link' => $this->canonical_url,
            'authorName' => $this->author->name
        ]);
    }

    /**
     * Returns all the items that will be used to generate the feed.
     */
    public static function getAllFeedItems($include_private = false)
    {
        $status = $include_private ? ['published', 'private'] : ['published'];
        $feed = self::whereIn('status', $status)->orderBy('updated_at', 'desc')->paginate(null, ['*'], 'paged');

        return collect($feed->items());
    }

    /**
     * Sends the post as a newsletter to all users.
     */
    public function sendNewsletter($type = 'test')
    {
        ignore_user_abort(true) and set_time_limit(0);

        $users = Base::user()->whereNull('unsubscribed_at')->where('email', '!=', '0x%');

        switch ($type):
            case 'test':
                $users->whereIn('email', Base::$developers);
            break;
            case 'customers':
                $users->whereNotNull('stripe_id');
            break;
            case 'non-customers':
                $users ->whereNull('stripe_id');
            break;
        endswitch;

        $this->setMetadata("sent_{$type}_newsletter_at", Carbon::now());

        foreach ($users->get() as $user)
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

        return $this;
    }

}
