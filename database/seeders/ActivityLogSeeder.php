<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: activity_log
     */
    public function run(): void
    {
        $admin  = User::where('role', 'admin')->first();
        $kepsek = User::where('role', 'kepala_sekolah')->first();
        $wali   = User::where('role', 'wali_kelas')->first();

        $logs = [
            [
                'user_id'    => $admin?->id,
                'subjek'     => 'Inisialisasi sistem database e-Rapor SLB Insan Mutiara',
                'model_type' => 'system',
                'model_id'   => null,
                'status'     => 'Sukses',
                'created_at' => now()->subDays(5),
            ],
            [
                'user_id'    => $wali?->id,
                'subjek'     => 'Wali Kelas mengajukan rapor siswa Bima Pratama ke Kepala Sekolah',
                'model_type' => 'rapor',
                'model_id'   => 2,
                'status'     => 'Diajukan',
                'created_at' => now()->subDays(2),
            ],
            [
                'user_id'    => $kepsek?->id,
                'subjek'     => 'Kepala Sekolah menyetujui pengajuan rapor Bima Pratama',
                'model_type' => 'rapor',
                'model_id'   => 2,
                'status'     => 'Disetujui',
                'created_at' => now()->subDay(),
            ],
            [
                'user_id'    => $wali?->id,
                'subjek'     => 'Wali Kelas mengajukan rapor siswa Alya Putri ke Kepala Sekolah',
                'model_type' => 'rapor',
                'model_id'   => 1,
                'status'     => 'Diajukan',
                'created_at' => now()->subHours(2),
            ],
        ];

        foreach ($logs as $log) {
            ActivityLog::create($log);
        }
    }
}
