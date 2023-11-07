@extends(config('base.shop_template'))

@push('styles')
    <script src="https://js.stripe.com/v3/"></script>
@endpush

@section('header')

@endsection

@section('content')
    <script type="text/javascript">
        Stripe("{{ config('cashier.key') }}").redirectToCheckout({
            sessionId: "{{ $session->id }}"
        });
    </script>
@endsection

@section('footer')

@endsection