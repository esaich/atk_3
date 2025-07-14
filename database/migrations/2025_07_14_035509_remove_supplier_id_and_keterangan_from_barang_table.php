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
        Schema::table('barang', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu jika ada
            // Pastikan nama constraint sesuai dengan yang digunakan Laravel,
            // biasanya 'nama_tabel_foreign_key_column_foreign'
            $table->dropForeign(['supplier_id']); // Menghapus foreign key constraint
            $table->dropColumn('supplier_id');   // Menghapus kolom supplier_id
            $table->dropColumn('keterangan');    // Menghapus kolom keterangan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            // Jika Anda ingin mengembalikan kolom ini saat rollback, tambahkan di sini
            // Perhatikan bahwa ini akan membuat kolom tanpa data jika rollback dilakukan
            $table->foreignId('supplier_id')->nullable()->constrained('supplier')->onDelete('set null');
            $table->text('keterangan')->nullable();
        });
    }
};