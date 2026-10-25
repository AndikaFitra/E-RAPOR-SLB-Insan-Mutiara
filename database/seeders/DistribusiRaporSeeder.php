<?php

namespace Database\Seeders;

use App\Models\DistribusiRapor;
use App\Models\OrangTua;
use App\Models\Rapor;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class DistribusiRaporSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tabel: distribusi_rapor
     */
    public function run(): void
    {
        $thAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$thAktif) {
            return;
        }

        // Rapor Bima (Status Disetujui -> Terkirim via WhatsApp)
        $siswaBima = Siswa::where('nisn', '0150001002')->first();
        if ($siswaBima) {
            $raporBima = Rapor::where('siswa_id', $siswaBima->id)
                ->where('tahun_ajaran_id', $thAktif->id)
                ->first();
            $ortuBima  = OrangTua::where('siswa_id', $siswaBima->id)
                ->where('is_penerima_utama', true)
                ->first();

            if ($raporBima && $ortuBima) {
                DistribusiRapor::updateOrCreate(
                    [
                        'rapor_id'     => $raporBima->id,
                        'orang_tua_id' => $ortuBima->id,
                    ],
                    [
                        'tgl_kirim'    => now()->subHours(6),
                        'status_kirim' => 'terkirim',
                        'catatan'      => 'Terkirim via WhatsApp ke nomor ' . $ortuBima->no_whatsapp,
                    ]
                );
            }
        }

        // Rapor Alya (Status Diajukan -> Pending distribusi)
        $siswaAlya = Siswa::where('nisn', '0160001001')->first();
        if ($siswaAlya) {
            $raporAlya = Rapor::where('siswa_id', $siswaAlya->id)
                ->where('tahun_ajaran_id', $thAktif->id)
                ->first();
            $ortuAlya  = OrangTua::where('siswa_id', $siswaAlya->id)
                ->where('is_penerima_utama', true)
                ->first();

            if ($raporAlya && $ortuAlya) {
                DistribusiRapor::updateOrCreate(
                    [
                        'rapor_id'     => $raporAlya->id,
                        'orang_tua_id' => $ortuAlya->id,
                    ],
                    [
                        'tgl_kirim'    => null,
                        'status_kirim' => 'pending',
                        'catatan'      => 'Menunggu persetujuan Kepala Sekolah',
                    ]
                );
            }
        }
    }
}
