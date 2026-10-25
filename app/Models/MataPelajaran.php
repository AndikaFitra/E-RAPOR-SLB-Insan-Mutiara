<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajaran';

    protected $fillable = [
        'nama',
        'kode',
        'kelompok',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
        ];
    }

    // ── Relasi ──────────────────────────────────────────────
    public function nilaiAsesmen()
    {
        return $this->hasMany(NilaiAsesmen::class);
    }

    public function kelas()
    {
        return $this->belongsToMany(
            Kelas::class,
            'kelas_mata_pelajaran',
            'mata_pelajaran_id',
            'kelas_id'
        )->withPivot('tahun_ajaran_id')->withTimestamps();
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('kode', 'like', "%{$search}%");
        });
    }
}
