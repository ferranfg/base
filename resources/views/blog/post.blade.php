@extends(config('base.blog_template'))

@push('head')

    @include('base::blog.post-meta')

@endpush

@section('content')
    <section class="bg-half d-table w-100 lazy" data-bg="url({{ $photo_url }})">
        <div class="bg-overlay"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
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
                            @if ( ! $post->is_page)
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
                            @endif
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
                <div class="col-md-1 d-none d-md-block">
                    <ul class="list-unstyled text-center social-icon">
                        <li><a href="{{ $post->intentFacebookUrl() }}" class="rounded" rel="noreferrer nofollow"><i class="fa fa-facebook-square"></i></a></li>
                        <li><a href="{{ $post->intentTweetUrl() }}" class="rounded" rel="noreferrer nofollow"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="{{ $post->intentWhatsAppUrl() }}" class="rounded" rel="noreferrer nofollow"><i class="fa fa-whatsapp"></i></a></li>
                        @if ($post->hasShortlink())
                            <li><a href="{{ $post->shortlink() }}" class="rounded" rel="noreferrer nofollow"><i class="fa fa-link"></i></a></li>
                        @endif
                    </ul>
                </div>
                <div class="col-lg-7 col-md-11">
                    @if ( ! $post->is_page)

                        @includeWhen(config('base.blog_post_banner'), config('base.blog_post_banner'))

                        <div class="post">
                            @basedownExtended(
                                blog_extended_post($post, $related)
                            )
                        </div>

                        @if ($keywords = $post->getKeywords() and $keywords->count())
                            <p class="text-monospace mt-5">
                                <span>Tags:</span>
                                @foreach ($keywords as $keyword)
                                    <a href="{{ $keyword->canonical_url }}" class="ml-2">{{ $keyword->name }}</a>@if ($loop->remaining),@endif
                                @endforeach
                            </p>
                        @endif

                        @if ($related->count())
                            <div class="mt-5">
                                @include('base::blog.related-posts', ['post' => $post, 'related' => $related])
                            </div>
                        @endif

                        @include('base::components.previous-next')

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
                    @else
                        <div class="post">
                            @basedownExtended($post->content)
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection