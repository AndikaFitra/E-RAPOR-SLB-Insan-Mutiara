<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'tahun',
        'semester',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
        ];
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    // ── Helpers ──────────────────────────────────────────────
    public static function getAktif(): ?self
    {
        return static::aktif()->first();
    }

    public function getLabelAttribute(): string
    {
        return "{$this->tahun} · Semester {$this->semester}";
    }

    // Aktifkan tahun ajaran ini dan nonaktifkan yang lain
    public function aktifkan(): void
    {
        static::query()->update(['is_aktif' => false]);
        $this->update(['is_aktif' => true]);
    }

    // ── Relasi ──────────────────────────────────────────────
    public function penugasanWaliKelas()
    {
        return $this->hasMany(PenugasanWaliKelas::class);
    }

    public function penugasanSiswaKelas()
    {
        return $this->hasMany(PenugasanSiswaKelas::class);
    }

    public function nilaiAsesmen()
    {
        return $this->hasMany(NilaiAsesmen::class);
    }

    public function rapor()
    {
        return $this->hasMany(Rapor::class);
    }
}
