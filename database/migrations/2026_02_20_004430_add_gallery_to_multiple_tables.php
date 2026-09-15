<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Village Contents - add gallery after image
        Schema::table('village_contents', function (Blueprint $table) {
            $table->json('gallery')->nullable()->after('image');
        });

        // Village Potentials - add gallery after image
        Schema::table('village_potentials', function (Blueprint $table) {
            $table->json('gallery')->nullable()->after('image');
        });

        // BUMDes - add gallery after logo
        Schema::table('bumdes', function (Blueprint $table) {
            $table->json('gallery')->nullable()->after('logo');
        });

        // KDMP - add gallery after cover_image
        Schema::table('kdmp', function (Blueprint $table) {
            $table->json('gallery')->nullable()->after('cover_image');
        });

        // Villages - add gallery after cover_image
        Schema::table('villages', function (Blueprint $table) {
            $table->json('gallery')->nullable()->after('cover_image');
        });
    }

    public function down(): void
    {
        $tables = ['village_contents', 'village_potentials', 'bumdes', 'kdmp', 'villages'];
        foreach ($tables as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropColumn('gallery');
            });
        }
    }
};
