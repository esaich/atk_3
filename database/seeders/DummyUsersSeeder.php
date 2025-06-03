<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DummyUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userData = [ 
            [
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'password' => bcrypt('admin123')
            ],
            [
            'name' => 'divisi keuangan',
            'email' => 'keuangan@gmail.com',
            'role' => 'divisi',
            'password' => bcrypt('admin123')
            ],
            [
            'name' => 'divisi perdagangan',
            'email' => 'perdagangan@gmail.com',
            'role' => 'divisi',
            'password' => bcrypt('admin123')
            ],
        ];

        foreach ($userData as $key => $val) {
            User::create($val);
        }
    }
}
