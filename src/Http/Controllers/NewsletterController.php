<?php

namespace Ferranfg\Base\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ferranfg\Base\Events\DiscordMessage;
use Ferranfg\Base\Repositories\UserRepository;
use Ferranfg\Base\Notifications\WelcomeNewsletter;
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
        $this->validate($request, [
            'email' => 'required|email|unique:users,email',
            'terms' => 'accepted'
        ]);

        $user = $this->userRepository->create([
            'email' => $request->email,
            'name' => (string) null,
            'password' => (string) null
        ]);

        $user->notify(new WelcomeNewsletter);

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

        activity()->performedOn($user)->log('unsubscribed');

        event(new DiscordMessage('UserUnsubscribed', ['email' => $user->email]));

        return redirect('/')->with('info', '
            <p class="mb-0">You have been unsubscribed from our newsletter 😥</p>
        ');
    }
}