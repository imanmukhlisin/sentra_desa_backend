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
        Schema::create('bumdes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Basic Info
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            
            // Legal & Establishment
            $table->string('legal_number')->nullable(); // Nomor SK Pendirian
            $table->date('established_date')->nullable();
            $table->decimal('initial_capital', 15, 2)->default(0); // Modal awal
            
            // Business Units
            $table->json('business_units')->nullable(); // Array of unit usaha
            
            // Contact
            $table->string('director_name')->nullable(); // Nama Direktur
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            
            // Performance
            $table->decimal('annual_revenue', 15, 2)->default(0); // Omzet tahunan
            $table->integer('employee_count')->default(0);
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->enum('performance_category', ['berkembang', 'maju', 'mandiri'])->default('berkembang');
            
            $table->timestamps();
            
            $table->index(['village_id', 'is_active']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bumdes');
    }
};
