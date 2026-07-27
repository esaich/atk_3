<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller
{
    /**
     * Tampilkan daftar semua barang.
     */
    public function index()
    {
        $barangs = Barang::all();
        return view('barang.index', compact('barangs'));
    }

    /**
     * Tampilkan formulir untuk membuat barang baru.
     */
    public function create()
    {
        return view('barang.create');
    }

    /**
     * Simpan barang baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi data input.
        // PERBAIKAN: Mengganti 'unique:barangs' menjadi 'unique:barang'
        $validated = $request->validate([
            'kode_barang' => 'required|unique:barang',
            'nama_barang' => 'required',
            'satuan' => 'required',
        ]);

        Barang::create($validated);

        // Menggunakan notifikasi berbasis session
        return redirect()->route('barang.index')->with('success', 'Data barang berhasil ditambahkan!');
    }

    /**
     * Tampilkan detail barang tertentu.
     */

    /**
     * Tampilkan formulir untuk mengedit barang.
     */
    public function edit(Barang $barang)
    {
        return view('barang.edit', compact('barang'));
    }

    /**
     * Perbarui data barang di database.
     */
    public function update(Request $request, Barang $barang)
    {
        // Validasi data input.
        // PERBAIKAN: Mengganti 'unique:barangs' menjadi 'unique:barang'
        $validated = $request->validate([
            'kode_barang' => 'required|unique:barang,kode_barang,' . $barang->id,
            'nama_barang' => 'required',
            'stok' => 'required|numeric|min:0',
            'satuan' => 'required',
        ]);

        $barang->update($validated);

        // Menggunakan notifikasi berbasis session
        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diupdate!');
    }

    /**
     * Hapus barang dari database.
     */
    public function destroy(Barang $barang)
    {
        // Cek dulu apakah barang ini masih punya riwayat transaksi.
        // Catatan: FK di migration pakai onDelete('cascade'), BUKAN restrict,
        // jadi $barang->delete() TIDAK akan pernah throw exception walau ada riwayat.
        $punyaRiwayat = $barang->barangMasuk()->exists()
            || $barang->barangKeluar()->exists()
            || $barang->permintaanBarang()->exists();

        if ($punyaRiwayat) {
            return redirect()->route('barang.index')
                ->with('error', 'Barang tidak bisa dihapus karena masih ada di riwayat barang masuk, barang keluar, atau permintaan barang.');
        }

        try {
            $barang->delete();
            return redirect()->route('barang.index')->with('success', 'Data barang berhasil dihapus!');
        } catch (\Exception $e) {
            // Fallback untuk error tak terduga lain (bukan soal riwayat),
            // misal koneksi DB putus, dsb.
            return redirect()->route('barang.index')->with('error', 'Barang gagal dihapus. Silakan coba lagi.');
        }
    }

    /**
     * Unduh daftar semua barang sebagai file PDF.
     */
    public function downloadPdf()
    {
        $barangs = Barang::all();

        // Mengirim data ke view 'barang.pdf_list' untuk dicetak
        $pdf = Pdf::loadView('barang.pdf_list', compact('barangs'));

        // Mengunduh file PDF dengan nama 'daftar_barang.pdf'
        return $pdf->download('daftar_barang.pdf');
    }
}
