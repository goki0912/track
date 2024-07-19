<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SpotifyController extends Controller
{
    public function getAuthUrl(): \Illuminate\Http\JsonResponse
    {
        $clientId = env('SPOTIFY_CLIENT_ID');
        $redirectUri = env('SPOTIFY_REDIRECT_URI');
        $scopes = 'user-read-private user-read-email';

        $url = 'https://accounts.spotify.com/authorize?' . http_build_query([
                'response_type' => 'code',
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'scope' => $scopes,
            ]);

        return response()->json(['url' => $url]);
    }

    public function handleCallback(Request $request)
    {
        $code = $request->query('code');

        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => env('SPOTIFY_REDIRECT_URI'),
            'client_id' => env('SPOTIFY_CLIENT_ID'),
            'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
        ]);

        $data = $response->json();

        // アクセストークンを取得
        $accessToken = $data['access_token'];
        // フロントエンドにJSON形式でトークンを返す
        return response()->json(['access_token' => $accessToken]);
    }
    public function getUserProfile(Request $request): \Illuminate\Http\JsonResponse
    {
        $accessToken = $request->header('Authorization');

        $response = Http::withHeaders([
            'Authorization' => $accessToken,
        ])->get('https://api.spotify.com/v1/me');

        return response()->json($response->json());
    }

}
