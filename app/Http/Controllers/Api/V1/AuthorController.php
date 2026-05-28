<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AuthorProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthorController extends Controller
{
    public function index(): JsonResponse
    {
        $profiles = AuthorProfile::where('is_active', true)
            ->with('user')
            ->get();

        return response()->json([
            'data' => $profiles,
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $profile = AuthorProfile::where('slug', $slug)
            ->where('is_active', true)
            ->with('user.posts')
            ->firstOrFail();

        return response()->json([
            'data' => $profile,
        ]);
    }
}
