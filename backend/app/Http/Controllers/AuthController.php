<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer'], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    /**
     * パスワードリセットリンクの送信
     * @throws ValidationException
     */
    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        // バリデーション：メールアドレスが必須で正しい形式
        $request->validate(['email' => 'required|email']);

        // リセットリンクの送信
        $status = Password::sendResetLink($request->only('email'));

        // ステータスに応じたレスポンスを返す
        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)], 200);
        }

        // リンク送信失敗時の処理
        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    /**
     * パスワードのリセット
     * @throws ValidationException
     */
    public function resetPassword(Request $request): JsonResponse
    {
        // バリデーション：パスワードの確認とトークンが必要
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required',
        ]);

        // パスワードリセット処理
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            static function ($user, $password) {
                // パスワードをハッシュ化して保存
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                // ユーザーのセッションを無効化（セキュリティのため）
                $user->tokens()->delete();
            }
        );

        // ステータスに応じたレスポンスを返す
        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)], 200);
        }

        // リセット失敗時の処理
        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}
