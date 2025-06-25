<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Barang;
use App\Models\Payment; // Pastikan model Payment sudah di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import DB facade untuk transaksi

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

        DB::beginTransaction(); // Mulai transaksi database

        try {
            // 1. Buat entri BarangMasuk
            $barangMasuk = BarangMasuk::create($request->all());

            // 2. Update stok barang
            $barang = Barang::findOrFail($request->barang_id);
            $barang->stok += $request->jumlah_masuk;
            $barang->save();

            // 3. Update total_harga di Payment terkait
            $payment = Payment::findOrFail($request->payment_id);
            $subTotal = $request->jumlah_masuk * $request->harga_satuan;
            
            // Menambahkan subTotal ke total_harga yang sudah ada di Payment
            // Ini mengasumsikan satu Payment bisa melayani banyak BarangMasuk
            $payment->total_harga += $subTotal;
            $payment->save();

            DB::commit(); // Komit transaksi jika semua berhasil

            return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil ditambahkan dan total pembayaran diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika ada kegagalan
            // Opsional: Log error untuk debugging lebih lanjut
            // Log::error('Error saat menyimpan barang masuk atau memperbarui payment: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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

        DB::beginTransaction(); // Mulai transaksi database

        try {
            // Hitung kembali subTotal lama sebelum perubahan
            $oldSubTotal = $barangMasuk->jumlah_masuk * $barangMasuk->harga_satuan;
            $currentPayment = $barangMasuk->payment; // Dapatkan payment sebelum update barangMasuk

            // Update stok: kurangi stok lama dari barang lama
            $barangLama = Barang::findOrFail($barangMasuk->barang_id);
            $barangLama->stok -= $barangMasuk->jumlah_masuk;
            $barangLama->save();

            // Update data BarangMasuk
            $barangMasuk->update($request->all());

            // Tambah stok baru ke barang baru (atau barang yang sama jika barang_id tidak berubah)
            $barangBaru = Barang::findOrFail($request->barang_id);
            $barangBaru->stok += $request->jumlah_masuk;
            $barangBaru->save();

            // Update total_harga di Payment terkait
            $newSubTotal = $barangMasuk->jumlah_masuk * $barangMasuk->harga_satuan;

            // Jika payment_id berubah, sesuaikan total_harga di payment lama dan payment baru
            if ($barangMasuk->payment_id !== $currentPayment->id) {
                // Kurangi dari payment lama
                $currentPayment->total_harga -= $oldSubTotal;
                $currentPayment->save();

                // Tambahkan ke payment baru
                $newPayment = Payment::findOrFail($barangMasuk->payment_id);
                $newPayment->total_harga += $newSubTotal;
                $newPayment->save();
            } else {
                // Jika payment_id tidak berubah, sesuaikan total_harga di payment yang sama
                $currentPayment->total_harga = ($currentPayment->total_harga - $oldSubTotal) + $newSubTotal;
                $currentPayment->save();
            }

            DB::commit(); // Komit transaksi jika semua berhasil

            return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil diupdate dan total pembayaran diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika ada kegagalan
            // Opsional: Log error untuk debugging lebih lanjut
            // Log::error('Error saat mengupdate barang masuk atau payment: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Hapus data barang masuk
    public function destroy(BarangMasuk $barangMasuk)
    {
        DB::beginTransaction(); // Mulai transaksi database

        try {
            // Kurangi stok barang
            $barang = Barang::findOrFail($barangMasuk->barang_id);
            $barang->stok -= $barangMasuk->jumlah_masuk;
            $barang->save();

            // Kurangi total_harga dari Payment terkait
            $payment = Payment::findOrFail($barangMasuk->payment_id);
            $subTotal = $barangMasuk->jumlah_masuk * $barangMasuk->harga_satuan;
            $payment->total_harga -= $subTotal;
            $payment->save();

            // Hapus entri BarangMasuk
            $barangMasuk->delete();

            DB::commit(); // Komit transaksi jika semua berhasil

            return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil dihapus dan total pembayaran diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika ada kegagalan
            // Opsional: Log error untuk debugging lebih lanjut
            // Log::error('Error saat menghapus barang masuk atau memperbarui payment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
