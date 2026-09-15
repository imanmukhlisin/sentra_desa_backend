<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GeospatialController extends Controller
{
    /**
     * Get all provinces
     */
    public function provinces()
    {
        $provinces = DB::table('provinces')
            ->select('id', 'name', 'code')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $provinces
        ]);
    }

    /**
     * Get regencies by province
     */
    public function regencies($provinceId)
    {
        $regencies = DB::table('regencies')
            ->where('province_id', $provinceId)
            ->select('id', 'name', 'code', 'province_id')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $regencies
        ]);
    }

    /**
     * Get districts by regency
     */
    public function districts($regencyId)
    {
        $districts = DB::table('districts')
            ->where('regency_id', $regencyId)
            ->select('id', 'name', 'code', 'regency_id')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $districts
        ]);
    }

    /**
     * Get villages by district
     */
    public function villages($districtId)
    {
        $villages = DB::table('villages')
            ->where('district_id', $districtId)
            ->select('id', 'name', 'code', 'district_id', 'area_size', 'population')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $villages
        ]);
    }

    /**
     * Get village detail
     */
    public function villageDetail($villageId)
    {
        $village = DB::table('villages')
            ->where('id', $villageId)
            ->orWhere('code', $villageId)
            ->first();
        
        if (!$village) {
            return response()->json([
                'status' => 'error',
                'message' => 'Village not found'
            ], 404);
        }

        // Load relations
        $village->district = DB::table('districts')->where('id', $village->district_id)->first();
        if ($village->district) {
            $village->district->regency = DB::table('regencies')->where('id', $village->district->regency_id)->first();
            if ($village->district->regency) {
                $village->district->regency->province = DB::table('provinces')->where('id', $village->district->regency->province_id)->first();
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $village
        ]);
    }
}
