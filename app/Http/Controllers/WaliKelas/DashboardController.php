<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Models\TahunAjaran;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $sekolah = Sekolah::instance();

        // 1:1 metrics from design mockup
        $stats = [
            'siswa_di_kelas'    => 6,
            'subtext_siswa'     => 'VII–IX · Fase D',
            'nilai_asesmen'     => '68 / 78',
            'subtext_asesmen'   => '87% komponen selesai',
            'non_akademik'      => '4 / 6',
            'subtext_nonakademik' => '2 siswa perlu dilengkapi',
            'perlu_revisi'      => 1,
            'subtext_revisi'    => 'Rapor Citra Lestari',
        ];

        // Pekerjaan berikutnya
        $pekerjaanBerikutnya = [
            [
                'title'       => 'Revisi rapor Citra Lestari',
                'subtitle'    => 'Tambahkan tindak lanjut pada capaian Bahasa Indonesia.',
                'button_text' => 'Buka Revisi',
                'button_href' => route('wali.rapor.index', ['status' => 'revisi']),
            ],
            [
                'title'       => 'Lengkapi asesmen 2 siswa',
                'subtitle'    => 'Dimas dan Fajar masih memiliki komponen Draft.',
                'button_text' => 'Input Nilai',
                'button_href' => route('wali.asesmen.index'),
            ],
            [
                'title'       => 'Lengkapi evaluasi non-akademik',
                'subtitle'    => 'Catatan perkembangan fisik belum tersimpan.',
                'button_text' => 'Lengkapi Evaluasi',
                'button_href' => route('wali.nonakademik.index'),
            ],
        ];

        // Kesiapan rapor kelas
        $kesiapanRapor = [
            [
                'title'      => 'Asesmen akademik',
                'sublabel'   => '87% · 68 dari 78 komponen',
                'percentage' => 87,
            ],
            [
                'title'      => 'Evaluasi non-akademik',
                'sublabel'   => '67% · 4 dari 6 siswa',
                'percentage' => 67,
            ],
            [
                'title'      => 'Rapor disetujui',
                'sublabel'   => '33% · 2 dari 6 siswa',
                'percentage' => 33,
            ],
        ];

        // Aktivitas terbaru
        $aktivitasTerbaru = [
            [
                'waktu'     => 'Hari ini, 09.20',
                'aktivitas' => 'Rapor Alya Putri diajukan untuk ditinjau',
                'oleh'      => 'Rina Pratiwi',
                'status'    => 'Diajukan',
            ],
            [
                'waktu'     => 'Hari ini, 08.45',
                'aktivitas' => 'Data kelas Tunarungu A diperbarui',
                'oleh'      => 'Admin Sekolah',
                'status'    => 'Aktif',
            ],
            [
                'waktu'     => '4 Okt, 14.10',
                'aktivitas' => 'Rapor Bima Pratama telah disetujui',
                'oleh'      => 'Novia Parenri',
                'status'    => 'Disetujui',
            ],
        ];

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
