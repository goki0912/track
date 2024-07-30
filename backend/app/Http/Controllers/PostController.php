<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Track;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index() :JsonResponse
    {
        $posts = Post::with(['user', 'track'])->orderBy('created_at', 'desc')->get();

        return response()->json($posts);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'spotify_track_id' => 'required|string',
            'track_name' => 'required|string',
            'artist_name' => 'required|string',
            'album_name' => 'required|string',
            'album_image_url' => 'required|string',
        ]);

        $track = Track::firstOrCreate(
            ['spotify_track_id' => $data['spotify_track_id']],
            $data
        );

        $post = Post::create([
            'user_id' => Auth::id(),
            'track_id' => $track->id,
        ]);

        return response()->json($post);
    }
}
