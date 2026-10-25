<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;

    protected $table = 'sekolah';

    protected $fillable = [
        'nama',
        'npsn',
        'alamat',
        'kota',
        'provinsi',
        'kode_pos',
        'telepon',
        'email',
        'kepala_sekolah',
        'nip_kepala',
        'logo',
    ];

    /**
     * Singleton instance data sekolah.
     */
    public static function instance(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'nama' => 'SLB Insan Mutiara',
                'npsn' => '10497554',
                'alamat' => 'Jl. HR. Soebrantas',
                'kota' => 'Pekanbaru',
                'provinsi' => 'Riau',
                'kepala_sekolah' => 'Novia Parenri, S.Pd.',
            ]
        );
    }
}
