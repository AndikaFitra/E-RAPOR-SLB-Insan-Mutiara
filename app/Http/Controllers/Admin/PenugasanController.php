<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\PenugasanSiswaKelas;
use App\Models\PenugasanWaliKelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenugasanController extends Controller
{
    public function index(): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $guruList = Guru::all();
        $kelasList = Kelas::all();
        $siswaModels = Siswa::with('penugasanKelas.kelas')->orderBy('nama_lengkap')->get();

        $siswaPenugasan = [];
        foreach ($siswaModels as $s) {
            $penugasan = $s->penugasanAktif();
            $siswaPenugasan[] = [
                'id'             => $s->id,
                'nama'           => $s->nama_lengkap,
                'nisn'           => $s->nisn,
                'tingkat_fase'   => ($penugasan?->tingkat ?? 'VII') . ' · ' . ($penugasan?->fase ?? 'D'),
                'kelas_saat_ini' => $penugasan?->kelas?->nama ?? 'Belum ditugaskan',
                'selected'       => ($penugasan !== null),
            ];
        }

        return view('admin.penugasan.index', compact('tahunAktif', 'guruList', 'kelasList', 'siswaPenugasan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'guru_id'  => 'required|exists:guru,id',
            'kelas_id' => 'required|exists:kelas,id',
            'siswa'    => 'nullable|array',
            'siswa.*'  => 'exists:siswa,id',
        ]);

        $tahunAktif = TahunAjaran::getAktif();

        // Tugaskan wali kelas
        PenugasanWaliKelas::updateOrCreate(
            [
                'kelas_id'        => $validated['kelas_id'],
                'tahun_ajaran_id' => optional($tahunAktif)->id,
            ],
            [
                'guru_id' => $validated['guru_id'],
            ]
        );

        // Tugaskan siswa yang dipilih
        if (!empty($validated['siswa'])) {
            foreach ($validated['siswa'] as $siswaId) {
                PenugasanSiswaKelas::updateOrCreate(
                    [
                        'siswa_id'        => $siswaId,
                        'tahun_ajaran_id' => optional($tahunAktif)->id,
                    ],
                    [
                        'kelas_id' => $validated['kelas_id'],
                        'tingkat'  => 'VII',
                        'fase'     => 'D',
                    ]
                );
            }
        }

        $guru = Guru::find($validated['guru_id']);
        $kelas = Kelas::find($validated['kelas_id']);

        ActivityLog::log("Menetapkan penugasan wali kelas {$guru->nama_lengkap} ke {$kelas->nama}", $kelas, 'Sukses');

        return redirect()->route('admin.kelas.index')
            ->with('success', "Penugasan wali kelas dan siswa untuk {$kelas->nama} berhasil diperbarui.");
    }
}
