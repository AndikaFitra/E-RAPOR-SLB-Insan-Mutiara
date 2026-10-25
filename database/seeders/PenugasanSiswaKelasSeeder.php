<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\PenugasanSiswaKelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class PenugasanSiswaKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: penugasan_siswa_kelas
     */
    public function run(): void
    {
        $thAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$thAktif) {
            return;
        }

        $kelasTunarungu   = Kelas::where('kode', 'TU-A')->first();
        $kelasTunagrahita = Kelas::where('kode', 'TG-A')->first();
        $kelasAutis       = Kelas::where('kode', 'AU-A')->first();

        $assignments = [
            // Tunarungu A
            ['nisn' => '0160001001', 'kelas' => $kelasTunarungu, 'tingkat' => 'VII', 'fase' => 'D'],
            ['nisn' => '0150001002', 'kelas' => $kelasTunarungu, 'tingkat' => 'VIII', 'fase' => 'D'],
            ['nisn' => '0140001003', 'kelas' => $kelasTunarungu, 'tingkat' => 'IX', 'fase' => 'D'],
            ['nisn' => '0160001004', 'kelas' => $kelasTunarungu, 'tingkat' => 'VII', 'fase' => 'D'],
            ['nisn' => '0150001005', 'kelas' => $kelasTunarungu, 'tingkat' => 'VIII', 'fase' => 'D'],
            ['nisn' => '0140001006', 'kelas' => $kelasTunarungu, 'tingkat' => 'IX', 'fase' => 'D'],
            ['nisn' => '0053935552', 'kelas' => $kelasTunarungu, 'tingkat' => 'XII', 'fase' => 'F'],
            ['nisn' => '0095723244', 'kelas' => $kelasTunarungu, 'tingkat' => 'VII', 'fase' => 'D'],

            // Tunagrahita A
            ['nisn' => '0160001007', 'kelas' => $kelasTunagrahita, 'tingkat' => 'VII', 'fase' => 'D'],
            ['nisn' => '0098765434', 'kelas' => $kelasTunagrahita, 'tingkat' => 'VIII', 'fase' => 'D'],

            // Autis A
            ['nisn' => '0150002001', 'kelas' => $kelasAutis, 'tingkat' => 'VII', 'fase' => 'D'],
        ];

        foreach ($assignments as $a) {
            $siswa = Siswa::where('nisn', $a['nisn'])->first();
            if ($siswa && $a['kelas']) {
                PenugasanSiswaKelas::updateOrCreate(
                    [
                        'siswa_id'        => $siswa->id,
                        'tahun_ajaran_id' => $thAktif->id,
                    ],
                    [
                        'kelas_id' => $a['kelas']->id,
                        'tingkat'  => $a['tingkat'],
                        'fase'     => $a['fase'],
                    ]
                );
            }
        }
    }
}

