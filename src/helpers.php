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
        if (is_null($currency)) $currency = config('base.shop_currency');

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
        if (config('services.imagekit.key'))
        {
            if (str_starts_with($path, 'http')) $path = urlencode($path);

            return ImageKit::init()->url([
                'path' => $path,
                'transformation' => $transformation,
                // 'signed' => true,
                // 'expireSeconds' => 300,
            ]);
        }

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

        return implode(' - ', $meta_title);
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

/**
 * Clean accents from a string.
 *
 * @param  string  $text
 * @return string
 */
if ( ! function_exists('clean_accents'))
{
    function clean_accents($text)
    {
        $utf8 = array(
            '/[áàâãªä]/u'   =>   'a',
            '/[ÁÀÂÃÄ]/u'    =>   'A',
            '/[ÍÌÎÏ]/u'     =>   'I',
            '/[íìîï]/u'     =>   'i',
            '/[éèêë]/u'     =>   'e',
            '/[ÉÈÊË]/u'     =>   'E',
            '/[óòôõºö]/u'   =>   'o',
            '/[ÓÒÔÕÖ]/u'    =>   'O',
            '/[úùûü]/u'     =>   'u',
            '/[ÚÙÛÜ]/u'     =>   'U',
            '/ç/'           =>   'c',
            '/Ç/'           =>   'C',
            '/ñ/'           =>   'n',
            '/Ñ/'           =>   'N',
            '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
            '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
            '/[“”«»„]/u'    =>   ' ', // Double quote
            '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
        );

        return preg_replace(array_keys($utf8), array_values($utf8), $text);
    }
}

/**
 * Extends the content of a post with related posts.
 *
 * @param  string  $content
 * @param  array  $related
 * @return string
 */
if ( ! function_exists('blog_extended_post'))
{
    function blog_extended_post($content, $related = null)
    {
        if ( ! config('base.blog_extended_post')) return $content;

        // Buscamos un punto final, salto de linea y el segundo h2
        $content = str_replace("\r", '', $content);
        $h_index = 1;

        preg_match_all('/\.\n\n## (.+)/i', $content, $matches);

        // Si hay un segundo h2, lo reemplazamos por el post-halfway
        if (array_key_exists(1, $matches) and array_key_exists($h_index, $matches[1]))
        {
            $replace = $matches[1][$h_index];
            $halfway = view('base::blog.post-halfway', ['related' => $related])->render();

            if ($halfway) $content = str_replace(
                ".\n\n## {$replace}",
                ".\n\n{$halfway}\n\n## {$replace}",
                $content
            );
        }

        return $content;
    }
}

/**
 * Get the URL in the desired locale.
 *
 * @param  string  $url
 * @param  string  $locale
 * @return string
 */
if ( ! function_exists('dialect_redirect_url'))
{
    function dialect_redirect_url($url = null, string $locale = null)
    {
        if ( ! function_exists('dialect')) return $url;

        $redirect_url = dialect()->redirectUrl($url, $locale);

        if ($locale == config('app.fallback_locale'))
        {
            $redirect_url = str_replace($locale . '.', '', $redirect_url);
        }

        return $redirect_url;
    }
}