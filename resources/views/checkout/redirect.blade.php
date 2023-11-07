@extends(config('base.shop_template'))

@push('styles')
    <script src="https://js.stripe.com/v3/"></script>
@endpush

@section('content')
    <div class="bg-dark py-5">
        <div class="text-center my-5 p-5 text-white">Estás siendo redirigido a la página de pago…</div>
        <script type="text/javascript">
            Stripe("{{ config('services.stripe.key') }}").redirectToCheckout({
                sessionId: "{{ $session->id }}"
            });
        </script>
    </div>
@endsection