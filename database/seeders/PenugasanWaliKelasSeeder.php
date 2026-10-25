<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\PenugasanWaliKelas;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class PenugasanWaliKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: penugasan_wali_kelas
     */
    public function run(): void
    {
        $thAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$thAktif) {
            return;
        }

        $guruRina  = Guru::where('nama_lengkap', 'like', '%Rina Pratiwi%')->first();
        $guruAhmad = Guru::where('nama_lengkap', 'like', '%Ahmad Fauzi%')->first();
        $guruSiti  = Guru::where('nama_lengkap', 'like', '%Siti Nurhaliza%')->first();

        $kelasTunarungu   = Kelas::where('kode', 'TU-A')->first();
        $kelasTunagrahita = Kelas::where('kode', 'TG-A')->first();
        $kelasAutis       = Kelas::where('kode', 'AU-A')->first();

        $assignments = [
            ['guru' => $guruRina, 'kelas' => $kelasTunarungu],
            ['guru' => $guruAhmad, 'kelas' => $kelasTunagrahita],
            ['guru' => $guruSiti, 'kelas' => $kelasAutis],
        ];

        foreach ($assignments as $a) {
            if ($a['guru'] && $a['kelas']) {
                PenugasanWaliKelas::updateOrCreate(
                    [
                        'kelas_id'        => $a['kelas']->id,
                        'tahun_ajaran_id' => $thAktif->id,
                    ],
                    [
                        'guru_id' => $a['guru']->id,
                    ]
                );
            }
        }
    }
}

