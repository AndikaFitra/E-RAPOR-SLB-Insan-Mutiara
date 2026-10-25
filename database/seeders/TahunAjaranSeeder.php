<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: tahun_ajaran
     */
    public function run(): void
    {
        TahunAjaran::updateOrCreate(
            ['tahun' => '2026/2027', 'semester' => 'Ganjil'],
            ['is_aktif' => true]
        );

        TahunAjaran::updateOrCreate(
            ['tahun' => '2025/2026', 'semester' => 'Genap'],
            ['is_aktif' => false]
        );

        TahunAjaran::updateOrCreate(
            ['tahun' => '2025/2026', 'semester' => 'Ganjil'],
            ['is_aktif' => false]
        );
    }
}

