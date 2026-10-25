<?php

namespace Database\Seeders;

use App\Models\Guru;
use Illuminate\Database\Seeder;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: guru
     */
    public function run(): void
    {
        $gurus = [
            [
                'nip'           => '198005122005012003',
                'nama_lengkap'  => 'Novia Parenri, S.Si, M.Si',
                'jenis_kelamin' => 'P',
                'jabatan'       => 'Kepala Sekolah',
                'no_whatsapp'   => '081268112233',
            ],
            [
                'nip'           => '198803152011012014',
                'nama_lengkap'  => 'Rina Pratiwi, S.Pd.',
                'jenis_kelamin' => 'P',
                'jabatan'       => 'Guru Kelas Tunarungu',
                'no_whatsapp'   => '081371445566',
            ],
            [
                'nip'           => '199107242015021002',
                'nama_lengkap'  => 'Ahmad Fauzi, S.Pd.',
                'jenis_kelamin' => 'L',
                'jabatan'       => 'Guru Kelas Tunagrahita',
                'no_whatsapp'   => '082199887766',
            ],
            [
                'nip'           => '199411082019032009',
                'nama_lengkap'  => 'Siti Nurhaliza, S.Pd.',
                'jenis_kelamin' => 'P',
                'jabatan'       => 'Guru Kelas Autis',
                'no_whatsapp'   => '085233445566',
            ],
            [
                'nip'           => '199002142014022005',
                'nama_lengkap'  => 'Nova Yani, S.Pd, Gr',
                'jenis_kelamin' => 'P',
                'jabatan'       => 'Guru Kelas SMALB',
                'no_whatsapp'   => '081277112288',
            ],
        ];

        foreach ($gurus as $g) {
            Guru::updateOrCreate(
                ['nip' => $g['nip']],
                $g
            );
        }
    }
}
