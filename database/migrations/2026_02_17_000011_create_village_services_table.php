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
        Schema::create('village_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->onDelete('cascade');
            
            // Service Details
            $table->string('name'); // Nama layanan (e.g., "Pembuatan KTP", "Surat Keterangan Usaha")
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Icon URL or name
            
            // Category
            $table->enum('category', [
                'administrasi_kependudukan',
                'pelayanan_umum',
                'kesehatan',
                'pendidikan',
                'ekonomi',
                'sosial',
                'infrastruktur'
            ])->default('administrasi_kependudukan');
            
            // Requirements & Process
            $table->json('requirements')->nullable(); // Array of required documents
            $table->text('process_steps')->nullable(); // Step-by-step process
            $table->integer('estimated_days')->default(1); // Waktu penyelesaian (hari)
            $table->decimal('fee', 10, 2)->default(0); // Biaya layanan
            
            // Contact
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('office_hours')->nullable(); // e.g., "Senin-Jumat, 08:00-15:00"
            
            // Online Service
            $table->boolean('is_online_available')->default(false);
            $table->string('online_url')->nullable(); // Link to online service
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0); // Tracking usage
            
            $table->timestamps();
            
            $table->index(['village_id', 'category', 'is_active']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('village_services');
    }
};
