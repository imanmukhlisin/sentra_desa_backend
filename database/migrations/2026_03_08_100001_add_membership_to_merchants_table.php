<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->timestamp('membership_expires_at')->nullable()->after('approved_at');
            $table->string('renewal_proof')->nullable()->after('membership_expires_at');
            $table->timestamp('renewal_requested_at')->nullable()->after('renewal_proof');
            $table->enum('renewal_status', ['none', 'pending', 'approved', 'rejected'])
                ->default('none')
                ->after('renewal_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn([
                'membership_expires_at',
                'renewal_proof',
                'renewal_requested_at',
                'renewal_status',
            ]);
        });
    }
};
