<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Rapor;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArsipController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $sekolah = Sekolah::instance();
        $tahunList = TahunAjaran::orderBy('id', 'desc')->get();
        $kelasList = Kelas::all();

        $search = $request->query('search');
        $tahunAjaranId = $request->query('tahun_ajaran_id');
        $kekhususan = $request->query('kekhususan');
        $statusSiswa = $request->query('status_siswa');

        $query = Siswa::with([
            'penugasanKelas.kelas',
            'rapor.tahunAjaran',
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if ($kekhususan && in_array($kekhususan, ['Tunarungu', 'Tunagrahita', 'Autis'])) {
            $query->where('kekhususan', $kekhususan);
        }

        if ($statusSiswa) {
            if ($statusSiswa === 'Aktif') {
                $query->where('is_aktif', true);
            } else {
                $query->where('is_aktif', false);
            }
        }

        $siswaModels = $query->get();

        $dataArsip = [];
        foreach ($siswaModels as $s) {
            $penugasan = $s->penugasanAktif();
            $raporList = $s->rapor;

            if ($tahunAjaranId) {
                $raporList = $raporList->where('tahun_ajaran_id', $tahunAjaranId);
            }

            if ($raporList->isNotEmpty()) {
                foreach ($raporList as $r) {
                    $th = $r->tahunAjaran ?? $tahunAktif;
                    $dataArsip[] = [
                        'siswa_id'     => $s->id,
                        'rapor_id'     => $r->id,
                        'nama'         => $s->nama_lengkap,
                        'nisn'         => $s->nisn,
                        'kekhususan'   => $s->kekhususan,
                        'periode'      => ($th?->tahun ?? '2026/2027') . ' · ' . ($th?->semester ?? 'Ganjil'),
                        'kelas'        => ($penugasan?->kelas?->nama ?? $s->kekhususan),
                        'tingkat'      => ($penugasan?->tingkat ?? 'VII') . ' · ' . ($penugasan?->fase ?? 'D'),
                        'status_siswa' => $s->is_aktif ? 'Aktif' : 'Non-aktif',
                        'status_rapor' => ucfirst($r->status),
                    ];
                }
            } else {
                // Tampilkan siswa meskipun rapor belum diarsip
                $dataArsip[] = [
                    'siswa_id'     => $s->id,
                    'rapor_id'     => null,
                    'nama'         => $s->nama_lengkap,
                    'nisn'         => $s->nisn,
                    'kekhususan'   => $s->kekhususan,
                    'periode'      => ($tahunAktif?->tahun ?? '2026/2027') . ' · ' . ($tahunAktif?->semester ?? 'Ganjil'),
                    'kelas'        => ($penugasan?->kelas?->nama ?? $s->kekhususan),
                    'tingkat'      => ($penugasan?->tingkat ?? 'VII') . ' · ' . ($penugasan?->fase ?? 'D'),
                    'status_siswa' => $s->is_aktif ? 'Aktif' : 'Non-aktif',
                    'status_rapor' => 'Draft',
                ];
            }
        }

        return view('wali_kelas.arsip.index', compact(
            'tahunAktif',
            'sekolah',
            'dataArsip',
            'tahunList',
            'kelasList',
            'search',
            'tahunAjaranId',
            'kekhususan',
            'statusSiswa'
        ));
    }
}
