<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Kelas;
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
        $tahunId = optional($tahunAktif)->id;

        $totalSiswa = Siswa::count();

        // 1. Live stats
        $menungguCount = Rapor::where('tahun_ajaran_id', $tahunId)->where('status', Rapor::STATUS_DIAJUKAN)->count();
        $disetujuiCount = Rapor::where('tahun_ajaran_id', $tahunId)->where('status', Rapor::STATUS_DISETUJUI)->count();
        $dikembalikanCount = Rapor::where('tahun_ajaran_id', $tahunId)->where('status', Rapor::STATUS_DIKEMBALIKAN)->count();

        $kelasList = Kelas::all();
        $kelasLengkapCount = 0;
        $penyelesaianKelas = [];

        foreach ($kelasList as $k) {
            $siswaIdsInKelas = Siswa::where('kekhususan', $k->kekhususan)->pluck('id');
            $jmlSiswa = $siswaIdsInKelas->count();
            $approvedInKelas = Rapor::where('tahun_ajaran_id', $tahunId)
                ->whereIn('siswa_id', $siswaIdsInKelas)
                ->where('status', Rapor::STATUS_DISETUJUI)
                ->count();

            $persen = $jmlSiswa > 0 ? round(($approvedInKelas / $jmlSiswa) * 100) : 0;
            if ($approvedInKelas === $jmlSiswa && $jmlSiswa > 0) {
                $kelasLengkapCount++;
            }

            $penyelesaianKelas[] = [
                'kelas'      => $k->nama,
                'sublabel'   => "{$approvedInKelas} dari {$jmlSiswa} disetujui",
                'percentage' => $persen,
            ];
        }

        $stats = [
            'menunggu_tinjauan' => $menungguCount,
            'subtext_tinjauan'  => 'Diajukan wali kelas',
            'rapor_disetujui'   => $disetujuiCount,
            'subtext_disetujui' => "Dari {$totalSiswa} siswa aktif",
            'dikembalikan'      => $dikembalikanCount,
            'subtext_kembali'   => 'Menunggu perbaikan wali',
            'kelas_lengkap'     => "{$kelasLengkapCount} / " . count($kelasList),
            'subtext_kelas'     => 'Seluruh rapor disetujui',
        ];

        // 2. Antrean tinjauan dari DB
        $raporsDiajukan = Rapor::with(['siswa.penugasanKelas.kelas', 'waliKelas'])
            ->where('tahun_ajaran_id', $tahunId)
            ->where('status', Rapor::STATUS_DIAJUKAN)
            ->latest('tgl_pengajuan')
            ->take(5)
            ->get();

        $antreanTinjauan = [];
        foreach ($raporsDiajukan as $r) {
            $siswa = $r->siswa;
            if (!$siswa) continue;
            $penugasan = $siswa->penugasanAktif();
            $antreanTinjauan[] = [
                'id'         => $r->id,
                'siswa_id'   => $siswa->id,
                'nama_siswa' => $siswa->nama_lengkap,
                'kelas'      => $penugasan?->kelas?->nama ?? ($siswa->kekhususan . ' A'),
                'wali_kelas' => $r->waliKelas?->nama_lengkap ?? 'Rina Pratiwi, S.Pd.',
                'tanggal'    => $r->tgl_pengajuan ? ('Diajukan ' . $r->tgl_pengajuan->translatedFormat('d F Y')) : 'Baru saja',
            ];
        }

        // 3. Aktivitas terbaru
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

        return view('kepala_sekolah.dashboard', compact(
            'tahunAktif',
            'sekolah',
            'stats',
            'antreanTinjauan',
            'penyelesaianKelas',
            'aktivitasTerbaru'
        ));
    }
}
