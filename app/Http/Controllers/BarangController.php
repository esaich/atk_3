<?php

namespace App\Http\Controllers;

use App\Models\Barang;
// Hapus use App\Models\Supplier; karena tidak lagi digunakan
// use App\Models\Supplier; 
use Illuminate\Http\Request;

class BarangController extends Controller
{
    // Tampilkan daftar barang
    public function index()
    {
        // Hapus eager loading 'supplier'
        $barangs = Barang::get(); 
        return view('barang.index', compact('barangs'));
    }

    // Tampilkan form tambah barang baru
    public function create()
    {
        // Hapus fetching suppliers
        // $suppliers = Supplier::all(); 
        return view('barang.create'); // Hapus compact('suppliers')
    }

    // Simpan barang baru
    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|unique:barang,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            // Hapus validasi supplier_id dan keterangan
            // 'supplier_id' => 'nullable|exists:supplier,id',
            // 'keterangan' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['stok'] = 0; // Set stok default 0 saat create

        Barang::create($data);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    // Tampilkan form edit barang
    public function edit(Barang $barang)
    {
        // Hapus fetching suppliers
        // $suppliers = Supplier::all(); 
        return view('barang.edit', compact('barang')); // Hapus 'suppliers' dari compact
    }

    // Update data barang
    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'kode_barang' => 'required|unique:barang,kode_barang,' . $barang->id,
            'nama_barang' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
            // Hapus validasi supplier_id dan keterangan
            // 'supplier_id' => 'nullable|exists:supplier,id',
            // 'keterangan' => 'nullable|string',
        ]);

        $barang->update($request->all());

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diupdate.');
    }

    // Hapus barang
    public function destroy(Barang $barang)
    {
        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }
}