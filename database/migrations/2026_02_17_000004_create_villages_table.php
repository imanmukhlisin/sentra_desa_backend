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
        Schema::create('villages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained()->onDelete('cascade');
            $table->string('code', 15)->unique(); // Kode BPS Desa/Kelurahan
            $table->string('name');
            $table->string('postal_code', 10)->nullable();
            
            // Profil Desa
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            
            // Geolocation
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Demografi
            $table->integer('population')->default(0);
            $table->decimal('area_size', 10, 2)->nullable(); // Luas wilayah (km²)
            
            // Status & Verifikasi
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            
            $table->timestamps();
            
            $table->index(['district_id', 'code']);
            $table->index('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villages');
    }
};
