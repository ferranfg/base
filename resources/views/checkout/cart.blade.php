@extends(config('base.shop_template'))

@section('index')
    <meta name="robots" content="noindex">
@endsection

@section('content')
    <section class="bg-half bg-dark d-table w-100" style="background-image:url('/images/saas/home-shape.webp')">
        <div class="justify-content-center text-center text-white">
            <div class="page-next-level">
                <h1 class="title">Cesta de compra 📦</h1>
                <div class="page-next">
                    <nav class="d-inline-block">
                        <ul class="breadcrumb bg-white rounded shadow mb-0">
                            <li class="breadcrumb-item"><a href="/shop">Tienda</a></li>
                            <li class="breadcrumb-item active">Cesta</li>
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
        <form method="POST" action="/cart" class="container">
            @csrf
            @if ($cart->count())
                <div class="table-responsive bg-white shadow">
                    <table class="table table-center table-padding mb-0">
                        <thead>
                            <tr>
                                <th class="py-3" style="min-width:20px"></th>
                                <th class="py-3" style="min-width: 300px;">Producto</th>
                                <th class="text-center py-3" style="min-width: 160px;">Precio</th>
                                <th class="text-center py-3" style="min-width: 160px;">Cantidad</th>
                                <th class="text-center py-3" style="min-width: 160px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cart as $item)
                                <tr>
                                    <td class="h6">
                                        <a href="/cart?remove={{ $item->id }}">❌</a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            {!! img($item->attributes->get('photo_url'), 75, 75, false, 'img-fluid avatar avatar-small rounded shadow', $item->name, 64, 64) !!}
                                            <h6 class="mb-0 ml-3">
                                                <a href="{{ $item->attributes->get('canonical_url') }}" class="text-dark" title="{{ $item->name }}">{{ $item->name }}</a>
                                            </h6>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ format_amount($item->price) }}</td>
                                    <td class="text-center">
                                        <input type="button" value="-" class="minus btn btn-icon btn-soft-primary font-weight-bold">
                                        <input type="text" step="1" min="1" name="quantity[{{ $item->id }}]" value="{{ $item->quantity }}" title="Qty" class="btn btn-icon btn-soft-primary font-weight-bold">
                                        <input type="button" value="+" class="plus btn btn-icon btn-soft-primary font-weight-bold">
                                    </td>
                                    <td class="text-center font-weight-bold">{{ format_amount($item->getPriceSum()) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-6 mt-4 pt-2">
                        <a href="{{ url()->previous() }}" class="btn btn-block btn-soft-secondary"><span class="fa fa-chevron-left"></span> Seguir comprando </a>
                    </div>
                    <div class="col-lg-3 col-6 mt-4 pt-2">
                        <button type="submit" class="btn btn-block btn-soft-primary">Actualizar cesta <span class="fa fa-redo"></span></button>
                    </div>
                    <div class="col-lg-6 col-md-12 ml-auto mt-4 pt-2">
                        <div class="table-responsive bg-white rounded shadow">
                            <table class="table table-center table-padding mb-0">
                                <tbody>
                                    <tr>
                                        <td class="h6">Subtotal</td>
                                        <td class="text-center font-weight-bold">{{ format_amount(Cart::getSubTotal()) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="h6">📦 Gastos de envío</td>
                                        <td class="text-center font-weight-bold">¡Gratis!</td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td class="h6">Total a pagar (IVA incluído)</td>
                                        <td class="text-center text-primary h4">{{ format_amount(Cart::getTotal()) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 pt-2 text-right">
                            <a href="/checkout" class="btn btn-primary">Completa el pago <span class="fa fa-arrow-right"></span></a>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center">
                    <div class="h1">📦</div>
                    <div class="h3">Tu cesta está vacía.</div>
                    <a href="/random" class="btn btn-light">Ver producto random 🤩</a>
                </div>
            @endif
        </form>
    </section>
@endsection