<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VillageContentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes - SENTRA DESA
|--------------------------------------------------------------------------
| Structure: Public -> UMKM -> Village -> Regency -> Province -> Superadmin
| Version: v1
*/

// Health Check Endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'SENTRA DESA API is running',
        'timestamp' => now()->toISOString(),
        'version' => 'v1.0.0',
    ]);
});

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (No authentication required)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/public')->group(function () {
    
    // Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Geospatial Hierarchy - Cascading Filter Support
    Route::get('/provinces', [\App\Http\Controllers\Api\GeospatialController::class, 'provinces']);
    Route::get('/provinces/{provinceId}/regencies', [\App\Http\Controllers\Api\GeospatialController::class, 'regencies']);
    Route::get('/regencies/{regencyId}/districts', [\App\Http\Controllers\Api\GeospatialController::class, 'districts']);
    Route::get('/districts/{districtId}/villages', [\App\Http\Controllers\Api\GeospatialController::class, 'villages']);
    Route::get('/villages/{villageId}', [\App\Http\Controllers\Api\GeospatialController::class, 'villageDetail']);
    
    // Sentra Produk - Product Catalog
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    
    // Desa Wisata - Tourism
    Route::get('/tourisms', [\App\Http\Controllers\Api\TourismController::class, 'index']);
    Route::get('/tourisms/{slug}', [\App\Http\Controllers\Api\TourismController::class, 'show']);
    
    // Desa Kita - Village Profile
    Route::get('/villages', [\App\Http\Controllers\Api\VillageController::class, 'index']);
    Route::get('/villages/{village}/profile', [\App\Http\Controllers\Api\VillageController::class, 'profile']);
    
    // Potensi Desa - Village Potentials
    Route::get('/village-potentials', [\App\Http\Controllers\Api\VillagePotentialController::class, 'index']);
    Route::get('/village-potentials/{id}', [\App\Http\Controllers\Api\VillagePotentialController::class, 'show']);
    Route::get('/villages/{village}/potentials', [\App\Http\Controllers\Api\VillagePotentialController::class, 'byVillage']);
    
    // Desa Ekspor - Export Products
    Route::get('/export-products', [\App\Http\Controllers\Api\ExportProductController::class, 'index']);
    Route::get('/export-products/{slug}', [\App\Http\Controllers\Api\ExportProductController::class, 'show']);
    
    // BUMDES
    Route::get('/bumdes', [\App\Http\Controllers\Api\BumdesController::class, 'index']);
    Route::get('/bumdes/{slug}', [\App\Http\Controllers\Api\BumdesController::class, 'show']);
    
    // KDMP - Kawasan Perdesaan
    Route::get('/kdmp', [\App\Http\Controllers\Api\KdmpController::class, 'index']);
    Route::get('/kdmp/{code}', [\App\Http\Controllers\Api\KdmpController::class, 'show']);
    
    // Layanan Desa - Village Services
    Route::get('/village-services', [\App\Http\Controllers\Api\VillageServiceController::class, 'index']);
    Route::get('/village-services/{id}', [\App\Http\Controllers\Api\VillageServiceController::class, 'show']);
    Route::get('/villages/{village}/services', [\App\Http\Controllers\Api\VillageServiceController::class, 'byVillage']);
    
    // Content/Articles
    Route::get('/contents', [VillageContentController::class, 'index']);
    Route::get('/contents/{slug}', [VillageContentController::class, 'show']);

    // LKDD - Laporan Keuangan Dana Desa
    Route::get('/lkdd', [\App\Http\Controllers\Api\VillageFundReportController::class, 'index']);
    Route::get('/lkdd/{id}', [\App\Http\Controllers\Api\VillageFundReportController::class, 'show']);

    // Artikel
    Route::get('/articles', [\App\Http\Controllers\Api\ArticleController::class, 'index']);
    Route::get('/articles/{slug}', [\App\Http\Controllers\Api\ArticleController::class, 'show']);

    // Highlights - Banner carousel
    Route::get('/highlights', [\App\Http\Controllers\Api\HighlightController::class, 'index']);

    // Wishlist antar desa (public read-only)
    Route::get('/wishlists', [\App\Http\Controllers\Api\WishlistController::class, 'index']);
    Route::get('/wishlists/{id}', [\App\Http\Controllers\Api\WishlistController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth & Profile Management
    Route::post('/v1/logout', [AuthController::class, 'logout']);
    Route::get('/v1/profile', [AuthController::class, 'profile']);
    Route::put('/v1/profile', [AuthController::class, 'updateProfile']);
    
    /*
    |--------------------------------------------------------------------------
    | BUYER ROUTES (General authenticated users)
    |--------------------------------------------------------------------------
    */
    Route::prefix('v1/buyer')->group(function () {
        // Wishlist, Cart, Orders, etc. (Future implementation)
        Route::get('/dashboard', function () {
            return response()->json(['message' => 'Buyer Dashboard']);
        });
    });

    // Merchant registration — any authenticated user can register (no role required yet)
    Route::post('v1/umkm/merchant/register', [\App\Http\Controllers\Api\MerchantController::class, 'register']);
    
    /*
    |--------------------------------------------------------------------------
    | UMKM ROUTES (Merchant Management)
    |--------------------------------------------------------------------------
    */
    Route::prefix('v1/umkm')->middleware(['role:umkm|village_admin|superadmin'])->group(function () {
        
        // Merchant Profile
        Route::get('/merchant', [\App\Http\Controllers\Api\MerchantController::class, 'myMerchant']);
        Route::put('/merchant', [\App\Http\Controllers\Api\MerchantController::class, 'update']);
        Route::post('/merchant/update', [\App\Http\Controllers\Api\MerchantController::class, 'update']); // POST alias for multipart
        
        // Merchant's own products
        Route::get('/my-products', [\App\Http\Controllers\Api\MerchantController::class, 'myProducts']);
        
        // Product Management
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);
        Route::post('/products/{product}/upload-images', [ProductController::class, 'uploadImages']);
        
        // Sales & Analytics
        Route::get('/analytics', [\App\Http\Controllers\Api\MerchantController::class, 'analytics']);
        
        // Membership Renewal
        Route::post('/merchant/renew', [\App\Http\Controllers\Api\MerchantController::class, 'requestRenewal']);
    });
    
    /*
    |--------------------------------------------------------------------------
    | VILLAGE ADMIN ROUTES (Desa Management)
    |--------------------------------------------------------------------------
    */
    Route::prefix('v1/village')->middleware(['role:village_admin|superadmin'])->group(function () {
        
        // Village Profile Management
        Route::get('/profile', [\App\Http\Controllers\Api\VillageController::class, 'myVillage']);
        Route::put('/profile', [\App\Http\Controllers\Api\VillageController::class, 'updateProfile']);
        
        // Tourism Management
        Route::apiResource('tourisms', \App\Http\Controllers\Api\TourismController::class)->except(['index', 'show']);
        
        // BUMDES Management
        Route::apiResource('bumdes', \App\Http\Controllers\Api\BumdesController::class)->except(['index', 'show']);
        
        // Village Potentials Management
        Route::apiResource('potentials', \App\Http\Controllers\Api\VillagePotentialController::class)->except(['index']);
        
        // Export Products Management
        Route::apiResource('export-products', \App\Http\Controllers\Api\ExportProductController::class)->except(['index', 'show']);
        
        // Village Services Management
        Route::apiResource('services', \App\Http\Controllers\Api\VillageServiceController::class)->except(['index']);
        
        // Merchant Approval (Village level)
        Route::get('/merchants/pending', [\App\Http\Controllers\Api\MerchantController::class, 'pendingVillage']);
        Route::post('/merchants/{merchant}/approve', [\App\Http\Controllers\Api\MerchantController::class, 'approve']);
        Route::post('/merchants/{merchant}/reject', [\App\Http\Controllers\Api\MerchantController::class, 'reject']);
        Route::post('/merchants/{merchant}/approve-renewal', [\App\Http\Controllers\Api\MerchantController::class, 'approveRenewal']);
        Route::post('/merchants/{merchant}/reject-renewal', [\App\Http\Controllers\Api\MerchantController::class, 'rejectRenewal']);
        
        // Content Management
        Route::apiResource('contents', VillageContentController::class)->except(['index', 'show']);

        // Article Management
        Route::apiResource('articles', \App\Http\Controllers\Api\ArticleController::class)->except(['index', 'show']);
        
        // LKDD Management
        Route::apiResource('fund-reports', \App\Http\Controllers\Api\VillageFundReportController::class)->except(['index', 'show']);

        // Analytics & Reports
        Route::get('/analytics', [\App\Http\Controllers\Api\VillageController::class, 'analytics']);
        Route::get('/reports/umkm', [\App\Http\Controllers\Api\VillageController::class, 'umkmReport']);
    });
    
    // TODO: Uncomment when controllers are implemented
    /*
    |--------------------------------------------------------------------------
    | REGENCY ADMIN ROUTES (PEMDA Kabupaten/Kota) - COMING SOON
    |--------------------------------------------------------------------------
    
    Route::prefix('v1/regency')->middleware(['role:regency_admin|superadmin'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\RegencyController::class, 'dashboard']);
        Route::get('/villages', [\App\Http\Controllers\Api\RegencyController::class, 'villages']);
        Route::get('/villages/{village}/detail', [\App\Http\Controllers\Api\RegencyController::class, 'villageDetail']);
        Route::apiResource('kdmp', \App\Http\Controllers\Api\KdmpController::class)->except(['index', 'show']);
        Route::get('/analytics/tourism', [\App\Http\Controllers\Api\RegencyController::class, 'tourismAnalytics']);
        Route::get('/analytics/products', [\App\Http\Controllers\Api\RegencyController::class, 'productAnalytics']);
        Route::get('/analytics/export', [\App\Http\Controllers\Api\RegencyController::class, 'exportAnalytics']);
        Route::get('/reports/villages', [\App\Http\Controllers\Api\RegencyController::class, 'villageReport']);
        Route::get('/reports/economic', [\App\Http\Controllers\Api\RegencyController::class, 'economicReport']);
    });
    
    |--------------------------------------------------------------------------
    | PROVINCE ADMIN ROUTES (PEMDA Provinsi) - COMING SOON
    |--------------------------------------------------------------------------
    
    Route::prefix('v1/province')->middleware(['role:province_admin|superadmin'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\ProvinceController::class, 'dashboard']);
        Route::get('/regencies', [\App\Http\Controllers\Api\ProvinceController::class, 'regencies']);
        Route::get('/regencies/{regency}/summary', [\App\Http\Controllers\Api\ProvinceController::class, 'regencySummary']);
        Route::get('/analytics/overview', [\App\Http\Controllers\Api\ProvinceController::class, 'overview']);
        Route::get('/analytics/comparison', [\App\Http\Controllers\Api\ProvinceController::class, 'regencyComparison']);
        Route::get('/reports/provincial', [\App\Http\Controllers\Api\ProvinceController::class, 'provincialReport']);
    });
    
    |--------------------------------------------------------------------------
    | SUPERADMIN ROUTES (Pusat/Kementerian) - COMING SOON
    |--------------------------------------------------------------------------
    
    Route::prefix('v1/superadmin')->middleware(['role:superadmin'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\SuperadminController::class, 'dashboard']);
        Route::apiResource('users', \App\Http\Controllers\Api\UserController::class);
        Route::post('/users/{user}/assign-role', [\App\Http\Controllers\Api\UserController::class, 'assignRole']);
        Route::apiResource('provinces', \App\Http\Controllers\Api\GeospatialController::class);
        Route::apiResource('regencies', \App\Http\Controllers\Api\RegencyManagementController::class);
        Route::apiResource('districts', \App\Http\Controllers\Api\DistrictManagementController::class);
        Route::apiResource('villages', \App\Http\Controllers\Api\VillageManagementController::class);
        Route::get('/analytics/national', [\App\Http\Controllers\Api\SuperadminController::class, 'nationalAnalytics']);
        Route::get('/analytics/provinces', [\App\Http\Controllers\Api\SuperadminController::class, 'provinceComparison']);
        Route::get('/config', [\App\Http\Controllers\Api\SuperadminController::class, 'getConfig']);
        Route::put('/config', [\App\Http\Controllers\Api\SuperadminController::class, 'updateConfig']);
        Route::get('/reports/comprehensive', [\App\Http\Controllers\Api\SuperadminController::class, 'comprehensiveReport']);
        Route::post('/reports/export', [\App\Http\Controllers\Api\SuperadminController::class, 'exportData']);
    });
    */
});
