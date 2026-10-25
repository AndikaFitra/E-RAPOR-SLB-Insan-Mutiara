<?php

namespace Database\Seeders;

use App\Models\MataPelajaran;
use Illuminate\Database\Seeder;

class MataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: mata_pelajaran
     */
    public function run(): void
    {
        $mapelList = [
            ['nama' => 'Pendidikan Agama dan Budi Pekerti', 'kode' => 'PABP', 'kelompok' => 'Umum', 'is_aktif' => true],
            ['nama' => 'Pendidikan Pancasila', 'kode' => 'PPKN', 'kelompok' => 'Umum', 'is_aktif' => true],
            ['nama' => 'Bahasa Indonesia', 'kode' => 'BIND', 'kelompok' => 'Umum', 'is_aktif' => true],
            ['nama' => 'Matematika', 'kode' => 'MTK', 'kelompok' => 'Umum', 'is_aktif' => true],
            ['nama' => 'Bahasa Inggris', 'kode' => 'BING', 'kelompok' => 'Umum', 'is_aktif' => true],
            ['nama' => 'Ilmu Pengetahuan Alam (IPA)', 'kode' => 'IPA', 'kelompok' => 'Umum', 'is_aktif' => true],
            ['nama' => 'Ilmu Pengetahuan Sosial (IPS)', 'kode' => 'IPS', 'kelompok' => 'Umum', 'is_aktif' => true],
            ['nama' => 'Seni Budaya (Seni Rupa)', 'kode' => 'SBR', 'kelompok' => 'Umum', 'is_aktif' => true],
            ['nama' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan', 'kode' => 'PJOK', 'kelompok' => 'Umum', 'is_aktif' => true],
            ['nama' => 'Keterampilan Pilihan: Suvenir', 'kode' => 'K-SUV', 'kelompok' => 'Keterampilan Pilihan', 'is_aktif' => true],
            ['nama' => 'Keterampilan Pilihan: Tata Kecantikan', 'kode' => 'K-TKC', 'kelompok' => 'Keterampilan Pilihan', 'is_aktif' => true],

            // 3 Program Kekhususan Berdasarkan 3 Ketunaan:
            ['nama' => 'Program Kekhususan (PKPBI)', 'kode' => 'PKPBI', 'kelompok' => 'Program Kekhususan', 'is_aktif' => true],
            ['nama' => 'Program Kekhususan (Bina Diri)', 'kode' => 'BINADIRI', 'kelompok' => 'Program Kekhususan', 'is_aktif' => true],
            ['nama' => 'Program Kekhususan (Pengembangan Komunikasi dan Interaksi)', 'kode' => 'PKISP', 'kelompok' => 'Program Kekhususan', 'is_aktif' => true],

            ['nama' => 'Muatan Lokal: Budaya Melayu Riau', 'kode' => 'BMR', 'kelompok' => 'Muatan Lokal', 'is_aktif' => true],
        ];

        foreach ($mapelList as $mapel) {
            MataPelajaran::updateOrCreate(
                ['kode' => $mapel['kode']],
                $mapel
            );
        }
    }
}
