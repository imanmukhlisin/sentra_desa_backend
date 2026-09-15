<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VillageService;
use Illuminate\Http\Request;

class VillageServiceController extends Controller
{
    /**
     * Get all village services
     */
    public function index(Request $request)
    {
        $query = VillageService::with(['village.district.regency.province'])
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
            $query->where('category', $request->category);
        }

        $services = $query->latest()->paginate($request->input('per_page', 12));

        return response()->json([
            'status' => 'success',
            'data' => $services,
        ]);
    }

    /**
     * Get village services by village
     */
    public function byVillage($villageId)
    {
        $services = VillageService::with(['village'])
            ->where('village_id', $villageId)
            ->where('is_active', true)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $services,
        ]);
    }

    /**
     * Get single village service by ID
     */
    public function show(int $id)
    {
        $service = VillageService::with(['village.district.regency.province'])
            ->where('id', $id)
            ->first();

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Village service not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $service,
        ]);
    }
}
