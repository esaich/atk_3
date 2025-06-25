<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Barang;
use App\Models\Payment;
use App\Models\Supplier; // Import model Supplier
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Untuk debugging

class BarangMasukController extends Controller
{
    // Menampilkan daftar barang masuk
    public function index()
    {
        // Eager load barang dan supplier (karena sekarang barang masuk akan terkait langsung dengan supplier)
        $barangMasuks = BarangMasuk::with('barang', 'supplier')->get();
        return view('barang-masuk.index', compact('barangMasuks'));
    }

    // Menampilkan form tambah barang masuk baru
    public function create()
    {
        $barangs = Barang::all();
        $suppliers = Supplier::all(); // Mengambil semua supplier
        // Kita tidak lagi memilih payment di sini, payment akan otomatis terkelola
        return view('barang-masuk.create', compact('barangs', 'suppliers'));
    }

    // Menyimpan barang masuk baru
    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'supplier_id' => 'required|exists:supplier,id', // Validasi supplier_id
            'jumlah_masuk' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            // Hitung sub total untuk barang masuk ini
            $subTotal = $request->jumlah_masuk * $request->harga_satuan;

            // Cari atau buat record Payment untuk supplier dan tanggal masuk ini
            $payment = Payment::firstOrCreate(
                [
                    'supplier_id' => $request->supplier_id,
                    'tanggal_bayar' => $request->tanggal_masuk, // Tanggal bayar = tanggal masuk barang
                ],
                [
                    'total_harga' => 0, // Inisialisasi jika baru dibuat
                    'keterangan' => 'Pembayaran untuk barang masuk pada ' . $request->tanggal_masuk,
                ]
            );

            // Tambahkan sub total ke total_harga pada record Payment yang ditemukan/dibuat
            $payment->total_harga += $subTotal;
            $payment->save();

            // Buat entri BarangMasuk dengan supplier_id
            $barangMasukData = $request->all();
            // Assign payment_id ke barangMasuk sebelum disimpan, ini opsional
            // Jika tabel barang_masuk tidak lagi memiliki payment_id, maka baris ini dihilangkan
            // atau jika Anda ingin melink BarangMasuk ke Payment secara tidak langsung melalui supplier dan tanggal
            // Untuk skenario ini, kita berasumsi BarangMasuk tidak lagi memiliki payment_id secara langsung
            // Jika Anda ingin BarangMasuk tetap menyimpan payment_id yang baru dibuat,
            // Anda perlu menambahkan 'payment_id' ke fillable BarangMasuk model dan
            // $barangMasukData['payment_id'] = $payment->id;
            
            $barangMasuk = BarangMasuk::create($barangMasukData);

            // Update stok barang
            $barang = Barang::findOrFail($request->barang_id);
            $barang->stok += $request->jumlah_masuk;
            $barang->save();

            DB::commit();

            return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil ditambahkan dan total pembayaran diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menyimpan barang masuk atau memperbarui payment: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menampilkan form edit barang masuk
    public function edit(BarangMasuk $barangMasuk)
    {
        $barangs = Barang::all();
        $suppliers = Supplier::all(); // Mengambil semua supplier
        return view('barang-masuk.edit', compact('barangMasuk', 'barangs', 'suppliers'));
    }

    // Memperbarui data barang masuk
    public function update(Request $request, BarangMasuk $barangMasuk)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'supplier_id' => 'required|exists:supplier,id', // Validasi supplier_id
            'jumlah_masuk' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            // Hitung kembali subTotal lama sebelum perubahan
            $oldSubTotal = $barangMasuk->jumlah_masuk * $barangMasuk->harga_satuan;
            $oldSupplierId = $barangMasuk->supplier_id;
            $oldTanggalMasuk = $barangMasuk->tanggal_masuk->format('Y-m-d'); // Pastikan format tanggal sama

            // 1. Kurangi subTotal lama dari Payment lama (supplier & tanggal lama)
            $oldPayment = Payment::where('supplier_id', $oldSupplierId)
                                 ->where('tanggal_bayar', $oldTanggalMasuk)
                                 ->first();
            if ($oldPayment) {
                $oldPayment->total_harga -= $oldSubTotal;
                $oldPayment->save();
                // Jika total_harga <= 0 dan tidak ada barang masuk lain yang terkait dengan payment ini,
                // mungkin Anda ingin menghapus record Payment ini.
                // Atau biarkan saja dengan total_harga 0. Untuk sekarang kita biarkan.
            }

            // Update data BarangMasuk dengan data baru
            $barangMasukData = $request->all();
            // Anda mungkin memiliki 'payment_id' di old($barangMasuk) jika itu adalah field yang ada
            // di BarangMasuk sebelum migrasi. Pastikan untuk menghapusnya jika tidak lagi ada di tabel
            // Karena kita sudah memigrasi, kita bisa memastikan tidak ada payment_id di fillable BarangMasuk.
            // Hapus baris ini jika Anda tidak ingin menyimpan payment_id di BarangMasuk lagi
            // unset($barangMasukData['payment_id']);
            $barangMasuk->update($barangMasukData);

            // 2. Tambahkan subTotal baru ke Payment baru (supplier & tanggal baru)
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

            // Update stok barang (ini sama seperti sebelumnya)
            // Kurangi stok lama dari barang lama (berdasarkan jumlah_masuk yang lama)
            $barangOldStockUpdate = Barang::findOrFail($barangMasuk->getOriginal('barang_id'));
            $barangOldStockUpdate->stok -= $barangMasuk->getOriginal('jumlah_masuk');
            $barangOldStockUpdate->save();

            // Tambah stok baru ke barang baru (atau barang yang sama jika barang_id tidak berubah)
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

    // Menghapus data barang masuk
    public function destroy(BarangMasuk $barangMasuk)
    {
        DB::beginTransaction();

        try {
            // Hitung sub total yang akan dihapus
            $subTotalToDelete = $barangMasuk->jumlah_masuk * $barangMasuk->harga_satuan;

            // Kurangi total_harga dari Payment terkait (berdasarkan supplier dan tanggal masuk)
            $payment = Payment::where('supplier_id', $barangMasuk->supplier_id)
                             ->where('tanggal_bayar', $barangMasuk->tanggal_masuk->format('Y-m-d'))
                             ->first();
            if ($payment) {
                $payment->total_harga -= $subTotalToDelete;
                $payment->save();

                // Opsional: Jika total_harga Payment menjadi 0 atau kurang, Anda bisa menghapus record Payment tersebut
                // if ($payment->total_harga <= 0) {
                //     $payment->delete();
                // }
            }

            // Kurangi stok barang
            $barang = Barang::findOrFail($barangMasuk->barang_id);
            $barang->stok -= $barangMasuk->jumlah_masuk;
            $barang->save();

            // Hapus entri BarangMasuk
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
