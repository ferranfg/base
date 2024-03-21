<?php

namespace Ferranfg\Base\Models;

use Exception;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Ferranfg\Base\Base;
use Ferranfg\Base\Clients\Facebook;
use Ferranfg\Base\Clients\Unsplash;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Ferranfg\Base\Traits\HasTags;
use Ferranfg\Base\Traits\HasSlug;
use Spatie\Activitylog\LogOptions;
use Ferranfg\Base\Traits\HasVisits;
use Ferranfg\Base\Traits\HasMetadata;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;
use Ferranfg\Base\Notifications\PostNewsletter;
use Ferranfg\Base\Traits\IsShareable;

class Post extends Model implements Feedable
{
    use HasTags, HasTranslations, HasSlug, HasMetadata, HasVisits, IsShareable, LogsActivity;

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
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    public $casts = [
        'scheduled_at' => 'datetime',
        'showcase_product_ids' => 'json',
    ];

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
     * Configure the model activity logger.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * Get the internal link for the post.
     */
    public function getInternalLinkAttribute()
    {
        if ($this->type == 'guide')
        {
            return $this->status == 'draft' ? "/guides/{$this->id}" : "/guides/{$this->slug}";
        }

        return "/blog/{$this->slug}";
    }

    /**
     * Get the post canonical URL.
     */
    public function getCanonicalUrlAttribute()
    {
        return url($this->internal_link);
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
        return mb_strlen($this->excerpt);
    }

    /**
     * Get the introduction text until the first line break.
     */
    public function getIntroductionAttribute()
    {
        return substr($this->content, 0, strpos($this->content, "\n"));
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
            $collection = Unsplash::randomFromCollections()->pluck('urls.regular');

            return $collection->only($this->id % $collection->count())->first();
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
     * Generate the html2img image for the post.
     */
    public function getSquareBannerUrlAttribute()
    {
        try
        {
            $endpoint = 'https://us-central1-ferran-figueredo.cloudfunctions.net/crawler';
            $filename = "banner-{$this->id}.png";

            // Check if exists and has less than 5 minutes
            if (Storage::exists($filename) and Storage::lastModified($filename) < (time() - 300))
            {
                return Storage::url($filename);
            }

            $params = [
                'filename' => $filename,
                'template' => 'square',
                'background' => $this->photo_url,
                'title' => $this->name,
                'description' => $this->excerpt,
            ];

            (new Client)->get("{$endpoint}?" . http_build_query([
                'url' => route('html2img.preview', $params),
                'wait' => 2000,
            ]));

            return Storage::url($filename);
        }
        catch (Exception $e)
        {
            return img_url($this->attached_url ?: $this->photo_url, [
                ['width' => 1080, 'height' => 1080]
            ]);
        }
    }

    /**
     * Check if the post is a page.
     */
    public function getIsPageAttribute()
    {
        return $this->type == 'page';
    }

    /**
     * Check if comments are disabled for this post.
     */
    public function getCommentsDisabledAttribute()
    {
        return $this->is_page or $this instanceof Note;
    }

    /**
     * Check if keywords are disabled for this post.
     */
    public function getKeywordsDisabledAttribute()
    {
        return $this->is_page or ! config('base.blog_keywords');
    }

    /**
     * Get the main keyword for the post.
     */
    public function getMainKeywordAttribute($value)
    {
        return $value ?: collect(explode(', ', (string) $this->keywords))->first();
    }

    /**
     * Used to create embeddings and search for them
     */
    public function toEmbedding()
    {
        return json_encode([
            'id' => $this->id,
            'name' => clean_accents($this->name),
            'internal_link' => $this->internal_link,
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
            'authorName' => $this->author->name,
            'image' => img_url($this->photo_url, [
                ['width' => 1920, 'height' => 1080]
            ]),
        ]);
    }

    /**
     * Returns all the items that will be used to generate the feed.
     */
    public static function getAllFeedItems($include_private = false)
    {
        $status = $include_private ? ['published', 'private'] : ['published'];

        $feed = self::whereIn('status', $status)
            ->whereIn('type', ['entry', 'dynamic', 'newsletter'])
            ->orderBy('updated_at', 'desc')
            ->paginate(null, ['*'], 'paged');

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
            case strtotime($type):
                $users->where('created_at', '>=', Carbon::createFromTimestamp($type));
            break;
        endswitch;

        $this->setMetadata("sent_{$type}_newsletter_at", Carbon::now());

        activity()->performedOn($this)->log("sent_{$type}_newsletter");

        $max_calls_per_second = 5;
        $microseconds_per_call = round(1000000 / $max_calls_per_second);

        $start_time = microtime(true);

        foreach ($users->get() as $user)
        {
            $user->notify(new PostNewsletter($this));

            $time_taken = microtime(true) - $start_time;
            $sleep_time = $microseconds_per_call - ($time_taken * 1000000);

            if ($sleep_time > 0) usleep($sleep_time);

            $start_time = microtime(true);
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

    /**
     * Publishes a post on Meta platforms.
     *
     * @return void
     */
    public function publishMeta($publish_facebook = true, $publish_instagram = true)
    {
        if ($this->author and $this->author->facebook_token)
        {
            if ($publish_facebook  and $this->author->facebook_id)  $this->publishFacebook();
            if ($publish_instagram and $this->author->instagram_id) $this->publishInstagram();
        }
    }

    /**
     * Publishes a post on Facebook if facebook_id is set.
     *
     * @return void
     */
    public function publishFacebook()
    {
        $message = $this->excerpt . "\n\n👉 " . $this->canonical_url;

        if ($this->keywords) $message .= "\n\n" . $this->getKeywordsAsHashtags();

        $res = Facebook::uploadPost($this->author->facebook_id, $this->author->facebook_token, [
            'url' => $this->square_banner_url,
            'link' => $this->canonical_url,
            'message' => $message,
            'published' => true,
        ]);

        logger(json_encode($res));
    }

    /**
     * Publishes a post on Instagram if instagram_id is set.
     *
     * @return void
     */
    public function publishInstagram()
    {
        $caption = $this->excerpt . "\n\n👉 Link in bio ➡️";

        if ($this->keywords) $caption .= "\n\n" . $this->getKeywordsAsHashtags();

        $res = Facebook::uploadMedia($this->author->instagram_id, $this->author->facebook_token, [
            'image_url' => $this->square_banner_url,
            'caption' => $caption,
        ]);

        logger(json_encode($res));
    }

    /**
     * Convert coma separated keywords to hashtags
     *
     * @return string
     */
    public function getKeywordsAsHashtags()
    {
        if ( ! $this->keywords) return (string) null;

        $hashtags = clean_accents($this->keywords);
        $hashtags = preg_replace("/[^a-zA-Z0-9,]/", '', $hashtags);
        $hashtags = str_replace(',', ' #', $hashtags);

        return "#" . $hashtags;
    }

    /**
     * Returns the post keywords as array
     *
     * @return array
     */
    public function getKeywords()
    {
        if ($this->keywords_disabled) return collect();

        $keywords = [];

        foreach (explode(',', $this->keywords) as $keyword)
        {
            $keyword = strtolower(
                trim($keyword)
            );

            if ($keyword) $keywords[] = (object) [
                'name' => $keyword,
                'canonical_url' => url('/tag/' . rawurlencode($keyword)),
                'updated_at' => $this->updated_at,
            ];
        }

        return collect($keywords);
    }
}
