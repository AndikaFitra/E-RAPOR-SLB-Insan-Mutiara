<?php

namespace Database\Seeders;

use App\Models\Ekstrakurikuler;
use Illuminate\Database\Seeder;

class EkstrakurikulerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: ekstrakurikuler
     */
    public function run(): void
    {
        $ekskuls = [
            'Pramuka',
            'Seni Pantomim & Tari Isyarat',
            'Olahraga Bocce',
            'Seni Lukis Kreatif',
        ];

        foreach ($ekskuls as $nama) {
            Ekstrakurikuler::updateOrCreate(
                ['nama' => $nama],
                ['nama' => $nama]
            );
        }
    }
}

