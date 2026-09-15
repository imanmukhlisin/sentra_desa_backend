<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VillageContent;
use Illuminate\Http\Request;

class VillageContentController extends Controller
{
    public function index(Request $request)
    {
        $query = VillageContent::with(['village.district.regency.province']);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('village_id')) {
            $query->where('village_id', $request->village_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $contents = $query->latest()->paginate($request->input('per_page', 12));

        return response()->json([
            'status' => 'success',
            'data' => $contents,
        ]);
    }

    /**
     * Get content detail by slug or ID
     */
    public function show($slugOrId)
    {
        $query = VillageContent::with(['village.district.regency.province']);
        $content = is_numeric($slugOrId)
            ? $query->where('id', $slugOrId)->first()
            : $query->where('slug', $slugOrId)->first();

        if (!$content) {
            return response()->json([
                'status' => 'error',
                'message' => 'Content not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $content,
        ]);
    }
}