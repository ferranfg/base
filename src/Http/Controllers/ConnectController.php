<?php

namespace Ferranfg\Base\Http\Controllers;

use Exception;
use Ferranfg\Base\Clients\Facebook;
use Ferranfg\Base\Clients\Unsplash;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Socialite\Facades\Socialite;
use League\OAuth1\Client\Credentials\CredentialsException;

class ConnectController extends Controller
{
    use ValidatesRequests;

    /**
     * Redirect user to the Facebook login page.
     *
     * @return Response
     */
    public function facebook()
    {
        if ( ! auth()->check()) return $this->cancel();

        return Socialite::driver('facebook')
            ->scopes(['pages_show_list', 'business_management', 'instagram_basic', 'instagram_content_publish'])
            ->usingGraphVersion('v15.0')
            ->redirect();
    }

    /**
     * Handles the callback from the Facebook login.
     *
     * @return Response
     */
    public function callbackFacebook()
    {
        try
        {
            $facebook = Socialite::driver('facebook')->user();
        }
        catch (CredentialsException $e)
        {
            return redirect('connect');
        }
        catch (Exception $e)
        {
            return $this->cancel();
        }

        $user = auth()->user();
        $user->facebook_id = $facebook->id;
        $user->facebook_token = $facebook->token;
        $user->save();

        return $this->instagram();
    }

    /**
     * Muestra al usuario sus páginas de Instagram
     *
     * @return Response
     */
    public function instagram()
    {
        $user = auth()->user();

        $response = Facebook::graphApi('me/accounts', [
            'fields' => 'name,picture,connected_instagram_account',
            'access_token' => $user->facebook_token
        ]);

        $accounts = collect($response->data)->map(function ($account) use ($user)
        {
            if (property_exists($account, 'connected_instagram_account'))
            {
                $account->connected_instagram_account = Facebook::graphApi($account->connected_instagram_account->id, [
                    'fields' => 'id,name,username,profile_picture_url',
                    'access_token' => $user->facebook_token
                ]);
            }

            return $account;
        });

        return view('base::connect.instagram', [
            'accounts' => $accounts,
            'header' => Unsplash::randomFromCollections()->pluck('urls.regular')->random(),
        ]);
    }

    /**
     * Handles the callback from the Facebook login.
     *
     * @return Response
     */
    public function callbackInstagram(Request $request)
    {
        $this->validate($request, [
            'instagram_id' => 'required'
        ]);

        $user = auth()->user();
        $user->instagram_id = $request->instagram_id;
        $user->save();

        return $this->success();
    }

    /**
     * URL en la que se recibe al usuario cuando se completa el onboarding.
     *
     * @return Response
     */
    public function success()
    {
        return redirect('/')->with('info', '
            <h4 class="alert-heading">You user has been connected to our platform 🥳</h4>
            <p>You\'re now ready to enjoy a seamless and personalized experience. Feel free to explore all the exciting features available to you.</p>
            <hr />
            <p class="mb-0">If you have any comments or suggestions, please contact us.</p>
        ');
    }

    /**
     * URL en la que se envia al usuario cuando se produce un error.
     *
     * @return Response
     */
    public function cancel()
    {
        return redirect('/')->with('info', '
            <h4 class="alert-heading">Your connection has not been completed 😥</h4>
            <p>We encountered an issue while trying to connect your account. It may be a cancellation on your part or a problem related to the data entered.</p>
            <hr />
            <p class="mb-0">If you think this is a mistake, please contact us.</p>
        ');
    }

    /**
     * Uploads a file to the user's Instagram account.
     *
     * @return Response
     */
    public function upload(Request $request)
    {
        $this->validate($request, [
            'image_url' => 'required|url',
        ]);

        $user = auth()->user();

        return Facebook::uploadMedia(
            $user->instagram_id,
            $user->facebook_token,
            $request->image_url
        );
    }
}