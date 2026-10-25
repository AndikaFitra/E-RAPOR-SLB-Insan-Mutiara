<?php

namespace Database\Seeders;

use App\Models\MataPelajaran;
use App\Models\NilaiAsesmen;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class NilaiAsesmenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: nilai_asesmen
     */
    public function run(): void
    {
        $thAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$thAktif) {
            return;
        }

        $mapels = MataPelajaran::all()->keyBy('kode');

        // 1. Alya Putri (Tunarungu - PKPBI)
        $siswaAlya = Siswa::where('nisn', '0160001001')->first();
        if ($siswaAlya) {
            $this->seedNilai($siswaAlya->id, $thAktif->id, [
                [
                    'mapel'  => $mapels->get('PABP'),
                    'nilai'  => 90,
                    'narasi' => 'Ananda menunjukkan banyak perubahan positif dalam berinteraksi dan bertingkah laku terpuji sesuai nilai keagamaan.',
                ],
                [
                    'mapel'  => $mapels->get('PPKN'),
                    'nilai'  => 88,
                    'narasi' => 'Ananda mampu mengenal simbol-simbol Pancasila dan menerapkannya dalam gotong royong di lingkungan sekolah.',
                ],
                [
                    'mapel'  => $mapels->get('BIND'),
                    'nilai'  => 85,
                    'narasi' => 'Ananda memiliki pemahaman membaca visual yang sangat baik dan mampu menyusun kosakata dasar dengan bantuan isyarat.',
                ],
                [
                    'mapel'  => $mapels->get('MTK'),
                    'nilai'  => 87,
                    'narasi' => 'Ananda terampil dalam operasi hitung penjumlahan dan pengurangan menggunakan benda konkret.',
                ],
                [
                    'mapel'  => $mapels->get('PKPBI'),
                    'nilai'  => 98,
                    'narasi' => 'Ananda sangat baik dalam mengidentifikasi langsung sumber bunyi dan irama lingkungan menggunakan sisa pendengaran secara optimal.',
                ],
                [
                    'mapel'  => $mapels->get('K-SUV'),
                    'nilai'  => 92,
                    'narasi' => 'Ananda kreatif dalam merangkai dan membuat cinderamata suvenir berbahan dasar kain flanel dan manik-manik.',
                ],
            ]);
        }

        // 2. Bima Pratama (Tunarungu - PKPBI)
        $siswaBima = Siswa::where('nisn', '0150001002')->first();
        if ($siswaBima) {
            $this->seedNilai($siswaBima->id, $thAktif->id, [
                [
                    'mapel'  => $mapels->get('PABP'),
                    'nilai'  => 86,
                    'narasi' => 'Ananda rajin mengikuti doa harian dan tertib dalam kegiatan ibadah di sekolah.',
                ],
                [
                    'mapel'  => $mapels->get('PKPBI'),
                    'nilai'  => 90,
                    'narasi' => 'Ananda menunjukkan ketekunan tinggi dalam persepsi bunyi latar belakang dan latihan artikulasi konsonan dasar.',
                ],
            ]);
        }

        // 3. Nadia Safira (Tunagrahita - Bina Diri)
        $siswaNadia = Siswa::where('nisn', '0160001007')->first();
        if ($siswaNadia) {
            $this->seedNilai($siswaNadia->id, $thAktif->id, [
                [
                    'mapel'  => $mapels->get('PABP'),
                    'nilai'  => 84,
                    'narasi' => 'Ananda mampu menirukan gerakan ibadah sederhana dengan bimbingan penuh kasih.',
                ],
                [
                    'mapel'  => $mapels->get('BINADIRI'),
                    'nilai'  => 92,
                    'narasi' => 'Ananda mengalami kemajuan signifikan dalam kemandirian mencuci tangan, makan sendiri, dan merapikan pakaian.',
                ],
            ]);
        }

        // 4. Rafi Al-Fathir (Autis - PKISP)
        $siswaRafi = Siswa::where('nisn', '0150002001')->first();
        if ($siswaRafi) {
            $this->seedNilai($siswaRafi->id, $thAktif->id, [
                [
                    'mapel'  => $mapels->get('PABP'),
                    'nilai'  => 85,
                    'narasi' => 'Ananda mulai tenang saat kegiatan pembacaan doa bersama dan mampu duduk tertib.',
                ],
                [
                    'mapel'  => $mapels->get('PKISP'),
                    'nilai'  => 89,
                    'narasi' => 'Ananda semakin responsif saat dipanggil namanya, menunjukkan kontak mata yang bertambah fokus, serta mampu mengomunikasikan kebutuhan dasar dengan kartu PECS.',
                ],
            ]);
        }

        // 5. Friska Carolin Monalisa (Tunarungu - Scan Asli)
        $siswaFriska = Siswa::where('nisn', '0053935552')->first();
        if ($siswaFriska) {
            $this->seedNilai($siswaFriska->id, $thAktif->id, [
                [
                    'mapel'  => $mapels->get('PABP'),
                    'nilai'  => 92,
                    'narasi' => 'Ananda menunjukkan kematangan sikap spiritual dan toleransi yang terpuji dalam kehidupan sekolah.',
                ],
                [
                    'mapel'  => $mapels->get('PKPBI'),
                    'nilai'  => 95,
                    'narasi' => 'Kemampuan komunikasi total dan keterbacaan ujaran ananda sangat prima.',
                ],
            ]);
        }
    }

    private function seedNilai(int $siswaId, int $tahunAjaranId, array $items): void
    {
        foreach ($items as $item) {
            if ($item['mapel']) {
                NilaiAsesmen::updateOrCreate(
                    [
                        'siswa_id'          => $siswaId,
                        'mata_pelajaran_id' => $item['mapel']->id,
                        'tahun_ajaran_id'   => $tahunAjaranId,
                    ],
                    [
                        'nilai_akhir'    => $item['nilai'],
                        'narasi_capaian' => $item['narasi'],
                    ]
                );
            }
        }
    }
}
