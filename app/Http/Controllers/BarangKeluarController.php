<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    /**
     * Menampilkan daftar semua barang keluar.
     * Barang keluar ini otomatis tercatat ketika permintaan disetujui oleh admin.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil semua data barang keluar
        // Dengan eager loading relasi 'permintaan' dan 'barang' untuk menghindari N+1 query problem.
        // Relasi 'permintaan' juga di-eager load relasi 'user' (permintaan.user)
        $barangKeluars = BarangKeluar::with('permintaan.user', 'barang')
            ->orderBy('tanggal_keluar', 'desc') // Mengurutkan berdasarkan tanggal keluar terbaru
            ->get(); // Mengambil semua data

        // Mengirim data barang keluar ke view yang sesuai
        return view('admin.barang-keluar.index', compact('barangKeluars'));
    }

    // Catatan:
    // Metode 'create' dan 'store' tidak diperlukan di sini karena
    // pembuatan data BarangKeluar dilakukan secara otomatis oleh PermintaanAdminController
    // saat suatu permintaan barang disetujui.

    // Jika di masa depan Anda perlu fungsionalitas untuk mengedit atau menghapus
    // data barang keluar secara manual dari sisi admin, Anda bisa menambahkan
    // metode 'edit', 'update', dan 'destroy' di sini.
}
