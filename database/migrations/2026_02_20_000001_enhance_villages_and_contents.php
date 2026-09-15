<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom profil detail ke tabel villages
        Schema::table('villages', function (Blueprint $table) {
            $table->text('vision')->nullable()->after('description');
            $table->text('mission')->nullable()->after('vision');
            $table->text('history')->nullable()->after('mission');
            $table->string('head_name')->nullable()->after('history'); // Kepala Desa
            $table->integer('dusun_count')->default(0)->after('area_size');
        });

        // Tambah village_id ke village_contents agar bisa relasi per desa
        Schema::table('village_contents', function (Blueprint $table) {
            $table->foreignId('village_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->onDelete('cascade');
            $table->index('village_id');
        });
    }

    public function down(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            $table->dropColumn(['vision', 'mission', 'history', 'head_name', 'dusun_count']);
        });

        Schema::table('village_contents', function (Blueprint $table) {
            $table->dropForeign(['village_id']);
            $table->dropColumn('village_id');
        });
    }
};
