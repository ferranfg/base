@if ($offers->count())
    <h5 class="mb-0">Mejores ofertas</h5>
    @include('base::components.products', ['products' => $offers])
@endif