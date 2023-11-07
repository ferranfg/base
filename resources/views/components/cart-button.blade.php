<div class="d-none d-sm-inline-block">
    @if (isset($cart) and $cart->count())
        <div class="dropdown">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-shopping-cart align-middle icons"></i> ({{ $cart->count() }})
            </button>
            <div class="dropdown-menu dropdown-menu-right bg-white shadow rounded border-0 mt-3 p-4" style="width: 300px;">
                @foreach ($cart as $item)
                    <div class="pb-4">
                        <div class="media align-items-center">
                            {!! img($item->attributes->get('photo_url'), 75, 75, true, 'shadow rounded lazy', $item->name, 64, 64)!!}
                            <div class="media-body text-left ml-3">
                                <h6 class="mb-0">
                                    <a href="{{ $item->attributes->get('canonical_url') }}" class="text-dark" title="{{ $item->name }}">{{ $item->name }}</a>
                                </h6>
                                <p class="text-muted mb-0">{{ $item->quantity }} unidad/es</p>
                            </div>
                            <h6 class="text-dark mb-0">{{ format_amount($item->getPriceSum()) }}</h6>
                        </div>
                    </div>
                @endforeach
                <div class="media align-items-center justify-content-between pt-4 border-top">
                    <h6 class="text-muted mb-0">📦 Gastos de envío</h6>
                    <h6 class="text-muted mb-0">¡Gratis!</h6>
                </div>
                <div class="media align-items-center justify-content-between pt-2">
                    <h6 class="text-dark mb-0">Total (IVA incluído):</h6>
                    <h6 class="text-dark mb-0">{{ format_amount(Cart::getTotal()) }}</h6>
                </div>
                <div class="mt-3 text-center">
                    <a href="/cart" class="btn btn-light mr-2">Cesta 📦</a>
                    <a href="/checkout" class="btn btn-primary">Pagar <span class="fa fa-arrow-right"></span></a>
                </div>
            </div>
        </div>
    @else
        <a href="/cart" class="btn btn-primary">
            <i class="fa fa-shopping-cart align-middle icons"></i>
        </a>
    @endif
</div>