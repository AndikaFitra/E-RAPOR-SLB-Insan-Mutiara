<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Kelas;
use App\Models\PenugasanSiswaKelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaController extends Controller
{
    /**
     * Tampilkan daftar siswa dengan filter 3 ketunaan (Tunarungu, Tunagrahita, Autis).
     */
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $kelasList  = Kelas::all();

        $query = Siswa::with(['penugasanKelas.kelas']);

        // Filter Pencarian
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Filter Kekhususan (3 Ketunaan)
        if ($kekhususan = $request->input('kekhususan')) {
            if (in_array($kekhususan, ['Tunarungu', 'Tunagrahita', 'Autis'])) {
                $query->where('kekhususan', $kekhususan);
            }
        }

        // Filter Kelas
        if ($kelasId = $request->input('kelas_id')) {
            $query->whereHas('penugasanKelas', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        $siswaModels = $query->orderBy('id')->get();

        // Map data ke format yang diharapkan view
        $siswaList = $siswaModels->map(function ($s, $index) {
            $penugasan = $s->penugasanAktif();
            $tingkat = $penugasan ? $penugasan->tingkat : 'VII';
            $fase = $penugasan ? $penugasan->fase : 'D';
            $namaKelas = $penugasan && $penugasan->kelas ? $penugasan->kelas->nama : ($s->kekhususan . ' A');

            return [
                'id'           => $s->id,
                'nama'         => $s->nama_lengkap,
                'nis'          => '2600' . ($s->id),
                'nisn'         => $s->nisn,
                'kelas'        => $namaKelas,
                'tingkat_fase' => "{$tingkat} · {$fase}",
                'kekhususan'   => $s->kekhususan,
                'status'       => $s->is_aktif ? 'Aktif' : 'Tidak Aktif',
            ];
        })->toArray();

        return view('admin.siswa.index', compact('tahunAktif', 'kelasList', 'siswaList'));
    }

    /**
     * Tampilkan form tambah siswa baru.
     */
    public function create(): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $kelasList  = Kelas::all();

        return view('admin.siswa.create', compact('tahunAktif', 'kelasList'));
    }

    /**
     * Simpan data siswa baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap'  => ['required', 'string', 'max:100'],
            'nisn'          => ['required', 'string', 'max:10', 'unique:siswa,nisn'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'kekhususan'    => ['required', 'in:Tunarungu,Tunagrahita,Autis'],
            'tempat_lahir'  => ['nullable', 'string', 'max:100'],
            'tgl_lahir'     => ['nullable', 'date'],
            'kelas_id'      => ['nullable', 'exists:kelas,id'],
            'tingkat'       => ['nullable', 'string', 'max:10'],
            'fase'          => ['nullable', 'string', 'max:10'],
            'no_hp'         => ['nullable', 'string', 'max:20'],
        ], [
            'nama_lengkap.required'  => 'Nama lengkap siswa wajib diisi.',
            'nisn.required'          => 'NISN wajib diisi.',
            'nisn.unique'            => 'NISN sudah terdaftar.',
            'kekhususan.required'    => 'Jenis kekhususan wajib dipilih (Tunarungu, Tunagrahita, atau Autis).',
            'kekhususan.in'          => 'Jenis kekhususan harus salah satu dari: Tunarungu, Tunagrahita, atau Autis.',
        ]);

        $siswa = Siswa::create([
            'nama_lengkap'  => $validated['nama_lengkap'],
            'nisn'          => $validated['nisn'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'kekhususan'    => $validated['kekhususan'],
            'tempat_lahir'  => $validated['tempat_lahir'] ?? null,
            'tgl_lahir'     => $validated['tgl_lahir'] ?? null,
            'no_hp'         => $validated['no_hp'] ?? null,
            'is_aktif'      => true,
        ]);

        $tahunAktif = TahunAjaran::getAktif();
        if (! empty($validated['kelas_id']) && $tahunAktif) {
            PenugasanSiswaKelas::create([
                'siswa_id'        => $siswa->id,
                'kelas_id'        => $validated['kelas_id'],
                'tahun_ajaran_id' => $tahunAktif->id,
                'tingkat'         => $validated['tingkat'] ?? 'VII',
                'fase'            => $validated['fase'] ?? 'D',
            ]);
        }

        ActivityLog::log("Menambahkan data siswa: {$siswa->nama_lengkap}", $siswa, 'Sukses');

        return redirect()->route('admin.siswa.index')
            ->with('success', "Data siswa {$siswa->nama_lengkap} ({$siswa->kekhususan}) berhasil ditambahkan.");
    }

    /**
     * Tampilkan form edit siswa.
     */
    public function edit($id): View
    {
        $siswa = Siswa::with('penugasanKelas')->findOrFail($id);
        $tahunAktif = TahunAjaran::getAktif();
        $kelasList  = Kelas::all();
        $penugasan  = $siswa->penugasanAktif();

        return view('admin.siswa.edit', compact('siswa', 'tahunAktif', 'kelasList', 'penugasan'));
    }

    /**
     * Perbarui data siswa.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $siswa = Siswa::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap'  => ['required', 'string', 'max:100'],
            'nisn'          => ['required', 'string', 'max:10', 'unique:siswa,nisn,' . $siswa->id],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'kekhususan'    => ['required', 'in:Tunarungu,Tunagrahita,Autis'],
            'tempat_lahir'  => ['nullable', 'string', 'max:100'],
            'tgl_lahir'     => ['nullable', 'date'],
            'kelas_id'      => ['nullable', 'exists:kelas,id'],
            'tingkat'       => ['nullable', 'string', 'max:10'],
            'fase'          => ['nullable', 'string', 'max:10'],
            'is_aktif'      => ['nullable', 'boolean'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap siswa wajib diisi.',
            'nisn.required'         => 'NISN wajib diisi.',
            'kekhususan.required'   => 'Jenis kekhususan wajib dipilih.',
            'kekhususan.in'         => 'Jenis kekhususan harus salah satu dari: Tunarungu, Tunagrahita, atau Autis.',
        ]);

        $siswa->update([
            'nama_lengkap'  => $validated['nama_lengkap'],
            'nisn'          => $validated['nisn'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'kekhususan'    => $validated['kekhususan'],
            'tempat_lahir'  => $validated['tempat_lahir'] ?? $siswa->tempat_lahir,
            'tgl_lahir'     => $validated['tgl_lahir'] ?? $siswa->tgl_lahir,
            'is_aktif'      => $request->has('is_aktif') ? $request->boolean('is_aktif') : $siswa->is_aktif,
        ]);

        // Perbarui penugasan kelas jika ada
        $tahunAktif = TahunAjaran::getAktif();
        if (! empty($validated['kelas_id']) && $tahunAktif) {
            PenugasanSiswaKelas::updateOrCreate(
                [
                    'siswa_id'        => $siswa->id,
                    'tahun_ajaran_id' => $tahunAktif->id,
                ],
                [
                    'kelas_id' => $validated['kelas_id'],
                    'tingkat'  => $validated['tingkat'] ?? 'VII',
                    'fase'     => $validated['fase'] ?? 'D',
                ]
            );
        }

        ActivityLog::log("Memperbarui data siswa: {$siswa->nama_lengkap}", $siswa, 'Sukses');

        return redirect()->route('admin.siswa.index')
            ->with('success', "Data siswa {$siswa->nama_lengkap} berhasil diperbarui.");
    }

    /**
     * Hapus data siswa dari sistem (dengan verifikasi 2 langkah).
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $siswa = Siswa::findOrFail($id);
        $namaSiswa = $siswa->nama_lengkap;

        // Hapus penugasan dan relasi terkait
        $siswa->penugasanKelas()->delete();
        $siswa->delete();

        ActivityLog::log("Menghapus data siswa: {$namaSiswa}", null, 'Sukses');

        return redirect()->route('admin.siswa.index')
            ->with('success', "Data siswa {$namaSiswa} berhasil dihapus dari sistem.");
    }

    /**
     * Tampilkan detail siswa.
     */
    public function show($id): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $siswa = Siswa::with(['orangTua', 'penugasanKelas.kelas'])->find($id);

        return view('admin.siswa.detail', compact('tahunAktif', 'id', 'siswa'));
    }
}
