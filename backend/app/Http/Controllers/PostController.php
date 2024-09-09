<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Track;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function index($theme_id, Request $request) :JsonResponse
    {
        $posts = Post::with(['user', 'track'])
            ->where('theme_id', $theme_id)
            ->orderBy('likes', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        if ($request->user()) {
            $likedPosts = $request->user()->likedPosts->pluck('id')->toArray();
        }

        return response()->json(['posts' => $posts, 'likedPosts' => $likedPosts]);
    }

    public function store($theme_id, Request $request): JsonResponse
    {
        $data = $request->validate([
            'spotify_track_id' => 'required|string',
            'track_name' => 'required|string',
            'artist_name' => 'required|string',
            'album_name' => 'required|string',
            'album_image_url' => 'required|string',
            'uri' => 'required|string',
        ]);

        $track = Track::firstOrCreate(
            ['spotify_track_id' => $data['spotify_track_id']],
            $data
        );

        $post = Post::create([
            'user_id' => Auth::id(),
            'track_id' => $track->id,
            'theme_id' => $theme_id,
        ]);

        return response()->json($post);
    }
    public function like(Request $request, $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        $user = $request->user();

        // すでにいいねしているかチェック
        if ($post->likes()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Already liked'], 400);
        }

        $post->likes()->attach($user->id);
        $post->increment('likes');
        return response()->json(['likes' => $post->likes]);
    }

    public function unlike(Request $request, $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        $user = $request->user();

        // いいねを取り消す
        if ($post->likes()->where('user_id', $user->id)->exists()) {
            $post->likes()->detach($user->id);
            $post->decrement('likes');
        }

        return response()->json(['likes' => $post->likes]);
    }

    public function destroy($id): JsonResponse
    {
        $post = Post::findOrFail($id);
        Log::error(\auth()->user()->id);

        // 現在のユーザーが投稿の所有者であることを確認
        if (auth()->user()->id !== $post->user_id) {
            return response()->json(['error' => 'You can only delete your own posts.'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully.'], 200);
    }


}
