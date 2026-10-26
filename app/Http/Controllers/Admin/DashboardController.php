<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\PenugasanWaliKelas;
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
        $siswaAktif = Siswa::where('is_aktif', true)->count();
        $totalGuru = Guru::count();
        $totalKelas = Kelas::count();
        $waliDitugaskan = PenugasanWaliKelas::where('tahun_ajaran_id', $tahunId)->count();

        $raporDisetujui = Rapor::where('tahun_ajaran_id', $tahunId)
            ->where('status', Rapor::STATUS_DISETUJUI)
            ->count();

        $persenRapor = $totalSiswa > 0 ? round(($raporDisetujui / $totalSiswa) * 100) : 0;

        $siswaBelumLengkap = Siswa::where(function ($q) {
            $q->whereNull('nisn')
              ->orWhere('nisn', '')
              ->orWhereNull('tempat_lahir');
        })->count();

        // Stat cards
        $stats = [
            'siswa_aktif'      => $siswaAktif,
            'kelas_campuran'   => "{$totalKelas} rombel (3 Ketunaan)",
            'guru_aktif'       => $totalGuru,
            'wali_ditugaskan'  => "{$waliDitugaskan} wali kelas ditugaskan",
            'rapor_disetujui'  => "{$raporDisetujui} / {$totalSiswa}",
            'persen_rapor'     => "{$persenRapor}% rapor disetujui",
            'perlu_diperiksa'  => $siswaBelumLengkap,
            'status_periksa'   => $siswaBelumLengkap > 0 ? "{$siswaBelumLengkap} siswa perlu dicek" : 'Semua data dasar lengkap',
        ];

        // Yang perlu ditindaklanjuti
        $tindakLanjut = [];
        if ($siswaBelumLengkap > 0) {
            $tindakLanjut[] = [
                'title'       => "Lengkapi identitas {$siswaBelumLengkap} siswa",
                'subtitle'    => 'Data dasar seperti NISN atau tempat lahir perlu dilengkapi.',
                'button_text' => 'Buka Data Siswa',
                'button_href' => route('admin.siswa.index'),
            ];
        }

        if ($waliDitugaskan < $totalKelas) {
            $tindakLanjut[] = [
                'title'       => 'Tugaskan wali kelas rombel',
                'subtitle'    => ($totalKelas - $waliDitugaskan) . ' kelas belum memiliki penugasan wali semester aktif.',
                'button_text' => 'Buka Penugasan',
                'button_href' => route('admin.penugasan.index'),
            ];
        }

        if (empty($tindakLanjut)) {
            $tindakLanjut[] = [
                'title'       => 'Seluruh data operasional lengkap',
                'subtitle'    => 'Data rombel, siswa, dan wali kelas telah terkonfigurasi dengan baik.',
                'button_text' => 'Lihat Penugasan',
                'button_href' => route('admin.penugasan.index'),
            ];
        }

        // Aksi Cepat
        $aksiCepat = [
            [
                'title'    => 'Tambah siswa',
                'subtitle' => 'Registrasi identitas siswa baru',
                'href'     => route('admin.siswa.create'),
            ],
            [
                'title'    => 'Tambah guru',
                'subtitle' => 'Siapkan penugasan semester',
                'href'     => route('admin.guru.create'),
            ],
            [
                'title'    => 'Atur tahun ajaran',
                'subtitle' => 'Kelola periode dan arsip',
                'href'     => route('admin.tahun-ajaran.index'),
            ],
        ];

        // Aktivitas Terbaru
        $dbLogs = ActivityLog::latest()->take(4)->get();
        $aktivitasTerbaru = [];
        foreach ($dbLogs as $log) {
            $aktivitasTerbaru[] = [
                'waktu'     => $log->created_at ? $log->created_at->diffForHumans() : 'Baru saja',
                'aktivitas' => $log->subjek,
                'oleh'      => $log->user?->name ?? 'Admin Sistem',
                'status'    => $log->status ?? 'Sukses',
            ];
        }

        return view('admin.dashboard', compact(
            'tahunAktif',
            'sekolah',
            'stats',
            'tindakLanjut',
            'aksiCepat',
            'aktivitasTerbaru'
        ));
    }
}
