<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\PermintaanBarang; // Pastikan model ini ada dan sesuai
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Menampilkan dashboard admin dengan statistik ringkas.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil total jumlah dari masing-masing tabel
        $totalSuppliers = Supplier::count();
        $totalBarang = Barang::count();
        $totalBarangMasuk = BarangMasuk::count();
        
        // Asumsi 'Permintaan Baru' berarti permintaan yang belum diproses.
        // Anda mungkin perlu menyesuaikan kondisi 'status' sesuai dengan logika aplikasi Anda.
        // Contoh: 'pending', 'baru', 'menunggu_persetujuan'
        $totalPermintaan = PermintaanBarang::where('status', 'pending')->count(); 
        // Jika Anda ingin total semua permintaan tanpa memandang status, gunakan:
        // $totalPermintaan = PermintaanBarang::count();


        // Meneruskan data ke view dashboard
        return view('admin.dashboard', compact(
            'totalSuppliers',
            'totalBarang',
            'totalBarangMasuk',
            'totalPermintaan'
        ));
    }
}

