<?php

namespace Database\Seeders;

use App\Models\Siswa;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: siswa (3 Ketunaan: Tunarungu, Tunagrahita, Autis)
     */
    public function run(): void
    {
        $siswas = [
            // Tunarungu
            [
                'nisn'          => '0160001001',
                'nama_lengkap'  => 'Alya Putri',
                'jenis_kelamin' => 'P',
                'tempat_lahir'  => 'Pekanbaru',
                'tgl_lahir'     => '2012-05-14',
                'kekhususan'    => 'Tunarungu',
                'no_hp'         => '082177889901',
                'is_aktif'      => true,
            ],
            [
                'nisn'          => '0150001002',
                'nama_lengkap'  => 'Bima Pratama',
                'jenis_kelamin' => 'L',
                'tempat_lahir'  => 'Kampar',
                'tgl_lahir'     => '2011-08-20',
                'kekhususan'    => 'Tunarungu',
                'no_hp'         => '082177889902',
                'is_aktif'      => true,
            ],
            [
                'nisn'          => '0140001003',
                'nama_lengkap'  => 'Citra Lestari',
                'jenis_kelamin' => 'P',
                'tempat_lahir'  => 'Dumai',
                'tgl_lahir'     => '2010-03-12',
                'kekhususan'    => 'Tunarungu',
                'no_hp'         => '082177889903',
                'is_aktif'      => true,
            ],
            [
                'nisn'          => '0160001004',
                'nama_lengkap'  => 'Dimas Saputra',
                'jenis_kelamin' => 'L',
                'tempat_lahir'  => 'Pekanbaru',
                'tgl_lahir'     => '2012-11-09',
                'kekhususan'    => 'Tunarungu',
                'no_hp'         => '082177889904',
                'is_aktif'      => true,
            ],
            [
                'nisn'          => '0150001005',
                'nama_lengkap'  => 'Elsa Rahma',
                'jenis_kelamin' => 'P',
                'tempat_lahir'  => 'Siak',
                'tgl_lahir'     => '2011-01-25',
                'kekhususan'    => 'Tunarungu',
                'no_hp'         => '082177889905',
                'is_aktif'      => true,
            ],
            [
                'nisn'          => '0140001006',
                'nama_lengkap'  => 'Fajar Ramadhan',
                'jenis_kelamin' => 'L',
                'tempat_lahir'  => 'Pekanbaru',
                'tgl_lahir'     => '2010-09-18',
                'kekhususan'    => 'Tunarungu',
                'no_hp'         => '082177889906',
                'is_aktif'      => true,
            ],
            [
                'nisn'          => '0053935552',
                'nama_lengkap'  => 'Friska Carolin Monalisa',
                'jenis_kelamin' => 'P',
                'tempat_lahir'  => 'Pekanbaru',
                'tgl_lahir'     => '2005-04-12',
                'kekhususan'    => 'Tunarungu',
                'no_hp'         => '082177889910',
                'is_aktif'      => true,
            ],
            [
                'nisn'          => '0095723244',
                'nama_lengkap'  => 'Arumi Nur Azizah',
                'jenis_kelamin' => 'P',
                'tempat_lahir'  => 'Padang',
                'tgl_lahir'     => '2009-11-13',
                'kekhususan'    => 'Tunarungu',
                'no_hp'         => '085263592816',
                'is_aktif'      => true,
            ],

            // Tunagrahita
            [
                'nisn'          => '0160001007',
                'nama_lengkap'  => 'Nadia Safira',
                'jenis_kelamin' => 'P',
                'tempat_lahir'  => 'Pekanbaru',
                'tgl_lahir'     => '2012-07-22',
                'kekhususan'    => 'Tunagrahita',
                'no_hp'         => '082177889907',
                'is_aktif'      => true,
            ],
            [
                'nisn'          => '0098765434',
                'nama_lengkap'  => 'Maya Anggraini',
                'jenis_kelamin' => 'P',
                'tempat_lahir'  => 'Siak',
                'tgl_lahir'     => '2011-06-30',
                'kekhususan'    => 'Tunagrahita',
                'no_hp'         => '082177889908',
                'is_aktif'      => true,
            ],

            // Autis
            [
                'nisn'          => '0150002001',
                'nama_lengkap'  => 'Rafi Al-Fathir',
                'jenis_kelamin' => 'L',
                'tempat_lahir'  => 'Pekanbaru',
                'tgl_lahir'     => '2011-10-15',
                'kekhususan'    => 'Autis',
                'no_hp'         => '082177889909',
                'is_aktif'      => true,
            ],
        ];

        foreach ($siswas as $s) {
            Siswa::updateOrCreate(
                ['nisn' => $s['nisn']],
                $s
            );
        }
    }
}
