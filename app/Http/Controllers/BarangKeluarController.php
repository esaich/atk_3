<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\Barang;
use App\Models\Payment;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // Import Carbon for date handling

class BarangKeluarController extends Controller
{
    /**
     * Menampilkan daftar semua barang keluar dengan opsi filter.
     * Barang keluar ini otomatis tercatat ketika permintaan disetujui oleh admin.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Start with the base query, eager load relationships
        $query = BarangKeluar::with('permintaan.user', 'barang');

        // Apply filters based on request parameters
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_keluar', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_keluar', '<=', $request->end_date);
        }

        if ($request->filled('month')) {
            $query->whereMonth('tanggal_keluar', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('tanggal_keluar', $request->year);
        }

        // Order by latest date
        $barangKeluars = $query->orderBy('tanggal_keluar', 'desc')->get();

        // Pass filter values back to the view to pre-fill the form
        $filterValues = $request->only(['start_date', 'end_date', 'month', 'year']);

        // Mengirim data barang keluar dan nilai filter ke view yang sesuai
        return view('admin.barang-keluar.index', compact('barangKeluars', 'filterValues'));
    }

    // Catatan:
    // Metode 'create' dan 'store' tidak diperlukan di sini karena
    // pembuatan data BarangKeluar dilakukan secara otomatis oleh PermintaanAdminController
    // saat suatu permintaan barang disetujui.

    // Jika di masa depan Anda perlu fungsionalitas untuk mengedit atau menghapus
    // data barang keluar secara manual dari sisi admin, Anda bisa menambahkan
    // metode 'edit', 'update', dan 'destroy' di sini.

    // Menampilkan form tambah barang masuk baru (Ini adalah bagian dari BarangMasukController, tidak relevan di sini)
    // public function create()
    // {
    //     $barangs = Barang::all();
    //     $suppliers = Supplier::all();
    //     return view('barang-masuk.create', compact('barangs', 'suppliers'));
    // }

    // Menyimpan barang masuk baru (Ini adalah bagian dari BarangMasukController, tidak relevan di sini)
    // public function store(Request $request)
    // {
    //     // ... (kode store BarangMasuk) ...
    // }

    // Menampilkan form edit barang masuk (Ini adalah bagian dari BarangMasukController, tidak relevan di sini)
    // public function edit(BarangMasuk $barangMasuk)
    // {
    //     // ... (kode edit BarangMasuk) ...
    // }

    // Memperbarui data barang masuk (Ini adalah bagian dari BarangMasukController, tidak relevan di sini)
    // public function update(Request $request, BarangMasuk $barangMasuk)
    // {
    //     // ... (kode update BarangMasuk) ...
    // }

    // Menghapus data barang masuk (Ini adalah bagian dari BarangMasukController, tidak relevan di sini)
    // public function destroy(BarangMasuk $barangMasuk)
    // {
    //     // ... (kode destroy BarangMasuk) ...
    // }
}
