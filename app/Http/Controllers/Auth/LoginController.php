<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Socialite;
use Illuminate\Support\Str;

class LoginController extends Controller
{

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        $state = Str::random(10);
        return Socialite::driver('spotify')
            ->setScopes(['streaming', 'user-read-playback-state', 'user-read-private', 'user-read-email',
            'user-modify-playback-state', 'playlist-modify-public', 'playlist-modify-private'])
            ->with(['show_dialog' => 'true'])
            ->stateless()
            ->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $token = Socialite::driver('spotify')->stateless()->user()->accessTokenResponseBody;
        return redirect('http://localhost:8080?'.http_build_query($token));
    }
}