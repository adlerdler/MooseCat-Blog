<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use Illuminate\Http\JsonResponse;

class SocialLinkController extends Controller
{
    public function index(): JsonResponse
    {
        $socialLinks = SocialLink::active()->get();

        return response()->json([
            'data' => $socialLinks,
        ]);
    }
}
