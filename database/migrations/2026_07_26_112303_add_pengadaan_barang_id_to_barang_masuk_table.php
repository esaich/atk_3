<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     * Menghubungkan barang_masuk (barang benar-benar diterima) ke pengadaan_barang
     * (pengajuan yang sudah disetujui bendahara) yang menjadi dasarnya. Nullable
     * dan nullOnDelete supaya:
     *  - barang masuk tanpa pengadaan tetap bisa dicatat (kasus darurat/koreksi),
     *  - riwayat barang masuk tidak ikut hilang kalau baris pengadaan-nya dihapus.
     */
    public function up(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->foreignId('pengadaan_barang_id')
                ->nullable()
                ->after('barang_id')
                ->constrained('pengadaan_barang')
                ->nullOnDelete();
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pengadaan_barang_id');
        });
    }
};