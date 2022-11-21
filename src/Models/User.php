<?php

namespace Ferranfg\Base\Models;

use Stripe\Customer;
use Soved\Laravel\Gdpr\Portable;
use Spatie\Activitylog\LogOptions;
use Ferranfg\Base\Traits\HasMetadata;
use Spatie\Activitylog\Traits\LogsActivity;

class User
{
    use HasMetadata, Portable, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'authy_id',
        'country_code',
        'phone',
        'two_factor_reset_code',
        'card_brand',
        'card_last_four',
        'card_country',
        'billing_address',
        'billing_address_line_2',
        'billing_city',
        'billing_zip',
        'billing_country',
        'extra_billing_information',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'uses_two_factor_auth' => 'boolean',
    ];

    /**
     * Configure the model activity logger.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * Get the GDPR compliant data portability array for the model.
     *
     * @return array
     */
    public function toPortableArray()
    {
        return $this->toArray();
    }

    /**
     * Route notifications for the Discord channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForDiscord($notification)
    {
        return config('base.discord_webhook');
    }

    /**
     * Get the customer id on Stripe.
     */
    public function stripeCustomerId()
    {
        if ( ! is_null($this->stripe_id)) return $this->stripe_id;

        $customer = Customer::create([
            'name' => $this->name,
            'email' => $this->email
        ]);

        $this->stripe_id = $customer->id;
        $this->save();

        return $this->stripe_id;
    }
}
