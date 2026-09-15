<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('village_fund_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->onDelete('cascade');
            $table->year('fiscal_year'); // Tahun anggaran
            $table->string('period')->default('tahunan'); // tahunan, semester1, semester2

            // ── Belanja (Expenditure) ──
            $table->decimal('belanja_pemerintahan', 18, 2)->default(0); // Penyelenggaraan Pemerintahan Desa
            $table->decimal('belanja_pembangunan', 18, 2)->default(0); // Pelaksanaan Pembangunan Desa
            $table->decimal('belanja_pembinaan', 18, 2)->default(0);   // Pembinaan Kemasyarakatan
            $table->decimal('belanja_pemberdayaan', 18, 2)->default(0); // Pemberdayaan Masyarakat
            $table->decimal('belanja_bencana', 18, 2)->default(0);     // Penanggulangan Bencana, Darurat, Mendesak

            // ── Pendapatan (Income) ──
            $table->decimal('pendapatan_asli_desa', 18, 2)->default(0);  // PADes
            $table->decimal('dana_desa', 18, 2)->default(0);             // DD (dari APBN)
            $table->decimal('bagi_hasil_pajak', 18, 2)->default(0);      // Bagi Hasil Pajak & Retribusi Daerah
            $table->decimal('alokasi_dana_desa', 18, 2)->default(0);     // ADD
            $table->decimal('bantuan_keuangan_kab', 18, 2)->default(0);  // Bantuan Keuangan Kabupaten
            $table->decimal('bantuan_keuangan_prov', 18, 2)->default(0); // Bantuan Keuangan Provinsi
            $table->decimal('pendapatan_lainnya', 18, 2)->default(0);    // Pendapatan Lain-lain

            // ── Pembiayaan (Financing) ──
            $table->decimal('penerimaan_pembiayaan', 18, 2)->default(0);
            $table->decimal('silpa', 18, 2)->default(0); // Sisa Lebih Pembiayaan Anggaran

            // ── Info Kepala Desa ──
            $table->string('head_name')->nullable(); // Nama Kepala Desa/Lurah
            $table->string('head_title')->nullable(); // Jabatan (Kepala Desa / Lurah / dll)

            // ── Infografis/Spanduk ──
            $table->string('infographic_image')->nullable(); // Foto spanduk publikasi

            // ── Status ──
            $table->boolean('is_published')->default(false);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['village_id', 'fiscal_year', 'period']);
            $table->index('fiscal_year');
            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('village_fund_reports');
    }
};
