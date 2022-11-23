<?php

namespace Ferranfg\Base;

use Illuminate\Support\Facades\Facade;
use Laravel\Cashier\Cashier;

/**
 * @see \Ferranfg\Base\Base
 */
class BaseFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'base';
    }

    /**
     * Get the default JavaScript variables for Spark.
     *
     * @return array
     */
    public static function scriptVariables()
    {
        $user = auth()->user();

        return [
            'translations' => static::getTranslations(),
            'csrfToken' => csrf_token(),
            'currency' => config('cashier.currency'),
            'currencyLocale' => config('cashier.currency_locale'),
            'env' => config('app.env'),
            'state' => [
                'user' => $user,
                'teams' => auth()->check() ? $user->teams : [],
                'currentTeam' => auth()->check() ? $user->currentTeam : null,
            ],
            'stripeApiVersion' => Cashier::STRIPE_VERSION,
            'stripeKey' => config('cashier.key'),
            'cashierPath' => config('cashier.path'),
            'locale' => config('app.locale')
        ];
    }

    /**
     * Get the translation keys from file.
     *
     * @return array
     */
    private static function getTranslations()
    {
        $translationFile = base_path('lang/'.app()->getLocale().'.json');

        if (! is_readable($translationFile)) {
            $translationFile = base_path('lang/'.app('translator')->getFallback().'.json');
        }

        return json_decode(file_get_contents($translationFile), true);
    }
}
