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
        Schema::create('kdmp', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique(); // Kode KDMP
            
            // Geospatial: KDMP bisa mencakup multiple villages
            $table->foreignId('regency_id')->constrained()->onDelete('cascade');
            $table->json('village_ids')->nullable(); // Array of village IDs in this KDMP
            
            // Details
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->enum('category', ['pusat_pertumbuhan', 'kawasan_agropolitan', 'kawasan_minapolitan', 'kawasan_industri'])->nullable();
            
            // Area & Demographics
            $table->decimal('total_area', 10, 2)->nullable(); // Total luas (km²)
            $table->integer('total_population')->default(0);
            $table->integer('village_count')->default(0); // Jumlah desa dalam kawasan
            
            // Economic Indicators
            $table->text('main_commodities')->nullable(); // Komoditas utama
            $table->decimal('economic_potential', 15, 2)->nullable(); // Potensi ekonomi (Rupiah)
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->year('established_year')->nullable();
            
            $table->timestamps();
            
            $table->index(['regency_id', 'is_active']);
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kdmp');
    }
};
