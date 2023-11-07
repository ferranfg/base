@extends(config('base.shop_template'))

@section('index')
    <meta name="robots" content="noindex">
@endsection

@section('content')
    <section class="bg-half bg-dark d-table w-100" style="background-image:url('/images/saas/home-shape.webp')">
        <div class="justify-content-center text-center text-white">
            <div class="page-next-level">
                <h1 class="title">¡Gracias! 😍😍</h1>
                <div class="page-next">
                    <nav class="d-inline-block">
                        <ul class="breadcrumb bg-white rounded shadow mb-0">
                            <li class="breadcrumb-item"><a href="/">Inicio</a></li>
                            <li class="breadcrumb-item active">Pedido completado</li>
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
            <div class="row">
                <div class="col-lg-7">
                    <h3 class="mb-4"><span class="text-muted">🔖 Tu identificador es el n.º</span> {{ $receipt->receipt_number }}</h3>
                    <p>Hemos recibido tu pedido y en breve nos pondremos a preparlo. El plazo de entrega estimado será de 5 días laborables 📦</p>
                    <p class="mb-4">Para tu comodidad, te recomendamos que guardes tu número de identificación.</p>
                    <div class="form-group row mb-4">
                        <label class="col-md-4 mb-0 text-md-right">Número de pedido 🔖</label>
                        <div class="col-md-6">{{ $receipt->receipt_number }}</div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-md-4 mb-0 text-md-right">Fecha de confirmación 📅</label>
                        <div class="col-md-6">{{ \Carbon\Carbon::parse($receipt->created) }}</div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-md-4 mb-0 text-md-right">Correo electrónico ✉️</label>
                        <div class="col-md-6">{{ $receipt->receipt_email }}</div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-md-4 mb-0 text-md-right">Dirección de entrega 🏠</label>
                        <div class="col-md-6">
                            <div>{{ $receipt->shipping->name }}</div>
                            <div>{{ $receipt->shipping->address->line1 }} {{ $receipt->shipping->address->line2 }}</div>
                            <div>{{ $receipt->shipping->address->postal_code }} {{ $receipt->shipping->address->city }}</div>
                        </div>
                    </div>
                    @php /*
                    @if (auth()->check())
                        <div class="row">
                            <div class="col-md-6 offset-md-4">
                                <a href="/home" class="btn btn-primary"><i class="fa fa-user"></i> Mi cuenta</a>
                            </div>
                        </div>
                    @else
                        <spark-register-stripe customer="{{ $session->customer }}" inline-template>
                            <div>
                                <hr class="my-4" />
                                <h3 class="mb-4 text-muted">🧐 Regístrate para seguir tu pedido</h3>
                                <p class="mb-4">Si quieres, puedes registrarte como usuario para consultar el estado de tus pedidos y, además, disfrutar de nuestras ofertas especiales para usuarios registrados 🎉.</p>
                                @include('spark::auth.register-common-form')
                            </div>
                        </spark-register-stripe>
                    @endif
                    */ @endphp
                </div>
                <div class="col-lg-5 mt-4 mt-sm-0 pt-2 pt-sm-0">
                    <div class="rounded shadow-lg p-4">
                        <h5 class="mb-3">Resumen de tu pedido:</h5>
                        <div class="table-responsive">
                            <table class="table table-center table-padding mb-0">
                                <tbody>
                                    <tr>
                                        <td class="h6 border-0">Subtotal</td>
                                        <td class="text-center font-weight-bold border-0">
                                            {{ format_amount($session->amount_subtotal) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="h6">📦 Gastos de envío</td>
                                        <td class="text-center font-weight-bold">¡Gratis!</td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td class="h5 font-weight-bold">Total (IVA incluído)</td>
                                        <td class="text-center text-primary h4 font-weight-bold">
                                            {{ format_amount($session->amount_total) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <form method="POST" action="/invoice?session_id={{ $session->id }}" class="mt-4 pt-2">
                                @csrf
                                <button type="submit" class="btn btn-block btn-primary">Ver recibo 🖨️</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ mix('js/app.js') }}"></script>
@endpush