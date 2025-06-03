<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Supplier;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    // Tampilkan daftar barang
    public function index()
    {
        $barangs = Barang::with('supplier')->get();
        return view('barang.index', compact('barangs'));
    }

    // Tampilkan form tambah barang baru
    public function create()
    {
        $suppliers = Supplier::all();
        return view('barang.create', compact('suppliers'));
    }

    // Simpan barang baru
    public function store(Request $request)
{
    $request->validate([
        'kode_barang' => 'required|unique:barang,kode_barang',
        'nama_barang' => 'required|string|max:255',
        // hapus validasi stok karena stok otomatis 0
        'satuan' => 'required|string|max:50',
        'supplier_id' => 'nullable|exists:supplier,id',
        'keterangan' => 'nullable|string',
    ]);

    $data = $request->all();
    $data['stok'] = 0; // Set stok default 0 saat create

    Barang::create($data);

    return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
}


    // Tampilkan form edit barang
    public function edit(Barang $barang)
    {
        $suppliers = Supplier::all();
        return view('barang.edit', compact('barang', 'suppliers'));
    }

    // Update data barang
    public function update(Request $request, Barang $barang)
{
    $request->validate([
        'kode_barang' => 'required|unique:barang,kode_barang,' . $barang->id,
        'nama_barang' => 'required|string|max:255',
        'stok' => 'required|integer|min:0',
        'satuan' => 'required|string|max:50',
        'supplier_id' => 'nullable|exists:supplier,id',
        'keterangan' => 'nullable|string',
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
