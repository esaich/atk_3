<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     * Menambahkan alur approval ke tabel pengadaan_barang: status pengajuan,
     * siapa yang menyetujui/menolak, kapan, dan catatan (mis. alasan penolakan).
     */
    public function up(): void
    {
        Schema::table('pengadaan_barang', function (Blueprint $table) {
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak'])
                ->default('diajukan')
                ->after('supplier_id');
            $table->foreignId('approved_by')
                ->nullable()
                ->after('status')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('catatan_approval')->nullable()->after('approved_at');
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::table('pengadaan_barang', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['status', 'approved_at', 'catatan_approval']);
        });
    }
};