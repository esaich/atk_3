<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     * Membuat tabel 'pengadaan_barang' untuk mencatat pengajuan barang dari dinas,
     * dengan relasi ke tabel 'supplier'.
     */
    public function up(): void
    {
        Schema::create('pengadaan_barang', function (Blueprint $table) {
            $table->id(); // Kolom ID utama
            $table->string('nama_barang'); // Nama barang yang diajukan
            $table->string('satuan')->nullable(); // Satuan barang (opsional)
            $table->integer('jumlah_diajukan'); // Jumlah barang yang diajukan
            $table->date('tanggal_pengajuan'); // Tanggal pengajuan dibuat
            $table->text('keterangan')->nullable(); // Keterangan tambahan (opsional)
            // Menambahkan kolom foreign key untuk supplier
            $table->foreignId('supplier_id')->constrained('supplier')->onDelete('cascade'); // Relasi ke tabel supplier
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Balikkan migrasi.
     * Menghapus tabel 'pengadaan_barang'.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengadaan_barang');
    }
};
