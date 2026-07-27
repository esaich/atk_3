<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SupplierSeeder::class,
            BarangSeeder::class,
            BarangMasukSeeder::class,
            PaymentSeeder::class,
            PermintaanBarangSeeder::class,
            BarangKeluarSeeder::class,
            PengadaanBarangSeeder::class,
        ]);
    }
}