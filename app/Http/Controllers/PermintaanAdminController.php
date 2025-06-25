<?php
namespace App\Http\Controllers;

use App\Models\PermintaanBarang;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermintaanAdminController extends Controller
{
    /**
     * Menampilkan seluruh riwayat permintaan barang (pending, disetujui, ditolak).
     * Metode ini sekarang berfungsi sebagai index utama untuk melihat semua permintaan.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Ambil semua permintaan barang beserta data user dan barang terkait
        $permintaans = PermintaanBarang::with('user', 'barang')
            ->orderBy('created_at', 'desc') // Urutkan berdasarkan tanggal terbaru
            ->get();

        // Kirim data permintaan ke view admin.permintaan.index
        return view('admin.permintaan.index', compact('permintaans'));
    }

    // Metode riwayat() yang sebelumnya mungkin sudah tidak diperlukan jika index() sudah menampilkan semua.
    // Jika Anda masih ingin memiliki halaman terpisah untuk "hanya pending" dan "riwayat penuh",
    // Anda bisa mempertahankan metode riwayat() ini dan metode index() akan memfilter pending.
    // Namun, berdasarkan permintaan Anda, metode index() sekarang akan menampilkan semua.
    /*
    public function riwayat()
    {
        $permintaans = PermintaanBarang::with('user', 'barang')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.permintaan.hasil', compact('permintaans'));
    }
    */

    /**
     * Menyetujui permintaan barang dan mengurangi stok barang.
     *
     * @param int $id ID dari permintaan barang.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        // Memulai transaksi database untuk memastikan konsistensi data
        DB::beginTransaction();

        try {
            $permintaan = PermintaanBarang::findOrFail($id);

            // Pastikan permintaan masih pending sebelum disetujui
            if ($permintaan->status !== 'pending') {
                DB::rollBack();
                // Setelah mengubah index() untuk menampilkan semua, kita mungkin ingin me-redirect ke index()
                return redirect()->route('admin.permintaan.index')->with('error', 'Permintaan ini sudah tidak dalam status pending.');
            }

            $barang = $permintaan->barang; // Mengakses relasi barang

            // Memeriksa apakah barang ditemukan
            if (!$barang) {
                DB::rollBack();
                return redirect()->route('admin.permintaan.index')->with('error', 'Barang terkait tidak ditemukan.');
            }

            // Memeriksa stok barang yang tersedia
            if ($barang->stok < $permintaan->jumlah) {
                DB::rollBack();
                return redirect()->route('admin.permintaan.index')->with('error', 'Stok ' . $barang->nama_barang . ' tidak mencukupi. Hanya tersedia ' . $barang->stok . '.');
            }

            // Kurangi stok barang
            $barang->stok -= $permintaan->jumlah;
            $barang->save(); // Simpan perubahan stok barang

            // Ubah status permintaan menjadi 'disetujui'
            $permintaan->status = 'disetujui';
            $permintaan->save(); // Simpan perubahan status permintaan

            DB::commit(); // Komit transaksi jika semua berhasil

            return redirect()->route('admin.permintaan.index')->with('success', 'Permintaan telah disetujui dan stok barang berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi error
            Log::error("Error approving request ID {$id}: " . $e->getMessage()); // Log error untuk debugging
            return redirect()->route('admin.permintaan.index')->with('error', 'Terjadi kesalahan saat menyetujui permintaan: ' . $e->getMessage());
        }
    }

    /**
     * Menolak permintaan barang dengan alasan.
     *
     * @param \Illuminate\Http\Request $request Objek request HTTP.
     * @param int $id ID dari permintaan barang.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string',
        ]);

        $permintaan = PermintaanBarang::findOrFail($id);

        // Pastikan permintaan masih pending sebelum ditolak
        if ($permintaan->status !== 'pending') {
            return redirect()->route('admin.permintaan.index')->with('error', 'Permintaan ini sudah tidak dalam status pending.');
        }

        $permintaan->status = 'ditolak';
        $permintaan->alasan = $request->alasan;
        $permintaan->save();

        return redirect()->route('admin.permintaan.index')->with('success', 'Permintaan telah ditolak.');
    }
}
