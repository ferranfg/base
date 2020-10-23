<?php

namespace Ferranfg\Base;

use Parsedown;

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

        return $output;
    }
}