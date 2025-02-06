<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SpotifyController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ThemeController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/spotify/auth-url', [SpotifyController::class, 'getAuthUrl']);
    Route::get('/spotify/callback', [SpotifyController::class, 'handleCallback']);
    Route::get('/spotify/user-profile', [SpotifyController::class, 'getUserProfile']);
    Route::post('/spotify/refresh-token', [SpotifyController::class, 'refreshToken']);

    Route::get('spotify/search', [SpotifyController::class, 'searchTrack']);

    Route::get('spotify/theme/{theme_id}/posts', [PostController::class, 'index']);

    Route::post('spotify/theme/{theme_id}/posts', [PostController::class, 'store']);
    Route::delete('spotify/posts/{id}', [PostController::class, 'destroy']);

    Route::post('/posts/{id}/like', [PostController::class, 'like']);
    Route::post('/posts/{id}/unlike', [PostController::class, 'unlike']);

    Route::get('/spotify/devices', [SpotifyController::class, 'getDevices']);
    Route::post('spotify/play-track', [SpotifyController::class, 'playTrack']);

    //全テーマ取得
    Route::get('spotify/themes', [ThemeController::class, 'index']);
});

//メールの画像取得用、api/imagesは意味不明だけどこれが楽なんです
Route::get('/images/{filename}', static function ($filename)
{
    $path = public_path('images/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
});


