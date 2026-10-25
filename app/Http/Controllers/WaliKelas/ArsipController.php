<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Models\TahunAjaran;
use Illuminate\View\View;

class ArsipController extends Controller
{
    public function index(): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $sekolah = Sekolah::instance();

        return view('wali_kelas.arsip.index', compact('tahunAktif', 'sekolah'));
    }
}

