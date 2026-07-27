<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Barang;
use App\Models\Payment;
use App\Models\PengadaanBarang;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangMasukController extends Controller
{
    private function filteredQuery(Request $request)
    {
        $query = BarangMasuk::with('barang', 'supplier');

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
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $suppliers = Supplier::all();
        $barangMasuks = $this->filteredQuery($request)->orderBy('tanggal_masuk', 'desc')->get();
        $filterValues = $request->only(['start_date', 'end_date', 'month', 'year', 'supplier_id']);

        return view('barang-masuk.index', compact('barangMasuks', 'filterValues', 'suppliers'));
    }

    public function create()
    {
        $barangs = Barang::all();
        $suppliers = Supplier::all();
        $pengadaanDisetujui = PengadaanBarang::where('status', 'disetujui')
            ->with('supplier')
            ->get()
            ->filter(fn ($p) => $p->sisa_jumlah > 0)
            ->values();
        return view('barang-masuk.create', compact('barangs', 'suppliers', 'pengadaanDisetujui'));
    }

    // === FIX 2 diterapkan di sini ===
    /**
     * Jika request menyertakan pengadaan_barang_id, pastikan pengajuan itu:
     * 1) benar-benar sudah disetujui bendahara, dan
     * 2) jumlah_masuk yang dicatat tidak melebihi sisa yang belum diterima.
     * Mengembalikan pesan error (string) jika tidak valid, atau null jika valid/kosong.
     */
    private function validasiPengadaan(?int $pengadaanBarangId, int $jumlahMasuk, ?int $abaikanBarangMasukId = null): ?string
    {
        if (!$pengadaanBarangId) {
            return null;
        }

        $pengadaan = PengadaanBarang::find($pengadaanBarangId);

        if (!$pengadaan) {
            return 'Pengajuan pengadaan yang dipilih tidak ditemukan.';
        }

        if ($pengadaan->status !== 'disetujui') {
            return 'Barang masuk hanya bisa dikaitkan dengan pengajuan pengadaan yang sudah disetujui bendahara.';
        }

        $sudahDiterima = $pengadaan->barangMasuks()
            ->when($abaikanBarangMasukId, fn ($q) => $q->where('id', '!=', $abaikanBarangMasukId))
            ->sum('jumlah_masuk');

        $sisa = $pengadaan->jumlah_diajukan - $sudahDiterima;

        if ($jumlahMasuk > $sisa) {
            return "Jumlah masuk ({$jumlahMasuk}) melebihi sisa yang disetujui untuk pengajuan ini (sisa: {$sisa}).";
        }

        return null;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'supplier_id' => 'required|exists:supplier,id',
            'pengadaan_barang_id' => 'nullable|exists:pengadaan_barang,id',
            'jumlah_masuk' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
        ]);

        $errorPengadaan = $this->validasiPengadaan(
            $validatedData['pengadaan_barang_id'] ?? null,
            $validatedData['jumlah_masuk']
        );

        if ($errorPengadaan) {
            return redirect()->back()->withInput()->with('error', $errorPengadaan);
        }

        DB::beginTransaction();

        try {
            $subTotal = $validatedData['jumlah_masuk'] * $validatedData['harga_satuan'];

            $tanggalBayar = Carbon::parse($validatedData['tanggal_masuk'])->format('Y-m-d');

            $payment = Payment::firstOrCreate(
                [
                    'supplier_id' => $validatedData['supplier_id'],
                    'tanggal_bayar' => $tanggalBayar,
                ],
                [
                    'total_harga' => 0,
                    'keterangan' => 'Pembayaran untuk barang masuk pada ' . $tanggalBayar,
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

    public function edit(BarangMasuk $barangMasuk)
    {
        $barangs = Barang::all();
        $suppliers = Supplier::all();
        $pengadaanDisetujui = PengadaanBarang::where('status', 'disetujui')
            ->with('supplier')
            ->get()
            ->filter(fn ($p) => $p->sisa_jumlah > 0 || $p->id === $barangMasuk->pengadaan_barang_id)
            ->values();
        return view('barang-masuk.edit', compact('barangMasuk', 'barangs', 'suppliers', 'pengadaanDisetujui'));
    }

    // === FIX 3 (Bug #5 + Bug #4) diterapkan di sini ===
    public function update(Request $request, BarangMasuk $barangMasuk)
    {
        $validatedData = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'supplier_id' => 'required|exists:supplier,id',
            'pengadaan_barang_id' => 'nullable|exists:pengadaan_barang,id',
            'jumlah_masuk' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
        ]);

        $errorPengadaan = $this->validasiPengadaan(
            $validatedData['pengadaan_barang_id'] ?? null,
            $validatedData['jumlah_masuk'],
            $barangMasuk->id // baris ini sendiri diabaikan saat menghitung "sudah diterima"
        );

        if ($errorPengadaan) {
            return redirect()->back()->withInput()->with('error', $errorPengadaan);
        }

        DB::beginTransaction();

        try {
            // Simpan nilai LAMA sebelum update() dipanggil (fix Bug #5)
            $oldBarangId = $barangMasuk->barang_id;
            $oldJumlahMasuk = $barangMasuk->jumlah_masuk;
            $oldSubTotal = $barangMasuk->jumlah_masuk * $barangMasuk->harga_satuan;
            $oldSupplierId = $barangMasuk->supplier_id;
            $oldTanggalMasuk = $barangMasuk->tanggal_masuk->format('Y-m-d');

            $oldPayment = Payment::where('supplier_id', $oldSupplierId)
                                 ->where('tanggal_bayar', $oldTanggalMasuk)
                                 ->first();
            if ($oldPayment) {
                $oldPayment->total_harga -= $oldSubTotal;
                if ($oldPayment->total_harga <= 0) {
                    $oldPayment->delete();
                } else {
                    $oldPayment->save();
                }
            }

            $barangMasuk->update($validatedData);

            $newSubTotal = $barangMasuk->jumlah_masuk * $barangMasuk->harga_satuan;
            $newTanggalBayar = Carbon::parse($request->tanggal_masuk)->format('Y-m-d');

            $newPayment = Payment::firstOrCreate(
                [
                    'supplier_id' => $request->supplier_id,
                    'tanggal_bayar' => $newTanggalBayar,
                ],
                [
                    'total_harga' => 0,
                    'keterangan' => 'Pembayaran untuk barang masuk pada ' . $newTanggalBayar,
                ]
            );
            $newPayment->total_harga += $newSubTotal;
            $newPayment->save();

            // Sesuaikan stok berdasarkan SELISIH (fix Bug #5)
            if ((int) $oldBarangId === (int) $barangMasuk->barang_id) {
                $barang = Barang::findOrFail($barangMasuk->barang_id);
                $barang->stok += ($barangMasuk->jumlah_masuk - $oldJumlahMasuk);
                $barang->save();
            } else {
                $barangLama = Barang::findOrFail($oldBarangId);
                $barangLama->stok -= $oldJumlahMasuk;
                $barangLama->save();

                $barangBaru = Barang::findOrFail($barangMasuk->barang_id);
                $barangBaru->stok += $barangMasuk->jumlah_masuk;
                $barangBaru->save();
            }

            DB::commit();

            return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil diupdate dan total pembayaran diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat mengupdate barang masuk atau payment: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // === FIX 4: baris where() di bawah ini TIDAK berubah, sudah benar sejak awal ===
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
                if ($payment->total_harga <= 0) {
                    $payment->delete();
                } else {
                    $payment->save();
                }
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

    public function downloadPdf(Request $request)
    {
        $supplierId = $request->input('supplier_id');
        $barangMasuks = $this->filteredQuery($request)->orderBy('tanggal_masuk', 'desc')->get();
        $supplierName = $supplierId ? Supplier::find($supplierId)->nama_supplier : 'Semua Supplier';

        $data = [
            'barangMasuks' => $barangMasuks,
            'filterValues' => [
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'month' => $request->input('month'),
                'year' => $request->input('year'),
                'supplier_id' => $supplierId
            ],
            'supplierName' => $supplierName
        ];

        $pdf = Pdf::loadView('barang-masuk.pdf-cetak', $data);

        return $pdf->download('laporan-barang-masuk-' . Carbon::now()->format('Ymd') . '.pdf');
    }
}