<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Barang;
use App\Models\Payment;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BarangMasukController extends Controller
{
    /**
     * Catatan Penting:
     * Agar controller ini berfungsi, pastikan model 'Barang' dan 'Supplier' Anda
     * dikonfigurasi untuk menggunakan tabel 'barang' dan 'supplier' (tunggal) di database.
     * File model Barang.php dan Supplier.php harus memiliki: protected $table = 'nama_tabel';
     */

    /**
     * Menampilkan daftar semua barang masuk dengan opsi filter.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = BarangMasuk::with('barang', 'supplier');

        // Menerapkan filter berdasarkan parameter request
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_masuk', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_masuk', '<=', $request->end_date);
        }

        if ($request->filled('month')) {
            $query->whereMonth('tanggal_masuk', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('tanggal_masuk', $request->year);
        }

        // Urutkan berdasarkan tanggal masuk terbaru
        $barangMasuks = $query->orderBy('tanggal_masuk', 'desc')->get();

        // Mengirimkan nilai filter kembali ke view untuk mengisi form
        $filterValues = $request->only(['start_date', 'end_date', 'month', 'year']);

        return view('barang-masuk.index', compact('barangMasuks', 'filterValues'));
    }

    /**
     * Menampilkan form untuk membuat barang masuk baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $barangs = Barang::all();
        $suppliers = Supplier::all();
        return view('barang-masuk.create', compact('barangs', 'suppliers'));
    }

    /**
     * Menyimpan barang masuk baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'supplier_id' => 'required|exists:supplier,id', // Diperbarui untuk merujuk ke tabel 'supplier'
            'jumlah_masuk' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
        ]);
    
        DB::beginTransaction();
    
        try {
            $subTotal = $validatedData['jumlah_masuk'] * $validatedData['harga_satuan'];

            $payment = Payment::firstOrCreate(
                [
                    'supplier_id' => $validatedData['supplier_id'],
                    'tanggal_bayar' => $validatedData['tanggal_masuk'],
                ],
                [
                    'total_harga' => 0,
                    'keterangan' => 'Pembayaran untuk barang masuk pada ' . $validatedData['tanggal_masuk'],
                ]
            );

            $payment->total_harga += $subTotal;
            $payment->save();

            $barangMasuk = BarangMasuk::create($validatedData);

            $barang = Barang::findOrFail($validatedData['barang_id']);
            $barang->stok += $validatedData['jumlah_masuk'];
            $barang->save();
    
            DB::commit();
    
            return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil ditambahkan dan total pembayaran diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing BarangMasuk: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    

    /**
     * Menampilkan form untuk mengedit barang masuk.
     *
     * @param  \App\Models\BarangMasuk  $barangMasuk
     * @return \Illuminate\View\View
     */
    public function edit(BarangMasuk $barangMasuk)
    {
        $barangs = Barang::all();
        $suppliers = Supplier::all();
        return view('barang-masuk.edit', compact('barangMasuk', 'barangs', 'suppliers'));
    }

    /**
     * Memperbarui data barang masuk di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BarangMasuk  $barangMasuk
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, BarangMasuk $barangMasuk)
    {
        $validatedData = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'supplier_id' => 'required|exists:supplier,id', // Diperbarui untuk merujuk ke tabel 'supplier'
            'jumlah_masuk' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $oldSubTotal = $barangMasuk->jumlah_masuk * $barangMasuk->harga_satuan;
            $oldSupplierId = $barangMasuk->supplier_id;
            $oldTanggalMasuk = $barangMasuk->tanggal_masuk->format('Y-m-d');

            $oldPayment = Payment::where('supplier_id', $oldSupplierId)
                                 ->where('tanggal_bayar', $oldTanggalMasuk)
                                 ->first();
            if ($oldPayment) {
                $oldPayment->total_harga -= $oldSubTotal;
                $oldPayment->save();
            }

            $barangMasukData = $validatedData;
            $barangMasuk->update($barangMasukData);

            $newSubTotal = $barangMasuk->jumlah_masuk * $barangMasuk->harga_satuan;
            
            $newPayment = Payment::firstOrCreate(
                [
                    'supplier_id' => $request->supplier_id,
                    'tanggal_bayar' => $request->tanggal_masuk,
                ],
                [
                    'total_harga' => 0,
                    'keterangan' => 'Pembayaran untuk barang masuk pada ' . $request->tanggal_masuk,
                ]
            );
            $newPayment->total_harga += $newSubTotal;
            $newPayment->save();

            $barangOldStockUpdate = Barang::findOrFail($barangMasuk->getOriginal('barang_id'));
            $barangOldStockUpdate->stok -= $barangMasuk->getOriginal('jumlah_masuk');
            $barangOldStockUpdate->save();

            $barangNewStockUpdate = Barang::findOrFail($request->barang_id);
            $barangNewStockUpdate->stok += $request->jumlah_masuk;
            $barangNewStockUpdate->save();

            DB::commit();

            return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil diupdate dan total pembayaran diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat mengupdate barang masuk atau payment: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data barang masuk dari database.
     *
     * @param  \App\Models\BarangMasuk  $barangMasuk
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(BarangMasuk $barangMasuk)
    {
        DB::beginTransaction();

        try {
            $subTotalToDelete = $barangMasuk->jumlah_masuk * $barangMasuk->harga_satuan;

            $payment = Payment::where('supplier_id', $barangMasuk->supplier_id)
                             ->where('tanggal_bayar', $barangMasuk->tanggal_masuk->format('Y-m-d'))
                             ->first();
            if ($payment) {
                $payment->total_harga -= $subTotalToDelete;
                $payment->save();
            }

            $barang = Barang::findOrFail($barangMasuk->barang_id);
            $barang->stok -= $barangMasuk->jumlah_masuk;
            $barang->save();

            $barangMasuk->delete();

            DB::commit();

            return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil dihapus dan total pembayaran diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menghapus barang masuk atau memperbarui payment: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
