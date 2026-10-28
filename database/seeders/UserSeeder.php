<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: users
     */
    public function run(): void
    {
        $guruKepsek = Guru::where('jabatan', 'Kepala Sekolah')->first();
        $guruRina   = Guru::where('nama_lengkap', 'like', '%Rina Pratiwi%')->first();
        $guruAhmad  = Guru::where('nama_lengkap', 'like', '%Ahmad Fauzi%')->first();

        // Hapus hanya dua akun contoh lama yang tidak termasuk empat akun bawaan.
        User::whereIn('email', [
            'sitinurhaliza@slbinsanmutiara.sch.id',
            'nonaktif@slbinsanmutiara.sch.id',
        ])->delete();

        // Admin
        User::updateOrCreate(
            ['email' => 'admin@slbinsanmutiara.sch.id'],
            [
                'name'     => 'Sari Wulandari, S.Pd.',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'is_aktif' => true,
                'guru_id'  => null,
                'nip'      => '198702122010011001',
            ]
        );

        // Kepala Sekolah
        User::updateOrCreate(
            ['email' => 'kepsek@slbinsanmutiara.sch.id'],
            [
                'name'     => 'Novia Parenri, S.Si, M.Si',
                'password' => Hash::make('password'),
                'role'     => 'kepala_sekolah',
                'is_aktif' => true,
                'guru_id'  => $guruKepsek?->id,
                'nip'      => $guruKepsek?->nip,
            ]
        );

        // Wali Kelas Tunarungu
        User::updateOrCreate(
            ['email' => 'rinapratiwi@slbinsanmutiara.sch.id'],
            [
                'name'     => 'Rina Pratiwi, S.Pd.',
                'password' => Hash::make('password'),
                'role'     => 'wali_kelas',
                'is_aktif' => true,
                'guru_id'  => $guruRina?->id,
                'nip'      => $guruRina?->nip,
            ]
        );

        // Wali Kelas Tunagrahita
        User::updateOrCreate(
            ['email' => 'ahmadfauzi@slbinsanmutiara.sch.id'],
            [
                'name'     => 'Ahmad Fauzi, S.Pd.',
                'password' => Hash::make('password'),
                'role'     => 'wali_kelas',
                'is_aktif' => true,
                'guru_id'  => $guruAhmad?->id,
                'nip'      => $guruAhmad?->nip,
            ]
        );

    }
}

