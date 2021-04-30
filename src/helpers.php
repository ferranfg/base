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

        $url2x = $img->url([
            'path' => $path,
            'transformation' => [
                ['width' => bcmul($width, 2), 'height' => bcmul($height, 2)]
            ]
        ]);

        if ($lazy)
        {
            array_push($tag, "src=\"\" data-src=\"{$url}\"");
            array_push($tag, "srcset=\"\" data-srcset=\"{$url} 1x, {$url2x} 2x\"");
        }
        else
        {
            array_push($tag, "src=\"{$url}\"");
            array_push($tag, "srcset=\"{$url} 1x, {$url2x} 2x\"");
        }

        if ( ! is_null($class)) array_push($tag, "class=\"{$class}\"");
        if ( ! is_null($alt)) array_push($tag, "alt=\"{$alt}\"");

        array_push($tag, '/>');

        return implode(' ', $tag);
    }
}