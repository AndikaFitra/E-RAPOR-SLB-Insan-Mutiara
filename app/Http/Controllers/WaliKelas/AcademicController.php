<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\{ActivityLog, DistribusiRapor, EkstrakurikulerSiswa, EvaluasiNonakademik, NilaiAsesmen, PrestasiSiswa, Rapor, Siswa, TahunAjaran};
use App\Services\{RaporService, WhatsAppService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AcademicController extends Controller
{
    public function __construct(private RaporService $reports) {}

    public function asesmen(Request $request)
    {
        $periode = $this->reports->periode();
        $kelasList = $this->reports->kelas($request->user(), $periode->id)->get();
        $kelas = $request->filled('kelas_id') ? $kelasList->firstWhere('id', $request->integer('kelas_id')) : $kelasList->first();
        abort_if($request->filled('kelas_id') && !$kelas, 403);
        $subjects = $kelas ? $this->reports->mapel($kelas, $periode->id) : collect();
        $subject = $request->filled('mapel_id') ? $subjects->firstWhere('id', $request->integer('mapel_id')) : null;
        abort_if($request->filled('mapel_id') && !$subject, 422, 'Mapel tidak terdaftar pada kelas.');
        $students = $kelas ? $this->reports->siswa($request->user(), $periode->id)->whereHas('penugasanKelas', fn ($q) => $q->where('tahun_ajaran_id', $periode->id)->where('kelas_id', $kelas->id))->orderBy('nama_lengkap')->get() : collect();
        $rows = $students->map(fn ($s) => [
            'siswa' => $s, 'assignment' => $this->reports->penugasan($s, $periode->id),
            'rapor' => $s->rapor()->where('tahun_ajaran_id', $periode->id)->first(),
            'nilai' => $s->nilaiAsesmen()->where('tahun_ajaran_id', $periode->id)->get()->keyBy('mata_pelajaran_id'),
        ]);
        return view('academic.asesmen', compact('periode', 'kelasList', 'kelas', 'subjects', 'subject', 'rows'));
    }

    public function saveScores(Request $request)
    {
        $input = $request->validate([
            'mapel_id' => 'required|integer|exists:mata_pelajaran,id',
            'kelas_id' => 'required|integer|exists:kelas,id',
            'nilai' => 'required|array|min:1',
            'nilai.*.siswa_id' => 'required|integer|distinct|exists:siswa,id',
            'nilai.*.nilai_akhir' => 'nullable|integer|between:0,100',
            'nilai.*.narasi_capaian' => 'nullable|string|max:5000',
        ]);
        $periode = $this->reports->periode();
        $kelas = $this->reports->kelas($request->user(), $periode->id)->findOrFail($input['kelas_id']);
        abort_unless($this->reports->mapel($kelas, $periode->id)->contains('id', $input['mapel_id']), 422, 'Mapel bukan bagian kurikulum kelas.');
        DB::transaction(function () use ($input, $periode, $kelas) {
            foreach (collect($input['nilai'])->sortBy('siswa_id') as $row) {
                $siswa = Siswa::findOrFail($row['siswa_id']);
                abort_unless($this->reports->penugasan($siswa, $periode->id)?->kelas_id === $kelas->id, 403);
                $this->reports->editable($siswa, $periode);
                NilaiAsesmen::updateOrCreate([
                    'siswa_id' => $siswa->id, 'tahun_ajaran_id' => $periode->id, 'mata_pelajaran_id' => $input['mapel_id'],
                ], ['nilai_akhir' => $row['nilai_akhir'] ?? null, 'narasi_capaian' => $row['narasi_capaian'] ?? null]);
            }
            ActivityLog::log('Menyimpan draft nilai kelas '.$kelas->nama, $kelas, 'Sukses');
        });
        return back()->with('success', 'Nilai kelas tersimpan. Isian yang kosong tetap sebagai draft.');
    }

    public function nonakademik(Request $request)
    {
        $periode = $this->reports->periode();
        $students = $this->reports->siswa($request->user(), $periode->id)->when($request->search, fn ($q) => $q->search($request->search))->orderBy('nama_lengkap')->get();
        $selected = $request->filled('siswa_id') ? $students->firstWhere('id', $request->integer('siswa_id')) : null;
        abort_if($request->filled('siswa_id') && !$selected, 403);
        $data = $selected ? $this->reports->data($selected, $periode) : null;
        $rapor = $selected?->rapor()->where('tahun_ajaran_id', $periode->id)->first();
        return view('academic.nonakademik', compact('periode', 'students', 'selected', 'data', 'rapor'));
    }

    public function saveEvaluation(Request $request)
    {
        $input = $request->validate([
            'siswa_id' => 'required|integer|exists:siswa,id',
            'sakit' => 'required|integer|between:0,366', 'izin' => 'required|integer|between:0,366', 'alpa' => 'required|integer|between:0,366',
            'perkembangan' => 'nullable|string|max:5000', 'catatan_wali' => 'nullable|string|max:5000',
            'tgl_rapor' => 'nullable|date', 'tidak_ada_prestasi' => 'nullable|boolean',
            'keputusan' => 'nullable|in:Naik ke kelas berikutnya,Tinggal kelas,Lulus,Belum lulus',
            'prestasi' => 'nullable|array', 'prestasi.*.kegiatan' => 'nullable|string|max:200', 'prestasi.*.keterangan' => 'nullable|string|max:200',
            'ekskul' => 'nullable|array', 'ekskul.*' => 'nullable|string|max:200',
        ]);
        if ($input['sakit'] + $input['izin'] + $input['alpa'] > 366) {
            throw ValidationException::withMessages(['sakit' => 'Jumlah ketidakhadiran tidak boleh melebihi 366 hari.']);
        }
        $periode = $this->reports->periode();
        $siswa = Siswa::findOrFail($input['siswa_id']);
        DB::transaction(function () use ($request, $input, $periode, $siswa) {
            $rapor = $this->reports->editable($siswa, $periode);
            $prestasi = collect($input['prestasi'] ?? [])->filter(fn ($p) => filled(trim((string) ($p['kegiatan'] ?? ''))));
            if ($request->boolean('tidak_ada_prestasi') && $prestasi->isNotEmpty()) {
                throw ValidationException::withMessages(['prestasi' => 'Hapus daftar prestasi atau nonaktifkan pilihan tidak ada prestasi.']);
            }
            EvaluasiNonakademik::updateOrCreate(['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $periode->id], [
                'sakit' => $input['sakit'], 'izin' => $input['izin'], 'alpa' => $input['alpa'],
                'perkembangan' => $input['perkembangan'] ?? null, 'tidak_ada_prestasi' => $request->boolean('tidak_ada_prestasi'),
            ]);
            $rapor->update(['catatan_wali_kelas' => $input['catatan_wali'] ?? null, 'tgl_rapor' => $input['tgl_rapor'] ?? null, 'keputusan' => $input['keputusan'] ?? null]);
            PrestasiSiswa::where('siswa_id', $siswa->id)->where('tahun_ajaran_id', $periode->id)->delete();
            foreach ($prestasi as $p) {
                PrestasiSiswa::create(['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $periode->id] + $p);
            }
            foreach ($input['ekskul'] ?? [] as $id => $description) {
                $entry = EkstrakurikulerSiswa::where('siswa_id', $siswa->id)->where('tahun_ajaran_id', $periode->id)->where('ekstrakurikuler_id', $id)->first();
                abort_unless($entry, 422, 'Kegiatan belum ditugaskan kepada siswa.');
                $entry->update(['keterangan' => $description]);
            }
            ActivityLog::log('Menyimpan evaluasi '.$siswa->nama_lengkap, $rapor, 'Sukses');
        });
        return back()->with('success', 'Evaluasi tersimpan.');
    }

    public function rapor(Request $request)
    {
        $periode = $this->reports->periode();
        $students = $this->reports->siswa($request->user(), $periode->id)->when($request->search, fn ($q) => $q->search($request->search))->orderBy('nama_lengkap')->get();
        $rows = $students->map(function ($s) use ($periode) {
            $rapor = $s->rapor()->where('tahun_ajaran_id', $periode->id)->first();
            $data = $this->reports->data($s, $periode, $rapor);
            return ['siswa' => $s, 'rapor' => $rapor, 'data' => $data, 'checklist' => $this->reports->checklist($data)];
        });
        $counts = $rows->countBy(fn ($r) => $r['rapor']?->status ?? 'draft');
        if ($request->filled('status')) {
            $rows = $rows->filter(fn ($r) => ($r['rapor']?->status ?? 'draft') === $request->status);
        }
        return view('academic.rapor', compact('periode', 'rows', 'counts'));
    }

    public function show(Request $request, int $id)
    {
        $periode = $this->reports->periode($request->integer('tahun_ajaran_id') ?: null);
        $siswa = Siswa::findOrFail($id);
        $this->reports->authorize($siswa, $periode->id);
        $rapor = $siswa->rapor()->where('tahun_ajaran_id', $periode->id)->first();
        $data = $this->reports->data($siswa, $periode, $rapor);
        $checklist = $this->reports->checklist($data);
        $history = $rapor ? ActivityLog::where('model_type', 'Rapor')->where('model_id', $rapor->id)->latest()->get() : collect();
        return view('academic.review', compact('periode', 'siswa', 'rapor', 'data', 'checklist', 'history'));
    }

    public function cetak(Request $request, int $id)
    {
        $periode = $this->reports->periode($request->integer('tahun_ajaran_id') ?: null);
        $siswa = Siswa::findOrFail($id);
        $this->reports->authorize($siswa, $periode->id);
        $rapor = $siswa->rapor()->where('tahun_ajaran_id', $periode->id)->first();
        $data = $this->reports->data($siswa, $periode, $rapor);
        if ($request->boolean('pdf')) {
            return app(\App\Http\Controllers\ReportController::class)->download($data, $rapor);
        }
        return view('reports.document', compact('data'));
    }

    public function submit(Request $request, int $id)
    {
        $this->reports->submit(Siswa::findOrFail($id), $this->reports->periode());
        return back()->with('success', 'Rapor diajukan dan penilaian dikunci.');
    }

    public function distribusi(Request $request)
    {
        $periode = $this->reports->periode();
        $rapors = Rapor::with(['siswa.orangTua', 'distribusi'])->where('tahun_ajaran_id', $periode->id)
            ->where('status', Rapor::STATUS_DISETUJUI)
            ->whereIn('siswa_id', $this->reports->siswa($request->user(), $periode->id)->select('siswa.id'))->get();
        return view('academic.distribusi', compact('periode', 'rapors'));
    }

    public function markSent(Request $request, int $id)
    {
        $input = $request->validate(['status_kirim' => 'required|in:terkirim,gagal', 'catatan' => 'nullable|string|max:1000']);
        $rapor = Rapor::with('siswa.orangTua')->findOrFail($id);
        $this->reports->authorize($rapor->siswa, $rapor->tahun_ajaran_id);
        abort_unless($rapor->isDisetujui() && $rapor->share_token && $rapor->snapshot, 422, 'Dokumen final belum siap.');
        $ortu = $rapor->siswa->orangTua->firstWhere('is_penerima_utama', true) ?? $rapor->siswa->orangTua->first();
        abort_unless($ortu, 422, 'Penerima belum ditetapkan.');
        if ($input['status_kirim'] === 'terkirim' && !WhatsAppService::validPhone($ortu->no_whatsapp)) {
            throw ValidationException::withMessages(['nomor' => 'Nomor WhatsApp tidak valid. Minta admin memperbaikinya.']);
        }
        DistribusiRapor::create([
            'rapor_id' => $rapor->id, 'orang_tua_id' => $ortu->id, 'tgl_kirim' => now(),
            'status_kirim' => $input['status_kirim'], 'catatan' => 'Dicatat manual oleh '.$request->user()->name.'. '.($input['catatan'] ?? ''),
        ]);
        ActivityLog::log('Distribusi rapor ditandai '.$input['status_kirim'], $rapor, $input['status_kirim']);
        return back()->with('success', 'Riwayat distribusi disimpan.');
    }
}
