@extends(config('base.blog_template'))

@push('head')

    @include('base::blog.list-meta')

@endpush

@section('content')
    <section class="bg-half d-table w-100 lazy" data-bg="url({{ hero_image() }})">
        <div class="bg-overlay"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 title-heading text-center">
                    <h1 class="text-white title-dark mb-3">{{ $hero_title }}</h1>
                    <p class="para-desc mx-auto text-white-50 mb-0">{{ $hero_description }}</p>
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

    <section class="section pt-0">
        @if ($featured->count())
            <div class="container mt-100 mt-60">
                <div class="row">
                    @foreach($featured as $post)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card blog rounded border-0 shadow overflow-hidden">
                                <div class="position-relative">
                                    {!! img($post->photo_url, 690, 462, true, 'card-img-top img-fluid lazy', $post->name, 690, 462) !!}
                                    <div class="overlay rounded-top bg-dark"></div>
                                </div>
                                <div class="card-body content">
                                    <h2 class="h5">
                                        <a href="{{ $post->canonical_url }}" class="card-title title text-dark" title="{{ $post->name }}">{{ $post->name }}</a>
                                    </h2>
                                    <p class="text-muted">
                                        {{ (mb_strlen($post->excerpt) > 165) ? mb_substr($post->excerpt, 0, 160) . '…' : $post->excerpt }}
                                    </p>
                                    <div class="post-meta d-flex justify-content-between mt-3">
                                        @if ( ! $post->comments_disabled)
                                            <ul class="list-unstyled mb-0">
                                                <li class="list-inline-item"><a href="{{ $post->canonical_url }}#comments" class="text-muted comments"><i class="fa fa-comments mr-1"></i>{{ $post->comments()->count() }}</a></li>
                                            </ul>
                                        @endif
                                        <a href="{{ $post->canonical_url }}" class="text-muted readmore">Read More <i class="fa fa-chevron-right"></i></a>
                                    </div>
                                </div>
                                <div class="author">
                                    <small class="text-light user d-block"><i class="fa fa-user"></i> {{ $post->author->name }}</small>
                                    <small class="text-light date"><i class="fa fa-calendar"></i> Updated {{ $post->updated_at_diff }}</small>
                                    <time class="updated" datetime="{{ $post->updated_at }}"></time>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @includeWhen(config('base.banner_path'), 'base::components.banner')

        @if ($posts->count())
            <div class="container mt-100 mt-60">
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-xl-10">
                        @foreach ($posts as $post)
                            @include('base::components.post', ['post' => $post])
                        @endforeach
                        {{ $posts->links('base::components.simple-pagination') }}
                    </div>
                </div>
            </div>
        @endif
    </section>

    @if ($pinned)
        <section class="bg-half pt-5 pb-5 d-table w-100 lazy" data-bg="url({{ $pinned_photo_url }})">
            <div class="bg-overlay"></div>
            <div class="container">
                <div class="row position-relative align-items-center pt-4">
                    <div class="col-lg-7 offset-lg-5">
                        <div class="title-heading studio-home rounded bg-white shadow">
                            <h3 class="heading mb-3">{{ $pinned->name }}</h3>
                            <p class="para-desc text-muted">{{ $pinned->excerpt }}</p>
                            <div class="mt-4">
                                <a href="{{ $pinned->canonical_url }}" class="btn btn-primary mt-2 mr-2">Seguir leyendo</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection