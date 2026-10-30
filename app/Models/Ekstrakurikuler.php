<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ekstrakurikuler extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ekstrakurikuler';

    protected $fillable = ['nama'];

    // ── Relasi ──────────────────────────────────────────────
    public function siswa()
    {
        return $this->hasMany(EkstrakurikulerSiswa::class);
    }
}
