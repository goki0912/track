<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SpotifyController;
use App\Http\Controllers\PostController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/spotify/auth-url', [SpotifyController::class, 'getAuthUrl']);
    Route::get('/spotify/callback', [SpotifyController::class, 'handleCallback']);
    Route::get('/spotify/user-profile', [SpotifyController::class, 'getUserProfile']);

    Route::get('spotify/search', [SpotifyController::class, 'searchTrack']);

    Route::get('spotify/posts', [PostController::class, 'index']);
    Route::post('spotify/posts', [PostController::class, 'store']);

    Route::post('/posts/{id}/like', [PostController::class, 'like']);
    Route::post('/posts/{id}/unlike', [PostController::class, 'unlike']);
});



