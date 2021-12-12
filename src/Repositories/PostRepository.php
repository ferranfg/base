<?php

namespace Ferranfg\Base\Repositories;

use Ferranfg\Base\Base;

class PostRepository
{
    public function findById(int $id)
    {
        return Base::post()->findOrFail($id);
    }

    public function findBySlug(string $slug, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return Base::post()->where("slug->{$locale}", $slug)->firstOrFail();
    }

    public function whereType($type)
    {
        return Base::post()->with('metadata')->whereType($type);
    }

    public function whereStatus($status)
    {
        return Base::post()->with('metadata')->whereStatus($status);
    }

    public function previousPost($post)
    {
        $key_name = Base::post()->getKeyName();
        $previous_id = Base::post()
            ->whereType($post->type)
            ->whereStatus($post->status)
            ->where($key_name, '<', $post->$key_name)
            ->max($key_name);

        return Base::post()->find($previous_id);
    }

    public function nextPost($post)
    {
        $key_name = Base::post()->getKeyName();
        $next_id = Base::post()
            ->whereType($post->type)
            ->whereStatus($post->status)
            ->where($key_name, '>', $post->$key_name)
            ->min($key_name);

        return Base::post()->find($next_id);
    }

}