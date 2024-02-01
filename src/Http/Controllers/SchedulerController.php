<?php

namespace Ferranfg\Base\Http\Controllers;

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
     * Endpoint para las tareas semanales. Recibirá una petición de Integromat
     *
     * @return Response
     */
    public function weekly()
    {
        $this->newsletterPublishedPosts();

        return response()->json([]);
    }

    /**
     * Newsletter las entradas que no han sido enviadas todavía.
     *
     * @return Response
     */
    public function newsletterPublishedPosts()
    {
        $posts = $this->postRepository->whereStatus('published')->orderBy('id', 'asc')->get();

        $post = $posts->first(function ($post)
        {
            return $post->getMetadata('newslettered_at') == null;
        });

        if ( ! is_null($post)) $post->sendNewsletter();
    }

    /**
     * Endpoint para las tareas diarias. Recibirá una petición de Integromat
     *
     * @return Response
     */
    public function daily()
    {
        $this->publishScheduledPosts();

        return response()->json([]);
    }

    /**
     * Publica las entradas que están programadas a un horario superario al actual.
     *
     * @return Response
     */
    private function publishScheduledPosts()
    {
        $posts = $this->postRepository->whereStatus('scheduled')->get();

        foreach ($posts as $post)
        {
            if ($post->scheduled_at and $post->scheduled_at->isPast()) $post->publish();
        }
    }

    /**
     * Recibe las peticiones que se envian desde App Engine con el start/stop de la instancia.
     *
     * @return Response
     */
    public function engine()
    {
        return response()->json();
    }
}