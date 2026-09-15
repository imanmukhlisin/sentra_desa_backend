<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kdmp;
use Illuminate\Http\Request;

class KdmpController extends Controller
{
    public function index(Request $request)
    {
        $query = Kdmp::with(['village.district.regency.province'])
            ->where('is_active', true);

        if ($request->filled('village_id')) {
            $query->where('village_id', $request->village_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('village', fn ($vq) => $vq->where('name', 'like', "%{$search}%"));
            });
        }

        $kdmps = $query->latest()->paginate($request->input('per_page', 12));

        return response()->json(['status' => 'success', 'data' => $kdmps]);
    }

    public function show($code)
    {
        $kdmp = Kdmp::with(['village.district.regency.province'])
            ->where('code', $code)
            ->orWhere('id', is_numeric($code) ? $code : 0)
            ->first();

        if (!$kdmp) {
            return response()->json(['status' => 'error', 'message' => 'Koperasi tidak ditemukan'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $kdmp]);
    }
}
