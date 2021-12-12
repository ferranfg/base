<?php

namespace Ferranfg\Base\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Ferranfg\Base\Repositories\PostRepository;

class SchedulerController extends Controller
{
    public $postRepository;

    public function __construct(
        PostRepository $postRepository
    )
    {
        $this->postRepository = $postRepository;
    }

    /**
     * Endpoint para las tareas diarias. Recibirá una petición de app engine
     *
     * @return Response
     */
    public function daily()
    {
        $this->publishScheduledPosts();

        return response()->json([]);
    }

    private function publishScheduledPosts()
    {
        $posts = $this->postRepository->whereStatus('scheduled')->get();

        foreach ($posts as $post)
        {
            $scheduled_at = $post->getMetadata('scheduled_at');

            if ($scheduled_at > Carbon::now()) $post->publish();
        }
    }
}