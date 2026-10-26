<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\{ActivityLog, Kelas, Rapor, TahunAjaran};
use App\Services\RaporService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    public function __construct(private RaporService $reports) {}

    public function index(Request $request)
    {
        $periode = $this->reports->periode($request->integer('tahun_ajaran_id') ?: null);
        $tahunList = TahunAjaran::orderByDesc('tahun')->get();
        $kelasList = Kelas::all();
        $query = Rapor::with(['siswa', 'waliKelas'])->where('tahun_ajaran_id', $periode->id)
            ->whereIn('status', ['diajukan', 'dikembalikan', 'disetujui'])
            ->whereHas('siswa')
            ->when($request->search, fn ($q) => $q->whereHas('siswa', fn ($s) => $s->search($request->search)))
            ->when($request->kelas_id, fn ($q) => $q->whereHas('siswa.penugasanKelas', fn ($s) => $s->where('tahun_ajaran_id', $periode->id)->where('kelas_id', $request->kelas_id)))
            ->when($request->kekhususan, fn ($q) => $q->whereHas('siswa', fn ($s) => $s->where('kekhususan', $request->kekhususan)));
        $counts = (clone $query)->get()->countBy('status');
        $rows = $query->when($request->status, fn ($q) => $q->where('status', $request->status))->latest('tgl_pengajuan')->get()
            ->map(fn ($r) => ['rapor' => $r, 'data' => $data = $this->reports->data($r->siswa, $periode, $r), 'checklist' => $this->reports->checklist($data)]);
        return view('academic.persetujuan', compact('periode', 'tahunList', 'kelasList', 'counts', 'rows'));
    }

    public function approve(Request $request, int $id)
    {
        $this->reports->decide(Rapor::findOrFail($id), true);
        return back()->with('success', 'Rapor disetujui. PDF final dan tautan penerima sudah dibuat.');
    }

    public function reject(Request $request, int $id)
    {
        $input = $request->validate(['catatan_revisi' => 'required|string|min:5|max:1000']);
        $this->reports->decide(Rapor::findOrFail($id), false, $input['catatan_revisi']);
        return back()->with('success', 'Rapor dikembalikan. Wali kelas dapat memperbaiki penilaian.');
    }

    public function returnLegacy(Request $request, int $id)
    {
        $input = $request->validate(['catatan_revisi' => 'required|string|min:5|max:1000']);
        $this->reports->decide(Rapor::findOrFail($id), false, $input['catatan_revisi'], true);
        return back()->with('success', 'Rapor lama dikembalikan untuk dilengkapi dan diajukan ulang.');
    }

    /** Explicit migration path for approved reports created before snapshots existed. */
    public function finalize(Request $request, int $id)
    {
        DB::transaction(function () use ($id) {
            $rapor = Rapor::findOrFail($id);
            \App\Models\Siswa::whereKey($rapor->siswa_id)->lockForUpdate()->firstOrFail();
            $rapor->refresh();
            abort_unless($rapor->isDisetujui() && !$rapor->snapshot, 422);
            $data = $this->reports->data($rapor->siswa, $rapor->tahunAjaran, $rapor);
            $this->reports->assertComplete($data);
            $path = 'rapor/'.$rapor->id.'-'.Str::uuid().'.pdf';
            abort_unless(Storage::disk('local')->put($path, $this->reports->pdf($data)), 500);
            $rapor->update(['snapshot' => $data, 'pdf_path' => $path, 'share_token' => Str::random(64)]);
            ActivityLog::log('Memfinalisasi dokumen rapor lama setelah peninjauan', $rapor, 'Disetujui');
        });
        return back()->with('success', 'Dokumen final disimpan berdasarkan data yang telah ditinjau.');
    }
}
