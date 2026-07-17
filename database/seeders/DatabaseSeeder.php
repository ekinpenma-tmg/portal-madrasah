<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\JenisDokumen;
use App\Models\ProfilOrganisasi;
use App\Models\Staff;

class DatabaseSeeder extends Seeder
{
       public function run(): void
    {
        // Cek dulu kalau belum ada user, baru buat
        if (User::count() === 0) {
            User::create([
                'name'     => 'Admin',
                'email'    => 'admin@madrasah.id',
                'password' => Hash::make('admin1234'),
                'role'     => 'super_admin',
            ]);
        }
        // Isi 18 poin Standar Pelayanan Penma (aman dijalankan berkali-kali)
        $this->call(LayananSeeder::class);
    }
}
