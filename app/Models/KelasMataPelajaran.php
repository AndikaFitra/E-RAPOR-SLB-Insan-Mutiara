<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasMataPelajaran extends Model
{
    use HasFactory;

    protected $table = 'kelas_mata_pelajaran';

    protected $fillable = [
        'kelas_id',
        'mata_pelajaran_id',
        'tahun_ajaran_id',
    ];

    // ── Relasi ──────────────────────────────────────────────
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
