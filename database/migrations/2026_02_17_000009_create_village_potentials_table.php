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
        Schema::create('village_potentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Category & Type
            $table->enum('category', [
                'pertanian', 
                'perkebunan', 
                'perikanan', 
                'peternakan', 
                'industri', 
                'pariwisata', 
                'tambang', 
                'kehutanan',
                'sdm' // Sumber Daya Manusia
            ]);
            
            // Details
            $table->string('name'); // Nama potensi
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            
            // Quantitative Data
            $table->decimal('total_area', 10, 2)->nullable(); // Luas lahan (ha)
            $table->integer('production_volume')->nullable(); // Volume produksi (ton/tahun)
            $table->decimal('economic_value', 15, 2)->nullable(); // Nilai ekonomi (Rupiah/tahun)
            
            // Investment
            $table->boolean('is_investment_ready')->default(false);
            $table->text('investment_needs')->nullable(); // Kebutuhan investasi
            
            // Status
            $table->enum('development_status', ['teridentifikasi', 'dikembangkan', 'produktif'])->default('teridentifikasi');
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            $table->index(['village_id', 'category', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('village_potentials');
    }
};
