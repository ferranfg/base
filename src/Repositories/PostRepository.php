<?php

namespace Ferranfg\Base\Repositories;

use Ferranfg\Base\Base;

class PostRepository
{
    public function whereType($type)
    {
        return Base::post()->with('metadata')->whereType($type);
    }

    public function findBySlug(string $slug, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return Base::post()->where("slug->{$locale}", $slug)->firstOrFail();
    }

    public function previousPost($post, $status = 'published')
    {
        $previous_id = Base::post()
            ->whereType($post->type)
            ->whereStatus($status)
            ->where(Base::post()->getKeyName(), '<', $post->id)
            ->max(Base::post()->getKeyName());

        return Base::post()->find($previous_id);
    }

    public function nextPost($post, $status = 'published')
    {
        $next_id = Base::post()
            ->whereType($post->type)
            ->whereStatus($status)
            ->where(Base::post()->getKeyName(), '>', $post->id)
            ->min(Base::post()->getKeyName());

        return Base::post()->find($next_id);
    }

}