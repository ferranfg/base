@extends(config('base.shop_template'))

@section('content')
    <section class="bg-half d-table w-100 lazy" data-bg="url({{ hero_image() }})">
        <div class="bg-overlay"></div>
        <div class="justify-content-center text-center position-relative">
            <div class="page-next-level">
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
            <h5 class="mb-0">Productos más visitados</h5>
            @include('base::components.products', ['products' => $products])
            {{ $products->links('base::components.simple-pagination') }}
        </div>
    </section>
@endsection