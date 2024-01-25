<?php

namespace Ferranfg\Base\Models;

use Schema;
use Ferranfg\Base\Base;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;
use Stripe\Price as StripePrice;
use Ferranfg\Base\Traits\HasSlug;
use Ferranfg\Base\Traits\HasTags;
use Ferranfg\Base\Traits\HasVisits;
use Stripe\TaxRate as StripeTaxRate;
use Stripe\Product as StripeProduct;
use Ferranfg\Base\Traits\HasMetadata;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasTags, HasSlug, HasMetadata, HasVisits;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['owner_id', 'name', 'slug', 'description', 'photo_url', 'video_url', 'attached_url', 'currency', 'amount', 'sale_amount', 'type', 'status'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['canonical_url', 'horizontal_photo_url', 'square_photo_url'];

    /**
     * The available status values.
     *
     * @var array
     */
    public static $status = [
        'in_stock' => 'In Stock',
        'out_of_stock' => 'Out of stock',
        'available' => 'Available',
        'private' => 'Private',
    ];

    /**
     * The available types values.
     *
     * @var array
     */
    public static $types = [
        'simple' => 'Simple',
        'affiliate' => 'Affiliate',
    ];

    /**
     * The available product currencies.
     *
     * @var array
     */
    public static $currencies = [
        'eur' => 'Euro (€)'
    ];

    /**
     * Get the owner of the product.
     */
    public function owner()
    {
        return $this->belongsTo(Base::$userModel, 'owner_id');
    }

    /**
     * Get the comments of the product.
     */
    public function comments()
    {
        return $this->morphMany(Base::$commentModel, 'commentable');
    }

    /**
     * Get the categories of the product.
     */
    public function categories()
    {
        return $this->tags()->where('type', 'category');
    }

    /**
     * Añade el método orderByDiscount().
     */
    public function scopeOrderByDiscount($query)
    {
        return $query->selectRaw('products.*, (amount - sale_amount) as discount')->orderBy('discount', 'desc');
    }

    /**
     * Get the product excerpt.
     */
    public function getExcerptAttribute()
    {
        return Str::limit($this->description, 165);
    }

    /**
     * Get the product canonical URL.
     */
    public function getCanonicalUrlAttribute()
    {
        return url("shop/{$this->slug}");
    }

    /**
     * Get the product attached URL.
     */
    public function getAttachedUrlAttribute($value)
    {
        if (str_contains($value, 'amazon.es') and ! str_contains($value, 'tag='))
        {
            $value .= (str_contains($value, '?') ? '&' : '?') . 'tag=ferranfg-21';
        }

        return $value;
    }

    /**
     * Get the product checkout URL.
     */
    public function getCheckoutUrlAttribute()
    {
        return url("checkout/{$this->slug}");
    }

    /**
     * Get the Horizontal Photo URL for the product.
     */
    public function getHorizontalPhotoUrlAttribute()
    {
        return img_url($this->photo_url, [
            ['width' => 1200, 'height' => 630]
        ]);
    }

    /**
     * Get the Instagram Photo URL for the product.
     */
    public function getSquarePhotoUrlAttribute()
    {
        return img_url($this->photo_url, [
            ['width' => 1080, 'height' => 1080]
        ]);
    }

    /**
     * Get the Instagram Photo URL for the product.
     */
    public function getGoogleCategoryAttribute()
    {
        return $this->getMetadata('google_category');
    }

    /**
     * Check if comments are disabled for this post.
     */
    public function getCommentsDisabledAttribute()
    {
        return ! Schema::hasTable(Base::comment()->getTable());
    }

    /**
     * Get the product discount.
     */
    public function getDiscountAttribute()
    {
        return $this->amount - $this->sale_amount;
    }

    /**
     * Set the amount attribute.
     */
    public function setAmountAttribute($value)
    {
        if ( ! $value) return null;

        $amount = (float) self::floatval($value);

        return $this->attributes['amount'] = bcmul($amount, 100);
    }

    /**
     * Set the sale amount attribute.
     */
    public function setSaleAmountAttribute($value)
    {
        if ( ! $value) return null;

        $amount = (float) self::floatval($value);

        return $this->attributes['sale_amount'] = bcmul($amount, 100);
    }

    /**
     * Get the amount attribute.
     */
    public function getAmountAttribute($value)
    {
        return is_numeric($value) ? bcdiv($value, 100, 2) : null;
    }

    /**
     * Get the sale amount attribute.
     */
    public function getSaleAmountAttribute($value)
    {
        return is_numeric($value) ? bcdiv($value, 100, 2) : null;
    }

    /**
     * Get the avg rating from the attached comments
     *
     * @var string
     */
    public function avgRating($type = 'review')
    {
        if ($this->comments_disabled) return 5;

        return $this->comments->where('type', $type)->avg('rating');
    }

    /**
     * Renders the HTML starts for the avg rating
     *
     * @var string
     */
    public function renderAvgRating($type = 'review')
    {
        $rating = $this->avgRating($type);
        $render = [];

        // NORMAL
        array_push($render, '<ul class="list-unstyled text-warning mb-0 d-none d-sm-inline-block">');

        for ($i = 1; $i <= 5; $i++):
            if ($i <= $rating):
                array_push($render, '<li class="list-inline-item"><i class="fa fa-star"></i></li>');
            else:
                array_push($render, '<li class="list-inline-item"><i class="fa fa-star-o"></i></li>');
            endif;
        endfor;

        array_push($render, '</ul>');

        // RESPONSIVE
        array_push($render, '<ul class="list-unstyled text-warning mb-0 d-block d-sm-none">');

        if ($rating)
        {
            array_push($render, '<li class="list-inline-item">' . $rating . ' <i class="fa fa-star"></i></li>');
        }
        else
        {
            array_push($render, '<li class="list-inline-item"><i class="fa fa-star-o"></i></li>');
        }

        array_push($render, '</ul>');

        return implode('', $render);
    }

    /**
     * Get the product price formated with currency.
     */
    public function formatAmount()
    {
        if (is_null($this->amount)) return null;

        return Cashier::formatAmount($this->amount, $this->currency);
    }

    /**
     * Get the product price formated with currency.
     */
    public function formatSaleAmount()
    {
        if (is_null($this->sale_amount)) return null;

        return Cashier::formatAmount($this->sale_amount, $this->currency);
    }

    /**
     * Get the product discount formated with currency.
     */
    public function formatDiscount()
    {
        if (is_null($this->discount)) return null;

        return Cashier::formatAmount($this->discount, $this->currency);
    }

    /**
     * Get the product id on Stripe.
     */
    public function stripeProductId()
    {
        $params = [
            'name' => $this->name,
            'description' => empty($this->description) ? $this->name : $this->description,
            'images' => [
                img_url($this->photo_url)
            ]
        ];

        if (is_null($this->stripe_id))
        {
            $product = StripeProduct::create($params);

            $this->stripe_id = $product->id;
            $this->save();
        }
        else
        {
            StripeProduct::update($this->stripe_id, $params);
        }

        return $this->stripe_id;
    }

    /**
     * Get the price id on Stripe.
     */
    public function stripePriceId()
    {
        $product_id = $this->stripeProductId();
        $price_id = $this->getMetadata('stripe_price_id');

        $create_price = true;

        if ( ! is_null($price_id))
        {
            $price = StripePrice::retrieve($price_id);

            // If the price hasn't changed, no need to create a new one
            if ($price->product == $product_id and $price->unit_amount == $this->amount)
            {
                $create_price = false;
            }
        }

        if ($create_price)
        {
            $price = StripePrice::create([
                'product' => $product_id,
                'unit_amount' => $this->amount,
                'currency' => $this->currency,
            ]);

            $price_id = $this->setMetadata('stripe_price_id', $price->id);
        }

        return $price_id;
    }

    /**
     * Get the product tax id on Stripe.
     */
    public function stripeTaxId()
    {
        $tax_id = $this->getMetadata('stripe_tax_id');

        if (is_null($tax_id))
        {
            $tax = StripeTaxRate::create([
                'display_name' => 'IVA',
                'inclusive' => true,
                'percentage' => 21,
                'country' => 'ES',
                'description' => $this->name
            ]);

            $tax_id = $this->setMetadata('stripe_tax_id', $tax->id);
        }

        return $tax_id;
    }

    /**
     * Determines if a product must remain hidden
     *
     * @var boolean
     */
    public function isPrivate()
    {
        return $this->status == 'private';
    }

    /**
     * Determines if a product is discounted
     *
     * @var boolean
     */
    public function isDiscounted()
    {
        return ! is_null($this->sale_amount) and $this->sale_amount < $this->amount;
    }

    /**
     * Get the Google Product Categories.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function googleCategories()
    {
        return cache()->remember('google_categories', 60 * 60 * 24, function ()
        {
            return static::fetchGoogleCategories();
        });
    }

    /**
     * Fetch the Google Product Categories.
     * 
     * @return \Illuminate\Support\Collection
     */
    public static function fetchGoogleCategories()
    {
        $categories = file_get_contents('https://www.google.com/basepages/producttype/taxonomy-with-ids.en-GB.txt');
        $categories = explode("\n", $categories);

        return collect($categories)->filter(function ($category)
        {
            return str_contains($category, ' - ') and ! str_contains($category, '>');

        })->map(function ($category)
        {
            $category = explode(' - ', $category);

            return (object) [
                'id' => $category[0],
                'name' => $category[1]
            ];
        })->values();
    }

    /**
     * Construye la URL de intent tweet para compartir directamente
     * https://developer.twitter.com/en/docs/twitter-for-websites/tweet-button/overview
     */
    public function intentTweetUrl()
    {
        $params = [
            'url' => $this->canonical_url,
        ];

        return 'https://twitter.com/intent/tweet?' . http_build_query($params);
    }

    /*
     * Construye la URL de intent Facebook para compartir.
     * https://developers.facebook.com/docs/sharing/reference/share-dialog
     */
    public function intentFacebookUrl()
    {
        return 'https://www.facebook.com/dialog/share?' . http_build_query([
            'app_id' => config('services.facebook.client_id'),
            'display' => 'popup',
            'redirect_uri' => $this->canonical_url,
            'href' => $this->canonical_url,
        ]);
    }

    /**
     * Construye la URL de intent Pinterest para compartir.
     * https://developers.pinterest.com/docs/add-ons/save-button/
     */
    public function intentPinterestUrl()
    {
        return 'https://www.pinterest.com/pin/create/button?' . http_build_query([
            'url' => $this->canonical_url,
            'media' => img_url($this->photo_url),
            'description' => $this->name,
        ]);
    }

    /**
     * Converts the value to float
     */
    public static function floatval($val)
    {
        $val = str_replace(",", ".", $val);
        $val = preg_replace('/\.(?=.*\.)/', '', $val);

        return floatval($val);
    }
}
