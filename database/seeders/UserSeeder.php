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
        $guruSiti   = Guru::where('nama_lengkap', 'like', '%Siti Nurhaliza%')->first();

        // Admin
        User::updateOrCreate(
            ['email' => 'admin@slbinsanmutiara.sch.id'],
            [
                'name'     => 'Administrator Sistem',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'is_aktif' => true,
                'guru_id'  => null,
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
            ]
        );

        // Wali Kelas Autis
        User::updateOrCreate(
            ['email' => 'sitinurhaliza@slbinsanmutiara.sch.id'],
            [
                'name'     => 'Siti Nurhaliza, S.Pd.',
                'password' => Hash::make('password'),
                'role'     => 'wali_kelas',
                'is_aktif' => true,
                'guru_id'  => $guruSiti?->id,
            ]
        );

        // Demo Akun Nonaktif
        User::updateOrCreate(
            ['email' => 'nonaktif@slbinsanmutiara.sch.id'],
            [
                'name'     => 'Akun Nonaktif Demo',
                'password' => Hash::make('password'),
                'role'     => 'wali_kelas',
                'is_aktif' => false,
                'guru_id'  => null,
            ]
        );
    }
}
