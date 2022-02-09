@extends(config('base.blog_template', 'template'))

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
                            <h1 class="text-white title-dark mb-3">{{ $post->name }}</h1>
                            <p class="para-desc mx-auto text-white-50 mb-0">{{ $post->excerpt }}</p>
                        </div>
                        <div class="page-next text-center">
                            <nav class="d-inline-block">
                                <ul class="breadcrumb bg-white rounded shadow mb-0">
                                    <li class="breadcrumb-item"><i class="fa fa-user"></i> {{ $post->author->name }}</li>
                                    <li class="breadcrumb-item"><i class="fa fa-calendar"></i> {{ $post->updated_at_diff }}</li>
                                    <li class="breadcrumb-item"><i class="fa fa-clock"></i> {{ $post->reading_time }} min read</li>
                                </ul>
                                <time class="updated" datetime="{{ $post->updated_at }}"></time>
                            </nav>
                        </div>
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
                    <div class="post">
                        @basedown($post->content)
                    </div>

                    @include('base::components.previous-next')

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
                </div>
            </div>
        </div>
    </section>
@endsection