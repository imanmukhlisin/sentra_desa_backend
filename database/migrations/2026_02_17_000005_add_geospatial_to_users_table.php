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
        Schema::table('users', function (Blueprint $table) {
            // Link user to village for village-level admins
            $table->foreignId('village_id')->nullable()->after('email')->constrained()->onDelete('set null');
            
            // Link user to regency for PEMDA (Kabupaten/Kota) level
            $table->foreignId('regency_id')->nullable()->after('village_id')->constrained()->onDelete('set null');
            
            // Link user to province for PEMDA (Provinsi) level
            $table->foreignId('province_id')->nullable()->after('regency_id')->constrained()->onDelete('set null');
            
            // User level identification
            $table->enum('user_level', ['buyer', 'umkm', 'village_admin', 'regency_admin', 'province_admin', 'superadmin'])
                  ->default('buyer')
                  ->after('province_id');
            
            // Additional profile fields
            $table->string('phone')->nullable()->after('user_level');
            $table->string('avatar')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('avatar');
            
            $table->index(['village_id', 'user_level']);
            $table->index(['regency_id', 'user_level']);
            $table->index(['province_id', 'user_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['village_id']);
            $table->dropForeign(['regency_id']);
            $table->dropForeign(['province_id']);
            $table->dropColumn([
                'village_id', 
                'regency_id', 
                'province_id', 
                'user_level', 
                'phone', 
                'avatar',
                'is_active'
            ]);
        });
    }
};
