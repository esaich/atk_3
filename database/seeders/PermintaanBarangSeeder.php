<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\PermintaanBarang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PermintaanBarangSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'divisi')->get();
        $barangs = Barang::all();
        $statuses = ['pending', 'pending', 'disetujui', 'disetujui', 'ditolak'];

        for ($i = 0; $i < 25; $i++) {
            $user = $users->random();
            $barang = $barangs->random();
            $status = $statuses[array_rand($statuses)];
            $tanggal = Carbon::now()->subDays(rand(1, 60));

            // Untuk yang disetujui, pastikan jumlah tidak terlalu besar
            $jumlah = $status === 'disetujui'
                ? rand(5, min(30, $barang->stok))
                : rand(5, 50);

            PermintaanBarang::create([
                'user_id' => $user->id,
                'barang_id' => $barang->id,
                'jumlah' => $jumlah,
                'status' => $status,
                'alasan' => $status === 'ditolak'
                    ? 'Stok tidak mencukupi untuk kebutuhan saat ini'
                    : 'Kebutuhan operasional harian',
                'created_at' => $tanggal,
                'updated_at' => $tanggal,
            ]);
        }
    }
}