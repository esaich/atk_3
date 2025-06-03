<?php

namespace App\Http\Controllers;

use App\Models\PermintaanBarang;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermintaanBarangController extends Controller
{
    // Tampilkan daftar permintaan barang milik user divisi yang login
    public function index()
    {
        $userId = Auth::id();
        $permintaans = PermintaanBarang::with('barang')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('divisi.permintaan-barang.index', compact('permintaans'));
    }

    // Form buat permintaan barang baru
    public function create()
    {
        $barangs = Barang::all();
        return view('divisi.permintaan-barang.create', compact('barangs'));
    }

    // Simpan data permintaan baru
    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'alasan' => 'nullable|string',
        ]);

        $data = $request->only(['barang_id', 'jumlah', 'alasan']);
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        PermintaanBarang::create($data);

        return redirect()->route('divisi.permintaan-barang.index')->with('success', 'Permintaan barang berhasil diajukan.');
    }

    // Form edit permintaan barang
    public function edit(PermintaanBarang $permintaan_barang)
    {
        $this->authorizeRequestOwner($permintaan_barang);

        $barangs = Barang::all();
        return view('divisi.permintaan-barang.edit', compact('permintaan_barang', 'barangs'));
    }

    // Update permintaan barang
    public function update(Request $request, PermintaanBarang $permintaan_barang)
    {
        $this->authorizeRequestOwner($permintaan_barang);

        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'alasan' => 'nullable|string',
        ]);

        $permintaan_barang->update($request->only(['barang_id', 'jumlah', 'alasan']));

        return redirect()->route('divisi.permintaan-barang.index')->with('success', 'Permintaan barang berhasil diperbarui.');
    }

    // Batalkan / hapus permintaan
    public function destroy(PermintaanBarang $permintaan_barang)
    {
        $this->authorizeRequestOwner($permintaan_barang);

        $permintaan_barang->delete();

        return redirect()->route('divisi.permintaan-barang.index')->with('success', 'Permintaan barang berhasil dibatalkan.');
    }

    // Helper untuk memastikan user hanya bisa mengubah permintaannya sendiri
    private function authorizeRequestOwner(PermintaanBarang $permintaan_barang)
    {
        if ($permintaan_barang->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
