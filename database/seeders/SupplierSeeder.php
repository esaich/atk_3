<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'nama_supplier' => 'PT. ATK Sejahtera',
                'alamat' => 'Jl. Sudirman No. 123, Jakarta',
                'telepon' => '021-1234567',
                'email' => 'atksejahtera@email.com',
            ],
            [
                'nama_supplier' => 'CV. Mandiri Stationery',
                'alamat' => 'Jl. Thamrin No. 45, Jakarta',
                'telepon' => '021-7654321',
                'email' => 'mandiri@email.com',
            ],
            [
                'nama_supplier' => 'Toko Serba Ada',
                'alamat' => 'Jl. Gatot Subroto No. 78, Jakarta',
                'telepon' => '021-9876543',
                'email' => null,
            ],
            [
                'nama_supplier' => 'PT. Sukses Abadi',
                'alamat' => 'Jl. MH Thamrin Kav. 10, Jakarta',
                'telepon' => '021-5558888',
                'email' => 'suksesabadi@email.com',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}