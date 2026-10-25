<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiNonakademik extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_nonakademik';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'sakit',
        'izin',
        'alpa',
        'perkembangan',
        'tidak_ada_prestasi',
        'is_locked',
    ];

    protected function casts(): array
    {
        return [
            'sakit'              => 'integer',
            'izin'               => 'integer',
            'alpa'               => 'integer',
            'tidak_ada_prestasi' => 'boolean',
            'is_locked'          => 'boolean',
        ];
    }

    // ── Relasi ──────────────────────────────────────────────
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    // ── Helper ──────────────────────────────────────────────
    public function isLengkap(): bool
    {
        // Kehadiran selalu ada angka default 0. Perkembangan fisik & kesehatan harus diisi.
        return !empty(trim((string)$this->perkembangan));
    }
}
