<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Rapor;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class RaporSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: rapor
     */
    public function run(): void
    {
        $thAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$thAktif) {
            return;
        }

        $guruRina  = Guru::where('nama_lengkap', 'like', '%Rina Pratiwi%')->first();
        $guruAhmad = Guru::where('nama_lengkap', 'like', '%Ahmad Fauzi%')->first();
        $guruSiti  = Guru::where('nama_lengkap', 'like', '%Siti Nurhaliza%')->first();

        $rapors = [
            // Tunarungu - Alya (Diajukan)
            '0160001001' => [
                'wali_kelas_id'      => $guruRina?->id,
                'status'             => Rapor::STATUS_DIAJUKAN,
                'catatan_wali_kelas' => 'Alya menunjukkan kemampuan luar biasa dalam pembelajaran visual dan komunikasi isyarat. Pertahankan semangat belajar yang tinggi.',
                'tgl_rapor'          => '2026-12-19',
                'tgl_pengajuan'      => now()->subHours(2),
            ],
            // Tunarungu - Bima (Disetujui)
            '0150001002' => [
                'wali_kelas_id'      => $guruRina?->id,
                'status'             => Rapor::STATUS_DISETUJUI,
                'catatan_wali_kelas' => 'Bima menunjukkan ketekunan tinggi dalam setiap sesi bina komunikasi.',
                'tgl_rapor'          => '2026-12-19',
                'tgl_pengajuan'      => now()->subDays(2),
                'tgl_persetujuan'    => now()->subDay(),
            ],
            // Tunarungu - Citra (Dikembalikan untuk revisi)
            '0140001003' => [
                'wali_kelas_id'      => $guruRina?->id,
                'status'             => Rapor::STATUS_DIKEMBALIKAN,
                'catatan_revisi'     => 'Mohon perbaiki narasi capaian pada mata pelajaran Bahasa Indonesia agar lebih spesifik.',
                'catatan_wali_kelas' => 'Citra memiliki potensi artistik yang kuat dalam seni visual.',
                'tgl_rapor'          => '2026-12-19',
                'tgl_pengajuan'      => now()->subDays(3),
            ],
            // Tunarungu - Elsa (Diajukan)
            '0150001005' => [
                'wali_kelas_id'      => $guruRina?->id,
                'status'             => Rapor::STATUS_DIAJUKAN,
                'catatan_wali_kelas' => 'Elsa sangat aktif dalam kegiatan kelas dan cepat memahami instruksi visual.',
                'tgl_rapor'          => '2026-12-19',
                'tgl_pengajuan'      => now()->subHours(5),
            ],
            // Tunagrahita - Nadia (Diajukan)
            '0160001007' => [
                'wali_kelas_id'      => $guruAhmad?->id,
                'status'             => Rapor::STATUS_DIAJUKAN,
                'catatan_wali_kelas' => 'Nadia mengalami kemajuan signifikan dalam kemandirian bina diri dan merapikan pakaian.',
                'tgl_rapor'          => '2026-12-19',
                'tgl_pengajuan'      => now()->subHours(4),
            ],
            // Autis - Rafi (Diajukan)
            '0150002001' => [
                'wali_kelas_id'      => $guruSiti?->id,
                'status'             => Rapor::STATUS_DIAJUKAN,
                'catatan_wali_kelas' => 'Rafi semakin nyaman berinteraksi sosial dan menunjukkan kontak mata yang lebih fokus.',
                'tgl_rapor'          => '2026-12-19',
                'tgl_pengajuan'      => now()->subHours(3),
            ],
            // Scan Asli - Friska (Disetujui)
            '0053935552' => [
                'wali_kelas_id'      => $guruRina?->id,
                'status'             => Rapor::STATUS_DISETUJUI,
                'catatan_wali_kelas' => 'Tingkatkan terus keterampilan vokasi dan tata boga untuk bekal kemandirian.',
                'tgl_rapor'          => '2026-12-19',
                'tgl_pengajuan'      => now()->subDays(3),
                'tgl_persetujuan'    => now()->subDays(2),
            ],
        ];

        foreach ($rapors as $nisn => $data) {
            $siswa = Siswa::where('nisn', $nisn)->first();
            if ($siswa && $data['wali_kelas_id']) {
                Rapor::updateOrCreate(
                    [
                        'siswa_id'        => $siswa->id,
                        'tahun_ajaran_id' => $thAktif->id,
                    ],
                    $data
                );
            }
        }
    }
}
