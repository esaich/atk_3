<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Supplier;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Menampilkan daftar pembayaran.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $payments = Payment::with('supplier')->orderBy('tanggal_bayar', 'desc')->get();
        return view('payment.index', compact('payments'));
    }

    /**
     * Menampilkan detail pembayaran dan barang masuk yang terkait.
     *
     * @param \App\Models\Payment $payment
     * @return \Illuminate\View\View
     */
    public function show(Payment $payment)
    {
        $payment->load('supplier');
        $tanggalBayarFormatted = $payment->tanggal_bayar->format('Y-m-d');

        $barangMasuks = BarangMasuk::with('barang')
            ->where('supplier_id', $payment->supplier_id)
            ->whereDate('tanggal_masuk', $tanggalBayarFormatted)
            ->get();
        
        return view('payment.detail', compact('payment', 'barangMasuks'));
    }

    /**
     * Mengunduh invoice pembayaran dalam format PDF.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf(Payment $payment)
    {
        $payment->load('supplier');
        $tanggalBayarFormatted = $payment->tanggal_bayar->format('Y-m-d');

        $barangMasuks = BarangMasuk::with('barang')
            ->where('supplier_id', $payment->supplier_id)
            ->whereDate('tanggal_masuk', $tanggalBayarFormatted)
            ->get();

        // Menggunakan view khusus untuk PDF
        $pdf = Pdf::loadView('payment.invoice-pdf', compact('payment', 'barangMasuks'));

        // Mengunduh PDF dengan nama file yang spesifik
        return $pdf->download('invoice-pembayaran-' . $payment->id . '.pdf');
    }
}
