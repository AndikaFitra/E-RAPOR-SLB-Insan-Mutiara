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
        $passwords = config('rapor.seed_account_passwords');
        if (app()->environment('production')) {
            if (count(array_filter($passwords, fn ($password) => is_string($password) && strlen($password) >= 12)) !== 4
                || count(array_unique($passwords)) !== 4) {
                throw new \RuntimeException('Empat SEED_*_PASSWORD yang berbeda dan minimal 12 karakter wajib diisi sebelum seeding production.');
            }
        } else {
            $fallback = config('rapor.seed_account_password') ?: 'password';
            $passwords = array_map(fn ($password) => $password ?: $fallback, $passwords);
        }

        $guruKepsek = Guru::where('jabatan', 'Kepala Sekolah')->first();
        $guruRina   = Guru::where('nama_lengkap', 'like', '%Rina Pratiwi%')->first();
        $guruAhmad  = Guru::where('nama_lengkap', 'like', '%Ahmad Fauzi%')->first();

        // Hapus hanya dua akun contoh lama yang tidak termasuk empat akun bawaan.
        User::whereIn('email', [
            'sitinurhaliza@slbinsanmutiara.sch.id',
            'nonaktif@slbinsanmutiara.sch.id',
        ])->delete();

        // Admin
        $this->upsertAccount(
            'admin@slbinsanmutiara.sch.id',
            [
                'name'     => 'Sari Wulandari, S.Pd.',
                'role'     => 'admin',
                'is_aktif' => true,
                'guru_id'  => null,
                'nip'      => '198702122010011001',
            ], $passwords['admin']
        );

        // Kepala Sekolah
        $this->upsertAccount(
            'kepsek@slbinsanmutiara.sch.id',
            [
                'name'     => 'Novia Parenri, S.Si, M.Si',
                'role'     => 'kepala_sekolah',
                'is_aktif' => true,
                'guru_id'  => $guruKepsek?->id,
                'nip'      => $guruKepsek?->nip,
            ], $passwords['kepala']
        );

        // Wali Kelas Tunarungu
        $this->upsertAccount(
            'rinapratiwi@slbinsanmutiara.sch.id',
            [
                'name'     => 'Rina Pratiwi, S.Pd.',
                'role'     => 'wali_kelas',
                'is_aktif' => true,
                'guru_id'  => $guruRina?->id,
                'nip'      => $guruRina?->nip,
            ], $passwords['wali_tunarungu']
        );

        // Wali Kelas Tunagrahita
        $this->upsertAccount(
            'ahmadfauzi@slbinsanmutiara.sch.id',
            [
                'name'     => 'Ahmad Fauzi, S.Pd.',
                'role'     => 'wali_kelas',
                'is_aktif' => true,
                'guru_id'  => $guruAhmad?->id,
                'nip'      => $guruAhmad?->nip,
            ], $passwords['wali_tunagrahita']
        );

    }

    private function upsertAccount(string $email, array $attributes, string $password): void
    {
        $user = User::firstOrNew(['email' => $email]);
        if (!$user->exists) {
            $user->password = Hash::make($password);
        }
        $user->fill($attributes)->save();
    }
}

