<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Village;
use App\Models\Kdmp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VillageController extends Controller
{
    /**
     * List semua desa (untuk halaman Profil Desa list)
     * Supports: search, province_id, regency_id, district_id, is_featured
     */
    public function index(Request $request)
    {
        $query = Village::with(['district.regency.province']);

        // PENTING: hanya tampilkan desa yang sudah diverifikasi (punya profil),
        // BUKAN data master wilayah mentah (74.954 desa dari formades.org)
        $query->where('is_verified', true);

        // Filter pencarian nama desa
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan district
        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        // Filter berdasarkan regency (melalui district)
        if ($request->filled('regency_id')) {
            $query->whereHas('district', function ($q) use ($request) {
                $q->where('regency_id', $request->regency_id);
            });
        }

        // Filter berdasarkan province (melalui district.regency)
        if ($request->filled('province_id')) {
            $query->whereHas('district.regency', function ($q) use ($request) {
                $q->where('province_id', $request->province_id);
            });
        }

        // Filter featured / verified only
        if ($request->boolean('is_featured', false)) {
            $query->where('is_featured', true);
        }

        $villages = $query->orderBy('name')
            ->paginate($request->input('per_page', 12));

        return response()->json([
            'status' => 'success',
            'data' => $villages,
        ]);
    }

    /**
     * Detail profil desa lengkap + preview item dari semua modul terkait
     * Digunakan untuk halaman Detail Profil Desa di Flutter
     */
    public function profile(Village $village)
    {
        // Load relasi geospatial
        $village->load(['district.regency.province']);

        // Ambil preview data (masing-masing max 3 item) untuk section "Selengkapnya"
        $potentials = $village->villagePotentials()
            ->where('is_active', true)
            ->latest()
            ->take(3)
            ->get();

        $contents = $village->villageContents()
            ->where('category', 'informasi')
            ->latest()
            ->take(3)
            ->get();

        $products = $village->products()
            ->with('merchant')
            ->latest()
            ->take(3)
            ->get();

        $exportProducts = $village->exportProducts()
            ->where('is_active', true)
            ->latest()
            ->take(3)
            ->get();

        $tourisms = $village->tourisms()
            ->where('is_active', true)
            ->latest()
            ->take(3)
            ->get();

        $bumdes = $village->bumdes()
            ->where('is_active', true)
            ->latest()
            ->take(3)
            ->get();

        // KDMP: cari yang mengandung village_id ini di JSON array
        $kdmpList = Kdmp::where('is_active', true)
            ->get()
            ->filter(function ($kdmp) use ($village) {
                $villageIds = is_array($kdmp->village_ids) ? $kdmp->village_ids : json_decode($kdmp->village_ids, true);
                return is_array($villageIds) && in_array($village->id, $villageIds);
            })
            ->take(3)
            ->values();

        // Hitung statistik
        $stats = [
            'total_potentials'      => $village->villagePotentials()->where('is_active', true)->count(),
            'total_contents'        => $village->villageContents()->count(),
            'total_products'        => $village->products()->count(),
            'total_export_products' => $village->exportProducts()->where('is_active', true)->count(),
            'total_tourisms'        => $village->tourisms()->where('is_active', true)->count(),
            'total_bumdes'          => $village->bumdes()->where('is_active', true)->count(),
            'total_merchants'       => $village->merchants()->count(),
            'total_kdmp'            => $kdmpList->count(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'village'         => $village,
                'statistics'      => $stats,
                'potentials'      => $potentials,
                'contents'        => $contents,
                'products'        => $products,
                'export_products' => $exportProducts,
                'tourisms'        => $tourisms,
                'bumdes'          => $bumdes,
                'kdmp'            => $kdmpList,
            ],
        ]);
    }

    /**
     * Get village admin's own desa
     */
    public function myVillage()
    {
        $user = auth()->user();

        if (!$user->village_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akun Anda tidak terkait dengan desa manapun',
            ], 404);
        }

        $village = Village::with(['district.regency.province'])->find($user->village_id);

        if (!$village) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data desa tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $village,
        ]);
    }

    /**
     * Update village admin's own desa profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        if (!$user->village_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akun Anda tidak terkait dengan desa manapun',
            ], 404);
        }

        $village = Village::find($user->village_id);

        if (!$village) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data desa tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'vision'      => 'nullable|string',
            'mission'     => 'nullable|string',
            'history'     => 'nullable|string',
            'head_name'   => 'nullable|string|max:255',
            'phone'       => 'nullable|string|max:20',
            'email'       => 'nullable|email|max:255',
            'website'     => 'nullable|string|max:255',
            'population'  => 'nullable|integer|min:0',
            'area_size'   => 'nullable|numeric|min:0',
            'dusun_count' => 'nullable|integer|min:0',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'logo'        => 'nullable|image|max:2048',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($village->logo) {
                Storage::disk('public')->delete($village->logo);
            }
            $validated['logo'] = $request->file('logo')->store('villages', 'public');
        }

        if ($request->hasFile('cover_image')) {
            if ($village->cover_image) {
                Storage::disk('public')->delete($village->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('villages', 'public');
        }

        $village->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Profil desa berhasil diperbarui',
            'data'    => $village->fresh()->load(['district.regency.province']),
        ]);
    }

    /**
     * Analytics for village admin dashboard
     */
    public function analytics()
    {
        $user = auth()->user();

        if (!$user->village_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akun Anda tidak terkait dengan desa manapun',
            ], 404);
        }

        $village = Village::find($user->village_id);

        if (!$village) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data desa tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'total_products'        => $village->products()->count(),
                'active_products'       => $village->products()->where('is_available', true)->count(),
                'total_merchants'       => $village->merchants()->count(),
                'approved_merchants'    => $village->merchants()->where('status', 'approved')->count(),
                'pending_merchants'     => $village->merchants()->where('status', 'pending')->count(),
                'total_tourisms'        => $village->tourisms()->where('is_active', true)->count(),
                'total_bumdes'          => $village->bumdes()->where('is_active', true)->count(),
                'total_export_products' => $village->exportProducts()->where('is_active', true)->count(),
                'total_potentials'      => $village->villagePotentials()->where('is_active', true)->count(),
                'total_contents'        => $village->villageContents()->count(),
            ],
        ]);
    }

    /**
     * UMKM/Merchant report for village admin
     */
    public function umkmReport()
    {
        $user = auth()->user();

        if (!$user->village_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akun Anda tidak terkait dengan desa manapun',
            ], 404);
        }

        $merchants = Merchant::where('village_id', $user->village_id)
            ->with('user')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'summary' => [
                    'total'             => $merchants->count(),
                    'approved'          => $merchants->where('status', 'approved')->count(),
                    'pending'           => $merchants->where('status', 'pending')->count(),
                    'rejected'          => $merchants->where('status', 'rejected')->count(),
                    'active_membership' => $merchants->filter(fn ($m) => $m->is_membership_active)->count(),
                ],
                'merchants' => $merchants,
            ],
        ]);
    }
}
