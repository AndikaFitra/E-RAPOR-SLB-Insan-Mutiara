<?php

namespace Database\Seeders;

use App\Models\OrangTua;
use App\Models\Siswa;
use Illuminate\Database\Seeder;

class OrangTuaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: orang_tua
     */
    public function run(): void
    {
        $dataOrangTua = [
            '0160001001' => [
                'nama'              => 'Bambang Santoso',
                'hubungan'          => 'Ayah',
                'no_whatsapp'       => '08127001001',
                'is_penerima_utama' => true,
            ],
            '0150001002' => [
                'nama'              => 'Ratna Sari',
                'hubungan'          => 'Ibu',
                'no_whatsapp'       => '081377882211',
                'is_penerima_utama' => true,
            ],
            '0140001003' => [
                'nama'              => 'H. Ridwan',
                'hubungan'          => 'Wali',
                'no_whatsapp'       => '082155667788',
                'is_penerima_utama' => true,
            ],
            '0160001004' => [
                'nama'              => 'Agus Saputra',
                'hubungan'          => 'Ayah',
                'no_whatsapp'       => '081266778899',
                'is_penerima_utama' => true,
            ],
            '0150001005' => [
                'nama'              => 'Nurhalimah',
                'hubungan'          => 'Ibu',
                'no_whatsapp'       => '081388990011',
                'is_penerima_utama' => true,
            ],
            '0140001006' => [
                'nama'              => 'Hendri Ramadhan',
                'hubungan'          => 'Ayah',
                'no_whatsapp'       => '085277889922',
                'is_penerima_utama' => true,
            ],
            '0053935552' => [
                'nama'              => 'Ibu Monalisa',
                'hubungan'          => 'Ibu',
                'no_whatsapp'       => '081277112233',
                'is_penerima_utama' => true,
            ],
            '0095723244' => [
                'nama'              => 'Usman',
                'hubungan'          => 'Ayah',
                'no_whatsapp'       => '085263592816',
                'is_penerima_utama' => true,
            ],
            '0160001007' => [
                'nama'              => 'Siti Aminah',
                'hubungan'          => 'Ibu',
                'no_whatsapp'       => '081277665544',
                'is_penerima_utama' => true,
            ],
            '0098765434' => [
                'nama'              => 'Kusnadi',
                'hubungan'          => 'Ayah',
                'no_whatsapp'       => '081366554433',
                'is_penerima_utama' => true,
            ],
            '0150002001' => [
                'nama'              => 'Dewi Lestari',
                'hubungan'          => 'Ibu',
                'no_whatsapp'       => '082266778811',
                'is_penerima_utama' => true,
            ],
        ];

        foreach ($dataOrangTua as $nisn => $ortu) {
            $siswa = Siswa::where('nisn', $nisn)->first();
            if ($siswa) {
                OrangTua::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'nama'     => $ortu['nama'],
                    ],
                    [
                        'hubungan'          => $ortu['hubungan'],
                        'no_whatsapp'       => $ortu['no_whatsapp'],
                        'is_penerima_utama' => $ortu['is_penerima_utama'],
                    ]
                );
            }
        }
    }
}
