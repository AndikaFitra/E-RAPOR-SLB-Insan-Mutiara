<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'nama',
        'kekhususan',
        'kode',
    ];

    // ── Relasi ──────────────────────────────────────────────
    public function penugasanWaliKelas()
    {
        return $this->hasMany(PenugasanWaliKelas::class);
    }

    public function penugasanSiswaKelas()
    {
        return $this->hasMany(PenugasanSiswaKelas::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsToMany(
            MataPelajaran::class,
            'kelas_mata_pelajaran',
            'kelas_id',
            'mata_pelajaran_id'
        )->withPivot('tahun_ajaran_id')->withTimestamps();
    }

    // ── Helper: wali kelas aktif saat ini ────────────────────
    public function waliKelasAktif(): ?Guru
    {
        $tahunAktif = TahunAjaran::getAktif();

        return $this->penugasanWaliKelas()
            ->where('tahun_ajaran_id', optional($tahunAktif)->id)
            ->with('guru')
            ->first()?->guru;
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('kode', 'like', "%{$search}%")
              ->orWhere('kekhususan', 'like', "%{$search}%");
        });
    }
}
