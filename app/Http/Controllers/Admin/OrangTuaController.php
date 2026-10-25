<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\OrangTua;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrangTuaController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $search = $request->query('search');

        $query = OrangTua::with('siswa');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_whatsapp', 'like', "%{$search}%")
                  ->orWhereHas('siswa', function ($sq) use ($search) {
                      $sq->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        $orangTuaModels = $query->latest()->get();
        $siswaList = Siswa::orderBy('nama_lengkap')->get();

        $orangTuaList = [];
        foreach ($orangTuaModels as $ot) {
            $orangTuaList[] = [
                'id'         => $ot->id,
                'siswa_id'   => $ot->siswa_id,
                'nama'       => $ot->nama,
                'siswa'      => $ot->siswa ? $ot->siswa->nama_lengkap : '—',
                'hubungan'   => $ot->hubungan ?: 'Orang Tua',
                'kontak'     => $ot->no_whatsapp ?: '—',
                'alamat'     => 'Kota Pekanbaru, Riau',
                'status'     => !empty($ot->no_whatsapp) ? 'Terverifikasi' : 'Perlu Konfirmasi',
                'raw'        => $ot,
            ];
        }

        return view('admin.orang_tua.index', compact('tahunAktif', 'orangTuaList', 'siswaList', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_id'    => 'required|exists:siswa,id',
            'nama'        => 'required|string|max:100',
            'hubungan'    => 'required|string|max:50',
            'no_whatsapp' => 'required|string|max:20',
        ]);

        $ot = OrangTua::create([
            'siswa_id'          => $validated['siswa_id'],
            'nama'              => $validated['nama'],
            'hubungan'          => $validated['hubungan'],
            'no_whatsapp'       => $validated['no_whatsapp'],
            'is_penerima_utama' => true,
        ]);

        ActivityLog::log('Menambahkan data kontak orang tua: ' . $ot->nama, $ot, 'Sukses');

        return redirect()->route('admin.orang-tua.index')
            ->with('success', 'Data orang tua ' . $ot->nama . ' berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $ot = OrangTua::findOrFail($id);

        $validated = $request->validate([
            'siswa_id'    => 'required|exists:siswa,id',
            'nama'        => 'required|string|max:100',
            'hubungan'    => 'required|string|max:50',
            'no_whatsapp' => 'required|string|max:20',
        ]);

        $ot->update([
            'siswa_id'    => $validated['siswa_id'],
            'nama'        => $validated['nama'],
            'hubungan'    => $validated['hubungan'],
            'no_whatsapp' => $validated['no_whatsapp'],
        ]);

        ActivityLog::log('Memperbarui data kontak orang tua: ' . $ot->nama, $ot, 'Sukses');

        return redirect()->route('admin.orang-tua.index')
            ->with('success', 'Data orang tua ' . $ot->nama . ' berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $ot = OrangTua::findOrFail($id);
        $nama = $ot->nama;
        $ot->delete();

        ActivityLog::log('Menghapus data orang tua: ' . $nama, null, 'Hapus');

        return redirect()->route('admin.orang-tua.index')
            ->with('success', 'Data orang tua ' . $nama . ' berhasil dihapus.');
    }
}
