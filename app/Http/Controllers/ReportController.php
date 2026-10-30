<?php

namespace App\Http\Controllers;

use App\Models\{ActivityLog, Rapor, Siswa, TahunAjaran};
use App\Services\RaporService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function __construct(private RaporService $reports) {}

    public function arsip(Request $request)
    {
        $periode = $this->reports->periode($request->integer('tahun_ajaran_id') ?: null);
        $tahunList = TahunAjaran::orderByDesc('tahun')->orderBy('semester')->get();
        $rows = Rapor::with(['siswa', 'tahunAjaran'])->where('tahun_ajaran_id', $periode->id)
            ->whereIn('siswa_id', $this->reports->siswa($request->user(), $periode->id)->select('siswa.id'))
            ->when($request->search, fn ($q) => $q->whereHas('siswa', fn ($q) => $q->search($request->search)))
            ->get()->map(fn ($r) => ['rapor' => $r, 'data' => $this->reports->data($r->siswa, $periode, $r)]);
        return view('academic.arsip', compact('periode', 'tahunList', 'rows'));
    }

    public function show(Request $request, int $id)
    {
        $rapor = Rapor::with(['siswa', 'tahunAjaran'])->findOrFail($id);
        $siswa = $rapor->siswa;
        abort_unless($siswa, 404);
        $periode = $rapor->tahunAjaran;
        $this->reports->authorize($siswa, $periode->id);
        $data = $this->reports->data($siswa, $periode, $rapor);
        $checklist = $this->reports->checklist($data);
        $history = ActivityLog::where('model_type', 'Rapor')->where('model_id', $rapor->id)->latest()->get();
        return view('academic.review', compact('periode', 'siswa', 'rapor', 'data', 'checklist', 'history'));
    }

    public function cetak(Request $request, int $id)
    {
        $rapor = Rapor::with(['siswa', 'tahunAjaran'])->findOrFail($id);
        abort_unless($rapor->siswa, 404);
        $this->reports->authorize($rapor->siswa, $rapor->tahun_ajaran_id);
        $data = $this->reports->data($rapor->siswa, $rapor->tahunAjaran, $rapor);
        return $request->boolean('pdf') ? $this->download($data, $rapor) : view('reports.document', compact('data'));
    }

    public function download(array $data, ?Rapor $rapor, bool $inline = false)
    {
        $bytes = $rapor?->isDisetujui() && $rapor->pdf_path && Storage::disk('local')->exists($rapor->pdf_path)
            ? Storage::disk('local')->get($rapor->pdf_path) : $this->reports->pdf($data);
        $name = Str::slug($data['siswa']['nama_lengkap'] ?? 'siswa', '-');
        $nisn = preg_replace('/[^0-9]/', '', (string) ($data['siswa']['nisn'] ?? ''));
        $semester = Str::slug($data['semester'] ?? 'semester', '-');
        $tahun = str_replace('/', '-', (string) ($data['tahun'] ?? 'tahun-ajaran'));
        $filename = "Rapor_{$name}_{$nisn}_{$semester}_{$tahun}.pdf";
        return response($bytes, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => ($inline ? 'inline' : 'attachment').'; filename="'.$filename.'"',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function shared(Request $request, string $token)
    {
        $rapor = Rapor::where('share_token', $token)->where('status', Rapor::STATUS_DISETUJUI)->firstOrFail();
        abort_unless($rapor->snapshot, 404);
        return $this->download($rapor->snapshot, $rapor, true)->header('X-Robots-Tag', 'noindex, nofollow')->header('Referrer-Policy', 'no-referrer');
    }
}
