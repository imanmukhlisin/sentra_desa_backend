<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExportProduct;
use Illuminate\Http\Request;

class ExportProductController extends Controller
{
    /**
     * Get all export products
     */
    public function index(Request $request)
    {
        $query = ExportProduct::with(['village.district.regency.province'])
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

        if ($request->filled('destination_country')) {
            $query->whereJsonContains('destination_countries', $request->destination_country);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $exportProducts = $query->latest()->paginate($request->input('per_page', 12));

        return response()->json([
            'status' => 'success',
            'data' => $exportProducts,
        ]);
    }

    /**
     * Get export product detail by slug or ID
     */
    public function show($slugOrId)
    {
        $query = ExportProduct::with(['village.district.regency.province']);
        $exportProduct = is_numeric($slugOrId)
            ? $query->where('id', $slugOrId)->first()
            : $query->where('slug', $slugOrId)->first();

        if (!$exportProduct) {
            return response()->json([
                'status' => 'error',
                'message' => 'Export product not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $exportProduct,
        ]);
    }
}
