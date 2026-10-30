<?php

namespace App\Services;

use App\Models\{ActivityLog, EvaluasiNonakademik, Kelas, MataPelajaran, NilaiAsesmen, PenugasanSiswaKelas, PenugasanWaliKelas, Rapor, Sekolah, Siswa, TahunAjaran, User};
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RaporService
{
    public function periode(?int $id = null): TahunAjaran
    {
        return $id ? TahunAjaran::findOrFail($id) : (TahunAjaran::getAktif() ?? abort(422, 'Aktifkan tahun ajaran terlebih dahulu.'));
    }

    public function kelas(User $user, int $periode): Builder
    {
        return Kelas::query()->when($user->role === 'wali_kelas', fn ($q) => $q->whereHas(
            'penugasanWaliKelas',
            fn ($q) => $q->where('tahun_ajaran_id', $periode)->where('guru_id', $user->guru_id ?? 0)
        ));
    }

    public function siswa(User $user, int $periode): Builder
    {
        return Siswa::query()->whereHas('penugasanKelas', fn ($q) => $q
            ->where('tahun_ajaran_id', $periode)
            ->whereIn('kelas_id', $this->kelas($user, $periode)->select('kelas.id')));
    }

    public function authorize(Siswa $siswa, int $periode, ?User $user = null): void
    {
        $user ??= auth()->user();
        abort_unless($user && in_array($user->role, ['admin', 'kepala_sekolah', 'wali_kelas']), 403);
        if ($user->role === 'wali_kelas') {
            abort_unless($this->siswa($user, $periode)->whereKey($siswa->id)->exists(), 403, 'Siswa bukan anggota kelas binaan Anda pada periode ini.');
        }
    }

    public function penugasan(Siswa $siswa, int $periode): ?PenugasanSiswaKelas
    {
        return $siswa->penugasanKelas()->with('kelas')->where('tahun_ajaran_id', $periode)->first();
    }

    public function mapel(Kelas $kelas, int $periode): Collection
    {
        return $kelas->mataPelajaran()->wherePivot('tahun_ajaran_id', $periode)
            ->orderBy('mata_pelajaran.id')->get();
    }

    /** Writers lock the same student row, including the first report creation. */
    public function editable(Siswa $siswa, TahunAjaran $periode): Rapor
    {
        $this->authorize($siswa, $periode->id);
        $periode = TahunAjaran::whereKey($periode->id)->lockForUpdate()->firstOrFail();
        abort_unless($periode->isPengisianTerbuka(), 422, 'Pengisian rapor periode ini sudah ditutup atau melewati batas waktu. Hubungi admin sekolah.');
        Siswa::whereKey($siswa->id)->lockForUpdate()->firstOrFail();
        $assignment = $this->penugasan($siswa, $periode->id);
        $wali = PenugasanWaliKelas::where('kelas_id', $assignment?->kelas_id)
            ->where('tahun_ajaran_id', $periode->id)->first();
        abort_unless($wali, 422, 'Wali kelas belum ditugaskan.');
        $rapor = Rapor::firstOrCreate(
            ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $periode->id],
            ['wali_kelas_id' => $wali->guru_id, 'status' => Rapor::STATUS_DRAFT]
        );
        abort_if($rapor->isLocked(), 422, 'Rapor sedang diajukan atau telah disetujui. Data terkunci.');
        if ($rapor->wali_kelas_id !== $wali->guru_id) $rapor->update(['wali_kelas_id' => $wali->guru_id]);
        return $rapor;
    }

    public function data(Siswa $siswa, TahunAjaran $periode, ?Rapor $rapor = null): array
    {
        $rapor ??= $siswa->rapor()->where('tahun_ajaran_id', $periode->id)->first();
        if ($rapor?->isDisetujui() && $rapor->snapshot) {
            return $rapor->snapshot;
        }
        $assignment = $this->penugasan($siswa, $periode->id);
        $subjects = $assignment?->kelas ? $this->mapel($assignment->kelas, $periode->id) : collect();
        $scores = $siswa->nilaiAsesmen()->where('tahun_ajaran_id', $periode->id)->get()->keyBy('mata_pelajaran_id');
        $rows = $subjects->map(fn ($m) => [
            'id' => $m->id, 'kode' => $m->kode, 'nama' => $m->nama, 'kelompok' => $m->kelompok,
            'nilai' => $scores->get($m->id)?->nilai_akhir,
            'narasi' => $scores->get($m->id)?->narasi_capaian,
        ])->values()->all();
        $eval = $siswa->evaluasiNonakademik()->where('tahun_ajaran_id', $periode->id)->first();
        $prestasi = $siswa->prestasi()->where('tahun_ajaran_id', $periode->id)->get(['kegiatan', 'keterangan'])->toArray();
        $ekskul = $siswa->ekstrakurikuler()->where('tahun_ajaran_id', $periode->id)->with('ekstrakurikuler')->get()
            ->map(fn ($e) => ['id' => $e->ekstrakurikuler_id, 'nama' => $e->ekstrakurikuler?->nama, 'keterangan' => $e->keterangan])->all();
        $wali = $rapor?->waliKelas ?? PenugasanWaliKelas::with('guru')->where('kelas_id', $assignment?->kelas_id)
            ->where('tahun_ajaran_id', $periode->id)->first()?->guru;
        $school = Sekolah::instance();
        $logoPath = $school->logo && Storage::disk('public')->exists($school->logo)
            ? Storage::disk('public')->path($school->logo)
            : public_path('images/logo-slb.png');
        $logo = $this->logoDataUri($logoPath);
        $parents = $siswa->orangTua()->get(['nama', 'hubungan', 'no_whatsapp']);
        return [
            'siswa' => $siswa->only(['id', 'nis', 'nisn', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'kekhususan', 'agama', 'alamat', 'no_hp']),
            'tgl_lahir' => $siswa->tgl_lahir?->format('d-m-Y'),
            'tanggal_diterima' => $siswa->tanggal_diterima,
            'sekolah' => $school->only(['nama', 'npsn', 'alamat', 'kota', 'provinsi', 'kode_pos', 'telepon', 'email', 'kepala_sekolah', 'nip_kepala']),
            'logo' => $logo,
            'orang_tua' => $parents->mapWithKeys(fn ($parent) => [strtolower($parent->hubungan) => $parent->only(['nama', 'no_whatsapp'])])->all(),
            'tahun' => $periode->tahun, 'semester' => $periode->semester, 'periode_id' => $periode->id,
            'kelas' => $assignment?->kelas?->nama, 'tingkat' => $assignment?->tingkat, 'fase' => $assignment?->fase,
            'wali' => $wali?->nama_lengkap, 'nip_wali' => $wali?->nip,
            'nilai' => $rows, 'evaluasi' => $eval?->only(['sakit', 'izin', 'alpa', 'perkembangan', 'tidak_ada_prestasi']),
            'prestasi' => $prestasi, 'ekskul' => $ekskul,
            'catatan' => $rapor?->catatan_wali_kelas, 'tanggal' => $rapor?->tgl_rapor?->format('d-m-Y'),
            'keputusan' => $rapor?->keputusan,
        ];
    }

    public function checklist(array $data): array
    {
        return [
            'Identitas, kelas, tingkat, dan fase' => filled($data['siswa']['nisn']) && filled($data['kelas']) && filled($data['tingkat']) && filled($data['fase']),
            'Mata pelajaran kelas sudah ditetapkan' => count($data['nilai']) > 0,
            'Seluruh nilai akhir dan narasi capaian' => count($data['nilai']) > 0 && collect($data['nilai'])->every(fn ($n) => $n['nilai'] !== null && filled(trim((string) $n['narasi']))),
            'Kehadiran dan perkembangan' => $data['evaluasi'] !== null && filled(trim((string) ($data['evaluasi']['perkembangan'] ?? ''))),
            'Prestasi atau pernyataan tidak ada prestasi' => count($data['prestasi']) > 0 || ($data['evaluasi']['tidak_ada_prestasi'] ?? false),
            'Penilaian kegiatan ekstrakurikuler yang diikuti' => collect($data['ekskul'])->every(fn ($e) => filled(trim((string) $e['keterangan']))),
            'Catatan wali kelas' => filled(trim((string) $data['catatan'])),
            'Tanggal rapor' => filled($data['tanggal']),
        ];
    }

    private function logoDataUri(string $path): ?string
    {
        if (!is_file($path)) return null;
        $info = @getimagesize($path);
        if (!$info || !in_array($info['mime'], ['image/png', 'image/jpeg'], true)) return null;
        $source = $info['mime'] === 'image/png' ? @imagecreatefrompng($path) : @imagecreatefromjpeg($path);
        if (!$source) return null;
        $scale = min(1, 256 / max($info[0], $info[1]));
        $image = imagecreatetruecolor(max(1, (int) round($info[0] * $scale)), max(1, (int) round($info[1] * $scale)));
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagecopyresampled($image, $source, 0, 0, 0, 0, imagesx($image), imagesy($image), $info[0], $info[1]);
        imagedestroy($source);
        ob_start();
        imagepng($image, null, 6);
        $bytes = ob_get_clean();
        imagedestroy($image);
        return $bytes ? 'data:image/png;base64,'.base64_encode($bytes) : null;
    }

    public function assertComplete(array $data): void
    {
        $missing = array_keys(array_filter($this->checklist($data), fn ($complete) => !$complete));
        if ($missing) {
            throw ValidationException::withMessages(['kelengkapan' => 'Lengkapi: '.implode('; ', $missing).'.']);
        }
    }

    public function submit(Siswa $siswa, TahunAjaran $periode): Rapor
    {
        return DB::transaction(function () use ($siswa, $periode) {
            $rapor = $this->editable($siswa, $periode);
            $this->assertComplete($this->data($siswa, $periode, $rapor));
            $rapor->update(['status' => Rapor::STATUS_DIAJUKAN, 'tgl_pengajuan' => now(), 'tgl_persetujuan' => null]);
            $this->lockComponents($rapor, true);
            ActivityLog::log('Mengajukan rapor '.$siswa->nama_lengkap, $rapor, 'Diajukan');
            return $rapor;
        });
    }

    public function decide(Rapor $rapor, bool $approved, ?string $note = null, bool $legacy = false): void
    {
        DB::transaction(function () use ($rapor, $approved, $note, $legacy) {
            Siswa::whereKey($rapor->siswa_id)->lockForUpdate()->firstOrFail();
            $rapor->refresh();
            if ($legacy) {
                abort_unless(!$approved && $rapor->isDisetujui() && !$rapor->snapshot && $rapor->tahunAjaran->is_aktif, 422, 'Hanya rapor lama tanpa dokumen final pada periode aktif yang dapat dikembalikan.');
            } else {
                abort_unless($rapor->isDiajukan(), 422, 'Hanya rapor berstatus diajukan yang dapat diputuskan.');
            }
            if ($approved) {
                $data = $this->data($rapor->siswa, $rapor->tahunAjaran, $rapor);
                $this->assertComplete($data);
                $path = 'rapor/'.$rapor->id.'-'.Str::uuid().'.pdf';
                if (!Storage::disk('local')->put($path, $this->pdf($data))) {
                    throw ValidationException::withMessages(['pdf' => 'PDF gagal disimpan. Silakan coba lagi.']);
                }
                $rapor->update([
                    'status' => Rapor::STATUS_DISETUJUI, 'tgl_persetujuan' => now(),
                    'snapshot' => $data, 'pdf_path' => $path, 'share_token' => Str::random(64),
                    'share_token_expires_at' => now()->addDays(Rapor::SHARE_LINK_DAYS),
                ]);
            } else {
                $rapor->update(['status' => Rapor::STATUS_DIKEMBALIKAN, 'catatan_revisi' => $note, 'tgl_persetujuan' => null, 'share_token' => null, 'share_token_expires_at' => null, 'snapshot' => null, 'pdf_path' => null]);
            }
            $this->lockComponents($rapor, $approved);
            ActivityLog::log($approved ? 'Rapor disetujui' : 'Rapor dikembalikan: '.$note, $rapor, $approved ? 'Disetujui' : 'Dikembalikan');
        });
    }

    private function lockComponents(Rapor $rapor, bool $locked): void
    {
        foreach ([NilaiAsesmen::class, EvaluasiNonakademik::class] as $model) {
            $model::where('siswa_id', $rapor->siswa_id)->where('tahun_ajaran_id', $rapor->tahun_ajaran_id)->update(['is_locked' => $locked]);
        }
    }

    public function pdf(array $data): string
    {
        $options = new Options(['isRemoteEnabled' => false, 'defaultFont' => 'DejaVu Sans', 'defaultMediaType' => 'print']);
        $pdf = new Dompdf($options);
        $pdf->loadHtml(view('reports.document', compact('data'))->render(), 'UTF-8');
        $pdf->setPaper('A4');
        $pdf->render();
        return $pdf->output();
    }
}
