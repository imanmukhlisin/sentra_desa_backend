<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $query = Wishlist::with(['village.district.regency.province']);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('village_id')) {
            $query->where('village_id', $request->village_id);
        }

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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $wishlists = $query->latest()->paginate($request->input('per_page', 12));

        return response()->json([
            'status' => 'success',
            'data' => $wishlists,
        ]);
    }

    public function show(int $id)
    {
        $wishlist = Wishlist::with(['village.district.regency.province'])
            ->where('id', $id)
            ->first();

        if (!$wishlist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wishlist not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $wishlist,
        ]);
    }
}
