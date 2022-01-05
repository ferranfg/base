<?php

namespace Ferranfg\Base\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ferranfg\Base\Clients\ImageKit;
use Ferranfg\Base\Repositories\PostRepository;
use Ferranfg\Base\Repositories\CommentRepository;
use Illuminate\Foundation\Validation\ValidatesRequests;

class BlogController extends Controller
{
    use ValidatesRequests;

    public function __construct(
        CommentRepository $commentRepository,
        PostRepository $postRepository
    )
    {
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
    }

    /**
     * Lista y pagina las entradas del blog
     *
     * @return Response
     */
    public function list(Request $request)
    {
        if ($request->has('guid'))
        {
            $post = $this->postRepository->findById($request->guid);

            return redirect($post->canonical_url, 302);
        }

        $posts = $this->postRepository
            ->whereType('entry')
            ->whereStatus('published')
            ->orderBy('updated_at', 'desc')
            ->simplePaginate(5);

        $posts->setPath('/blog');

        abort_unless($posts->count(), 404);

        view()->share([
            'meta_title' => meta_title(config('base.blog_title')),
            'meta_description' => config('base.blog_description')
        ]);

        return view('base::blog.list', [
            'posts' => $posts
        ]);
    }

    /**
     * Página para la entrada individual de un post.
     *
     * @return Response
     */
    public function post(Request $request)
    {
        $post = $this->postRepository->findBySlug($request->slug);

        abort_unless($post->type == 'entry', 404);

        if (is_null($request->preview))
        {
            abort_unless($post->status == 'published', 404);

            $post->trackVisit();
        }

        $photo_url = ImageKit::init()->url([
            'path' => $post->photo_url,
            'transformation' => [
                ['width' => 1920, 'height' => 1280]
            ]
        ]);

        view()->share([
            'meta_title' => meta_title($post->name),
            'meta_description' => $post->excerpt,
            'meta_image' => $photo_url
        ]);

        return view('base::blog.post', [
            'post' => $post,
            'photo_url' => $photo_url,
            'previous' => $this->postRepository->previousPost($post),
            'next' => $this->postRepository->nextPost($post)
        ]);
    }

    /**
     * Añade un comentario a un post.
     *
     * @return Response
     */
    public function comment(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'content' => 'required|max:255'
        ]);

        $post = $this->postRepository->findBySlug($request->slug);

        abort_unless($post->type == 'entry' and $post->status == 'published', 404);

        $this->commentRepository->comment('comment', $post, $request);

        return redirect()->back()->with('success', __('Gracias por tu comentario.'));
    }
}