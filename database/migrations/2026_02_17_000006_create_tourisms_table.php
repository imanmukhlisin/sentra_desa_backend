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
        Schema::create('tourisms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            
            // Media
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable(); // Array of images
            
            // Details
            $table->enum('category', ['alam', 'budaya', 'kuliner', 'edukasi', 'religi', 'buatan'])->default('alam');
            $table->decimal('entrance_fee', 10, 2)->default(0);
            $table->string('opening_hours')->nullable(); // e.g., "08:00 - 17:00"
            $table->text('facilities')->nullable(); // JSON array of facilities
            
            // Location
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Contact
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('view_count')->default(0);
            
            $table->timestamps();
            
            $table->index(['village_id', 'is_active']);
            $table->index('slug');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tourisms');
    }
};
