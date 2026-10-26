<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Guru;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class GuruController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $search = $request->query('search');
        $kekhususan = $request->query('kekhususan');
        $status = $request->query('status');

        $query = Guru::with(['user', 'penugasanWaliKelas.kelas']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($kekhususan && $kekhususan !== 'Semua') {
            $query->where('jabatan', 'like', "%{$kekhususan}%");
        }

        $guruModels = $query->latest()->get();

        $guruList = [];
        foreach ($guruModels as $g) {
            $kelasAktif = $g->kelasSaatIni();
            $statusUser = optional($g->user)->is_aktif ?? true;

            $guruList[] = [
                'id'         => $g->id,
                'nama'       => $g->nama_lengkap,
                'nip'        => $g->nip ?: '—',
                'kekhususan' => $g->jabatan ?: 'Tunarungu, Tunagrahita, Autis',
                'kontak'     => $g->no_whatsapp ?: '—',
                'kelas'      => $kelasAktif ? $kelasAktif->nama : 'Belum ditugaskan',
                'status'     => $statusUser ? 'Aktif' : 'Tidak aktif',
                'raw'        => $g,
            ];
        }

        return view('admin.guru.index', compact('tahunAktif', 'guruList', 'search', 'kekhususan', 'status'));
    }

    public function create(): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        return view('admin.guru.create', compact('tahunAktif'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap'  => 'required|string|max:100',
            'nip'           => 'nullable|string|max:30|unique:guru,nip',
            'jenis_kelamin' => 'required|in:L,P',
            'kekhususan'    => 'required|string',
            'no_whatsapp'   => 'required|string|max:20',
            'email'         => 'nullable|email|max:100|unique:users,email',
            'status'        => 'required|in:Aktif,Tidak aktif',
        ]);

        $guru = Guru::create([
            'nama_lengkap'  => $validated['nama_lengkap'],
            'nip'           => $validated['nip'] ?: null,
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'jabatan'       => $validated['kekhususan'],
            'no_whatsapp'   => $validated['no_whatsapp'],
        ]);

        if (!empty($validated['email'])) {
            User::create([
                'name'     => $guru->nama_lengkap,
                'email'    => $validated['email'],
                'password' => Hash::make('password'),
                'role'     => 'wali_kelas',
                'is_aktif' => ($validated['status'] === 'Aktif'),
                'guru_id'  => $guru->id,
            ]);
        }

        ActivityLog::log('Menambahkan data guru baru: ' . $guru->nama_lengkap, $guru, 'Sukses');

        return redirect()->route('admin.guru.index')
            ->with('success', 'Data guru ' . $guru->nama_lengkap . ' berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $guru = Guru::with('user')->findOrFail($id);

        return view('admin.guru.edit', compact('tahunAktif', 'guru'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $guru = Guru::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap'  => 'required|string|max:100',
            'nip'           => 'nullable|string|max:30|unique:guru,nip,' . $guru->id,
            'jenis_kelamin' => 'required|in:L,P',
            'kekhususan'    => 'required|string',
            'no_whatsapp'   => 'required|string|max:20',
            'status'        => 'required|in:Aktif,Tidak aktif',
        ]);

        $guru->update([
            'nama_lengkap'  => $validated['nama_lengkap'],
            'nip'           => $validated['nip'] ?: null,
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'jabatan'       => $validated['kekhususan'],
            'no_whatsapp'   => $validated['no_whatsapp'],
        ]);

        if ($guru->user) {
            $guru->user->update([
                'name'     => $guru->nama_lengkap,
                'is_aktif' => ($validated['status'] === 'Aktif'),
            ]);
        }

        ActivityLog::log('Memperbarui data guru: ' . $guru->nama_lengkap, $guru, 'Sukses');

        return redirect()->route('admin.guru.index')
            ->with('success', 'Data guru ' . $guru->nama_lengkap . ' berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $guru = Guru::findOrFail($id);
        $nama = $guru->nama_lengkap;
        if ($guru->user || $guru->penugasanWaliKelas()->exists() || $guru->penugasanPembimbing()->exists()
            || \Illuminate\Support\Facades\DB::table('penugasan_ekstrakurikuler')->where('guru_id', $id)->exists()) {
            return back()->with('error', 'Guru tidak dapat dihapus karena masih terkait akun atau penugasan.');
        }
        $guru->delete();

        ActivityLog::log('Menghapus data guru: ' . $nama, null, 'Hapus');

        return redirect()->route('admin.guru.index')
            ->with('success', 'Data guru ' . $nama . ' berhasil dihapus dari sistem.');
    }
}
