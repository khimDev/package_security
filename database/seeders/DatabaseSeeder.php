<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Administrator Utama
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'password' => 'admin123',
                'is_admin' => true,
            ]
        );

        // 2. Akun Petugas Satpam Resmi Universitas Widyagama Husada Malang
        $officialSatpams = [
            ['name' => 'LINDUNG AGUNG', 'username' => 'lindung'],
            ['name' => 'FERY', 'username' => 'fery'],
            ['name' => 'RISKY N', 'username' => 'risky_n'],
            ['name' => 'RISKY SAIFUL', 'username' => 'risky_saiful'],
            ['name' => 'AMIN', 'username' => 'amin'],
            ['name' => 'FRANDY', 'username' => 'frandy'],
            ['name' => 'RAHMAD', 'username' => 'rahmad'],
        ];

        foreach ($officialSatpams as $satpam) {
            User::firstOrCreate(
                ['username' => $satpam['username']],
                [
                    'name' => $satpam['name'],
                    'password' => 'satpam123',
                    'is_admin' => false,
                ]
            );
        }
    }
}
