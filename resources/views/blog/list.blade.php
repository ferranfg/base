@extends(config('base.blog_template', 'layouts.web'))

@push('head')

    @include('base::blog.list-meta')

@endpush

@section('content')
    <section class="bg-half d-table w-100 lazy" data-bg="url({{ hero_image() }})">
        <div class="bg-overlay"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 title-heading text-center">
                    <h1 class="text-white title-dark mb-3">{{ config('base.blog_title') }}</h1>
                    <p class="para-desc mx-auto text-white-50 mb-0">{{ config('base.blog_description') }}</p>
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

    @if ($posts->count())
        <section class="section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-xl-10">
                        @foreach ($posts as $post)
                            @include('base::components.post', ['post' => $post])
                        @endforeach
                        {{ $posts->links('base::components.simple-pagination') }}
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if ($featured)
        <section class="bg-half pt-5 pb-5 d-table w-100 lazy" data-bg="url({{ $featured_photo_url }})">
            <div class="bg-overlay"></div>
            <div class="container">
                <div class="row position-relative align-items-center pt-4">
                    <div class="col-lg-7 offset-lg-5">
                        <div class="title-heading studio-home rounded bg-white shadow">
                            <h3 class="heading mb-3">{{ $featured->name }}</h3>
                            <p class="para-desc text-muted">{{ $featured->excerpt }}</p>
                            <div class="mt-4">
                                <a href="{{ $featured->canonical_url }}" class="btn btn-primary mt-2 mr-2">Seguir leyendo</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @includeWhen(config('base.blog_substack_mode'), 'base::components.newsletter-modal')

@endsection