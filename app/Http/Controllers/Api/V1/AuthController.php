<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * 用户登录
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        // 防止时序攻击：如果用户不存在，则使用一个格式合法的虚拟哈希值让 Hash::check 运行以对齐耗时
        $passwordHash = $user ? $user->password : '$2y$10$I6/pG5wV/lPex3wWbK3Qx.c3V9y7T9V9m5d1n9o8p7q6r5s4t3u2v';

        if (! $user || ! Hash::check($request->password, $passwordHash)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        // 记录最后登录时间
        $user->update(['last_login_at' => now()]);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->pluck('name')->first() ?? 'user',
            ],
        ], 200);
    }

    /**
     * 用户登出
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ], 200);
    }

    /**
     * 获取当前用户信息
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->pluck('name')->first() ?? 'user',
            ],
        ], 200);
    }
}
