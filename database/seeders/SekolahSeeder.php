<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Seeder;

class SekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: sekolah
     */
    public function run(): void
    {
        Sekolah::updateOrCreate(
            ['id' => 1],
            [
                'nama'           => 'SLB Insan Mutiara Pekanbaru',
                'npsn'           => '10497554',
                'alamat'         => 'Jalan Cendana No. 33A / Adi Sucipto',
                'kota'           => 'Pekanbaru',
                'provinsi'       => 'Riau',
                'kode_pos'       => '28293',
                'telepon'        => '(0761) 589123',
                'email'          => 'info@slbinsanmutiara.sch.id',
                'kepala_sekolah' => 'Novia Parenri, S.Si, M.Si',
                'nip_kepala'     => '198005122005012003',
                'logo'           => 'images/logo-slb.png',
            ]
        );
    }
}

