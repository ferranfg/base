<?php

use Laravel\Cashier\Cashier;

if ( ! function_exists('format_amount'))
{
    function format_amount($raw_amount, $currency = null)
    {
        if (is_null($currency)) $currency = config('cashier.currency');

        return Cashier::formatAmount($raw_amount, $currency);
    }
}