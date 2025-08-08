<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\Barang;
use App\Models\User; // Import model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan Anda sudah menginstal dan mengkonfigurasi pustaka ini

class BarangKeluarController extends Controller
{
    /**
     * Menampilkan daftar semua barang keluar dengan opsi filter.
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

        // Filter berdasarkan user_id yang dipilih (dari select box)
        if ($request->filled('user_id')) {
            $query->whereHas('permintaan.user', function ($q) use ($request) {
                $q->where('id', $request->user_id);
            });
        }

        // Order by latest date and get the results
        $barangKeluars = $query->orderBy('tanggal_keluar', 'desc')->get();

        // Pass filter values back to the view to pre-fill the form
        $filterValues = $request->only(['start_date', 'end_date', 'month', 'year', 'user_id']);

        // Dapatkan semua pengguna untuk dropdown filter
        $users = User::all();

        // Mengirim data barang keluar, nilai filter, dan daftar user ke view
        return view('admin.barang-keluar.index', compact('barangKeluars', 'filterValues', 'users'));
    }

    /**
     * Mencetak daftar barang keluar ke PDF.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function cetakPdf(Request $request)
    {
        // Jalankan logika query yang sama seperti pada metode index
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

        if ($request->filled('user_id')) {
            $query->whereHas('permintaan.user', function ($q) use ($request) {
                $q->where('id', $request->user_id);
            });
        }
        
        $barangKeluars = $query->orderBy('tanggal_keluar', 'desc')->get();
        
        // Buat PDF dari tampilan 'admin.barang-keluar.cetak'
        $pdf = Pdf::loadView('admin.barang-keluar.cetak', compact('barangKeluars'));
        
        // Kembalikan file PDF untuk diunduh
        return $pdf->download('laporan-barang-keluar-'.date('Y-m-d').'.pdf');
    }
}
