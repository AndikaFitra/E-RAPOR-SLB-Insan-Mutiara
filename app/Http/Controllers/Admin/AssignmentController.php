<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ActivityLog, Ekstrakurikuler, EkstrakurikulerSiswa, Guru, Kelas, KelasMataPelajaran, MataPelajaran, PenugasanSiswaKelas, PenugasanWaliKelas, Rapor, Siswa};
use App\Services\RaporService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssignmentController extends Controller
{
    public function index(Request $request, RaporService $reports)
    {
        $periode = $reports->periode();
        $kelasList = Kelas::all();
        $kelas = $request->kelas_id ? Kelas::findOrFail($request->kelas_id) : $kelasList->first();
        $guruList = Guru::all();
        $students = Siswa::aktif()->with(['penugasanKelas' => fn ($q) => $q->where('tahun_ajaran_id', $periode->id)->with('kelas')])->orderBy('nama_lengkap')->get();
        $wali = $kelas?->penugasanWaliKelas()->where('tahun_ajaran_id', $periode->id)->first();
        $subjects = MataPelajaran::aktif()->get();
        $selectedSubjects = $kelas ? $reports->mapel($kelas, $periode->id)->pluck('id')->all() : [];
        return view('academic.assignment', compact('periode', 'kelasList', 'kelas', 'guruList', 'students', 'wali', 'subjects', 'selectedSubjects'));
    }

    public function store(Request $request, RaporService $reports)
    {
        $input = $request->validate([
            'kelas_id'=>'required|integer|exists:kelas,id', 'guru_id'=>'required|integer|exists:guru,id',
            'siswa'=>'nullable|array', 'siswa.*'=>'integer|distinct|exists:siswa,id',
            'tingkat'=>'required|array', 'tingkat.*'=>'nullable|in:I,II,III,IV,V,VI,VII,VIII,IX,X,XI,XII',
            'fase'=>'required|array', 'fase.*'=>'nullable|in:A,B,C,D,E,F',
            'mapel'=>'nullable|array', 'mapel.*'=>'integer|distinct|exists:mata_pelajaran,id',
            'konfirmasi_pindah'=>'sometimes|accepted',
        ]);
        $periode = $reports->periode();
        DB::transaction(function () use ($input, $request, $periode) {
            // Serialize assignment edits for a period.
            \App\Models\TahunAjaran::whereKey($periode->id)->lockForUpdate()->firstOrFail();
            if (PenugasanWaliKelas::where('tahun_ajaran_id', $periode->id)->where('guru_id', $input['guru_id'])->where('kelas_id', '!=', $input['kelas_id'])->exists()) {
                throw ValidationException::withMessages(['guru_id'=>'Guru sudah menjadi wali kelas lain pada periode ini.']);
            }
            $current = PenugasanSiswaKelas::where('tahun_ajaran_id', $periode->id)->where('kelas_id', $input['kelas_id'])->pluck('siswa_id');
            $selected = collect($input['siswa'] ?? [])->map(fn ($id) => (int) $id);
            $affected = $current->merge($selected)->unique()->sort()->values();
            Siswa::whereIn('id', $affected)->orderBy('id')->lockForUpdate()->get();
            if (Rapor::where('tahun_ajaran_id', $periode->id)->whereIn('siswa_id', $affected)->whereIn('status', ['diajukan','disetujui'])->exists()) {
                throw ValidationException::withMessages(['siswa'=>'Penugasan atau kurikulum kelas dengan rapor terkunci tidak dapat diubah.']);
            }
            foreach ($selected as $id) {
                $old = PenugasanSiswaKelas::where('tahun_ajaran_id', $periode->id)->where('siswa_id', $id)->first();
                if ($old && $old->kelas_id != $input['kelas_id'] && !$request->boolean('konfirmasi_pindah')) {
                    throw ValidationException::withMessages(['konfirmasi_pindah'=>'Ada siswa dari kelas lain. Periksa daftar dan centang konfirmasi perpindahan.']);
                }
                if (empty($input['tingkat'][$id]) || empty($input['fase'][$id])) {
                    throw ValidationException::withMessages(['tingkat'=>'Isi tingkat dan fase setiap siswa yang dipilih.']);
                }
                PenugasanSiswaKelas::updateOrCreate(['siswa_id'=>$id, 'tahun_ajaran_id'=>$periode->id], [
                    'kelas_id'=>$input['kelas_id'], 'tingkat'=>$input['tingkat'][$id], 'fase'=>$input['fase'][$id],
                ]);
            }
            PenugasanSiswaKelas::where('tahun_ajaran_id', $periode->id)->where('kelas_id', $input['kelas_id'])->whereNotIn('siswa_id', $selected)->delete();
            PenugasanWaliKelas::updateOrCreate(['kelas_id'=>$input['kelas_id'], 'tahun_ajaran_id'=>$periode->id], ['guru_id'=>$input['guru_id']]);
            Rapor::where('tahun_ajaran_id', $periode->id)->whereIn('siswa_id', $selected)->update(['wali_kelas_id'=>$input['guru_id']]);
            KelasMataPelajaran::where('kelas_id', $input['kelas_id'])->where('tahun_ajaran_id', $periode->id)->delete();
            foreach ($input['mapel'] ?? [] as $id) KelasMataPelajaran::create(['kelas_id'=>$input['kelas_id'], 'tahun_ajaran_id'=>$periode->id, 'mata_pelajaran_id'=>$id]);
            ActivityLog::log('Memperbarui penugasan dan kurikulum kelas', Kelas::find($input['kelas_id']), 'Sukses');
        });
        return redirect()->route('admin.penugasan.index', ['kelas_id' => $input['kelas_id']])
            ->with('success', 'Penugasan, tingkat/fase, dan mata pelajaran kelas disimpan.');
    }

    public function ekskul(Request $request, RaporService $reports)
    {
        $periode = $reports->periode();
        $activities = Ekstrakurikuler::all();
        $activity = $request->ekstrakurikuler_id ? Ekstrakurikuler::findOrFail($request->ekstrakurikuler_id) : $activities->first();
        $guruList = Guru::all();
        $students = Siswa::aktif()->orderBy('nama_lengkap')->get();
        $assignment = $activity ? DB::table('penugasan_ekstrakurikuler')->where('ekstrakurikuler_id', $activity->id)->where('tahun_ajaran_id', $periode->id)->first() : null;
        $selected = $activity ? EkstrakurikulerSiswa::where('ekstrakurikuler_id', $activity->id)->where('tahun_ajaran_id', $periode->id)->pluck('siswa_id')->all() : [];
        return view('academic.ekskul-assignment', compact('periode','activities','activity','guruList','students','assignment','selected'));
    }

    public function saveEkskul(Request $request, RaporService $reports)
    {
        $input = $request->validate(['ekstrakurikuler_id'=>'required|exists:ekstrakurikuler,id', 'guru_id'=>'required|exists:guru,id', 'jadwal'=>'nullable|string|max:150', 'siswa'=>'nullable|array', 'siswa.*'=>'integer|distinct|exists:siswa,id']);
        $periode = $reports->periode();
        DB::transaction(function () use ($input, $periode) {
            \App\Models\TahunAjaran::whereKey($periode->id)->lockForUpdate()->firstOrFail();
            $current = EkstrakurikulerSiswa::where('ekstrakurikuler_id', $input['ekstrakurikuler_id'])->where('tahun_ajaran_id', $periode->id)->pluck('siswa_id');
            $selected = collect($input['siswa'] ?? [])->map(fn ($id) => (int)$id);
            $changed = $current->diff($selected)->merge($selected->diff($current))->sort()->values();
            Siswa::whereIn('id', $changed)->orderBy('id')->lockForUpdate()->get();
            if (Rapor::where('tahun_ajaran_id', $periode->id)->whereIn('siswa_id', $changed)->whereIn('status', ['diajukan','disetujui'])->exists()) {
                throw ValidationException::withMessages(['siswa'=>'Keanggotaan ekskul siswa dengan rapor terkunci tidak dapat diubah.']);
            }
            DB::table('penugasan_ekstrakurikuler')->updateOrInsert(['ekstrakurikuler_id'=>$input['ekstrakurikuler_id'], 'tahun_ajaran_id'=>$periode->id], ['guru_id'=>$input['guru_id'],'jadwal'=>$input['jadwal'] ?? null,'updated_at'=>now(),'created_at'=>now()]);
            EkstrakurikulerSiswa::where('ekstrakurikuler_id',$input['ekstrakurikuler_id'])->where('tahun_ajaran_id',$periode->id)->whereNotIn('siswa_id',$selected)->delete();
            foreach ($selected as $id) EkstrakurikulerSiswa::firstOrCreate(['siswa_id'=>$id,'ekstrakurikuler_id'=>$input['ekstrakurikuler_id'],'tahun_ajaran_id'=>$periode->id]);
            ActivityLog::log('Memperbarui peserta dan pembimbing ekstrakurikuler', null, 'Sukses');
        });
        return redirect()->route('admin.ekskul.penugasan', ['ekstrakurikuler_id' => $input['ekstrakurikuler_id']])
            ->with('success', 'Peserta dan pembimbing disimpan tanpa penugasan ganda.');
    }
}
