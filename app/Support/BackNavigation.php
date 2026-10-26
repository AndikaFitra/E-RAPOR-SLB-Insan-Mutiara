<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class BackNavigation
{
    /** Return a stable, role-safe destination for the shared top bar. */
    public static function current(): ?array
    {
        $name = Route::currentRouteName();
        $role = auth()->user()?->role;
        if (!$name || !$role || str_ends_with($name, '.dashboard')) return null;

        if (in_array($name, ['reports.show', 'kepala.persetujuan.show'], true)) {
            if (request('from') === 'arsip') {
                $params = array_filter(request()->only(['tahun_ajaran_id', 'search']), fn ($value) => filled($value));
                return ['url' => route(match ($role) {
                    'admin' => 'admin.arsip.index',
                    'kepala_sekolah' => 'kepala.arsip.index',
                    default => 'wali.arsip.index',
                }, $params), 'label' => 'Arsip'];
            }
            if ($role === 'admin' && request('from') === 'siswa' && request()->integer('siswa_id') > 0) {
                return ['url' => route('admin.siswa.show', request()->integer('siswa_id')), 'label' => 'Detail siswa'];
            }
            if ($role === 'kepala_sekolah') {
                $params = array_filter(request()->only(['tahun_ajaran_id', 'kelas_id', 'kekhususan', 'search', 'status']), fn ($value) => filled($value));
                return ['url' => route('kepala.persetujuan.index', $params), 'label' => 'Persetujuan rapor'];
            }
            return $role === 'wali_kelas'
                ? ['url' => route('wali.rapor.index'), 'label' => 'Rapor saya']
                : ['url' => route('admin.arsip.index'), 'label' => 'Arsip'];
        }

        $parent = match ($name) {
            'admin.siswa.create', 'admin.siswa.edit', 'admin.siswa.show' => 'admin.siswa.index',
            'admin.guru.create', 'admin.guru.edit' => 'admin.guru.index',
            'admin.akun.edit' => 'admin.akun.index',
            'admin.penugasan.index' => 'admin.kelas.index',
            'admin.ekskul.penugasan' => 'admin.ekskul.index',
            'wali.rapor.show' => 'wali.rapor.index',
            default => match ($role) {
                'admin' => 'admin.dashboard',
                'kepala_sekolah' => 'kepala.dashboard',
                default => 'wali.dashboard',
            },
        };

        return ['url' => route($parent), 'label' => match ($parent) {
            'admin.siswa.index' => 'Data siswa',
            'admin.guru.index' => 'Data guru',
            'admin.akun.index' => 'Akun',
            'admin.kelas.index' => 'Kelas',
            'admin.ekskul.index' => 'Ekstrakurikuler',
            'wali.rapor.index' => 'Rapor saya',
            default => 'Beranda',
        }];
    }
}
