<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Kelas;
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
        $kelasList = Kelas::all();

        $search = $request->query('search');
        $kelasId = $request->query('kelas_id');
        $selectedKelas = $kelasId ? Kelas::find($kelasId) : Kelas::where('kekhususan', 'Tunarungu')->first();
        
        $kekhususan = $selectedKelas ? $selectedKelas->kekhususan : 'Tunarungu';

        // Siswa di kelas / kekhususan ini
        $siswaList = Siswa::where('kekhususan', $kekhususan)->get();
        $siswaIds = $siswaList->pluck('id')->toArray();
        $totalSiswa = count($siswaIds);

        // Mapel yang relevan
        $mapelQuery = MataPelajaran::where('is_aktif', true);

        // Filter kekhususan: sembunyikan program kekhususan yang tidak sesuai
        if ($kekhususan === 'Tunarungu') {
            $mapelQuery->whereNotIn('kode', ['BINADIRI', 'PKISP']);
        } elseif ($kekhususan === 'Tunagrahita') {
            $mapelQuery->whereNotIn('kode', ['PKPBI', 'PKISP']);
        } elseif ($kekhususan === 'Autis') {
            $mapelQuery->whereNotIn('kode', ['PKPBI', 'BINADIRI']);
        }

        if ($search) {
            $mapelQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        $allMapel = $mapelQuery->get();

        // Ambil semua nilai asesmen siswa kelas ini
        $nilaiDb = NilaiAsesmen::whereIn('siswa_id', $siswaIds)
            ->where('tahun_ajaran_id', optional($tahunAktif)->id)
            ->get();

        $existingScores = [];
        foreach ($nilaiDb as $n) {
            $existingScores[$n->mata_pelajaran_id][$n->siswa_id] = [
                'nilai_akhir'    => $n->nilai_akhir,
                'narasi_capaian' => $n->narasi_capaian,
            ];
        }

        $listAsesmen = [];
        foreach ($allMapel as $m) {
            $nilaiCount = $nilaiDb->where('mata_pelajaran_id', $m->id)->whereNotNull('nilai_akhir')->count();
            $narasiCount = $nilaiDb->where('mata_pelajaran_id', $m->id)->filter(fn($row) => !empty(trim((string)$row->narasi_capaian)))->count();

            $isLengkap = ($totalSiswa > 0 && $nilaiCount >= $totalSiswa && $narasiCount >= $totalSiswa);

            $listAsesmen[] = [
                'id'       => $m->id,
                'kode'     => $m->kode,
                'mapel'    => $m->nama,
                'tingkat'  => 'VII–IX / D',
                'nilai'    => "{$nilaiCount} / {$totalSiswa}",
                'narasi'   => "{$narasiCount} / {$totalSiswa}",
                'status'   => $isLengkap ? 'Lengkap' : 'Belum lengkap',
            ];
        }

        return view('wali_kelas.asesmen.index', compact(
            'tahunAktif',
            'sekolah',
            'listAsesmen',
            'siswaList',
            'kelasList',
            'selectedKelas',
            'search',
            'existingScores'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mapel_id'       => 'nullable|exists:mata_pelajaran,id',
            'mapel_nama'     => 'required|string',
            'siswa_id'       => 'required|exists:siswa,id',
            'nilai_akhir'    => 'required|numeric|min:0|max:100',
            'narasi_capaian' => 'required|string|min:5|max:1000',
        ]);

        $tahunAktif = TahunAjaran::getAktif();

        $mapel = null;
        if (!empty($validated['mapel_id'])) {
            $mapel = MataPelajaran::find($validated['mapel_id']);
        }
        if (!$mapel) {
            $mapel = MataPelajaran::firstWhere('nama', 'like', "%{$validated['mapel_nama']}%")
                     ?? MataPelajaran::first();
        }

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
            ->with('success', 'Penilaian mata pelajaran ' . $validated['mapel_nama'] . ' untuk ' . ($siswa?->nama_lengkap ?? 'Siswa') . ' berhasil disimpan.');
    }
}
