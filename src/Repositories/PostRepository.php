<?php

namespace Ferranfg\Base\Repositories;

use Ferranfg\Base\Base;

class PostRepository
{
    public function findById(int $id)
    {
        return Base::post()->findOrFail($id);
    }

    public function findBySlug(string $slug)
    {
        return Base::post()->where("slug", $slug)->firstOrFail();
    }

    public function whereType($type)
    {
        return Base::post()->with('author', 'metadata')->whereType($type);
    }

    public function whereStatus($status)
    {
        return Base::post()->with('author', 'metadata')->whereStatus($status);
    }

    public function closePost($post, $comparison, $sort)
    {
        return Base::post()
            ->whereType($post->type)
            ->whereStatus($post->status)
            ->where('created_at', $comparison, $post->updated_at)
            ->orderBy('created_at', $sort)
            ->first();
    }

    public function previousPost($post)
    {
        return $this->closePost($post, '<', 'desc');
    }

    public function nextPost($post)
    {
        return $this->closePost($post, '>', 'asc');
    }

    public function randomPost($post, $seed = '')
    {
        return Base::post()
            ->whereType($post->type)
            ->whereStatus($post->status)
            ->where('id', '!=', $post->id)
            ->inRandomOrder($seed)
            ->first();
    }

}