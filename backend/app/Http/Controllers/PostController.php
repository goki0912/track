<?php

namespace App\Http\Controllers;

use App\Events\PostUpdated;
use App\Models\Post;
use App\Models\Track;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class PostController extends Controller
{
    public function index($theme_id, Request $request) :JsonResponse
    {
        $posts = Post::with(['user', 'track'])
            ->where('theme_id', $theme_id)
            ->orderBy('likes_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        if ($request->user()) {
            $likedPosts = $request->user()->likedPosts->pluck('id')->toArray();
        }

        return response()->json(['posts' => $posts, 'likedPosts' => $likedPosts]);
    }

    public function store($theme_id, Request $request): JsonResponse
    {
        try {
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

            // 現在のユーザーIDを取得
            $userId = Auth::id();

            // 1. user_id と theme_id の組み合わせがユニークかどうかをチェック
            if (Post::where('user_id', $userId)
                ->where('theme_id', $theme_id)
                ->exists()) {
                return response()->json(['error' => 'You have already submitted a post for this theme.'], 422);
            }

            // 2. track_id と theme_id の組み合わせがユニークかどうかをチェック
            if (Post::where('track_id', $track->id)
                ->where('theme_id', $theme_id)
                ->exists()) {
                return response()->json(['error' => 'This track is already registered for this theme.'], 422);
            }

            $post = Post::create([
                'user_id' => $userId,
                'track_id' => $track->id,
                'theme_id' => $theme_id,
            ]);

            return response()->json($post);

        } catch (QueryException $e) {
            // エラーコードを取得
            $errorCode = $e->errorInfo[1];

            if ($errorCode === 1062) { // MySQL の場合
                return response()->json(['error' => 'Duplicate entry detected.'], 422);
            }
            // その他のデータベースエラー
            Log::error($e);
            return response()->json(['error' => 'Database error occurred.'], 500);
        } catch (\Exception $e) {
            // その他のエラー
            Log::error($e);
            return response()->json(['error' => 'Failed to create post.'], 500);
        }
    }
    public function like(Request $request, $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        $user = $request->user();

        DB::beginTransaction();

        try {
            if (!$post->likes()->where('user_id', $user->id)->exists()) {
                // 中間テーブルにレコードを挿入
                $post->likes()->attach($user->id);
                // いいねを1増やす
                $post->increment('likes_count');
            }

            DB::commit();

            // リアルタイム反映用
            event(new PostUpdated($post->theme_id, $post->id, $post->likes_count));

            return response()->json(['likes_count' => $post->likes_count], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred'], 500);
        }
    }

    public function unlike(Request $request, $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        $user = $request->user();

        DB::beginTransaction();

        try {
            if ($post->likes()->where('user_id', $user->id)->exists()) {
                // 中間テーブルのレコードを削除
                $post->likes()->detach($user->id);
                // いいねを1減らす
                $post->decrement('likes_count');
            }

            DB::commit();

            // リアルタイム反映用
            event(new PostUpdated($post->theme_id, $post->id, $post->likes_count));

            return response()->json(['likes_count' => $post->likes_count], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred'], 500);
        }
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
