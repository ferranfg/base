<?php

namespace Ferranfg\Base\Http\Controllers;

use Ferranfg\Base\Clients\Unsplash;
use Ferranfg\Base\Models\Assistance;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuidesController extends BlogController
{
    /**
     * Show the questions landing page.
     *
     * @return Response
     */
    public function index()
    {
        $tags = $this->tagRepository->whereType('guide-tag')->with('posts')->get();

        abort_unless($tags->count(), 404);

        return view('base::guides.index', [
            'photo_url' => Unsplash::randomFromCollections()->pluck('urls.regular')->random(),
            'tags' => $tags
        ]);
    }

    /**
     * Question response page;
     *
     * @return Response
     */
    public function show(Request $request)
    {
        $locale = app()->getLocale();

        // No podemos utilizar findBySlug porque algunos pueden tener vacio y da error
        $question = $this->postRepository->whereType('guide')
            ->where("slug->{$locale}", $request->slug)
            ->where('status', 'published')
            ->firstOrFail();

        $photo_url = Unsplash::randomFromCollections()->pluck('urls.regular')->random();

        view()->share([
            'meta_title' => meta_title($question->name),
            'meta_image' => $photo_url,
        ]);

        return view('base::guides.show', [
            'post' => $question,
            'photo_url' => $photo_url,
            'previous' => $this->postRepository->previousPost($question),
            'next' => $this->postRepository->nextPost($question),
            'random' => $this->postRepository->randomPost($question),
            'related' => $this->getRelated($question, ['guide'], 6),
        ]);
    }

    /**
     * Dynamic page that generates the question response.
     *
     * @return Response
     */
    public function answer(Request $request)
    {
        $question = $this->postRepository->findById($request->id);

        if ($question->type == 'guide' and $question->status == 'published')
        {
            return redirect()->to($question->canonical_url, 301);
        }

        abort_unless($question->type == 'guide' and $question->status == 'draft', 404);

        $question->status = 'scheduled';
        $question->save();

        $assistance = Assistance::completion($question->name, [
            'temperature' => 0.7,
            'max_tokens' => 2048
        ]);

        $question->slug = Str::slug($question->name);
        $question->content = $assistance->choices[0]->message->content;
        $question->status = 'published';
        $question->save();

        return redirect()->to($question->canonical_url, 302);
    }
}