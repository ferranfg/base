<?php

namespace Ferranfg\Base\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ferranfg\Base\Events\DiscordMessage;
use Ferranfg\Base\Repositories\UserRepository;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Validation\ValidatesRequests;

class NewsletterController extends Controller
{
    use ValidatesRequests;

    public $userRepository;

    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Endpoint del formulario de registro.
     *
     * @return Response
     */
    public function subscribe(Request $request)
    {
        if ($request->has('from')) $request->merge([
            'email' => $request->from,
            'terms' => true,
        ]);

        $this->validate($request, [
            'email' => 'required|email|unique:users,email',
            'terms' => 'accepted'
        ]);

        $user = $this->userRepository->create([
            'email' => $request->email,
            'name' => (string) null,
            'password' => (string) null
        ]);

        $user->unsubscribed_at = null;
        $user->save();

        $user->notify(new (config('base.newsletter_notification')));

        activity()->performedOn($user)->log('subscribed');

        event(new DiscordMessage('UserSubscribed', ['email' => $user->email]));

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Página destino para darse de baja de la newsletter.
     *
     * @return Response
     */
    public function unsubscribe(Request $request)
    {
        try
        {
            $user = $this->userRepository->find(decrypt($request->token));
        }
        catch (DecryptException $e)
        {
            return redirect('cancel');
        }

        if (is_null($user)) return redirect('cancel');

        $user->unsubscribed_at = Carbon::now();
        $user->save();

        event(new DiscordMessage('UserUnsubscribed', ['email' => $user->email]));

        return redirect('/')->with('info', '
            <p class="mb-0">You have been unsubscribed from our newsletter 😥</p>
        ');
    }
}