<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\EvaluasiNonakademik;
use App\Models\MataPelajaran;
use App\Models\NilaiAsesmen;
use App\Models\Rapor;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $sekolah = Sekolah::instance();

        // Data siswa di kelas binaan (Tunarungu)
        $siswaKelas = Siswa::where('kekhususan', 'Tunarungu')->get();
        $totalSiswa = $siswaKelas->count();
        $siswaIds = $siswaKelas->pluck('id')->toArray();

        // Hitung Asesmen Selesai
        $mapelCount = MataPelajaran::where('is_aktif', true)->whereNotIn('kode', ['BINADIRI', 'PKISP'])->count();
        $targetKomponen = $totalSiswa * $mapelCount;
        $terisiKomponen = NilaiAsesmen::whereIn('siswa_id', $siswaIds)
            ->where('tahun_ajaran_id', optional($tahunAktif)->id)
            ->whereNotNull('nilai_akhir')
            ->count();
        $persenAsesmen = $targetKomponen > 0 ? round(($terisiKomponen / $targetKomponen) * 100) : 0;

        // Hitung Non-akademik Selesai
        $nonAkademikCount = EvaluasiNonakademik::whereIn('siswa_id', $siswaIds)
            ->where('tahun_ajaran_id', optional($tahunAktif)->id)
            ->count();
        $persenNonAkademik = $totalSiswa > 0 ? round(($nonAkademikCount / $totalSiswa) * 100) : 0;

        // Rapor Perlu Revisi & Disetujui
        $revisiCount = Rapor::whereIn('siswa_id', $siswaIds)
            ->where('tahun_ajaran_id', optional($tahunAktif)->id)
            ->where('status', Rapor::STATUS_DIKEMBALIKAN)
            ->count();

        $disetujuiCount = Rapor::whereIn('siswa_id', $siswaIds)
            ->where('tahun_ajaran_id', optional($tahunAktif)->id)
            ->where('status', Rapor::STATUS_DISETUJUI)
            ->count();
        $persenDisetujui = $totalSiswa > 0 ? round(($disetujuiCount / $totalSiswa) * 100) : 0;

        $stats = [
            'siswa_di_kelas'      => $totalSiswa,
            'subtext_siswa'       => 'VII–IX · Fase D (Tunarungu)',
            'nilai_asesmen'       => "{$terisiKomponen} / {$targetKomponen}",
            'subtext_asesmen'     => "{$persenAsesmen}% komponen selesai",
            'non_akademik'        => "{$nonAkademikCount} / {$totalSiswa}",
            'subtext_nonakademik' => ($totalSiswa - $nonAkademikCount) > 0 ? ($totalSiswa - $nonAkademikCount) . ' siswa perlu dilengkapi' : 'Semua siswa terisi',
            'perlu_revisi'        => $revisiCount,
            'subtext_revisi'      => $revisiCount > 0 ? "{$revisiCount} rapor perlu perbaikan" : 'Tidak ada revisi pending',
        ];

        // Pekerjaan berikutnya
        $pekerjaanBerikutnya = [];
        if ($revisiCount > 0) {
            $pekerjaanBerikutnya[] = [
                'title'       => 'Perbaiki rapor yang dikembalikan',
                'subtitle'    => 'Terdapat ' . $revisiCount . ' rapor dengan catatan revisi dari Kepala Sekolah.',
                'button_text' => 'Buka Revisi',
                'button_href' => route('wali.rapor.index', ['status' => 'dikembalikan']),
            ];
        }
        if ($terisiKomponen < $targetKomponen) {
            $pekerjaanBerikutnya[] = [
                'title'       => 'Lengkapi asesmen mata pelajaran',
                'subtitle'    => ($targetKomponen - $terisiKomponen) . ' komponen nilai & narasi capaian belum lengkap.',
                'button_text' => 'Input Nilai',
                'button_href' => route('wali.asesmen.index'),
            ];
        }
        if ($nonAkademikCount < $totalSiswa) {
            $pekerjaanBerikutnya[] = [
                'title'       => 'Lengkapi evaluasi non-akademik',
                'subtitle'    => ($totalSiswa - $nonAkademikCount) . ' siswa belum memiliki catatan kehadiran & perkembangan.',
                'button_text' => 'Lengkapi Evaluasi',
                'button_href' => route('wali.nonakademik.index'),
            ];
        }
        if (empty($pekerjaanBerikutnya)) {
            $pekerjaanBerikutnya[] = [
                'title'       => 'Semua data kelas lengkap!',
                'subtitle'    => 'Seluruh nilai, evaluasi, dan pengajuan rapor telah diproses.',
                'button_text' => 'Lihat Rapor',
                'button_href' => route('wali.rapor.index'),
            ];
        }

        // Kesiapan rapor kelas
        $kesiapanRapor = [
            [
                'title'      => 'Asesmen akademik',
                'sublabel'   => "{$persenAsesmen}% · {$terisiKomponen} dari {$targetKomponen} komponen",
                'percentage' => $persenAsesmen,
            ],
            [
                'title'      => 'Evaluasi non-akademik',
                'sublabel'   => "{$persenNonAkademik}% · {$nonAkademikCount} dari {$totalSiswa} siswa",
                'percentage' => $persenNonAkademik,
            ],
            [
                'title'      => 'Rapor disetujui',
                'sublabel'   => "{$persenDisetujui}% · {$disetujuiCount} dari {$totalSiswa} siswa",
                'percentage' => $persenDisetujui,
            ],
        ];

        // Aktivitas terbaru dari DB
        $dbLogs = ActivityLog::latest()->take(4)->get();
        $aktivitasTerbaru = [];
        foreach ($dbLogs as $log) {
            $aktivitasTerbaru[] = [
                'waktu'     => $log->created_at ? $log->created_at->diffForHumans() : 'Baru saja',
                'aktivitas' => $log->deskripsi,
                'oleh'      => $log->user?->name ?? 'Sistem',
                'status'    => $log->status ?? 'Sukses',
            ];
        }

        return view('wali_kelas.dashboard', compact(
            'tahunAktif',
            'sekolah',
            'stats',
            'pekerjaanBerikutnya',
            'kesiapanRapor',
            'aktivitasTerbaru'
        ));
    }
}
