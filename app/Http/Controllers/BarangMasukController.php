<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Barang;
use App\Models\Payment;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    // Tampilkan daftar barang masuk
    public function index()
    {
        $barangMasuks = BarangMasuk::with('barang', 'payment')->get();
        return view('barang-masuk.index', compact('barangMasuks'));
    }

    // Tampilkan form tambah barang masuk baru
    public function create()
    {
        $barangs = Barang::all();
        $payments = Payment::all();
        return view('barang-masuk.create', compact('barangs', 'payments'));
    }

    // Simpan barang masuk baru
    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'payment_id' => 'required|exists:payments,id',
            'jumlah_masuk' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
        ]);

        BarangMasuk::create($request->all());

        // Update stok barang
        $barang = Barang::findOrFail($request->barang_id);
        $barang->stok += $request->jumlah_masuk;
        $barang->save();

        return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil ditambahkan.');
    }

    // Tampilkan form edit barang masuk
    public function edit(BarangMasuk $barangMasuk)
    {
        $barangs = Barang::all();
        $payments = Payment::all();
        return view('barang-masuk.edit', compact('barangMasuk', 'barangs', 'payments'));
    }

    // Update data barang masuk
    public function update(Request $request, BarangMasuk $barangMasuk)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'payment_id' => 'required|exists:payments,id',
            'jumlah_masuk' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
        ]);

        // Update stok: kurangi stok lama, tambah stok baru
        $barangLama = Barang::findOrFail($barangMasuk->barang_id);
        $barangLama->stok -= $barangMasuk->jumlah_masuk;
        $barangLama->save();

        $barangMasuk->update($request->all());

        $barangBaru = Barang::findOrFail($request->barang_id);
        $barangBaru->stok += $request->jumlah_masuk;
        $barangBaru->save();

        return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil diupdate.');
    }

    // Hapus data barang masuk
    public function destroy(BarangMasuk $barangMasuk)
    {
        $barang = Barang::findOrFail($barangMasuk->barang_id);
        $barang->stok -= $barangMasuk->jumlah_masuk;
        $barang->save();

        $barangMasuk->delete();

        return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil dihapus.');
    }
}
