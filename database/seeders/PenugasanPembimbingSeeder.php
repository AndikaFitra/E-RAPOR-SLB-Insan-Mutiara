<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\PenugasanPembimbing;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class PenugasanPembimbingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: penugasan_pembimbing
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

        $pairs = [
            '0160001001' => $guruRina,
            '0150001002' => $guruRina,
            '0160001007' => $guruAhmad,
            '0150002001' => $guruSiti,
        ];

        foreach ($pairs as $nisn => $guru) {
            $siswa = Siswa::where('nisn', $nisn)->first();
            if ($siswa && $guru) {
                PenugasanPembimbing::updateOrCreate(
                    [
                        'siswa_id'        => $siswa->id,
                        'tahun_ajaran_id' => $thAktif->id,
                    ],
                    [
                        'guru_id' => $guru->id,
                    ]
                );
            }
        }
    }
}
