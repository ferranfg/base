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
if ( ! function_exists('img'))
{
    function img(
        $path,
        $width,
        $height,
        $lazy = true,
        $class = null,
        $alt = null,
        $img_width = null,
        $img_height = null
    )
    {
        $tag = ['<img'];
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
        if ( ! is_null($img_width)) array_push($tag, "width=\"{$img_width}\"");
        if ( ! is_null($img_height)) array_push($tag, "height=\"{$img_height}\"");

        array_push($tag, '/>');

        return implode(' ', $tag);
    }
}

/**
 * Returns if the string is RTL o LTR.
 *
 * @return boolean
 */
if ( ! function_exists('is_rtl'))
{
    function is_rtl($value)
    {
        $rtl_char = '/[\x{0590}-\x{083F}]|[\x{08A0}-\x{08FF}]|[\x{FB1D}-\x{FDFF}]|[\x{FE70}-\x{FEFF}]/u';

        return preg_match($rtl_char, $value) != 0;
    }
}