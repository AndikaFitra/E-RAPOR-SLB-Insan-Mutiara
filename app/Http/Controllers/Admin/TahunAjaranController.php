<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TahunAjaranController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $tahunModels = TahunAjaran::orderByDesc('tahun')->orderBy('semester')->get();

        $tahunAjaranList = [];
        foreach ($tahunModels as $t) {
            $tahunAjaranList[] = [
                'id'           => $t->id,
                'tahun'        => $t->tahun,
                'semester'     => $t->semester,
                'tgl_mulai'    => $t->semester === 'Ganjil' ? '13 Jul 2026' : '4 Jan 2027',
                'tgl_selesai'  => $t->semester === 'Ganjil' ? '19 Des 2026' : '19 Jun 2027',
                'status'       => $t->is_aktif ? 'Aktif' : 'Belum aktif',
                'data_terkait' => $t->is_aktif ? '48 siswa · 6 kelas' : 'Arsip periode',
                'raw'          => $t,
            ];
        }

        return view('admin.tahun_ajaran.index', compact('tahunAktif', 'tahunAjaranList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tahun'    => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        $exists = TahunAjaran::where('tahun', $validated['tahun'])
            ->where('semester', $validated['semester'])
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['tahun' => 'Tahun ajaran dan semester tersebut sudah terdaftar.']);
        }

        $th = TahunAjaran::create([
            'tahun'    => $validated['tahun'],
            'semester' => $validated['semester'],
            'is_aktif' => false,
        ]);

        ActivityLog::log('Menambahkan tahun ajaran baru: ' . $th->label, $th, 'Sukses');

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran ' . $th->label . ' berhasil ditambahkan.');
    }

    public function setAktif(Request $request, int $id): RedirectResponse
    {
        $th = TahunAjaran::findOrFail($id);
        $th->aktifkan();

        ActivityLog::log('Mengaktifkan tahun ajaran: ' . $th->label, $th, 'Sukses');

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran ' . $th->label . ' sekarang berstatus Aktif.');
    }
}
