@extends(config('base.shop_template'))

@section('content')
    <section class="bg-half d-table w-100 lazy" data-bg="url({{ hero_image() }})">
        <div class="bg-overlay"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12 text-center">
                    <div class="page-next-level">
                        <h1 class="text-white title">{{ $product->name }}</h1>
                        <div class="page-next">
                            <nav class="d-inline-block">
                                <ul class="breadcrumb bg-white rounded shadow mb-0">
                                    <li class="breadcrumb-item"><a href="/shop">Tienda</a></li>
                                    <li class="breadcrumb-item active">{{ $product->name }}</li>
                                </ul>
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
            @include('base::shop.detail', ['product' => $product])
        </div>
        <div class="container mt-100 mt-60">
            <ul class="nav nav-pills shadow flex-column flex-sm-row d-md-inline-flex mb-0 p-1 bg-white rounded position-relative overflow-hidden" id="pills-tab">
                <li class="nav-item m-1">
                    <a class="nav-link py-2 px-5 active rounded text-center" data-toggle="pill" href="#description">
                        <h6 class="mb-0">Descripción</h6>
                    </a>
                </li>
                @if ( ! $product->comments_disabled)
                    <li class="nav-item m-1">
                        <a class="nav-link py-2 px-5 rounded text-center" data-toggle="pill" href="#reviews">
                            <h6 class="mb-0">Valoraciones</h6>
                        </a>
                    </li>
                @endif
            </ul>
            <div class="tab-content mt-5">
                <div class="card border-0 tab-pane fade show active text-muted" id="description">
                    @basedown($product->description)
                </div>
                @if ( ! $product->comments_disabled)
                    <div class="card border-0 tab-pane fade" id="reviews">
                        <div class="row">
                            <div class="col-lg-6">
                                @if ($product->comments->count())
                                    <ul class="media-list list-unstyled mb-0">
                                        @foreach ($product->comments as $comment)
                                            @include('base::components.comment', ['comment' => $comment])
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-center">
                                        <div class="h3">🙈</div>
                                        <div class="text-muted">Este producto no tiene ninguna valoración todavía.</div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-lg-6 mt-4 mt-lg-0 pt-2 pt-lg-0" id="comment-submit">
                                <h5>Añade tu valoración:</h5>
                                @include('base::components.comment-submit', [
                                    'action' => "{$product->canonical_url}/review#comment-submit",
                                    'errors' => $errors,
                                    'rating' => true
                                ])
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @if ($related->count())
            <div class="container mt-100 mt-60">
                <h5 class="mb-0">Productos relacionados</h5>
                @include('base::components.products', ['products' => $related])
            </div>
        @endif
    </section>

    <div class="container-fluid mt-60 px-0">
        <div class="py-5 bg-light">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-between">
                            @if ($previous)
                                <a href="{{ $previous->canonical_url }}" class="text-dark align-items-center">
                                    <span class="fa fa-arrow-left"></span>
                                    <span class="text-muted d-none d-md-inline-block">{{ $previous->name }}</span>
                                    {!! img($previous->photo_url, 75, 75, true, 'avatar avatar-small rounded shadow ml-2 lazy', $previous->name, 65, 65) !!}
                                </a>
                            @else
                                <a href="/random" class="text-dark align-items-center">
                                    <span class="fa fa-arrow-left"></span>
                                    <span class="text-muted d-none d-md-inline-block">Ver producto random 🤩</span>
                                </a>
                            @endif
                            <a href="/" class="btn btn-lg btn-pills btn-icon btn-soft-primary">
                                <span class="fa fa-home"></span>
                            </a>
                            @if ($next)
                                <a href="{{ $next->canonical_url }}" class="text-dark align-items-center">
                                    {!! img($next->photo_url, 75, 75, true, 'avatar avatar-small rounded shadow mr-2 lazy', $next->name, 65, 65) !!}
                                    <span class="text-muted d-none d-md-inline-block">{{ $next->name }}</span>
                                    <span class="fa fa-arrow-right"></span>
                                </a>
                            @else
                                <a href="/random" class="text-dark align-items-center">
                                    <span class="text-muted d-none d-md-inline-block">Ver producto random 🤩</span>
                                    <span class="fa fa-arrow-right"></span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection