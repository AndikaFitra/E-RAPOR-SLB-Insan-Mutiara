<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: kelas (Hanya 3 Ketunaan di SLB Insan Mutiara)
     */
    public function run(): void
    {
        $kelasList = [
            [
                'nama'       => 'Tunarungu A',
                'kekhususan' => 'Tunarungu',
                'kode'       => 'TU-A',
            ],
            [
                'nama'       => 'Tunagrahita A',
                'kekhususan' => 'Tunagrahita',
                'kode'       => 'TG-A',
            ],
            [
                'nama'       => 'Autis A',
                'kekhususan' => 'Autis',
                'kode'       => 'AU-A',
            ],
        ];

        foreach ($kelasList as $kelas) {
            Kelas::updateOrCreate(
                ['kode' => $kelas['kode']],
                $kelas
            );
        }
    }
}
