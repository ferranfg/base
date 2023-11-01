@extends(config('base.blog_template'))

@push('head')

    @include('base::blog.post-meta')

@endpush

@section('content')
    <section class="bg-half d-table w-100 lazy" data-bg="url({{ $photo_url }})">
        <div class="bg-overlay"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="page-next-level">
                        <div class="title-heading text-center">
                            @if ($post->excerpt)
                                <h1 class="text-white title-dark mb-3">{{ $post->name }}</h1>
                                <p class="para-desc mx-auto text-white-50 mb-0">{{ $post->excerpt }}</p>
                            @else
                                <h1 class="text-white title-dark mb-0">{{ $post->name }}</h1>
                            @endif
                        </div>
                        @section('post-breadcrumb')
                            <div class="page-next text-center">
                                <nav class="d-inline-block">
                                    <ul class="breadcrumb bg-white rounded shadow mb-0">
                                        @if ($post->author)
                                            @if ($post->author->name == 'Ferran Figueredo')
                                                <li class="breadcrumb-item"><i class="fa fa-user"></i> <a href="https://ferranfigueredo.com" target="_blank" rel="noopener nofollow">Ferran Figueredo</a></li>
                                            @else
                                                <li class="breadcrumb-item"><i class="fa fa-user"></i> {{ $post->author->name }}</li>
                                            @endif
                                        @endif
                                        <li class="breadcrumb-item"><i class="fa fa-calendar"></i> {{ $post->updated_at->toFormattedDateString() }}</li>
                                    </ul>
                                    <time class="updated" datetime="{{ $post->updated_at }}"></time>
                                </nav>
                            </div>
                        @show
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="position-relative">
        <div class="shape overflow-hidden text-white">
            <svg viewBox="0 0 2880 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 48H1437.5H2880V0H2160C1442.5 52 720 0 720 0H0V48Z" fill="currentColor"></path>
            </svg>
        </div>
    </div>

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="alert alert-light text-center border-0">
                        <span class="mr-4"><i class="fa fa-clock"></i> {{ $post->reading_time }} min read</span>
                        @if ( ! $post->comments_disabled)
                            <span class="d-none d-md-inline mr-4"><i class="fa fa-comment"></i> <a href="#comments" class="alert-link">{{ $post->comments->count() }} comments</a></span>
                        @endif
                        <span class="mr-4"><i class="fa fa-twitter"></i> <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->name) }}&url={{ urlencode($post->canonical_url) }}" class="alert-link" target="_blank" rel="noreferrer nofollow">Share</a></span>
                        <span><i class="fa fa-facebook-square"></i> <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($post->canonical_url) }}" class="alert-link" target="_blank" rel="noreferrer nofollow">Share</a></span>
                    </div>

                    @includeWhen(config('base.blog_before_post'), config('base.blog_before_post'))

                    <div class="post">
                        @basedownExtended($post->content)
                    </div>

                    @includeWhen(config('base.blog_after_post'), config('base.blog_after_post'))

                    @if ($related->count() and $post->type == 'guide')
                        <h5 class="mt-5">Related Guides:</h5>
                        <ul class="list-unstyled mt-4 mb-0">
                            @foreach ($related as $question)
                                <li class="mt-2">
                                    <a href="{{ $question->canonical_url }}" class="text-muted"><span class="fa fa-arrow-right text-primary"></span> {{ $question->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @elseif ($related->count())
                        <h5 class="mt-5">Related Posts:</h5>
                        @foreach ($related as $related_post)
                            @include('base::components.post', ['post' => $related_post, 'compact' => true])
                        @endforeach
                    @endif

                    @includeUnless($post->type == 'page', 'base::components.previous-next')

                    @if ( ! $post->comments_disabled)
                        <div id="comments">
                            @if ($post->comments->count())
                                <h5 class="mt-4">Comments:</h5>
                                <ul class="media-list list-unstyled mb-0">
                                    @foreach ($post->comments as $comment)
                                        @include('base::components.comment', ['comment' => $comment])
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <h5 class="mt-4" id="comment-submit">Leave a comment:</h5>

                        @include('base::components.comment-submit', [
                            'action' => "{$post->canonical_url}#comment-submit",
                            'errors' => $errors
                        ])
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection