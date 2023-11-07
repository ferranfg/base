<?php

namespace Ferranfg\Base\Http\Controllers;

use Stripe\PaymentIntent;
use Stripe\Checkout\Session;
use Illuminate\Http\Request;
use Ferranfg\Base\Models\Cart;
use Stripe\Exception\InvalidRequestException;

class CheckoutController extends ShopController
{
    /**
     * Show the shopping cart screen.
     *
     * @return Response
     */
    public function cart(Request $request)
    {
        // Remove item from cart
        if ($request->has('remove')) Cart::remove('cart', (int) $request->remove);

        // Updates the shopping cart screen.
        if ($request->has('quantity'))
        {
            foreach ($request->quantity as $id => $quantity)
            {
                Cart::updateQuantity('cart', $id, $quantity);
            }
        }

        view()->share('cart', Cart::init('cart'));

        return view('base::checkout.cart');
    }

    /**
     * Redirects the user to the Stripe Checkout page.
     *
     * @return Response
     */
    public function redirect()
    {
        try
        {
            $line_items = Cart::init('cart')->map(function ($item)
            {
                return [
                    'quantity' => $item->quantity,
                    'price' => $item->model->stripePriceId()
                ];
            });

            $metadata = Cart::init('cart')->map(function ($item)
            {
                return [
                    'quantity' => $item->quantity,
                    'product_id' => $item->model->id
                ];
            });

            $session = Session::create([
                'customer' => auth()->check() ? auth()->user()->stripe_id : null,
                'payment_method_types' => ['card'],
                'shipping_address_collection' => [
                    'allowed_countries' => ['ES'],
                ],
                'line_items' => $line_items->values()->toArray(),
                'mode' => 'payment',
                'success_url' => url('success?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => url('cancel?session_id={CHECKOUT_SESSION_ID}'),
                'locale' => config('app.locale'),
                'metadata' => [
                    'cart' => json_encode($metadata->values())
                ]
            ]);

            return view('base::checkout.redirect', [
                'session' => $session
            ]);
        }
        catch (InvalidRequestException $e)
        {
            report($e);

            return redirect('cancel');
        }
    }

    /**
     * Show the checkout success screen.
     *
     * @return Response
     */
    public function success(Request $request)
    {
        abort_if(is_null($request->session_id), 404);

        try
        {
            $session = Session::retrieve($request->session_id);
        }
        catch (InvalidRequestException $e)
        {
            report($e);

            return redirect('cancel');
        }

        if (Cart::clear()) view()->share('cart', Cart::init('cart'));

        $payment = PaymentIntent::retrieve($session->payment_intent);

        return view('base::checkout.success', [
            'session' => $session,
            'payment' => $payment,
            'receipt' => $payment->charges ? $payment->charges->first() : (object) [
                'receipt_number' => '',
                'created' => '',
                'receipt_email' => '',
            ],
        ]);
    }

    /**
     * Show the checkout stripe invoice screen.
     *
     * @return Response
     */
    public function invoice(Request $request)
    {
        try
        {
            $session = Session::retrieve($request->get('session_id'));
        }
        catch (InvalidRequestException $e)
        {
            report($e);

            return redirect('cancel');
        }

        $payment = PaymentIntent::retrieve($session->payment_intent);
        $receipt = collect($payment->charges->data)->first();

        return redirect($receipt->receipt_url);
    }

    /**
     * Show the checkout error screen.
     *
     * @return Response
     */
    public function cancel()
    {
        return redirect('/')->with('info', '
            <h4 class="alert-heading">Tu solicitud no se ha completado 😥</h4>
            <p>El proceso no ha sido completado correctamente. Puede tratarse de una cancelación por tu parte o un problema relacionado con los datos introducidos.</p>
            <hr />
            <p class="mb-0">Si crees que se trata de un error, por favor, contacta conmigo en ✉️ <a href="mailto:hola@ferranfigueredo.com" class="text-white">hola@ferranfigueredo.com</a></p>
        ');
    }
}