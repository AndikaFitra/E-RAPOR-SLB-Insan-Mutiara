<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Masing-masing tabel memiliki seeder terpisah yang dieksekusi secara berurutan.
     */
    public function run(): void
    {
        $this->call([
            SekolahSeeder::class,              // 1.  Tabel: sekolah
            TahunAjaranSeeder::class,          // 2.  Tabel: tahun_ajaran
            GuruSeeder::class,                 // 3.  Tabel: guru
            UserSeeder::class,                 // 4.  Tabel: users
            KelasSeeder::class,                // 5.  Tabel: kelas (3 Ketunaan)
            MataPelajaranSeeder::class,        // 6.  Tabel: mata_pelajaran (PKPBI, Bina Diri, PKISP)
            EkstrakurikulerSeeder::class,      // 7.  Tabel: ekstrakurikuler
            SiswaSeeder::class,                // 8.  Tabel: siswa
            OrangTuaSeeder::class,             // 9.  Tabel: orang_tua
            PenugasanWaliKelasSeeder::class,   // 10. Tabel: penugasan_wali_kelas
            PenugasanSiswaKelasSeeder::class,  // 11. Tabel: penugasan_siswa_kelas
            PenugasanPembimbingSeeder::class,  // 12. Tabel: penugasan_pembimbing
            KelasMataPelajaranSeeder::class,   // 13. Tabel: kelas_mata_pelajaran
            RaporSeeder::class,                // 14. Tabel: rapor
            NilaiAsesmenSeeder::class,         // 15. Tabel: nilai_asesmen
            EvaluasiNonakademikSeeder::class,  // 16. Tabel: evaluasi_nonakademik
            PrestasiSiswaSeeder::class,        // 17. Tabel: prestasi_siswa
            EkstrakurikulerSiswaSeeder::class, // 18. Tabel: ekstrakurikuler_siswa
            DistribusiRaporSeeder::class,      // 19. Tabel: distribusi_rapor
            ActivityLogSeeder::class,          // 20. Tabel: activity_log

            DemoScenarioSeeder::class,         // 21. Skenario demo lintas peran dan status rapor
        ]);
    }
}
