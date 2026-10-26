<?php

namespace App\Services;

use App\Models\{Kelas, Rapor, Siswa, TahunAjaran};
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class LegerService
{
    public function __construct(private RaporService $reports) {}

    public function result(Request $request): array
    {
        $input = $request->validate([
            'tahun_ajaran_id' => 'nullable|integer|exists:tahun_ajaran,id',
            'kelas_id' => 'nullable|integer|exists:kelas,id',
            'mapel_id' => 'nullable|integer|exists:mata_pelajaran,id',
            'minimum' => 'nullable|numeric|between:0,100',
            'maksimum' => ['nullable', 'numeric', 'between:0,100', ...($request->filled('minimum') ? ['gte:minimum'] : [])],
            'ketuntasan' => 'nullable|in:Tuntas,Belum Tuntas,Belum lengkap,Belum ditetapkan',
            'search' => 'nullable|string|max:100',
        ]);
        $periode = $this->reports->periode($request->integer('tahun_ajaran_id') ?: null);
        $students = Siswa::whereHas('penugasanKelas', fn ($q) => $q->where('tahun_ajaran_id', $periode->id)
            ->when($request->kelas_id, fn ($q) => $q->where('kelas_id', $request->kelas_id)))
            ->when($request->search, fn ($q) => $q->search($request->search))->orderBy('nama_lengkap')->get();
        $rows = $students->map(function ($s) use ($periode) {
            $data = $this->reports->data($s, $periode);
            $scores = collect($data['nilai']);
            $complete = $scores->isNotEmpty() && $scores->every(fn ($n) => $n['nilai'] !== null);
            $threshold = config('rapor.nilai_tuntas');
            $status = !$complete ? 'Belum lengkap' : ($threshold === null ? 'Belum ditetapkan' : ($scores->every(fn ($n) => $n['nilai'] >= $threshold) ? 'Tuntas' : 'Belum Tuntas'));
            return ['data' => $data, 'rata' => $scores->whereNotNull('nilai')->avg('nilai'), 'status' => $status];
        });
        $subjects = $rows->flatMap(fn ($r) => $r['data']['nilai'])->unique('id')->sortBy('id')->values();
        if ($request->mapel_id) {
            $subjects = $subjects->where('id', $request->integer('mapel_id'))->values();
        }
        $rows = $rows->filter(function ($row) use ($request) {
            $value = $request->mapel_id ? collect($row['data']['nilai'])->firstWhere('id', $request->integer('mapel_id'))['nilai'] ?? null : $row['rata'];
            return (!$request->filled('ketuntasan') || $row['status'] === $request->ketuntasan)
                && (!$request->filled('minimum') || ($value !== null && $value >= $request->minimum))
                && (!$request->filled('maksimum') || ($value !== null && $value <= $request->maksimum))
                && (!$request->mapel_id || collect($row['data']['nilai'])->contains('id', $request->integer('mapel_id')));
        })->values();
        return compact('periode', 'subjects', 'rows');
    }
}
