<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\EvaluasiNonakademik;
use App\Models\Rapor;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvaluasiNonakademikController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $sekolah = Sekolah::instance();

        $search = $request->query('search');

        $query = Siswa::with([
            'penugasanKelas.kelas',
            'evaluasiNonakademik' => fn($q) => $q->where('tahun_ajaran_id', optional($tahunAktif)->id),
            'rapor' => fn($q) => $q->where('tahun_ajaran_id', optional($tahunAktif)->id),
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $siswaModels = $query->get();

        $dataNonAkademik = [];
        foreach ($siswaModels as $s) {
            $penugasan = $s->penugasanAktif();
            $eval = $s->evaluasiNonakademik->first();
            $rapor = $s->rapor->first();

            $isLengkap = ($eval !== null);

            $dataNonAkademik[] = [
                'id'           => $s->id,
                'nama'         => $s->nama_lengkap,
                'nisn'         => $s->nisn,
                'tingkat'      => ($penugasan?->tingkat ?? 'VII') . ' / ' . ($penugasan?->fase ?? 'D'),
                'kehadiran'    => $eval ? ("S:{$eval->sakit} · I:{$eval->izin} · A:{$eval->alpa}") : 'Belum diisi',
                'sakit'        => $eval?->sakit ?? 0,
                'izin'         => $eval?->izin ?? 0,
                'alpa'         => $eval?->alpa ?? 0,
                'perkembangan' => $eval?->perkembangan ?? 'Perkembangan fisik dan adaptasi lingkungan baik.',
                'catatan_wali' => $rapor?->catatan_wali_kelas ?? 'Ananda menunjukkan antusiasme belajar yang membanggakan.',
                'kegiatan'     => 'Terisi',
                'status'       => $isLengkap ? 'Lengkap' : 'Belum lengkap',
            ];
        }

        return view('wali_kelas.nonakademik.index', compact('tahunAktif', 'sekolah', 'dataNonAkademik', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_id'     => 'required|exists:siswa,id',
            'sakit'        => 'required|integer|min:0',
            'izin'         => 'required|integer|min:0',
            'alpa'         => 'required|integer|min:0',
            'perkembangan' => 'required|string|min:5|max:1000',
            'catatan_wali' => 'nullable|string|max:1000',
        ]);

        $tahunAktif = TahunAjaran::getAktif();

        EvaluasiNonakademik::updateOrCreate(
            [
                'siswa_id'        => $validated['siswa_id'],
                'tahun_ajaran_id' => optional($tahunAktif)->id,
            ],
            [
                'sakit'              => $validated['sakit'],
                'izin'               => $validated['izin'],
                'alpa'               => $validated['alpa'],
                'perkembangan'       => $validated['perkembangan'],
                'tidak_ada_prestasi' => false,
            ]
        );

        if (!empty($validated['catatan_wali'])) {
            Rapor::updateOrCreate(
                [
                    'siswa_id'        => $validated['siswa_id'],
                    'tahun_ajaran_id' => optional($tahunAktif)->id,
                ],
                [
                    'catatan_wali_kelas' => $validated['catatan_wali'],
                ]
            );
        }

        $siswa = Siswa::find($validated['siswa_id']);
        ActivityLog::log('Evaluasi non-akademik diisi untuk ' . ($siswa?->nama_lengkap ?? 'Siswa'), null, 'Sukses');

        return redirect()->route('wali.nonakademik.index')
            ->with('success', 'Evaluasi non-akademik untuk ' . ($siswa?->nama_lengkap ?? 'Siswa') . ' berhasil disimpan.');
    }
}
