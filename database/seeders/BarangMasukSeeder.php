<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BarangMasukSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Supplier::all();
        $barangs = Barang::all();

        for ($i = 0; $i < 30; $i++) {
            $barang = $barangs->random();
            $supplier = $suppliers->random();
            $jumlah = rand(10, 100);
            $harga = rand(1000, 50000);
            $tanggal = Carbon::now()->subDays(rand(1, 90));

            BarangMasuk::create([
                'barang_id' => $barang->id,
                'supplier_id' => $supplier->id,
                'jumlah_masuk' => $jumlah,
                'harga_satuan' => $harga,
                'tanggal_masuk' => $tanggal,
            ]);

            // Sinkron stok barang
            $barang->stok += $jumlah;
            $barang->save();
        }
    }
}