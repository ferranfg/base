<?php

namespace Ferranfg\Base\Http\Controllers;

use Schema;
use Closure;
use Ferranfg\Base\Base;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Ferranfg\Base\Clients\ImageKit;
use Ferranfg\Base\Models\Assistance;
use Ferranfg\Base\Repositories\CommentRepository;
use Ferranfg\Base\Repositories\PostRepository;
use Ferranfg\Base\Repositories\TagRepository;
use Illuminate\Foundation\Validation\ValidatesRequests;

class BlogController extends Controller
{
    use ValidatesRequests;

    public $commentRepository;

    public $postRepository;

    public $tagRepository;

    public function __construct(
        CommentRepository $commentRepository,
        PostRepository $postRepository,
        TagRepository $tagRepository,
    )
    {
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
        $this->tagRepository = $tagRepository;

        $this->middleware(function (Request $request, Closure $next)
        {
            abort_unless(Schema::hasTable(Base::post()->getTable()), 404);

            return $next($request);
        });
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

        $featured = $this->postRepository
            ->whereStatus('published')
            ->whereFeatured(true)
            ->latest()
            ->first();

        $posts = $this->postRepository
            ->whereStatus('published')
            ->whereIn('type', ['entry', 'dynamic', 'newsletter'])
            ->whereFeatured(false)
            ->orderBy('updated_at', 'desc')
            ->simplePaginate(8);

        $posts->setPath(config('base.blog_path'));

        abort_unless($posts->count(), 404);

        view()->share([
            'meta_title' => meta_title(config('base.blog_title')),
            'meta_description' => config('base.blog_description')
        ]);

        return view('base::blog.list', [
            'hero_title' => config('base.blog_title'),
            'hero_description' => config('base.blog_description'),
            'posts' => $posts,
            'featured' => $featured,
            'featured_photo_url' => $featured ? img_url($featured->photo_url, [
                ['width' => 1920, 'height' => 1080]
            ]) : hero_image(),
        ]);
    }

    /**
     * Lista y pagina las entradas del blog
     *
     * @return Response
     */
    public function tag(Request $request)
    {
        $keyword = rawurldecode($request->keyword);
        $posts = $this->postRepository
            ->whereStatus('published')
            ->whereIn('type', ['entry', 'dynamic', 'newsletter'])
            ->whereFeatured(false)
            ->where('keywords', 'like', "%{$keyword}%")
            ->orderBy('updated_at', 'desc')
            ->simplePaginate(8);

        abort_unless($posts->count(), 404);

        view()->share([
            'meta_title' => meta_title("Tag: {$keyword}"),
            'meta_description' => config('base.blog_description')
        ]);

        return view('base::blog.list', [
            'hero_title' => $keyword,
            'hero_description' => null,
            'posts' => $posts,
            'featured' => false,
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

        if (is_null($request->preview))
        {
            abort_unless($post->status == 'published', 404);

            $post->trackVisit();
        }

        $photo_url = img_url($post->photo_url, [
            ['width' => 1920, 'height' => 1080]
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
            'next' => $this->postRepository->nextPost($post),
            'random' => $this->postRepository->randomPost($post),
            'related' => $this->getRelated($post, ['entry', 'dynamic']),
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

        abort_unless($post->status == 'published', 404);

        $this->commentRepository->comment('comment', $post, $request);

        return redirect()->back()->with('success', __('Gracias por tu comentario.'));
    }

    /**
     * Get the embedding guides for a question.
     *
     * @return Response
     */
    protected function getRelated($post, array $type, $match_count = 5, $match_threshold = 0.6)
    {
        if ( ! config('base.assistance_embeddings')) return collect();

        if (cache()->has("related-v2-{$post->id}")) return cache("related-v2-{$post->id}");

        $assistance = Assistance::whereContent($post->toEmbedding())->first();
        $embeddings = collect();

        if ($assistance)
        {
            $matching = Assistance::match($assistance->embedding, $match_threshold, $match_count);

            $posts = collect($matching)->pluck('content')->map(function($content)
            {
                return json_decode($content);
            });

            if ($posts->count())
            {
                $embeddings = $this->postRepository->whereStatus('published')
                    ->whereIn('type', $type)
                    ->whereIn('id', $posts->pluck('id')->toArray())
                    ->where('id', '!=', $post->id)
                    ->get();
            }

            cache()->put("related-v2-{$post->id}", $embeddings, now()->addDays(2));
        }

        return $embeddings;
    }
}