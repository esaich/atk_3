<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    // Tampilkan daftar barang keluar
    public function index()
    {
        $barangKeluars = BarangKeluar::with('permintaan.user', 'barang')
            ->orderBy('tanggal_keluar', 'desc')
            ->get();

        return view('admin.barang-keluar.index', compact('barangKeluars'));
    }
}
