<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'siswa';

    protected $fillable = [
        'nisn',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tgl_lahir',
        'kekhususan',
        'no_hp',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'tgl_lahir' => 'date',
            'is_aktif'  => 'boolean',
        ];
    }

    // ── Relasi ──────────────────────────────────────────────
    public function orangTua()
    {
        return $this->hasMany(OrangTua::class);
    }

    public function penugasanKelas()
    {
        return $this->hasMany(PenugasanSiswaKelas::class);
    }

    public function rapor()
    {
        return $this->hasMany(Rapor::class);
    }

    public function nilaiAsesmen()
    {
        return $this->hasMany(NilaiAsesmen::class);
    }

    public function evaluasiNonakademik()
    {
        return $this->hasMany(EvaluasiNonakademik::class);
    }

    public function prestasi()
    {
        return $this->hasMany(PrestasiSiswa::class);
    }

    public function ekstrakurikuler()
    {
        return $this->hasMany(EkstrakurikulerSiswa::class);
    }

    // ── Helper: program kekhususan berdasarkan 3 ketunaan ───
    public function getProgramKekhususanAttribute(): string
    {
        return match (strtolower($this->kekhususan ?? '')) {
            'tunarungu'   => 'PKPBI (Pengembangan Komunikasi, Persepsi Bunyi dan Irama)',
            'tunagrahita' => 'Bina Diri (Pengembangan Diri)',
            'autis'       => 'Pengembangan Komunikasi dan Interaksi (PKISP)',
            default       => 'Program Kekhususan',
        };
    }

    public function getProgramKekhususanShortAttribute(): string
    {
        return match (strtolower($this->kekhususan ?? '')) {
            'tunarungu'   => 'PKPBI',
            'tunagrahita' => 'Bina Diri',
            'autis'       => 'Pengembangan Komunikasi & Interaksi',
            default       => 'Program Khusus',
        };
    }

    // ── Helper: penugasan tahun ajaran aktif ─────────────────
    public function penugasanAktif()
    {
        $tahunAktif = TahunAjaran::aktif()->first();

        return $this->penugasanKelas()
            ->where('tahun_ajaran_id', optional($tahunAktif)->id)
            ->with('kelas')
            ->first();
    }

    public function raporAktif()
    {
        $tahunAktif = TahunAjaran::aktif()->first();

        return $this->rapor()
            ->where('tahun_ajaran_id', optional($tahunAktif)->id)
            ->first();
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama_lengkap', 'like', "%{$search}%")
              ->orWhere('nisn', 'like', "%{$search}%");
        });
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}
