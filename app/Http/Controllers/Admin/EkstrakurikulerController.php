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
        $defaults = [
            ['pembimbing' => 'Ahmad Fauzi, S.Pd', 'jadwal' => 'Jumat, 14.00 - 16.00', 'siswa' => 24],
            ['pembimbing' => 'Siti Nurhaliza, S.Pd', 'jadwal' => 'Sabtu, 09.00 - 11.00', 'siswa' => 16],
            ['pembimbing' => 'Hendra Saputra, S.Pd', 'jadwal' => 'Selasa, 15.00 - 17.00', 'siswa' => 12],
        ];

        foreach ($ekskulModels as $idx => $e) {
            $def = $defaults[$idx % count($defaults)];
            $ekskulList[] = [
                'id'           => $e->id,
                'nama'         => $e->nama,
                'pembimbing'   => $def['pembimbing'],
                'jadwal'       => $def['jadwal'],
                'jumlah_siswa' => $def['siswa'],
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
        $ekskul->delete();

        ActivityLog::log('Menghapus ekstrakurikuler: ' . $nama, null, 'Hapus');

        return redirect()->route('admin.ekskul.index')
            ->with('success', 'Ekstrakurikuler ' . $nama . ' berhasil dihapus.');
    }
}
