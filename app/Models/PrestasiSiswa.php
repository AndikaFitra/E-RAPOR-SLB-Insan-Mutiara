<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrestasiSiswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'prestasi_siswa';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'kegiatan',
        'keterangan',
    ];

    // ── Relasi ──────────────────────────────────────────────
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
