<?php

namespace Database\Seeders;

use App\Models\EvaluasiNonakademik;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class EvaluasiNonakademikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: evaluasi_nonakademik
     */
    public function run(): void
    {
        $thAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$thAktif) {
            return;
        }

        $siswas = Siswa::all();

        foreach ($siswas as $siswa) {
            $isAlya = ($siswa->nisn === '0160001001');

            EvaluasiNonakademik::updateOrCreate(
                [
                    'siswa_id'        => $siswa->id,
                    'tahun_ajaran_id' => $thAktif->id,
                ],
                [
                    'sakit'              => $isAlya ? 0 : rand(0, 2),
                    'izin'               => $isAlya ? 3 : rand(0, 2),
                    'alpa'               => 0,
                    'perkembangan'       => $isAlya
                        ? 'Pertumbuhan fisik sangat baik, pendengaran terlatih dengan konsisten serta postur tubuh tegap.'
                        : 'Pertumbuhan fisik dan adaptasi interaksi lingkungan kelas berjalan sangat baik dan sehat.',
                    'tidak_ada_prestasi' => !$isAlya,
                ]
            );
        }
    }
}

