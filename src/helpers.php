<?php

use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;
use Ferranfg\Base\Clients\ImageKit;
use Ferranfg\Base\Clients\Unsplash;

/**
 * Formatea una moneda que llega en bruto al correspondiente de currency.
 *
 * @return string
 */
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
if ( ! function_exists('img_url'))
{
    function img_url($path, $transformation = [])
    {
        if (Str::startsWith($path, 'http')) return $path;

        if (config('services.imagekit.key')) return ImageKit::init()->url([
            'path' => $path,
            'transformation' => $transformation
        ]);

        return $path;
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
        $height_or_ratio,
        $lazy = true,
        $class = null,
        $alt = null,
        $img_width = null,
        $img_height = null
    )
    {
        $tag = ['<img'];

        $transformation = ['width' => (int) $width];
        $transformation2x = ['width' => (int) bcmul($width, 2)];

        if (Str::contains($height_or_ratio, '-'))
        {
            $transformation['aspectRatio'] = $height_or_ratio;
            $transformation2x['aspectRatio'] = $height_or_ratio;
        }
        else
        {
            $transformation['height'] = (int) $height_or_ratio;
            $transformation2x['height'] = (int) bcmul($height_or_ratio, 2);
        }

        $url = img_url($path, [$transformation]);
        $url2x = img_url($path, [$transformation2x]);

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
 * Get the URL to the header image service.
 *
 * @return string
 */
if ( ! function_exists('hero_image'))
{
    function hero_image()
    {
        if (is_null(config('services.unsplash.collections'))) return config('base.hero_image');

        return Unsplash::randomFromCollections()->pluck('urls.regular')->random();
    }
}

/**
 * Construye un canonical_url.
 *
 * @return string
 */
if ( ! function_exists('meta_url'))
{
    function meta_url()
    {
        return url()->current();
    }
}

/**
 * Construye un meta_title con el formato de {pagina} | {nombre}.
 *
 * @return string
 */
if ( ! function_exists('meta_title'))
{
    function meta_title($title = null)
    {
        $meta_title = [];

        if ($page = request()->page and $page > 1) $meta_title[] = "Page {$page}";
        if (is_string($title)) $meta_title[] = $title;

        $meta_title[] = config('app.name');

        return implode(' | ', $meta_title);
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