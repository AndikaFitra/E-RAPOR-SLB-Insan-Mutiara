<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\MataPelajaran;
use App\Models\NilaiAsesmen;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NilaiAsesmenController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $sekolah = Sekolah::instance();
        $siswaList = Siswa::where('kekhususan', 'Tunarungu')->get();

        $listAsesmen = [
            ['id' => 1, 'kode' => 'PABP', 'mapel' => 'Pendidikan Agama dan Budi Pekerti', 'tingkat' => 'VII–IX / D', 'nilai' => '6 / 6', 'narasi' => '6 / 6', 'status' => 'Lengkap'],
            ['id' => 2, 'kode' => 'PPKN', 'mapel' => 'Pendidikan Pancasila', 'tingkat' => 'VII–IX / D', 'nilai' => '6 / 6', 'narasi' => '6 / 6', 'status' => 'Lengkap'],
            ['id' => 3, 'kode' => 'BIND', 'mapel' => 'Bahasa Indonesia', 'tingkat' => 'VII–IX / D', 'nilai' => '6 / 6', 'narasi' => '6 / 6', 'status' => 'Lengkap'],
            ['id' => 4, 'kode' => 'MTK', 'mapel' => 'Matematika', 'tingkat' => 'VII–IX / D', 'nilai' => '6 / 6', 'narasi' => '5 / 6', 'status' => 'Belum lengkap'],
            ['id' => 5, 'kode' => 'BING', 'mapel' => 'Bahasa Inggris', 'tingkat' => 'VII–IX / D', 'nilai' => '6 / 6', 'narasi' => '5 / 6', 'status' => 'Belum lengkap'],
            ['id' => 6, 'kode' => 'IPA', 'mapel' => 'IPA', 'tingkat' => 'VII–IX / D', 'nilai' => '5 / 6', 'narasi' => '5 / 6', 'status' => 'Belum lengkap'],
            ['id' => 7, 'kode' => 'IPS', 'mapel' => 'IPS', 'tingkat' => 'VII–IX / D', 'nilai' => '5 / 6', 'narasi' => '5 / 6', 'status' => 'Belum lengkap'],
            ['id' => 8, 'kode' => 'SBR', 'mapel' => 'Seni Budaya · Seni Rupa', 'tingkat' => 'VII–IX / D', 'nilai' => '5 / 6', 'narasi' => '5 / 6', 'status' => 'Belum lengkap'],
            ['id' => 9, 'kode' => 'PJOK', 'mapel' => 'PJOK', 'tingkat' => 'VII–IX / D', 'nilai' => '5 / 6', 'narasi' => '5 / 6', 'status' => 'Belum lengkap'],
            ['id' => 10, 'kode' => 'K-SUV', 'mapel' => 'Keterampilan Pilihan · Suvenir', 'tingkat' => 'VII–IX / D', 'nilai' => '5 / 6', 'narasi' => '5 / 6', 'status' => 'Belum lengkap'],
            ['id' => 11, 'kode' => 'K-TKC', 'mapel' => 'Keterampilan Pilihan · Tata Kecantikan', 'tingkat' => 'VII–IX / D', 'nilai' => '5 / 6', 'narasi' => '5 / 6', 'status' => 'Belum lengkap'],
            ['id' => 12, 'kode' => 'PKPBI', 'mapel' => 'Program Kekhususan · PKPBI (Tunarungu)', 'tingkat' => 'VII–IX / D', 'nilai' => '6 / 6', 'narasi' => '6 / 6', 'status' => 'Lengkap'],
            ['id' => 13, 'kode' => 'BMR', 'mapel' => 'Muatan Lokal · Budaya Melayu Riau', 'tingkat' => 'VII–IX / D', 'nilai' => '5 / 6', 'narasi' => '5 / 6', 'status' => 'Belum lengkap'],
        ];

        return view('wali_kelas.asesmen.index', compact('tahunAktif', 'sekolah', 'listAsesmen', 'siswaList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mapel_nama'     => 'required|string',
            'siswa_id'       => 'required|exists:siswa,id',
            'nilai_akhir'    => 'required|numeric|min:0|max:100',
            'narasi_capaian' => 'required|string|min:5|max:1000',
        ]);

        $tahunAktif = TahunAjaran::getAktif();
        $mapel = MataPelajaran::firstWhere('nama', 'like', "%{$validated['mapel_nama']}%")
                 ?? MataPelajaran::first();

        if ($mapel) {
            NilaiAsesmen::updateOrCreate(
                [
                    'siswa_id'          => $validated['siswa_id'],
                    'mata_pelajaran_id' => $mapel->id,
                    'tahun_ajaran_id'   => optional($tahunAktif)->id,
                ],
                [
                    'nilai_akhir'    => $validated['nilai_akhir'],
                    'narasi_capaian' => $validated['narasi_capaian'],
                ]
            );
        }

        $siswa = Siswa::find($validated['siswa_id']);
        ActivityLog::log('Input nilai asesmen: ' . $validated['mapel_nama'] . ' untuk ' . ($siswa?->nama_lengkap ?? 'Siswa'), null, 'Sukses');

        return redirect()->route('wali.asesmen.index')
            ->with('success', 'Penilaian mata pelajaran ' . $validated['mapel_nama'] . ' berhasil disimpan.');
    }
}
