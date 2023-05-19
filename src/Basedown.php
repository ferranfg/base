<?php

namespace Ferranfg\Base;

use Parsedown;
use diversen\markdownSplit;

class Basedown extends Parsedown
{
    /**
     * Converts the markdown text into the output
     *
     * @return string
     */
    public function directive($expression)
    {
        $output = $this->text($expression);
        $output = preg_replace('/\[youtube id=&quot;(.+)&quot;]/m', '<div class="embed-container"><iframe src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe></div>', $output, 1);
        $output = str_replace('href="/', 'href="' . url('/') . '/', $output); // relative links

        return $output;
    }

    /**
     * Splits the markdown text into an array of headings
     *
     * @return array
     */
    public static function split($content, $level = 2)
    {
        $content = "## Empty Header\n\n" . $content;
        $pieces = (new markdownSplit)->splitMarkdownAtLevel($content, true, $level);

        return collect($pieces)
            ->filter(function ($item)
            {
                return ! ($item['header'] == '');
            })
            ->map(function ($item)
            {
                return (object) $item;
            });
    }
}