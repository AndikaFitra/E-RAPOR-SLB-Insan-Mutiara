<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ActivityLog, PenugasanSiswaKelas, Rapor, TahunAjaran};
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReportEntryController extends Controller
{
    public function close(Request $request, int $id): RedirectResponse
    {
        DB::transaction(function () use ($id) {
            $year = TahunAjaran::whereKey($id)->lockForUpdate()->firstOrFail();
            abort_unless($year->is_aktif, 422, 'Hanya periode aktif yang dapat ditutup.');
            if ($year->pengisian_ditutup_pada) return;
            $studentIds = PenugasanSiswaKelas::where('tahun_ajaran_id', $id)
                ->whereHas('siswa', fn ($q) => $q->where('is_aktif', true))->pluck('siswa_id')->unique();
            $approved = Rapor::where('tahun_ajaran_id', $id)->whereIn('siswa_id', $studentIds)
                ->where('status', Rapor::STATUS_DISETUJUI)->count();
            if ($studentIds->isEmpty() || $approved !== $studentIds->count()) {
                throw ValidationException::withMessages(['pengisian' => "Belum semua rapor disetujui ({$approved}/{$studentIds->count()})."]);
            }
            $year->update(['pengisian_ditutup_pada' => now()]);
            ActivityLog::log('Menutup pengisian rapor '.$year->label, $year, 'Ditutup');
        });
        return back()->with('success', 'Pengisian rapor ditutup. Wali kelas tidak dapat mengubah nilai atau evaluasi.');
    }

    public function reopen(Request $request, int $id): RedirectResponse
    {
        $input = $request->validate([
            'alasan' => 'required|string|min:10|max:500',
            'batas_pengisian' => 'nullable|date|after_or_equal:today',
        ]);
        DB::transaction(function () use ($id, $input) {
            $year = TahunAjaran::whereKey($id)->lockForUpdate()->firstOrFail();
            abort_unless($year->is_aktif && $year->pengisian_ditutup_pada, 422, 'Periode ini tidak sedang ditutup.');
            if ($year->batas_pengisian && $year->batas_pengisian->toDateString() < now('Asia/Jakarta')->toDateString() && empty($input['batas_pengisian'])) {
                throw ValidationException::withMessages(['batas_pengisian' => 'Isi batas pengisian baru agar wali kelas dapat mengedit.']);
            }
            $year->update(['pengisian_ditutup_pada' => null] + (isset($input['batas_pengisian']) ? ['batas_pengisian' => $input['batas_pengisian']] : []));
            ActivityLog::log('Membuka kembali pengisian rapor '.$year->label.'; alasan: '.$input['alasan'], $year, 'Dibuka kembali');
        });
        return back()->with('success', 'Pengisian rapor dibuka kembali.');
    }
}
