<?php

namespace Ferranfg\Base\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ferranfg\Base\Repositories\UserRepository;
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

    public function subscribe(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users,email',
        ]);

        $this->userRepository->create([
            'email' => $request->email,
            'name' => (string) null,
            'password' => (string) null
        ]);

        return response()->json([
            'success' => true
        ]);
    }
}