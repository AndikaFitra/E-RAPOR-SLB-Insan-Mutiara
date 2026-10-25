<?php

namespace Database\Seeders;

use App\Models\PrestasiSiswa;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class PrestasiSiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: prestasi_siswa
     */
    public function run(): void
    {
        $thAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$thAktif) {
            return;
        }

        $siswaAlya = Siswa::where('nisn', '0160001001')->first();
        if ($siswaAlya) {
            PrestasiSiswa::updateOrCreate(
                [
                    'siswa_id'        => $siswaAlya->id,
                    'tahun_ajaran_id' => $thAktif->id,
                    'kegiatan'        => 'Lomba Memperingati Hari Disabilitas oleh DINSOS Pekanbaru',
                ],
                [
                    'keterangan' => 'Juara 1',
                ]
            );

            PrestasiSiswa::updateOrCreate(
                [
                    'siswa_id'        => $siswaAlya->id,
                    'tahun_ajaran_id' => $thAktif->id,
                    'kegiatan'        => 'Lomba Mendongeng Bahasa Isyarat Bulan Bahasa FKIP UNRI',
                ],
                [
                    'keterangan' => 'Juara Harapan 1',
                ]
            );
        }

        $siswaBima = Siswa::where('nisn', '0150001002')->first();
        if ($siswaBima) {
            PrestasiSiswa::updateOrCreate(
                [
                    'siswa_id'        => $siswaBima->id,
                    'tahun_ajaran_id' => $thAktif->id,
                    'kegiatan'        => 'Kejuaraan Olahraga Disabilitas (Bocce) Tingkat Kota Pekanbaru',
                ],
                [
                    'keterangan' => 'Juara 2',
                ]
            );
        }
    }
}

