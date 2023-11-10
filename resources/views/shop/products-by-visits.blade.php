@if ($products->count())
    <h5 class="mb-0">Productos más populares</h5>
    @include('base::components.products', ['products' => $products])
    {{ $products->links('base::components.simple-pagination') }}
@endif