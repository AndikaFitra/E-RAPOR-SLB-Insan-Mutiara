<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\KelasMataPelajaran;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class KelasMataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: kelas_mata_pelajaran
     */
    public function run(): void
    {
        $thAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$thAktif) {
            return;
        }

        $kelasList = Kelas::all();
        $mapelList = MataPelajaran::all();

        foreach ($kelasList as $kelas) {
            foreach ($mapelList as $mapel) {
                // Jangan kaitkan program kekhususan yang tidak cocok dengan kelas
                if ($mapel->kelompok === 'Program Kekhususan') {
                    if ($kelas->kekhususan === 'Tunarungu' && $mapel->kode !== 'PKPBI') {
                        continue;
                    }
                    if ($kelas->kekhususan === 'Tunagrahita' && $mapel->kode !== 'BINADIRI') {
                        continue;
                    }
                    if ($kelas->kekhususan === 'Autis' && $mapel->kode !== 'PKISP') {
                        continue;
                    }
                }

                KelasMataPelajaran::updateOrCreate(
                    [
                        'kelas_id'          => $kelas->id,
                        'mata_pelajaran_id' => $mapel->id,
                        'tahun_ajaran_id'   => $thAktif->id,
                    ]
                );
            }
        }
    }
}

