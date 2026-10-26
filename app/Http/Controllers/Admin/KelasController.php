<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $search = $request->query('search');
        $kekhususan = $request->query('kekhususan');

        $query = Kelas::with(['penugasanWaliKelas.guru', 'penugasanSiswaKelas.siswa']);

        if ($search) {
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
        }

        if ($kekhususan && $kekhususan !== 'Semua') {
            $query->where('kekhususan', $kekhususan);
        }

        $kelasModels = $query->get();

        $kelasList = [];
        foreach ($kelasModels as $k) {
            $wali = $k->waliKelasAktif();
            $siswaCount = $k->penugasanSiswaKelas()->where('tahun_ajaran_id', optional($tahunAktif)->id)->count();


            // Kumpulkan tingkat siswa
            $tingkatList = $k->penugasanSiswaKelas()->where('tahun_ajaran_id', optional($tahunAktif)->id)->pluck('tingkat')->unique()->filter()->implode(', ');

            $kelasList[] = [
                'id'            => $k->id,
                'nama'          => $k->nama,
                'kode'          => $k->kode,
                'kekhususan'    => $k->kekhususan,
                'tingkat_siswa' => $tingkatList ?: 'Belum ditetapkan',
                'wali_kelas'    => $wali ? $wali->nama_lengkap : 'Belum ditugaskan',
                'siswa_count'   => $siswaCount,
                'status'        => 'Aktif',
                'raw'           => $k,
            ];
        }

        return view('admin.kelas.index', compact('tahunAktif', 'kelasList', 'search', 'kekhususan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'       => 'required|string|max:100',
            'kekhususan' => 'required|in:Tunarungu,Tunagrahita,Autis',
            'kode'       => 'nullable|string|max:20|unique:kelas,kode',
        ]);

        $kode = $validated['kode'] ?: strtoupper(substr($validated['kekhususan'], 0, 2) . '-' . rand(10, 99));

        $kelas = Kelas::create([
            'nama'       => $validated['nama'],
            'kekhususan' => $validated['kekhususan'],
            'kode'       => $kode,
        ]);

        ActivityLog::log('Menambahkan kelas baru: ' . $kelas->nama, $kelas, 'Sukses');

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas ' . $kelas->nama . ' berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $kelas = Kelas::findOrFail($id);

        $validated = $request->validate([
            'nama'       => 'required|string|max:100',
            'kekhususan' => 'required|in:Tunarungu,Tunagrahita,Autis',
            'kode'       => 'nullable|string|max:20|unique:kelas,kode,' . $kelas->id,
        ]);

        $kelas->update([
            'nama'       => $validated['nama'],
            'kekhususan' => $validated['kekhususan'],
            'kode'       => $validated['kode'] ?: $kelas->kode,
        ]);

        ActivityLog::log('Memperbarui data kelas: ' . $kelas->nama, $kelas, 'Sukses');

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas ' . $kelas->nama . ' berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $kelas = Kelas::findOrFail($id);
        $nama = $kelas->nama;
        if ($kelas->penugasanSiswaKelas()->exists() || $kelas->penugasanWaliKelas()->exists()) {
            return back()->with('error', 'Kelas masih memiliki penugasan dan tidak dapat dihapus.');
        }
        $kelas->delete();

        ActivityLog::log('Menghapus data kelas: ' . $nama, null, 'Hapus');

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas ' . $nama . ' berhasil dihapus dari sistem.');
    }
}
