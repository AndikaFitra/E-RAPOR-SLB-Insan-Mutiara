<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\PenugasanWaliKelas;
use App\Models\Rapor;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArsipController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $tahunList = TahunAjaran::orderBy('id', 'desc')->get();
        $kelasList = Kelas::all();

        $search = $request->query('search');
        $tahunId = $request->query('tahun_ajaran_id');
        $kelasId = $request->query('kelas_id');

        $selectedTahunList = $tahunId ? $tahunList->where('id', $tahunId) : $tahunList;
        $selectedKelasList = $kelasId ? $kelasList->where('id', $kelasId) : $kelasList;

        $arsipList = [];

        foreach ($selectedTahunList as $th) {
            foreach ($selectedKelasList as $k) {
                // Cari wali kelas
                $penugasanWali = PenugasanWaliKelas::with('guru')
                    ->where('kelas_id', $k->id)
                    ->where('tahun_ajaran_id', $th->id)
                    ->first();

                // Hitung total siswa di kelas ini
                $totalSiswa = Siswa::where('kekhususan', $k->kekhususan)->count();

                // Hitung rapor terarsip
                $raporCount = Rapor::where('tahun_ajaran_id', $th->id)
                    ->whereHas('siswa', fn($q) => $q->where('kekhususan', $k->kekhususan))
                    ->count();

                $row = [
                    'tahun_id'    => $th->id,
                    'kelas_id'    => $k->id,
                    'tahun'       => $th->tahun,
                    'semester'    => $th->semester,
                    'kelas'       => $k->nama,
                    'kekhususan'  => $k->kekhususan,
                    'wali_kelas'  => $penugasanWali?->guru?->nama_lengkap ?? 'Rina Pratiwi, S.Pd.',
                    'total_siswa' => $totalSiswa,
                    'rapor_count' => $raporCount,
                    'is_aktif'    => $th->is_aktif,
                    'status'      => $th->is_aktif ? 'Semester Berjalan' : 'Arsip Permanen',
                ];

                if ($search) {
                    $matchSearch = (stripos($row['kelas'], $search) !== false) ||
                                   (stripos($row['wali_kelas'], $search) !== false) ||
                                   (stripos($row['tahun'], $search) !== false);
                    if (!$matchSearch) {
                        continue;
                    }
                }

                $arsipList[] = $row;
            }
        }

        return view('admin.arsip.index', compact(
            'tahunAktif',
            'arsipList',
            'tahunList',
            'kelasList',
            'search',
            'tahunId',
            'kelasId'
        ));
    }
}
