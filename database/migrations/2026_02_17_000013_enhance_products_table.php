<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Extract village_id from merchant relationship (denormalized for faster queries)
            $table->foreignId('village_id')->nullable()->after('merchant_id')->constrained()->onDelete('cascade');
            
            // Product categorization
            $table->enum('category', [
                'makanan_minuman',
                'kerajinan',
                'fashion',
                'pertanian',
                'perikanan',
                'peternakan',
                'jasa',
                'lainnya'
            ])->default('lainnya')->after('merchant_id');
            
            // Product details enhancement
            $table->string('sku')->nullable()->after('name'); // Stock Keeping Unit
            $table->json('gallery')->nullable()->after('image'); // Multiple images
            $table->integer('stock')->default(0)->after('description');
            $table->string('unit')->default('pcs')->after('stock'); // Unit (pcs, kg, liter, etc.)
            
            // Pricing
            $table->integer('discount_price')->nullable()->after('price'); // Harga diskon
            $table->boolean('is_available')->default(true)->after('discount_price');
            
            // Additional info
            $table->decimal('weight', 8, 2)->nullable()->after('is_available'); // Berat produk (kg)
            $table->json('tags')->nullable()->after('weight'); // Array of tags for search
            
            // Stats
            $table->integer('view_count')->default(0)->after('tags');
            $table->integer('order_count')->default(0)->after('view_count');
            $table->boolean('is_featured')->default(false)->after('order_count');
            
            $table->index(['village_id', 'category', 'is_available']);
            $table->index(['merchant_id', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['village_id']);
            $table->dropColumn([
                'village_id',
                'category',
                'sku',
                'gallery',
                'stock',
                'unit',
                'discount_price',
                'is_available',
                'weight',
                'tags',
                'view_count',
                'order_count',
                'is_featured'
            ]);
        });
    }
};
