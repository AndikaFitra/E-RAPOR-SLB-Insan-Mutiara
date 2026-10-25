<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guru extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'guru';

    protected $fillable = [
        'nip',
        'nama_lengkap',
        'jenis_kelamin',
        'jabatan',
        'no_whatsapp',
    ];

    // ── Relasi ──────────────────────────────────────────────
    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function penugasanWaliKelas()
    {
        return $this->hasMany(PenugasanWaliKelas::class);
    }

    public function penugasanPembimbing()
    {
        return $this->hasMany(PenugasanPembimbing::class);
    }

    public function kelasSaatIni()
    {
        $tahunAktif = TahunAjaran::aktif()->first();

        return $this->penugasanWaliKelas()
            ->where('tahun_ajaran_id', optional($tahunAktif)->id)
            ->with('kelas')
            ->first()?->kelas;
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama_lengkap', 'like', "%{$search}%")
              ->orWhere('nip', 'like', "%{$search}%");
        });
    }
}
