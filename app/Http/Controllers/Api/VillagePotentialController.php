<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VillagePotential;
use Illuminate\Http\Request;

class VillagePotentialController extends Controller
{
    /**
     * Get all village potentials
     */
    public function index(Request $request)
    {
        $query = VillagePotential::with(['village.district.regency.province'])
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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $potentials = $query->latest()->paginate($request->input('per_page', 12));

        return response()->json([
            'status' => 'success',
            'data' => $potentials,
        ]);
    }

    /**
     * Get village potentials by village
     */
    public function byVillage($villageId)
    {
        $potentials = VillagePotential::with(['village'])
            ->where('village_id', $villageId)
            ->where('is_active', true)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $potentials,
        ]);
    }

    /**
     * Get single village potential by ID
     */
    public function show(int $id)
    {
        $potential = VillagePotential::with(['village.district.regency.province'])
            ->where('id', $id)
            ->first();

        if (!$potential) {
            return response()->json([
                'status' => 'error',
                'message' => 'Village potential not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $potential,
        ]);
    }
}
