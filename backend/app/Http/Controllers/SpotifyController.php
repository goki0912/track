<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SpotifyController extends Controller
{
    private $client_id;
    private $client_secret;
    private $redirect_uri;

    public function __construct()
    {
        $this->client_id = env('SPOTIFY_CLIENT_ID');
        $this->client_secret = env('SPOTIFY_CLIENT_SECRET');
        $this->redirect_uri = env('SPOTIFY_REDIRECT_URI');
    }

    public function getAuthUrl(): \Illuminate\Http\JsonResponse
    {
//        $clientId = env('SPOTIFY_CLIENT_ID');
//        $redirectUri = env('SPOTIFY_REDIRECT_URI');
        $scopes = 'user-read-private user-read-email';

        $url = 'https://accounts.spotify.com/authorize?' . http_build_query([
                'response_type' => 'code',
                'client_id' => $this->client_id,
                'redirect_uri' => $this->redirect_uri,
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
            'redirect_uri' => $this->redirect_uri,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
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

    private function getAccessToken()
    {
        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
        ]);

        return $response->json()['access_token'];
    }

    public function searchTrack(Request $request)
    {
        $query = $request->query('query');


        $accessToken = $this->getAccessToken();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get('https://api.spotify.com/v1/search', [
            'q' => $query,
            'type' => 'track',
            'limit' => 10, // 検索結果の最大数を指定
        ]);

        return response()->json($response->json());
    }

}
