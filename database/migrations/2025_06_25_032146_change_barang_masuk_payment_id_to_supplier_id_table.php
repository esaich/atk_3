<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            // Langkah 1: Hapus foreign key 'payment_id' jika ada
            // Penting: Pastikan nama foreign key sesuai jika Laravel tidak membuat nama standar
            // Contoh nama foreign key standar Laravel: barang_masuk_payment_id_foreign
            if (Schema::hasColumn('barang_masuk', 'payment_id')) {
                $table->dropForeign(['payment_id']); // Menghapus foreign key
                $table->dropColumn('payment_id'); // Menghapus kolom
            }

            // Langkah 2: Tambahkan kolom 'supplier_id' baru
            // 'after('barang_id')' untuk menempatkan kolom setelah 'barang_id'
            // 'nullable()' jika barang masuk bisa tanpa supplier (opsional)
            // 'onDelete('set null')' akan mengatur supplier_id menjadi NULL jika supplier dihapus
            // Jika Anda ingin record barang_masuk ikut terhapus saat supplier dihapus, gunakan onDelete('cascade')
            $table->foreignId('supplier_id')
                  ->nullable() // Sesuaikan nullable sesuai kebutuhan Anda
                  ->constrained('supplier') // Pastikan tabel 'supplier' sudah ada saat migrasi ini dijalankan
                  ->onDelete('set null') // Sesuaikan onDelete sesuai kebutuhan Anda (set null, cascade, restrict)
                  ->after('barang_id');
        });
    }

    /**
     * Balikkan migrasi (undo).
     */
    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            // Langkah 1 (undo): Hapus foreign key 'supplier_id'
            if (Schema::hasColumn('barang_masuk', 'supplier_id')) {
                $table->dropForeign(['supplier_id']);
                $table->dropColumn('supplier_id');
            }

            // Langkah 2 (undo): Kembalikan kolom 'payment_id'
            // Ini akan mengembalikan kolom payment_id, tetapi tanpa data lama
            $table->foreignId('payment_id')
                  ->nullable() // Sesuaikan nullable asli jika Anda punya
                  ->constrained('payments')
                  ->onDelete('cascade') // Sesuaikan onDelete asli
                  ->after('barang_id');
        });
    }
};
