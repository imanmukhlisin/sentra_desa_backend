<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VillageFundReport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VillageFundReportController extends Controller
{
    /**
     * List published LKDD with cascading wilayah filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = VillageFundReport::with(['village.district.regency.province'])
            ->where('is_published', true);

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

        if ($request->filled('fiscal_year')) {
            $query->where('fiscal_year', $request->fiscal_year);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('village', fn ($vq) =>
                    $vq->where('name', 'like', "%{$search}%")
                )->orWhere('head_name', 'like', "%{$search}%");
            });
        }

        $reports = $query->latest('fiscal_year')
            ->latest()
            ->paginate($request->input('per_page', 12));

        return response()->json([
            'status' => 'success',
            'data' => $reports,
        ]);
    }

    /**
     * Get single LKDD detail
     */
    public function show(int $id): JsonResponse
    {
        $report = VillageFundReport::with(['village.district.regency.province'])
            ->where('is_published', true)
            ->find($id);

        if (!$report) {
            return response()->json([
                'status' => 'error',
                'message' => 'Laporan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $report,
        ]);
    }

    /**
     * Store a new LKDD report (village admin)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'village_id'              => 'required|exists:villages,id',
            'fiscal_year'             => 'required|integer|min:2000|max:2099',
            'period'                  => 'required|in:tahunan,semester_1,semester_2',
            'head_name'               => 'nullable|string|max:255',
            'head_title'              => 'nullable|string|max:255',
            'belanja_pemerintahan'    => 'nullable|numeric|min:0',
            'belanja_pembangunan'     => 'nullable|numeric|min:0',
            'belanja_pembinaan'       => 'nullable|numeric|min:0',
            'belanja_pemberdayaan'    => 'nullable|numeric|min:0',
            'belanja_bencana'         => 'nullable|numeric|min:0',
            'pendapatan_asli_desa'    => 'nullable|numeric|min:0',
            'dana_desa'               => 'nullable|numeric|min:0',
            'bagi_hasil_pajak'        => 'nullable|numeric|min:0',
            'alokasi_dana_desa'       => 'nullable|numeric|min:0',
            'bantuan_keuangan_kab'    => 'nullable|numeric|min:0',
            'bantuan_keuangan_prov'   => 'nullable|numeric|min:0',
            'pendapatan_lainnya'      => 'nullable|numeric|min:0',
            'penerimaan_pembiayaan'   => 'nullable|numeric|min:0',
            'silpa'                   => 'nullable|numeric|min:0',
            'infographic_image'       => 'nullable|image|max:4096',
            'is_published'            => 'boolean',
            'notes'                   => 'nullable|string',
        ]);

        if ($request->hasFile('infographic_image')) {
            $validated['infographic_image'] = $request->file('infographic_image')->store('lkdd', 'public');
        }

        $report = VillageFundReport::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $report->load('village.district.regency.province'),
        ], 201);
    }

    /**
     * Update an existing LKDD report (village admin)
     */
    public function update(Request $request, VillageFundReport $fundReport): JsonResponse
    {
        $validated = $request->validate([
            'village_id'              => 'sometimes|exists:villages,id',
            'fiscal_year'             => 'sometimes|integer|min:2000|max:2099',
            'period'                  => 'sometimes|in:tahunan,semester_1,semester_2',
            'head_name'               => 'nullable|string|max:255',
            'head_title'              => 'nullable|string|max:255',
            'belanja_pemerintahan'    => 'nullable|numeric|min:0',
            'belanja_pembangunan'     => 'nullable|numeric|min:0',
            'belanja_pembinaan'       => 'nullable|numeric|min:0',
            'belanja_pemberdayaan'    => 'nullable|numeric|min:0',
            'belanja_bencana'         => 'nullable|numeric|min:0',
            'pendapatan_asli_desa'    => 'nullable|numeric|min:0',
            'dana_desa'               => 'nullable|numeric|min:0',
            'bagi_hasil_pajak'        => 'nullable|numeric|min:0',
            'alokasi_dana_desa'       => 'nullable|numeric|min:0',
            'bantuan_keuangan_kab'    => 'nullable|numeric|min:0',
            'bantuan_keuangan_prov'   => 'nullable|numeric|min:0',
            'pendapatan_lainnya'      => 'nullable|numeric|min:0',
            'penerimaan_pembiayaan'   => 'nullable|numeric|min:0',
            'silpa'                   => 'nullable|numeric|min:0',
            'infographic_image'       => 'nullable|image|max:4096',
            'is_published'            => 'boolean',
            'notes'                   => 'nullable|string',
        ]);

        if ($request->hasFile('infographic_image')) {
            if ($fundReport->infographic_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($fundReport->infographic_image);
            }
            $validated['infographic_image'] = $request->file('infographic_image')->store('lkdd', 'public');
        }

        $fundReport->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $fundReport->fresh()->load('village.district.regency.province'),
        ]);
    }

    /**
     * Delete an LKDD report (village admin)
     */
    public function destroy(VillageFundReport $fundReport): JsonResponse
    {
        $fundReport->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan berhasil dihapus',
        ]);
    }
}
