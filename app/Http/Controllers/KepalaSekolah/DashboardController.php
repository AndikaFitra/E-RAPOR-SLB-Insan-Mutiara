<?php

namespace App\Http\Controllers\KepalaSekolah;

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
            'menunggu_tinjauan' => 3,
            'subtext_tinjauan'  => 'Diajukan wali kelas',
            'rapor_disetujui'   => 18,
            'subtext_disetujui' => 'Dari 48 siswa aktif',
            'dikembalikan'      => 4,
            'subtext_kembali'   => 'Menunggu perbaikan',
            'kelas_lengkap'     => '2 / 6',
            'subtext_kelas'     => 'Seluruh rapor diajukan',
        ];

        // Antrean tinjauan
        $antreanTinjauan = [
            [
                'id'         => 1,
                'nama_siswa' => 'Alya Putri',
                'kelas'      => 'Tunarungu A',
                'wali_kelas' => 'Rina Pratiwi',
                'tanggal'    => 'Diajukan 5 Oktober 2026',
            ],
            [
                'id'         => 2,
                'nama_siswa' => 'Elsa Rahma',
                'kelas'      => 'Tunarungu A',
                'wali_kelas' => 'Rina Pratiwi',
                'tanggal'    => 'Diajukan 5 Oktober 2026',
            ],
            [
                'id'         => 3,
                'nama_siswa' => 'Nadia Safira',
                'kelas'      => 'Tunagrahita A',
                'wali_kelas' => 'Dewi Anggraini',
                'tanggal'    => 'Diajukan 4 Oktober 2026',
            ],
        ];

        // Penyelesaian per kelas
        $penyelesaianKelas = [
            [
                'kelas'      => 'Tunarungu A',
                'sublabel'   => '2 dari 6 disetujui',
                'percentage' => 33,
            ],
            [
                'kelas'      => 'Tunagrahita A',
                'sublabel'   => '6 dari 8 disetujui',
                'percentage' => 75,
            ],
            [
                'kelas'      => 'Tunarungu B',
                'sublabel'   => '4 dari 8 disetujui',
                'percentage' => 50,
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
