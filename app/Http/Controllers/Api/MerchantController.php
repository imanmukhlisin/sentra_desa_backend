<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Product;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    /**
     * Get authenticated merchant's data
     */
    public function myMerchant()
    {
        $merchant = auth()->user()->merchant;

        if (!$merchant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Merchant profile not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $merchant->load('village')
        ]);
    }

    /**
     * Register as merchant — handles file uploads (logo, payment_proof)
     */
    public function register(Request $request)
    {
        // Prevent duplicate registration
        if (auth()->user()->merchant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah terdaftar sebagai merchant'
            ], 422);
        }

        $validated = $request->validate([
            'store_name'       => 'required|string|max:255',
            'village_id'       => 'required|exists:villages,id',
            'address'          => 'required|string',
            'phone'            => 'required|string|max:20',
            'description'      => 'nullable|string',
            'business_type'    => 'nullable|string|max:100',
            'established_year' => 'nullable|digits:4',
            'logo'             => 'nullable|image|max:2048',
            'payment_proof'    => 'nullable|image|max:2048',
        ]);

        // Handle file uploads
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('merchants/logos', 'public');
        }

        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('merchants/proofs', 'public');
        }

        $merchant = Merchant::create([
            'user_id'          => auth()->id(),
            'store_name'       => $validated['store_name'],
            'village_id'       => $validated['village_id'],
            'address'          => $validated['address'],
            'phone'            => $validated['phone'],
            'description'      => $validated['description'] ?? null,
            'business_type'    => $validated['business_type'] ?? null,
            'established_year' => $validated['established_year'] ?? null,
            'logo'             => $logoPath,
            'payment_proof'    => $proofPath,
            'status'           => 'pending',
        ]);

        // Assign umkm role if using Spatie
        if (!auth()->user()->hasRole('umkm')) {
            auth()->user()->assignRole('umkm');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Pendaftaran merchant berhasil dikirim, menunggu persetujuan admin.',
            'data' => $merchant->load('village')
        ], 201);
    }

    /**
     * Update merchant profile — supports logo upload
     */
    public function update(Request $request)
    {
        $merchant = auth()->user()->merchant;

        if (!$merchant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Merchant profile not found'
            ], 404);
        }

        $validated = $request->validate([
            'store_name'       => 'sometimes|string|max:255',
            'address'          => 'sometimes|string',
            'phone'            => 'sometimes|string|max:20',
            'description'      => 'nullable|string',
            'business_type'    => 'nullable|string|max:100',
            'established_year' => 'nullable|digits:4',
            'logo'             => 'nullable|image|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('merchants/logos', 'public');
        }

        $merchant->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Profil merchant berhasil diperbarui',
            'data' => $merchant->fresh()->load('village')
        ]);
    }

    /**
     * Get merchant's own products (paginated)
     */
    public function myProducts(Request $request)
    {
        $merchant = auth()->user()->merchant;

        if (!$merchant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Merchant profile not found'
            ], 404);
        }

        $query = $merchant->products()->with('village');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate($request->input('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }

    /**
     * Get pending merchants for village admin
     */
    public function pendingVillage()
    {
        $villageId = auth()->user()->village_id;

        $merchants = Merchant::where('village_id', $villageId)
            ->where('status', 'pending')
            ->with('user')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $merchants
        ]);
    }

    /**
     * Approve merchant — sets membership for 1 year
     */
    public function approve(Merchant $merchant)
    {
        $merchant->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'membership_expires_at' => now()->addYear(),
            'renewal_status' => 'none',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Merchant approved, membership aktif 1 tahun',
            'data' => $merchant->fresh(),
        ]);
    }

    /**
     * Reject merchant
     */
    public function reject(Merchant $merchant)
    {
        $merchant->update(['status' => 'rejected']);

        return response()->json([
            'status' => 'success',
            'message' => 'Merchant rejected'
        ]);
    }

    /**
     * Get merchant analytics
     */
    public function analytics()
    {
        $merchant = auth()->user()->merchant;

        if (!$merchant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Merchant profile not found'
            ], 404);
        }

        $stats = [
            'total_products' => $merchant->products()->count(),
            'active_products' => $merchant->products()->where('is_available', true)->count(),
            'total_views' => $merchant->products()->sum('view_count'),
            'total_orders' => $merchant->products()->sum('order_count'),
            'out_of_stock' => $merchant->products()->where('stock', 0)->count(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }

    /**
     * UMKM requests membership renewal — upload new payment proof
     */
    public function requestRenewal(Request $request)
    {
        $merchant = auth()->user()->merchant;

        if (!$merchant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Merchant profile not found',
            ], 404);
        }

        if ($merchant->renewal_status === 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Permintaan perpanjangan sudah dikirim, menunggu persetujuan admin.',
            ], 422);
        }

        $request->validate([
            'renewal_proof' => 'required|image|max:2048',
        ]);

        $path = $request->file('renewal_proof')->store('merchants/renewals', 'public');

        $merchant->update([
            'renewal_proof' => $path,
            'renewal_requested_at' => now(),
            'renewal_status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Permintaan perpanjangan berhasil dikirim.',
            'data' => $merchant->fresh(),
        ]);
    }

    /**
     * Admin approves renewal — extends membership 1 year from current expiry (or now)
     */
    public function approveRenewal(Merchant $merchant)
    {
        if ($merchant->renewal_status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada permintaan perpanjangan yang pending.',
            ], 422);
        }

        // Extend from current expiry date if still valid, otherwise from now
        $baseDate = $merchant->membership_expires_at && $merchant->membership_expires_at->isFuture()
            ? $merchant->membership_expires_at
            : now();

        $merchant->update([
            'membership_expires_at' => $baseDate->copy()->addYear(),
            'renewal_status' => 'approved',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Perpanjangan membership disetujui.',
            'data' => $merchant->fresh(),
        ]);
    }

    /**
     * Admin rejects renewal
     */
    public function rejectRenewal(Merchant $merchant)
    {
        $merchant->update(['renewal_status' => 'rejected']);

        return response()->json([
            'status' => 'success',
            'message' => 'Perpanjangan membership ditolak.',
        ]);
    }
}
