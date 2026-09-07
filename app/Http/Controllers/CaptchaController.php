<?php

namespace App\Http\Controllers;

use App\Services\CaptchaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    /**
     * Refresh the security challenge and return fresh SVG.
     */
    public function refresh(Request $request): JsonResponse
    {
        $challenge = CaptchaService::generate();

        return response()->json([
            'success' => true,
            'svg' => $challenge['svg'],
        ]);
    }
}
