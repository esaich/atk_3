<?php

namespace App\Http\Controllers;

use App\Models\PermintaanBarang;
use App\Models\BarangKeluar;
use App\Models\Barang;
use Illuminate\Http\Request;

class PermintaanAdminController extends Controller
{
    // Tampilkan semua permintaan (pending) dari semua divisi
    public function index()
    {
        $permintaans = PermintaanBarang::with('user', 'barang')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.permintaan.index', compact('permintaans'));
    }

    // Approve permintaan
    public function approve($id)
    {
        $permintaan = PermintaanBarang::findOrFail($id);

        if ($permintaan->status != 'pending') {
            return redirect()->back()->with('error', 'Permintaan sudah diproses.');
        }

        $barang = Barang::findOrFail($permintaan->barang_id);

        if ($barang->stok < $permintaan->jumlah) {
            return redirect()->back()->with('error', 'Stok barang tidak cukup.');
        }

        // Kurangi stok barang
        $barang->stok -= $permintaan->jumlah;
        $barang->save();

        // Update status permintaan
        $permintaan->status = 'disetujui';
        $permintaan->alasan = 'Disetujui oleh admin';
        $permintaan->save();

        // Catat barang keluar
        BarangKeluar::create([
            'permintaan_id' => $permintaan->id,
            'barang_id' => $permintaan->barang_id,
            'jumlah_keluar' => $permintaan->jumlah,
            'tanggal_keluar' => now(),
            'keterangan' => 'Pengeluaran untuk permintaan ID: ' . $permintaan->id . ' oleh user ' . $permintaan->user->name,
        ]);

        return redirect()->route('admin.permintaan.index')->with('success', 'Permintaan berhasil disetujui dan stok diperbarui.');
    }

    // Reject permintaan
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|max:255',
        ]);

        $permintaan = PermintaanBarang::findOrFail($id);

        if ($permintaan->status != 'pending') {
            return redirect()->back()->with('error', 'Permintaan sudah diproses.');
        }
        
        $permintaan->status = 'ditolak';
        $permintaan->alasan = $request->alasan;
        $permintaan->save();

        return redirect()->route('admin.permintaan.index')->with('success', 'Permintaan berhasil ditolak.');
    }
}
