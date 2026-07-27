<?php

namespace Database\Seeders;

use App\Models\BarangMasuk;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $grouped = BarangMasuk::select(
                'supplier_id',
                DB::raw('DATE(tanggal_masuk) as tanggal'),
                DB::raw('SUM(jumlah_masuk * harga_satuan) as total')
            )
            ->groupBy('supplier_id', DB::raw('DATE(tanggal_masuk)'))
            ->get();

        foreach ($grouped as $group) {
            Payment::create([
                'supplier_id' => $group->supplier_id,
                'total_harga' => $group->total,
                'tanggal_bayar' => $group->tanggal,
                'keterangan' => 'Pembayaran untuk barang masuk pada ' . Carbon::parse($group->tanggal)->format('Y-m-d'),
            ]);
        }
    }
}