<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Socialite;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class LoginController extends Controller
{

    private $scopes = ['streaming', 'user-read-playback-state', 'user-read-private', 'user-read-email',
    'user-modify-playback-state', 'playlist-modify-public', 'playlist-modify-private', 'user-follow-read',
    'user-follow-modify', 'user-top-read', 'user-read-recently-played', 'playlist-read-collaborative',
    'playlist-read-private', 'user-read-currently-playing'];

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
            'user-modify-playback-state', 'playlist-modify-public', 'playlist-modify-private', 'user-follow-read',
            'user-follow-modify', 'user-top-read', 'user-read-recently-played', 'playlist-read-collaborative',
            'playlist-read-private', 'user-read-currently-playing'])
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

    /**
     * Obtain a new token with the refresh token.
     *
     * @return \Illuminate\Http\Redirect
     */
    public function refreshToken(Request $request)
    {
        $header = 'Basic '.base64_encode(env('SPOTIFY_KEY').':'.env('SPOTIFY_SECRET'));
        $client = new Client();
        $response = $client->request('POST', 'https://accounts.spotify.com/api/token', [
            'headers' => ['Authorization' => $header],
            'form_params' =>  [
                'refresh_token' => $request->get('refresh_token'),
                'grant_type' => 'refresh_token'
            ]
        ]);
        return response($response->getBody()->getContents());
    }
}