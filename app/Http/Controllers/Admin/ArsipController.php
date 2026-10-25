<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\View\View;

class ArsipController extends Controller
{
    public function index(): View
    {
        $tahunAktif = TahunAjaran::getAktif();

        $arsipList = [
            [
                'tahun'     => '2025/2026',
                'semester'  => 'Genap',
                'kelas'     => 'Tunarungu A',
                'wali_kelas'=> 'Rina Pratiwi, S.Pd',
                'total_siswa'=> 8,
                'status'    => 'Arsip Permanen',
            ],
            [
                'tahun'     => '2025/2026',
                'semester'  => 'Genap',
                'kelas'     => 'Tunagrahita A',
                'wali_kelas'=> 'Dewi Anggraini, S.Pd',
                'total_siswa'=> 8,
                'status'    => 'Arsip Permanen',
            ],
            [
                'tahun'     => '2025/2026',
                'semester'  => 'Ganjil',
                'kelas'     => 'Tunarungu A',
                'wali_kelas'=> 'Rina Pratiwi, S.Pd',
                'total_siswa'=> 8,
                'status'    => 'Arsip Permanen',
            ],
        ];

        return view('admin.arsip.index', compact('tahunAktif', 'arsipList'));
    }
}

