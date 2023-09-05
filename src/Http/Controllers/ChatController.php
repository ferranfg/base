<?php

namespace Ferranfg\Base\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ferranfg\Base\Clients\Unsplash;
use Ferranfg\Base\Models\Assistance;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ChatController extends Controller
{
    use ValidatesRequests;

    /**
     * Muestra la página de lista de espera mientras desarrollo el chat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        abort_unless(config('base.assistance_enabled'), 404);

        return view('base::chat.index', [
            'input' => $request->input,
            'header' => Unsplash::randomFromCollections()->pluck('urls.regular')->random()
        ]);
    }

    /**
     * Endpoint que recibe la llamada del chat y llama a OpenAI
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function message(Request $request)
    {
        abort_unless(config('base.assistance_enabled'), 404);

        $this->validate($request, [
            'input' => 'required|string'
        ]);

        $assistance = Assistance::completion($request->input);

        return response()->json([
            'text' => $assistance->choices[0]->message->content
        ]);
    }
}