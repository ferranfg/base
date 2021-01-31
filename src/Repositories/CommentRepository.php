<?php

namespace Ferranfg\Base\Repositories;

use Ferranfg\Base\Base;
use Ferranfg\Base\Clients\Akismet;

class CommentRepository
{
    public function whereType($type)
    {
        return Base::comment()->whereType($type);
    }

    public function comment($comment_type, $commentable, $request)
    {
        $is_spam = Akismet::isSpam(
            $request->ip(),
            $commentable->canonical_url,
            $comment_type,
            $request->name,
            $request->email,
            $request->content
        );

        return $commentable->comments()->create([
            'author_name' => $request->name,
            'author_email' => $request->email,
            'author_IP' => $request->ip(),
            'content' => $request->content,
            'type' => $is_spam ? 'spam' : $comment_type
        ]);
    }

}