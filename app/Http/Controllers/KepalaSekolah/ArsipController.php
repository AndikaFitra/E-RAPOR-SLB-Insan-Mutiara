<?php

namespace App\Http\Controllers\KepalaSekolah;

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

        return view('kepala_sekolah.arsip.index', compact('tahunAktif', 'sekolah'));
    }
}

