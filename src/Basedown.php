<?php

namespace Ferranfg\Base;

use diversen\markdownSplit;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\MarkdownConverter;
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
        $output = str_replace('href="/', 'href="' . url('/') . '/', $output); // relative links

        return $output;
    }

    /**
     * Converts the markdown text into the output
     *
     * @return string
     */
    public function directiveExtended($expression)
    {
        $environment = new Environment([
            'external_link' => [
                'internal_hosts' => '/(^|\.)' . parse_url(config('app.url'), PHP_URL_HOST) . '$/',
                'nofollow' => 'external',
            ],
            'heading_permalink' => [
                'html_class' => 'd-none',
                'apply_id_to_heading' => true,
                'insert' => 'after',
                'symbol' => ' #',
            ],
            'table_of_contents' => [
                'position' => 'before-headings',
                'max_heading_level' => 2,
            ],
        ]);

        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());
        $environment->addExtension(new TableOfContentsExtension());
        $environment->addExtension(new ExternalLinkExtension());

        $converter = new MarkdownConverter($environment);

        $output = $converter->convert($expression);
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