<?php

namespace App\Http\Controllers\Admin;

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

        // Stat cards matching design mockup
        $stats = [
            'siswa_aktif'      => 48,
            'kelas_campuran'   => '6 kelas campuran',
            'guru_aktif'       => 12,
            'wali_ditugaskan'  => '6 wali kelas ditugaskan',
            'rapor_disetujui'  => '18 / 48',
            'persen_rapor'     => '38% rapor semester ini',
            'perlu_diperiksa'  => 3,
            'status_periksa'   => 'Data dasar belum lengkap',
        ];

        // Yang perlu ditindaklanjuti
        $tindakLanjut = [
            [
                'title'       => 'Lengkapi identitas 2 siswa',
                'subtitle'    => 'NISN dan data orang tua masih belum lengkap.',
                'button_text' => 'Buka Data Siswa',
                'button_href' => route('admin.siswa.index', ['status' => 'belum_lengkap']),
            ],
            [
                'title'       => 'Tugaskan wali kelas Tunagrahita B',
                'subtitle'    => 'Kelas belum memiliki wali pada semester aktif.',
                'button_text' => 'Buka Penugasan',
                'button_href' => route('admin.penugasan.index'),
            ],
            [
                'title'       => 'Periksa 1 nomor WhatsApp',
                'subtitle'    => 'Nomor kontak perlu dikonfirmasi kepada wali.',
                'button_text' => 'Periksa Kontak',
                'button_href' => route('admin.orang-tua.index'),
            ],
        ];

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
