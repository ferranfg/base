<?php

namespace Ferranfg\Base\Models;

use Ferranfg\Base\Base;
use Spatie\Tags\HasTags;
use Laravel\Cashier\Cashier;
use Stripe\Price as StripePrice;
use Ferranfg\Base\Traits\HasSlug;
use Stripe\Product as StripeProduct;
use Ferranfg\Base\Traits\HasMetadata;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasTags, HasTranslations, HasSlug, HasMetadata;

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
    protected $fillable = ['name', 'slug', 'description', 'currency', 'amount', 'type', 'status'];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = ['name', 'slug', 'description'];

    /**
     * The available status values.
     *
     * @var array
     */
    public static $status = [
        'out_stock' => 'Out of stock',
        'available' => 'Available',
        'private' => 'Private'
    ];

    /**
     * The available types values.
     *
     * @var array
     */
    public static $types = [
        'simple' => 'Simple',
        'downloadable' => 'Downloadable'
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
     * Get the product canonical URL.
     */
    public function getCanonicalUrlAttribute()
    {
        return url("product/{$this->slug}");
    }

    /**
     * Get the product checkout URL.
     */
    public function getCheckoutUrlAttribute()
    {
        return url("checkout/{$this->slug}");
    }

    /**
     * Convert the object to its Slack message representation.
     */
    public function toMessage()
    {
        return $this->name;
    }

    /**
     * Get the product price on the min divisable.
     */
    public function rawAmount()
    {
        return bcmul($this->amount, 100);
    }

    /**
     * Get the product price formated with currency.
     */
    public function formatAmount()
    {
        return Cashier::formatAmount($this->rawAmount(), $this->currency);
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
                Storage::url($this->photo_url)
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
            if ($price->product == $product_id and $price->unit_amount == $this->rawAmount())
            {
                $create_price = false;
            }
        }

        if ($create_price)
        {
            $price = StripePrice::create([
                'product' => $product_id,
                'unit_amount' => $this->rawAmount(),
                'currency' => $this->currency,
            ]);

            $price_id = $this->setMetadata('stripe_price_id', $price->id);
        }

        return $price_id;
    }
}
