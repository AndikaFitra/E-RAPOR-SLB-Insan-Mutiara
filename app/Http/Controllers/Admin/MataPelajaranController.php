<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MataPelajaranController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $search = $request->query('search');
        $kelompok = $request->query('kelompok');

        $query = MataPelajaran::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        if ($kelompok && $kelompok !== 'Semua') {
            $query->where('kelompok', $kelompok);
        }

        $mapelModels = $query->orderBy('kelompok')->orderBy('id')->get();

        $mapelList = [];
        foreach ($mapelModels as $m) {
            $mapelList[] = [
                'id'       => $m->id,
                'kode'     => $m->kode,
                'nama'     => $m->nama,
                'kelompok' => $m->kelompok,
                'fase'     => 'Sesuai penugasan siswa',
                'status'   => $m->is_aktif ? 'Aktif' : 'Tidak aktif',
                'raw'      => $m,
            ];
        }

        return view('admin.mapel.index', compact('tahunAktif', 'mapelList', 'search', 'kelompok'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode'     => 'required|string|max:20|unique:mata_pelajaran,kode',
            'nama'     => 'required|string|max:100',
            'kelompok' => 'required|in:Umum,Seni dan Vokasi,Program Kekhususan,Keterampilan Pilihan,Muatan Lokal',
        ]);

        $mapel = MataPelajaran::create([
            'kode'     => strtoupper($validated['kode']),
            'nama'     => $validated['nama'],
            'kelompok' => $validated['kelompok'],
        ]);

        ActivityLog::log('Menambahkan mata pelajaran: ' . $mapel->nama, $mapel, 'Sukses');

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mata pelajaran ' . $mapel->nama . ' berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $mapel = MataPelajaran::findOrFail($id);

        $validated = $request->validate([
            'kode'     => 'required|string|max:20|unique:mata_pelajaran,kode,' . $mapel->id,
            'nama'     => 'required|string|max:100',
            'kelompok' => 'required|in:Umum,Seni dan Vokasi,Program Kekhususan,Keterampilan Pilihan,Muatan Lokal',
        ]);

        $mapel->update([
            'kode'     => strtoupper($validated['kode']),
            'nama'     => $validated['nama'],
            'kelompok' => $validated['kelompok'],
        ]);

        ActivityLog::log('Memperbarui mata pelajaran: ' . $mapel->nama, $mapel, 'Sukses');

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mata pelajaran ' . $mapel->nama . ' berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $mapel = MataPelajaran::findOrFail($id);
        $nama = $mapel->nama;
        if ($mapel->nilaiAsesmen()->exists() || $mapel->kelas()->exists()) {
            return back()->with('error', 'Mata pelajaran masih digunakan pada penilaian atau kurikulum kelas.');
        }
        $mapel->delete();

        ActivityLog::log('Mengarsipkan mata pelajaran: ' . $nama, $mapel, 'Diarsipkan');

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mata pelajaran ' . $nama . ' berhasil dihapus dari kurikulum.');
    }
}
