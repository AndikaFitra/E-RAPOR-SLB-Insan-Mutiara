<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Ekstrakurikuler;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EkstrakurikulerController extends Controller
{
    public function index(): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $ekskulModels = Ekstrakurikuler::all();

        $ekskulList = [];
        foreach ($ekskulModels as $idx => $e) {
            $assignment = \Illuminate\Support\Facades\DB::table('penugasan_ekstrakurikuler')->where('ekstrakurikuler_id',$e->id)->where('tahun_ajaran_id',$tahunAktif?->id)->first();
            $guru = $assignment ? \App\Models\Guru::find($assignment->guru_id) : null;
            $ekskulList[] = [
                'id'           => $e->id,
                'nama'         => $e->nama,
                'pembimbing'   => $guru?->nama_lengkap ?? 'Belum ditugaskan',
                'jadwal'       => $assignment?->jadwal ?? 'Belum ditetapkan',
                'jumlah_siswa' => $e->siswa()->where('tahun_ajaran_id',$tahunAktif?->id)->count(),
                'status'       => 'Aktif',
            ];
        }

        return view('admin.ekskul.index', compact('tahunAktif', 'ekskulList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:ekstrakurikuler,nama',
        ]);

        $ekskul = Ekstrakurikuler::create(['nama' => $validated['nama']]);
        ActivityLog::log('Menambahkan kegiatan ekstrakurikuler: ' . $ekskul->nama, $ekskul, 'Sukses');

        return redirect()->route('admin.ekskul.index')
            ->with('success', 'Ekstrakurikuler ' . $ekskul->nama . ' berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $ekskul = Ekstrakurikuler::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:ekstrakurikuler,nama,' . $ekskul->id,
        ]);

        $ekskul->update(['nama' => $validated['nama']]);
        ActivityLog::log('Memperbarui kegiatan ekstrakurikuler: ' . $ekskul->nama, $ekskul, 'Sukses');

        return redirect()->route('admin.ekskul.index')
            ->with('success', 'Ekstrakurikuler ' . $ekskul->nama . ' berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $ekskul = Ekstrakurikuler::findOrFail($id);
        $nama = $ekskul->nama;
        if ($ekskul->siswa()->exists() || \Illuminate\Support\Facades\DB::table('penugasan_ekstrakurikuler')->where('ekstrakurikuler_id',$id)->exists()) {
            return back()->with('error','Ekstrakurikuler masih digunakan oleh peserta atau pembimbing.');
        }
        $ekskul->delete();

        ActivityLog::log('Mengarsipkan ekstrakurikuler: ' . $nama, $ekskul, 'Diarsipkan');

        return redirect()->route('admin.ekskul.index')
            ->with('success', 'Ekstrakurikuler ' . $nama . ' berhasil dihapus.');
    }
}
