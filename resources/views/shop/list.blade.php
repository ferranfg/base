@extends(config('base.shop_template'))

@push('head')

    @include('base::shop.list-meta')

@endpush

@section('content')
    @section('shop-hero')
        <section class="bg-half d-table w-100 lazy" data-bg="url({{ hero_image() }})">
            <div class="bg-overlay"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-7 page-next-level title-heading text-center">
                        <h1 class="text-white title-dark mb-3">{{ config('base.shop_title') }}</h1>
                        <p class="para-desc mx-auto text-white-50 mb-0">{{ config('base.shop_description') }}</p>
                        <div class="page-next">
                            <nav class="d-inline-block">
                                <ul class="breadcrumb bg-white rounded shadow mb-0">
                                    <li class="breadcrumb-item"><a href="/">Inicio</a></li>
                                    <li class="breadcrumb-item active">Tienda</li>
                                </ul>
                            </nav>
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
    @show

    <section class="section pt-0">
        <div class="container mt-100 mt-60">
            @if ($base_profile->grade > 6)
                @include('base::shop.products-by-visits', ['products' => $products])
            @else
                @include('base::shop.products-by-discount', ['offers' => $offers])
            @endif
        </div>

        @includeWhen(config('base.banner_path'), 'base::components.banner')

        @if ($brands->count())
            <div class="container mt-100 mt-60">
                <h5 class="mb-0">Nuestras marcas</h5>
                <div class="row">
                    @foreach ($brands as $brand)
                        <div class="col-lg-2 col-md-4 col-6 mt-4 pt-2">
                            <div class="card explore-feature border-0 rounded text-center bg-white">
                                <div class="card-body">
                                    <div class="icon rounded-circle shadow-lg d-inline-block h2">
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="content mt-3">
                                        <h6 class="mb-0">{{ $brand->value }}</a></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="container mt-100 mt-60">
            @if ($base_profile->grade > 6)
                @include('base::shop.products-by-discount', ['offers' => $offers])
            @else
                @include('base::shop.products-by-visits', ['products' => $products])
            @endif
        </div>
    </section>
@endsection