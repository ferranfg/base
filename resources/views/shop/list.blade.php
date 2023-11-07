@extends(config('base.shop_template'))

@section('content')
    <section class="bg-half bg-primary d-table w-100" style="background-image:url('/images/saas/home-shape.webp')">
        <div class="justify-content-center text-center text-white">
            <div class="page-next-level">
                <h1 class="title">{{ config('base.shop_title') }}</h1>
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
            @include('base::components.products', ['products' => $products])
            {{ $products->links('base::components.simple-pagination') }}
        </div>
    </section>
@endsection