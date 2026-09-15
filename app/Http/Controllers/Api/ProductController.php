<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Get all products with wilayah cascade filters
     */
    public function index(Request $request)
    {
        $query = Product::with(['merchant', 'village.district.regency.province']);

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

        if ($request->filled('merchant_id')) {
            $query->where('merchant_id', $request->merchant_id);
        }

        if ($request->filled('exclude_id')) {
            $query->where('id', '!=', $request->exclude_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('limit')) {
            $products = $query->latest()->limit((int) $request->input('limit'))->get();
            return response()->json([
                'status' => 'success',
                'data' => $products,
            ]);
        }

        $products = $query->latest()->paginate($request->input('per_page', 12));

        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }

    /**
     * Get product detail by slug or ID
     */
    public function show($slugOrId)
    {
        $query = Product::with(['merchant', 'village.district.regency.province']);

        if (is_numeric($slugOrId)) {
            $product = $query->where('id', $slugOrId)->first();
        } else {
            $product = $query->where('slug', $slugOrId)->first();
        }

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $product,
        ]);
    }

    /**
     * Store a new product (Merchant CRUD)
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $merchant = auth()->user()->merchant;

        if (!$merchant || $merchant->status !== 'approved') {
            return response()->json([
                'status' => 'error',
                'message' => 'Merchant belum terverifikasi',
            ], 403);
        }

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'price'          => 'required|numeric|min:0',
            'description'    => 'required|string',
            'category'       => 'required|string|in:makanan_minuman,kerajinan,fashion,pertanian,perikanan,peternakan,jasa,lainnya',
            'stock'          => 'nullable|integer|min:0',
            'unit'           => 'nullable|string|max:20',
            'discount_price' => 'nullable|numeric|min:0',
            'weight'         => 'nullable|numeric|min:0',
            'sku'            => 'nullable|string|max:100',
            'tags'           => 'nullable|array',
            'image'          => 'nullable|image|max:2048',
            'gallery'        => 'nullable|array|max:4',
            'gallery.*'      => 'image|max:2048',
        ]);

        // Handle main image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Handle gallery images upload
        $gallery = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $img) {
                $gallery[] = $img->store('products/gallery', 'public');
            }
        }

        $product = Product::create([
            'merchant_id'    => $merchant->id,
            'village_id'     => $merchant->village_id,
            'name'           => $validated['name'],
            'slug'           => Str::slug($validated['name']) . '-' . Str::random(5),
            'price'          => $validated['price'],
            'description'    => $validated['description'],
            'category'       => $validated['category'],
            'stock'          => $validated['stock'] ?? 0,
            'unit'           => $validated['unit'] ?? 'pcs',
            'discount_price' => $validated['discount_price'] ?? null,
            'weight'         => $validated['weight'] ?? null,
            'sku'            => $validated['sku'] ?? null,
            'tags'           => $validated['tags'] ?? null,
            'image'          => $imagePath,
            'gallery'        => !empty($gallery) ? $gallery : null,
            'is_available'   => true,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil ditambahkan',
            'data'    => $product->load(['merchant', 'village']),
        ], 201);
    }

    /**
     * Update an existing product (Merchant CRUD)
     */
    public function update(Request $request, Product $product): \Illuminate\Http\JsonResponse
    {
        $merchant = auth()->user()->merchant;

        // Ensure product belongs to authenticated merchant
        if (!$merchant || $product->merchant_id !== $merchant->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke produk ini',
            ], 403);
        }

        $validated = $request->validate([
            'name'           => 'sometimes|string|max:255',
            'price'          => 'sometimes|numeric|min:0',
            'description'    => 'sometimes|string',
            'category'       => 'sometimes|string|in:makanan_minuman,kerajinan,fashion,pertanian,perikanan,peternakan,jasa,lainnya',
            'stock'          => 'nullable|integer|min:0',
            'unit'           => 'nullable|string|max:20',
            'discount_price' => 'nullable|numeric|min:0',
            'weight'         => 'nullable|numeric|min:0',
            'sku'            => 'nullable|string|max:100',
            'tags'           => 'nullable|array',
            'is_available'   => 'nullable|boolean',
            'image'          => 'nullable|image|max:2048',
            'gallery'        => 'nullable|array|max:4',
            'gallery.*'      => 'image|max:2048',
            'remove_images'  => 'nullable|array',
            'remove_images.*'=> 'string',
        ]);

        // Handle main image upload
        if ($request->hasFile('image')) {
            // Delete old main image if exists
            if ($product->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Handle removal of existing images
        $currentGallery = $product->gallery ?? [];
        if ($request->has('remove_images')) {
            $toRemove = $request->input('remove_images', []);
            foreach ($toRemove as $path) {
                // Check if it's the main image being removed
                if ($product->image === $path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                    $validated['image'] = null;
                }
                // Remove from gallery
                $currentGallery = array_values(array_filter($currentGallery, fn($g) => $g !== $path));
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
            }
        }

        // Handle new gallery images
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $img) {
                $currentGallery[] = $img->store('products/gallery', 'public');
            }
        }
        $validated['gallery'] = !empty($currentGallery) ? array_values($currentGallery) : null;

        // Remove non-column keys before updating
        unset($validated['remove_images']);

        $product->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil diperbarui',
            'data'    => $product->fresh()->load(['merchant', 'village']),
        ]);
    }

    /**
     * Delete a product (Merchant CRUD)
     */
    public function destroy(Product $product): \Illuminate\Http\JsonResponse
    {
        $merchant = auth()->user()->merchant;

        if (!$merchant || $product->merchant_id !== $merchant->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke produk ini',
            ], 403);
        }

        $product->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil dihapus',
        ]);
    }

    /**
     * Upload additional gallery images for a product
     */
    public function uploadImages(Request $request, Product $product): \Illuminate\Http\JsonResponse
    {
        $merchant = auth()->user()->merchant;

        if (!$merchant || $product->merchant_id !== $merchant->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke produk ini',
            ], 403);
        }

        $request->validate([
            'images'   => 'required|array|max:5',
            'images.*' => 'image|max:2048',
        ]);

        $gallery = $product->gallery ?? [];
        foreach ($request->file('images') as $image) {
            $gallery[] = $image->store('products/gallery', 'public');
        }
        $product->update(['gallery' => $gallery]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Galeri berhasil diperbarui',
            'data'    => $product->fresh(),
        ]);
    }
}