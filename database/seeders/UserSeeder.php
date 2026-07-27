<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@atk.app',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $divisiUsers = [
            ['name' => 'Bagian Keuangan', 'email' => 'keuangan@atk.app'],
            ['name' => 'Bagian Umum', 'email' => 'umum@atk.app'],
            ['name' => 'Bagian IT', 'email' => 'it@atk.app'],
            ['name' => 'Bagian HRD', 'email' => 'hrd@atk.app'],
        ];

        foreach ($divisiUsers as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('password'),
                'role' => 'divisi',
            ]);
        }
    }
}