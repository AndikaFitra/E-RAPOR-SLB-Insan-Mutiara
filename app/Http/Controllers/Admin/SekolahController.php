<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Sekolah;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SekolahController extends Controller
{
    public function index(): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $sekolah = Sekolah::instance();

        return view('admin.sekolah.index', compact('tahunAktif', 'sekolah'));
    }

    public function update(Request $request): RedirectResponse
    {
        $sekolah = Sekolah::instance();

        $validated = $request->validate([
            'nama'           => 'required|string|max:150',
            'npsn'           => 'required|string|max:20',
            'kepala_sekolah' => 'required|string|max:100',
            'nip_kepala'     => 'nullable|string|max:30',
            'alamat'         => 'required|string|max:255',
            'email'          => 'nullable|email|max:100',
            'telepon'        => 'nullable|string|max:30',
        ]);

        $sekolah->update($validated);

        ActivityLog::log('Memperbarui profil data lembaga dan kepala sekolah', $sekolah, 'Sukses');

        return redirect()->route('admin.sekolah.index')
            ->with('success', 'Data profil sekolah berhasil diperbarui.');
    }
}
