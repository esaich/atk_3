<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     * Menambahkan 'bendahara' ke enum kolom users.role.
     * Memakai Blueprint::change() (bukan raw SQL) supaya portable baik di MySQL
     * maupun SQLite (SQLite meng-enforce enum lewat CHECK constraint yang
     * hanya bisa diubah dengan merekonstruksi tabel — ->change() menangani ini).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'divisi', 'bendahara'])->default('admin')->change();
        });
    }

    /**
     * Balikkan migrasi.
     * Catatan: jika sudah ada user dengan role 'bendahara', rollback ini akan gagal
     * kecuali user tersebut dipindah/dihapus dulu, karena enum lama tidak memuat
     * 'bendahara'.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'divisi'])->default('admin')->change();
        });
    }
};