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
        Schema::table('merchants', function (Blueprint $table) {
            // Link merchant to village
            $table->foreignId('village_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
            
            // Additional merchant info
            $table->text('description')->nullable()->after('store_name');
            $table->string('logo')->nullable()->after('description');
            $table->string('phone')->nullable()->after('logo');
            $table->text('address')->nullable()->after('phone');
            
            // Business details
            $table->string('business_type')->nullable()->after('address'); // Jenis usaha
            $table->string('entitas')->nullable()->after('business_type'); // Entitas (PT, CV, UMKM, dll)
            $table->year('established_year')->nullable()->after('business_type');
            
            // Verification & status enhancement
            $table->timestamp('approved_at')->nullable()->after('status');
            $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->onDelete('set null');
            
            $table->index(['village_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropForeign(['village_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'village_id',
                'description',
                'logo',
                'phone',
                'address',
                'business_type',
                'established_year',
                'approved_at',
                'approved_by'
            ]);
        });
    }
};
