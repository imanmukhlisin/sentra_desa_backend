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
        Schema::create('export_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null'); // Link to products table if applicable
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Product Details
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            
            // Export Info
            $table->string('hs_code')->nullable(); // Harmonized System Code
            $table->json('destination_countries')->nullable(); // Array of country codes
            $table->enum('export_status', ['potensial', 'proses_ekspor', 'sudah_ekspor'])->default('potensial');
            
            // Certification
            $table->json('certifications')->nullable(); // Array of certifications (HALAL, ORGANIC, etc.)
            $table->boolean('has_export_license')->default(false);
            
            // Volume & Value
            $table->integer('export_volume')->default(0); // Volume ekspor (kg/bulan)
            $table->decimal('export_value', 15, 2)->default(0); // Nilai ekspor (USD/bulan)
            $table->string('unit')->default('kg'); // Unit measurement
            
            // Contact for Export Inquiry
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            
            $table->timestamps();
            
            $table->index(['village_id', 'export_status', 'is_active']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_products');
    }
};
