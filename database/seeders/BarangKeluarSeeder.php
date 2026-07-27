<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\PermintaanBarang;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BarangKeluarSeeder extends Seeder
{
    public function run(): void
    {
        $approvedPermintaans = PermintaanBarang::where('status', 'disetujui')->get();

        foreach ($approvedPermintaans as $permintaan) {
            $barang = Barang::find($permintaan->barang_id);

            // Hindari stok negatif
            if ($barang->stok >= $permintaan->jumlah) {
                BarangKeluar::create([
                    'permintaan_id' => $permintaan->id,
                    'barang_id' => $permintaan->barang_id,
                    'jumlah_keluar' => $permintaan->jumlah,
                    'tanggal_keluar' => Carbon::parse($permintaan->created_at)->addDays(rand(0, 2)),
                    'keterangan' => 'Disetujui dari permintaan ID: ' . $permintaan->id . ' oleh Admin',
                ]);

                $barang->stok -= $permintaan->jumlah;
                $barang->save();
            }
        }
    }
}