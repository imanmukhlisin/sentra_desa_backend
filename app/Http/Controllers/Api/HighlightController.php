<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Highlight;
use Illuminate\Http\JsonResponse;

class HighlightController extends Controller
{
    public function index(): JsonResponse
    {
        $highlights = Highlight::published()
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $highlights,
        ]);
    }
}
