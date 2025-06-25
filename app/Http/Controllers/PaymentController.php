<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Supplier; // Pastikan ini diimport
use App\Models\BarangMasuk; // Pastikan ini diimport
use Illuminate\Http\Request;
use Carbon\Carbon; // Untuk memastikan format tanggal yang benar
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    // Tampilkan daftar pembayaran
    public function index()
    {
        $payments = Payment::with('supplier')->get();
        return view('payment.index', compact('payments'));
    }

    // Tampilkan form tambah pembayaran
    public function create()
    {
        $suppliers = Supplier::all();
        return view('payment.create', compact('suppliers'));
    }

    // Simpan pembayaran baru
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
            'total_harga' => 'required|numeric|min:0',
            'tanggal_bayar' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        Payment::create($request->all());

        return redirect()->route('payment.index')->with('success', 'Pembayaran berhasil ditambahkan.');
    }

    // Tampilkan form edit pembayaran
    public function edit(Payment $payment)
    {
        $suppliers = Supplier::all();
        return view('payment.edit', compact('payment', 'suppliers'));
    }

    // Update data pembayaran
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
            'total_harga' => 'required|numeric|min:0',
            'tanggal_bayar' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $payment->update($request->all());

        return redirect()->route('payment.index')->with('success', 'Pembayaran berhasil diupdate.');
    }

    // Hapus pembayaran
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('payment.index')->with('success', 'Pembayaran berhasil dihapus.');
    }

    /**
     * Menampilkan detail pembayaran dan barang masuk yang terkait.
     *
     * @param \App\Models\Payment $payment
     * @return \Illuminate\View\View
     */
    public function show(Payment $payment)
    {
        // Pastikan payment memiliki relasi supplier
        $payment->load('supplier');

        // Cari semua BarangMasuk yang terkait dengan supplier dan tanggal pembayaran ini
        // Kita menggunakan tanggal saja karena payment diakumulasikan per hari per supplier
        $tanggalBayarFormatted = $payment->tanggal_bayar->format('Y-m-d');

        $barangMasuks = BarangMasuk::with('barang')
            ->where('supplier_id', $payment->supplier_id)
            ->whereDate('tanggal_masuk', $tanggalBayarFormatted)
            ->get();
        
        // Catatan: Anda mungkin ingin memfilter ini lebih lanjut jika ada banyak barang masuk
        // yang cocok dengan supplier dan tanggal yang sama tetapi tidak terkait dengan payment ini.
        // Namun, berdasarkan logika firstOrCreate di BarangMasukController, ini seharusnya akurat.

        return view('payment.detail', compact('payment', 'barangMasuks'));
    }

    public function downloadPdf(Payment $payment)
{
    $payment->load('supplier');
    $tanggalBayarFormatted = $payment->tanggal_bayar->format('Y-m-d');

    $barangMasuks = BarangMasuk::with('barang')
        ->where('supplier_id', $payment->supplier_id)
        ->whereDate('tanggal_masuk', $tanggalBayarFormatted)
        ->get();

    // Load view yang ingin dijadikan PDF (bisa juga 'payment.detail' langsung)
    // Anda mungkin ingin membuat view khusus yang lebih rapi untuk PDF
    $pdf = Pdf::loadView('payment.detail', compact('payment', 'barangMasuks'));

    // Opsional: Atur ukuran kertas dan orientasi
    // $pdf->setPaper('A4', 'landscape');

    // Mengunduh PDF
    return $pdf->download('invoice-pembayaran-' . $payment->id . '.pdf');
}
}
