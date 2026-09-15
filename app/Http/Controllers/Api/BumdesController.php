<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bumdes;
use Illuminate\Http\Request;

class BumdesController extends Controller
{
    /**
     * Get all BUMDes
     */
    public function index(Request $request)
    {
        $query = Bumdes::with(['village.district.regency.province'])
            ->where('is_active', true);

        // Wilayah cascade filters
        if ($request->filled('province_id')) {
            $query->whereHas('village.district.regency', fn ($q) =>
                $q->where('province_id', $request->province_id)
            );
        }

        if ($request->filled('regency_id')) {
            $query->whereHas('village.district', fn ($q) =>
                $q->where('regency_id', $request->regency_id)
            );
        }

        if ($request->filled('district_id')) {
            $query->whereHas('village', fn ($q) =>
                $q->where('district_id', $request->district_id)
            );
        }

        if ($request->filled('village_id')) {
            $query->where('village_id', $request->village_id);
        }

        if ($request->filled('category')) {
            $query->where('performance_category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $bumdes = $query->latest()->paginate($request->input('per_page', 12));

        return response()->json([
            'status' => 'success',
            'data' => $bumdes,
        ]);
    }

    /**
     * Get BUMDes detail by slug or ID
     */
    public function show($slugOrId)
    {
        $query = Bumdes::with(['village.district.regency.province']);
        $bumdes = is_numeric($slugOrId)
            ? $query->where('id', $slugOrId)->first()
            : $query->where('slug', $slugOrId)->first();

        if (!$bumdes) {
            return response()->json([
                'status' => 'error',
                'message' => 'BUMDes not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $bumdes,
        ]);
    }
}
