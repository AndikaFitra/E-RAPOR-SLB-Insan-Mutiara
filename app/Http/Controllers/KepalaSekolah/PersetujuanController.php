<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Kelas;
use App\Models\Rapor;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PersetujuanController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $sekolah = Sekolah::instance();
        $kelasList = Kelas::all();

        $search = $request->query('search');
        $statusFilter = $request->query('status');
        $kelasId = $request->query('kelas_id');
        $kekhususan = $request->query('kekhususan');

        $query = Rapor::with(['siswa.penugasanKelas.kelas', 'waliKelas'])
            ->where('tahun_ajaran_id', optional($tahunAktif)->id);

        if ($search) {
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if ($kelasId) {
            $query->whereHas('siswa.penugasanKelas', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($kekhususan && in_array($kekhususan, ['Tunarungu', 'Tunagrahita', 'Autis'])) {
            $query->whereHas('siswa', function ($q) use ($kekhususan) {
                $q->where('kekhususan', $kekhususan);
            });
        }

        $allRapors = $query->latest('tgl_pengajuan')->get();

        // Hitung counts status
        $counts = [
            'total'        => 0,
            'diajukan'     => 0,
            'disetujui'    => 0,
            'dikembalikan' => 0,
        ];

        foreach ($allRapors as $r) {
            $counts['total']++;
            $st = strtolower($r->status);
            if (isset($counts[$st])) {
                $counts[$st]++;
            }
        }

        // Filter status jika dipilih
        if ($statusFilter && in_array(strtolower($statusFilter), ['diajukan', 'disetujui', 'dikembalikan'])) {
            $raporModels = $allRapors->filter(fn($r) => strtolower($r->status) === strtolower($statusFilter));
        } else {
            $raporModels = $allRapors;
        }

        $antrean = [];
        foreach ($raporModels as $r) {
            $siswa = $r->siswa;
            if (!$siswa) continue;
            $penugasan = $siswa->penugasanAktif();
            $antrean[] = [
                'id'         => $r->id,
                'siswa_id'   => $siswa->id,
                'nama_siswa' => $siswa->nama_lengkap,
                'nis'        => '26' . str_pad($siswa->id, 3, '0', STR_PAD_LEFT),
                'nisn'       => $siswa->nisn,
                'kelas'      => $penugasan?->kelas?->nama ?? ($siswa->kekhususan . ' A'),
                'fase'       => ($penugasan?->tingkat ?? 'VII') . ' / ' . ($penugasan?->fase ?? 'D'),
                'wali_kelas' => $r->waliKelas?->nama_lengkap ?? 'Rina Pratiwi, S.Pd.',
                'tanggal'    => $r->tgl_pengajuan ? $r->tgl_pengajuan->translatedFormat('d F Y') : '5 Oktober 2026',
                'status'     => ucfirst($r->status),
                'kekhususan' => $siswa->kekhususan,
            ];
        }

        return view('kepala_sekolah.persetujuan.index', compact(
            'tahunAktif',
            'sekolah',
            'antrean',
            'kelasList',
            'search',
            'statusFilter',
            'kelasId',
            'kekhususan',
            'counts'
        ));
    }

    public function show(int $id): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $sekolah = Sekolah::instance();

        $rapor = Rapor::with(['siswa.penugasanKelas.kelas', 'waliKelas'])->find($id);
        if ($rapor) {
            $siswa = $rapor->siswa;
        } else {
            $siswa = Siswa::with(['penugasanKelas.kelas', 'rapor'])->find($id) ?? Siswa::first();
            $rapor = $siswa?->rapor()->latest()->first();
        }

        return view('kepala_sekolah.persetujuan.show', compact('tahunAktif', 'sekolah', 'id', 'siswa', 'rapor'));
    }

    public function cetak(int $id): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $sekolah = Sekolah::instance();

        $rapor = Rapor::with(['siswa.penugasanKelas.kelas', 'waliKelas'])->find($id);
        if ($rapor) {
            $siswa = $rapor->siswa;
        } else {
            $siswa = Siswa::with(['penugasanKelas.kelas', 'rapor'])->findOrFail($id);
            $rapor = $siswa->rapor()->latest()->first();
        }

        return view('wali_kelas.rapor.cetak', compact('tahunAktif', 'sekolah', 'id', 'siswa', 'rapor'));
    }

    public function approve(Request $request, int $id): RedirectResponse
    {
        $rapor = Rapor::find($id);
        if (!$rapor) {
            $rapor = Rapor::where('siswa_id', $id)->first();
        }

        if ($rapor) {
            $rapor->update([
                'status'          => Rapor::STATUS_DISETUJUI,
                'tgl_persetujuan' => now(),
            ]);
            ActivityLog::log('Persetujuan Rapor disetujui untuk ' . ($rapor->siswa?->nama_lengkap ?? 'Siswa'), $rapor, 'Sukses');
        }

        return redirect()->route('kepala.persetujuan.index')
            ->with('success', 'Rapor hasil belajar berhasil disetujui secara resmi oleh Kepala Sekolah.');
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'catatan_revisi' => 'required|string|min:5|max:1000',
        ]);

        $rapor = Rapor::find($id);
        if (!$rapor) {
            $rapor = Rapor::where('siswa_id', $id)->first();
        }

        if ($rapor) {
            $rapor->update([
                'status'         => Rapor::STATUS_DIKEMBALIKAN,
                'catatan_revisi' => $request->catatan_revisi,
            ]);
            ActivityLog::log('Persetujuan Rapor dikembalikan untuk revisi: ' . ($rapor->siswa?->nama_lengkap ?? 'Siswa'), $rapor, 'Dikembalikan');
        }

        return redirect()->route('kepala.persetujuan.index')
            ->with('info', 'Rapor telah dikembalikan ke Wali Kelas beserta catatan perbaikan revisi.');
    }
}
