<?php

use Laravel\Cashier\Cashier;
use Ferranfg\Base\Clients\ImageKit;

if ( ! function_exists('format_amount'))
{
    function format_amount($raw_amount, $currency = null)
    {
        if (is_null($currency)) $currency = config('cashier.currency');

        return Cashier::formatAmount($raw_amount, $currency);
    }
}

/**
 * Get the URL to the resizer image service.
 *
 * @return string
 */
if ( ! function_exists('img_tag'))
{
    function img_tag($path, $width, $height, $lazy = true, $class = null, $alt = null)
    {
        $tag = ['<img', "width=\"{$width}\"", "height=\"{$height}\""];
        $img = ImageKit::init();

        $url = $img->url([
            'path' => $path,
            'transformation' => [
                ['width' => $width, 'height' => $height]
            ]
        ]);

        if ($lazy)
        {
            array_push($tag, "src=\"\" data-src=\"{$url}\"");
        }
        else
        {
            array_push($tag, "src=\"{$url}\"");
        }

        if ( ! is_null($class)) array_push($tag, "class=\"{$class}\"");
        if ( ! is_null($alt)) array_push($tag, "alt=\"{$alt}\"");

        array_push($tag, '/>');

        return implode(' ', $tag);
    }
}