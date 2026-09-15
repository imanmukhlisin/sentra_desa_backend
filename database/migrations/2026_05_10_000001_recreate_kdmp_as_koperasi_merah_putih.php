<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('kdmp');

        Schema::create('kdmp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->onDelete('cascade');

            $table->string('name');                        // "KopDes Merah Putih Samusa"
            $table->string('code', 30)->unique();          // kode registrasi
            $table->string('nomor_badan_hukum')->nullable();

            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();

            $table->enum('status', ['aktif', 'persiapan', 'tidak_aktif'])->default('persiapan');
            $table->json('unit_usaha')->nullable(); // ['simpan_pinjam','perdagangan','pertanian',...]

            $table->string('ketua_name')->nullable();
            $table->string('sekretaris_name')->nullable();
            $table->string('bendahara_name')->nullable();

            $table->unsignedInteger('total_members')->default(0);
            $table->decimal('modal_awal', 15, 2)->nullable();
            $table->decimal('total_assets', 15, 2)->nullable();

            $table->date('established_date')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['village_id', 'status']);
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kdmp');
    }
};
