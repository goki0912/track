<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    public function getAuthUrl(): JsonResponse
    {
//        もっと色々許可する？
        $scopes = 'user-read-private user-read-email user-modify-playback-state user-read-playback-state';

        $url = 'https://accounts.spotify.com/authorize?' . http_build_query([
                'response_type' => 'code',
                'client_id' => $this->client_id,
                'redirect_uri' => $this->redirect_uri,
                'scope' => $scopes,
            ]);

        return response()->json(['url' => $url]);
    }

    public function handleCallback(Request $request): JsonResponse
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
        $refreshToken = $data['refresh_token'];
        // フロントエンドにJSON形式でトークンを返す
        return response()->json(['access_token' => $accessToken, 'refresh_token' => $refreshToken]);
    }
    public function getUserProfile(Request $request): JsonResponse
    {
        $accessToken = $request->header('spotifyAuthorization');

        try {
            $response = Http::withHeaders([
                'Authorization' => $accessToken,
            ])->get('https://api.spotify.com/v1/me');

            // トークンが無効な場合、401 Unauthorizedを返して知らせる
            if ($response->status() === 401) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (ConnectionException $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Failed to connect to Spotify API'], 500);
        }

        return response()->json($response->json());
    }

    private function getAccessToken()
    {
        // まずキャッシュにトークンが存在するか確認
        if (Cache::has('spotify_access_token')) {
            return Cache::get('spotify_access_token');
        }

        // トークンがキャッシュされていない場合、新しいトークンを取得
        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
        ]);

        $accessToken = $response->json()['access_token'];
        $expiresIn = $response->json()['expires_in']; // トークンの有効期限 (秒単位)

        // トークンをキャッシュに保存し、有効期限に基づいて期限を設定
        Cache::put('spotify_access_token', $accessToken, $expiresIn - 60); // 余裕を持って1分短く設定

        return $accessToken;
    }

    public function searchTrack(Request $request)
    {
        $query = $request->query('query');
        if (!$query) {
            return response()->json(['error' => 'Query parameter is required'], 400);
        }

        $accessToken = $this->getAccessToken();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get('https://api.spotify.com/v1/search', [
                'q' => $query,
                'type' => 'track',
                'limit' => 10, // 検索結果の最大数を指定
                'market' => 'JP', // 日本の楽曲を対象にする
            ]);
        } catch (ConnectionException $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Failed to connect to Spotify API'], 500);
        }

        return response()->json($response->json());
    }

    public function getDevices(Request $request): JsonResponse
    {
        $accessToken = $request->header('spotifyAuthorization');
        try {
            $response = Http::withHeaders([
                'Authorization' => $accessToken,
            ])->get('https://api.spotify.com/v1/me/player/devices');
        } catch (ConnectionException $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Failed to connect to Spotify API'], 500);
        }

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['message' => 'Failed to get devices', 'error' => $response->body()], 500);
    }
    public function playTrack(Request $request): JsonResponse
    {
        $accessToken = $request->header('spotifyAuthorization');
        $trackUri = $request->input('uri');
        $deviceId = $request->input('device_id');

        try {
            $response = Http::withHeaders([
                'Authorization' => $accessToken,
                'Content-Type' => 'application/json',
            ])->withOptions([
                'query' => ['device_id' => $deviceId],
            ])->put('https://api.spotify.com/v1/me/player/play', [
                'uris' => [$trackUri],
            ]);
        } catch (ConnectionException $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Failed to connect to Spotify API'], 500);
        }

        if ($response->successful()) {
            return response()->json(['message' => 'Track is playing'], 200);
        }

        return response()->json(['message' => 'Failed to play track', 'error' => $response->body()], 500);
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $refreshToken = $request->input('refresh_token');

        try {
            $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
            ]);

            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            return response()->json($data);
        } catch (ConnectionException $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Failed to connect to Spotify API'], 500);
        }
    }
}
