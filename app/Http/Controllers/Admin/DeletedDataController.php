<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ActivityLog, Ekstrakurikuler, Guru, Kelas, MataPelajaran, OrangTua, Siswa, User};
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeletedDataController extends Controller
{
    private const TYPES = [
        'akun' => [User::class, 'Akun', 'name'],
        'guru' => [Guru::class, 'Guru', 'nama_lengkap'],
        'siswa' => [Siswa::class, 'Siswa', 'nama_lengkap'],
        'orang-tua' => [OrangTua::class, 'Orang tua/wali', 'nama'],
        'kelas' => [Kelas::class, 'Kelas', 'nama'],
        'mapel' => [MataPelajaran::class, 'Mata pelajaran', 'nama'],
        'ekskul' => [Ekstrakurikuler::class, 'Ekstrakurikuler', 'nama'],
    ];

    public function index(): View
    {
        $rows = collect(self::TYPES)->flatMap(function (array $definition, string $type) {
            [$model, $label, $nameField] = $definition;
            return $model::onlyTrashed()->latest('deleted_at')->get()->map(fn ($record) => [
                'type' => $type, 'label' => $label, 'name' => $record->$nameField,
                'id' => $record->id, 'deleted_at' => $record->deleted_at,
            ]);
        })->sortByDesc('deleted_at')->values();

        return view('academic.deleted-data', compact('rows'));
    }

    public function restore(string $type, int $id): RedirectResponse
    {
        abort_unless(isset(self::TYPES[$type]), 404);
        [$model, $label, $nameField] = self::TYPES[$type];
        $record = $model::onlyTrashed()->findOrFail($id);
        if ($record instanceof User) $record->is_aktif = false;
        $record->restore();
        if ($record instanceof User) $record->save();
        ActivityLog::log('Memulihkan '.$label.': '.$record->$nameField, $record, 'Dipulihkan');

        return back()->with('success', $label.' berhasil dipulihkan.');
    }
}
