<?php

namespace Ferranfg\Base\Models;

use Stripe\Customer;
use Soved\Laravel\Gdpr\Portable;
use Ferranfg\Base\Traits\HasMetadata;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use HasMetadata, Portable, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

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
