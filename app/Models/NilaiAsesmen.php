<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiAsesmen extends Model
{
    use HasFactory;

    protected $table = 'nilai_asesmen';

    protected $fillable = [
        'siswa_id',
        'mata_pelajaran_id',
        'tahun_ajaran_id',
        'nilai_akhir',
        'narasi_capaian',
        'is_locked',
    ];

    protected function casts(): array
    {
        return [
            'nilai_akhir' => 'integer',
            'is_locked'   => 'boolean',
        ];
    }

    // ── Relasi ──────────────────────────────────────────────
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    // ── Helpers ──────────────────────────────────────────────
    public function isLengkap(): bool
    {
        return !is_null($this->nilai_akhir) && !empty(trim((string)$this->narasi_capaian));
    }
}
