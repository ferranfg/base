@extends(config('base.blog_template', 'template'))

@section('content')
    <section class="bg-half d-table w-100 lazy" data-bg="url({{ config('base.blog_image') }})">
        <div class="bg-overlay"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 title-heading text-center">
                    <h1 class="text-white title-dark mb-3">{{ config('base.blog_title') }}</h1>
                    <p class="para-desc mx-auto text-white-50 mb-0 text-balance">{{ config('base.blog_subtitle') }}</p>
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
@endsection