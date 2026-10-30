<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ActivityLog, EkstrakurikulerSiswa, KelasMataPelajaran, PenugasanPembimbing, PenugasanSiswaKelas, PenugasanWaliKelas, Rapor, Siswa, TahunAjaran};
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class YearClosureController extends Controller
{
    private const LEVELS = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];

    public function close(Request $request, int $id): RedirectResponse
    {
        $next = DB::transaction(function () use ($id) {
            $current = TahunAjaran::whereKey($id)->lockForUpdate()->firstOrFail();
            abort_unless($current->is_aktif && $current->semester === 'Genap' && !$current->ditutup_pada, 422,
                'Hanya semester Genap aktif yang belum ditutup dapat diproses.');
            if (!$current->pengisian_ditutup_pada) {
                throw ValidationException::withMessages(['tahun_ajaran' => 'Tutup pengisian rapor terlebih dahulu.']);
            }
            $assignments = PenugasanSiswaKelas::with('siswa')->where('tahun_ajaran_id', $id)->get()
                ->filter(fn ($item) => $item->siswa?->is_aktif);
            if ($assignments->isEmpty()) {
                throw ValidationException::withMessages(['tahun_ajaran' => 'Tidak ada siswa aktif pada periode ini.']);
            }
            $studentIds = $assignments->pluck('siswa_id');
            $approved = Rapor::where('tahun_ajaran_id', $id)->whereIn('siswa_id', $studentIds)
                ->where('status', Rapor::STATUS_DISETUJUI)->count();
            if ($approved !== $studentIds->count()) {
                throw ValidationException::withMessages(['tahun_ajaran' => "Persetujuan rapor belum lengkap ({$approved}/{$studentIds->count()})."]);
            }
            $nextStart = (int) substr($current->tahun, 5, 4);
            $nextYear = $nextStart.'/'.($nextStart + 1);
            $next = TahunAjaran::firstOrCreate(['tahun'=>$nextYear, 'semester'=>'Ganjil'], ['is_aktif'=>false]);
            if ($next->is_aktif || $next->ditutup_pada) {
                throw ValidationException::withMessages(['tahun_ajaran' => 'Periode tujuan sudah aktif atau pernah ditutup.']);
            }
            foreach (['penugasan_siswa_kelas','penugasan_wali_kelas','kelas_mata_pelajaran','penugasan_pembimbing',
                'penugasan_ekstrakurikuler','rapor','nilai_asesmen','evaluasi_nonakademik','prestasi_siswa','ekstrakurikuler_siswa'] as $table) {
                if (DB::table($table)->where('tahun_ajaran_id', $next->id)->exists()) {
                    throw ValidationException::withMessages(['tahun_ajaran' => 'Periode tujuan sudah berisi data. Periksa dahulu agar tidak menimpa penugasan.']);
                }
            }

            foreach (PenugasanWaliKelas::where('tahun_ajaran_id', $id)->get() as $item) {
                PenugasanWaliKelas::create(['guru_id'=>$item->guru_id, 'kelas_id'=>$item->kelas_id, 'tahun_ajaran_id'=>$next->id]);
            }
            foreach (KelasMataPelajaran::where('tahun_ajaran_id', $id)->get() as $item) {
                KelasMataPelajaran::create(['kelas_id'=>$item->kelas_id, 'mata_pelajaran_id'=>$item->mata_pelajaran_id, 'tahun_ajaran_id'=>$next->id]);
            }
            foreach ($assignments as $item) {
                $position = array_search($item->tingkat, self::LEVELS, true);
                if ($position === false) {
                    throw ValidationException::withMessages(['tahun_ajaran' => 'Ada siswa tanpa tingkat yang valid: '.$item->siswa->nama_lengkap]);
                }
                if ($position === 11) {
                    $item->siswa->update(['is_aktif'=>false, 'lulus_pada_tahun_ajaran_id'=>$current->id]);
                    continue;
                }
                $nextLevel = $position + 1;
                PenugasanSiswaKelas::create([
                    'siswa_id'=>$item->siswa_id, 'kelas_id'=>$item->kelas_id, 'tahun_ajaran_id'=>$next->id,
                    'tingkat'=>self::LEVELS[$nextLevel], 'fase'=>match (true) {
                        $nextLevel <= 1 => 'A', $nextLevel <= 3 => 'B', $nextLevel <= 5 => 'C',
                        $nextLevel <= 8 => 'D', $nextLevel <= 10 => 'E', default => 'F',
                    },
                ]);
            }
            $promotedIds = PenugasanSiswaKelas::where('tahun_ajaran_id', $next->id)->pluck('siswa_id');
            foreach (PenugasanPembimbing::where('tahun_ajaran_id', $id)->whereIn('siswa_id', $promotedIds)->get() as $item) {
                PenugasanPembimbing::create(['siswa_id'=>$item->siswa_id, 'guru_id'=>$item->guru_id, 'tahun_ajaran_id'=>$next->id]);
            }
            foreach (DB::table('penugasan_ekstrakurikuler')->where('tahun_ajaran_id', $id)->get() as $item) {
                DB::table('penugasan_ekstrakurikuler')->insert([
                    'ekstrakurikuler_id'=>$item->ekstrakurikuler_id, 'guru_id'=>$item->guru_id,
                    'tahun_ajaran_id'=>$next->id, 'jadwal'=>$item->jadwal, 'created_at'=>now(), 'updated_at'=>now(),
                ]);
            }
            foreach (EkstrakurikulerSiswa::where('tahun_ajaran_id', $id)->whereIn('siswa_id', $promotedIds)->get() as $item) {
                EkstrakurikulerSiswa::create(['siswa_id'=>$item->siswa_id, 'ekstrakurikuler_id'=>$item->ekstrakurikuler_id,
                    'tahun_ajaran_id'=>$next->id, 'keterangan'=>null]);
            }
            $current->update(['is_aktif'=>false, 'ditutup_pada'=>now()]);
            $next->update(['is_aktif'=>true]);
            ActivityLog::log('Menutup tahun ajaran '.$current->label.' dan membuka '.$next->label, $current, 'Ditutup');
            return $next;
        });
        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Tahun ajaran ditutup. '.$next->label.' aktif; siswa kelas XII ditandai lulus.');
    }
}
