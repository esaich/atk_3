<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // Tampilkan daftar payment
    public function index()
    {
        $payments = Payment::with('supplier')->get();
        return view('payment.index', compact('payments'));
    }

    // Form tambah payment baru
    public function create()
    {
        $suppliers = Supplier::all();
        return view('payment.create', compact('suppliers'));
    }

    // Simpan payment baru
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
            'total_harga' => 'required|numeric|min:0',
            'tanggal_bayar' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        Payment::create($request->all());

        return redirect()->route('payment.index')->with('success', 'Payment berhasil ditambahkan.');
    }

    // Form edit payment
    public function edit(Payment $payment)
    {
        $suppliers = Supplier::all();
        return view('payment.edit', compact('payment', 'suppliers'));
    }

    // Update payment
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
            'total_harga' => 'required|numeric|min:0',
            'tanggal_bayar' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $payment->update($request->all());

        return redirect()->route('payment.index')->with('success', 'Payment berhasil diupdate.');
    }

    // Hapus payment
    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payment.index')->with('success', 'Payment berhasil dihapus.');
    }
}
