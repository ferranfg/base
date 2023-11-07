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
                                    <tr>
                                        <td class="small">Donaremos <b>el 1% de tu compra</b> ({{ format_amount(bcdiv(Cart::getTotal(), 100)) }}) para ayudar a eliminar el CO₂ de la atmósfera. <a href="https://climate.stripe.com/RsU174" target="_blank" rel="noreferrer nofollow">Más información.</a></td>
                                        <td class="text-center"><svg class="ProductIcon ProductIcon--Climate" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><title>Climate logo</title><path d="M33.24 21.09c-4.28 0-9.09-2.96-13.24-5.81-4.4-3.04-9.24-7.05-13.24-7.05C2.68 8.23 0 11.96 0 15.28v.28a20 20 0 1 0 40 0c-.13 2.68-2.03 5.53-6.76 5.53z" fill="url(#product-icon-climate-ProductHeroCaption-a)"></path><path d="M33.24 8.24c-4 0-8.84 4-13.24 7.04-4.15 2.85-8.96 5.8-13.24 5.8-4.73 0-6.63-2.84-6.76-5.52a20 20 0 1 0 40 0v-.28c0-3.32-2.67-7.05-6.76-7.04z" fill="url(#product-icon-climate-ProductHeroCaption-b)"></path><path d="M20 15.28c4.15 2.85 8.96 5.8 13.24 5.8 4.73 0 6.63-2.84 6.76-5.52a20 20 0 1 1-40 0c.13 2.68 2.03 5.53 6.76 5.53 4.28 0 9.09-2.96 13.24-5.81z" fill="url(#product-icon-climate-ProductHeroCaption-c)"></path><defs><linearGradient id="product-icon-climate-ProductHeroCaption-a" x1="20" y1="20.63" x2="20" y2="9.57" gradientUnits="userSpaceOnUse"><stop stop-color="#FFD748"></stop><stop offset=".21" stop-color="#FFD644"></stop><stop offset=".33" stop-color="#FFD438"></stop><stop offset=".45" stop-color="#FFD024"></stop><stop offset=".57" stop-color="#FFCB09"></stop><stop offset="1" stop-color="#FFC900"></stop></linearGradient><linearGradient id="product-icon-climate-ProductHeroCaption-b" x1="20" y1="9.56" x2="20" y2="21.9" gradientUnits="userSpaceOnUse"><stop stop-color="#009C00"></stop><stop offset="1" stop-color="#00BA18"></stop></linearGradient><linearGradient id="product-icon-climate-ProductHeroCaption-c" x1="20" y1="35.28" x2="20" y2="15.28" gradientUnits="userSpaceOnUse"><stop offset=".13" stop-color="#00CB1B"></stop><stop offset="1" stop-color="#00D924"></stop></linearGradient></defs></svg></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 pt-2 text-right">
                            @if (Cart::getTotal() < config('pistol.amount_min_order'))
                                <div class="float-right" data-toggle="tooltip" title="Necesitas hacer un pedido mínimo de {{ str_replace(' ', ' ', format_amount(config('pistol.amount_min_order'))) }} para poder completar el pago">
                                    <button type="button" class="btn btn-primary" style="pointer-events:none" disabled>Completa el pago <span class="fa fa-arrow-right"></span></button>
                                </div>
                            @else
                                <a href="/checkout" class="btn btn-primary">Completa el pago <span class="fa fa-arrow-right"></span></a>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center">
                    <div class="h1">🙈</div>
                    <div class="h3">Tu cesta está vacía.</div>
                    <a href="/random" class="btn btn-light">Ver producto random 🤩</a>
                </div>
            @endif
        </form>
    </section>
@endsection