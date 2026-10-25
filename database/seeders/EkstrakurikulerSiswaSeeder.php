<?php

namespace Database\Seeders;

use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerSiswa;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class EkstrakurikulerSiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: ekstrakurikuler_siswa
     */
    public function run(): void
    {
        $thAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$thAktif) {
            return;
        }

        $pramuka  = Ekstrakurikuler::where('nama', 'like', '%Pramuka%')->first();
        $pantomim = Ekstrakurikuler::where('nama', 'like', '%Pantomim%')->first();
        $bocce    = Ekstrakurikuler::where('nama', 'like', '%Bocce%')->first();
        $lukis    = Ekstrakurikuler::where('nama', 'like', '%Lukis%')->first();

        $siswaAlya  = Siswa::where('nisn', '0160001001')->first();
        $siswaBima  = Siswa::where('nisn', '0150001002')->first();
        $siswaNadia = Siswa::where('nisn', '0160001007')->first();

        if ($siswaAlya && $pramuka) {
            EkstrakurikulerSiswa::updateOrCreate(
                [
                    'siswa_id'           => $siswaAlya->id,
                    'ekstrakurikuler_id' => $pramuka->id,
                    'tahun_ajaran_id'    => $thAktif->id,
                ],
                [
                    'keterangan' => 'Sangat Baik (Aktif dalam kegiatan regu penggalang khusus)',
                ]
            );
        }

        if ($siswaAlya && $pantomim) {
            EkstrakurikulerSiswa::updateOrCreate(
                [
                    'siswa_id'           => $siswaAlya->id,
                    'ekstrakurikuler_id' => $pantomim->id,
                    'tahun_ajaran_id'    => $thAktif->id,
                ],
                [
                    'keterangan' => 'Sangat Baik (Menunjukkan ekspresi gerak dan penjiwaan visual luar biasa)',
                ]
            );
        }

        if ($siswaBima && $bocce) {
            EkstrakurikulerSiswa::updateOrCreate(
                [
                    'siswa_id'           => $siswaBima->id,
                    'ekstrakurikuler_id' => $bocce->id,
                    'tahun_ajaran_id'    => $thAktif->id,
                ],
                [
                    'keterangan' => 'Baik (Memiliki akurasi lemparan bola bocce yang konsisten)',
                ]
            );
        }

        if ($siswaNadia && $lukis) {
            EkstrakurikulerSiswa::updateOrCreate(
                [
                    'siswa_id'           => $siswaNadia->id,
                    'ekstrakurikuler_id' => $lukis->id,
                    'tahun_ajaran_id'    => $thAktif->id,
                ],
                [
                    'keterangan' => 'Baik (Terampil dalam perpaduan warna cat poster)',
                ]
            );
        }
    }
}
