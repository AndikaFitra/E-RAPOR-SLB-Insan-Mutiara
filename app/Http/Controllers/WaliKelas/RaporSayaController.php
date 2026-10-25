<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Rapor;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RaporSayaController extends Controller
{
    /**
     * Tampilkan daftar rekapitulasi rapor siswa wali kelas.
     */
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $sekolah = Sekolah::instance();
        $siswaList = Siswa::with(['penugasanKelas.kelas', 'rapor' => fn($q) => $q->where('tahun_ajaran_id', optional($tahunAktif)->id)])
            ->orderBy('id')
            ->get();

        return view('wali_kelas.rapor.index', compact('tahunAktif', 'sekolah', 'siswaList'));
    }

    /**
     * Tampilkan lembar rapor lengkap (Sesuai PDF scan resmi SLB Insan Mutiara).
     */
    public function show($id): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $sekolah = Sekolah::instance();
        $siswa = Siswa::with(['penugasanKelas.kelas', 'orangTua', 'prestasi', 'rapor'])->find($id) 
                 ?? Siswa::first();
        $rapor = $siswa?->rapor()->where('tahun_ajaran_id', optional($tahunAktif)->id)->first();

        return view('wali_kelas.rapor.show', compact('tahunAktif', 'sekolah', 'siswa', 'id', 'rapor'));
    }

    /**
     * Ajukan rapor ke Kepala Sekolah
     */
    public function submit(Request $request, $id): RedirectResponse
    {
        $tahunAktif = TahunAjaran::getAktif();
        $siswa = Siswa::findOrFail($id);

        $guru = auth()->user()->guru ?? Guru::first();
        $rapor = Rapor::updateOrCreate(
            [
                'siswa_id'        => $siswa->id,
                'tahun_ajaran_id' => optional($tahunAktif)->id,
            ],
            [
                'wali_kelas_id' => $guru?->id,
                'status'        => Rapor::STATUS_DIAJUKAN,
                'tgl_pengajuan' => now(),
            ]
        );

        ActivityLog::log('Mengajukan rapor siswa ke Kepala Sekolah: ' . $siswa->nama_lengkap, $rapor, 'Sukses');

        return redirect()->route('wali.rapor.index')
            ->with('success', 'Rapor ' . $siswa->nama_lengkap . ' berhasil diajukan ke Kepala Sekolah untuk persetujuan.');
    }
}
