<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangKeluarController extends Controller
{
    /**
     * Query dasar dengan filter yang dipakai ulang oleh index() dan cetakPdf(),
     * supaya logic filter tidak duplikat di dua tempat.
     */
    private function filteredQuery(Request $request)
    {
        $query = BarangKeluar::with('permintaan.user', 'barang');

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

        return $query;
    }

    public function index(Request $request)
    {
        $barangKeluars = $this->filteredQuery($request)->orderBy('tanggal_keluar', 'desc')->get();

        $filterValues = $request->only(['start_date', 'end_date', 'month', 'year', 'user_id']);

        $users = User::all();

        return view('admin.barang-keluar.index', compact('barangKeluars', 'filterValues', 'users'));
    }

    public function cetakPdf(Request $request)
    {
        $barangKeluars = $this->filteredQuery($request)->orderBy('tanggal_keluar', 'desc')->get();

        $pdf = Pdf::loadView('admin.barang-keluar.cetak', compact('barangKeluars'));

        return $pdf->download('laporan-barang-keluar-'.date('Y-m-d').'.pdf');
    }
}