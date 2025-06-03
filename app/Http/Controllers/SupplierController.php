<?php

namespace App\Http\Controllers;


use App\Models\Supplier;
use App\Models\Suppliers;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // Tampilkan daftar supplier
    public function index()
    {
        $suppliers = Supplier::all();
        return view('supplier.index', compact('suppliers'));
    }

    // Tampilkan form tambah supplier
    public function create()
    {
        return view('supplier.create');
    }

    // Simpan supplier baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        Supplier::create($request->all());

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    // Tampilkan form edit supplier
    public function edit(Supplier $supplier)
    {
        return view('supplier.edit', compact('supplier'));
    }

    // Update data supplier
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $supplier->update($request->all());

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diupdate.');
    }

    // Hapus supplier
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
