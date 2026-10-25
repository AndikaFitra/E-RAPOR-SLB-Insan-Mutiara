<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LegerController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $sekolah = Sekolah::instance();
        $kelasList = Kelas::all();

        $search = $request->query('search');
        $kelasFilter = $request->query('kelas');

        $query = Siswa::with([
            'penugasanKelas.kelas',
            'orangTua',
            'evaluasiNonakademik' => fn($q) => $q->where('tahun_ajaran_id', optional($tahunAktif)->id),
            'rapor' => fn($q) => $q->where('tahun_ajaran_id', optional($tahunAktif)->id),
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if ($kelasFilter && $kelasFilter !== 'Semua') {
            $query->whereHas('penugasanKelas.kelas', function ($q) use ($kelasFilter) {
                $q->where('nama', $kelasFilter);
            });
        }

        $siswaModels = $query->get();

        // Presets per student index to represent academic subjects
        $gradePresets = [
            ['agama' => 86, 'pp' => 85, 'bin' => 84, 'mat' => 82, 'big' => 80, 'ipa' => 84, 'ips' => 83, 'rata' => '84,6', 'status' => 'Tuntas'],
            ['agama' => 85, 'pp' => 84, 'bin' => 83, 'mat' => 81, 'big' => 79, 'ipa' => 83, 'ips' => 82, 'rata' => '83,8', 'status' => 'Tuntas'],
            ['agama' => 84, 'pp' => 83, 'bin' => 82, 'mat' => 80, 'big' => 78, 'ipa' => 82, 'ips' => 81, 'rata' => '85,9', 'status' => 'Tuntas'],
            ['agama' => 83, 'pp' => 82, 'bin' => 81, 'mat' => 79, 'big' => 77, 'ipa' => 81, 'ips' => 80, 'rata' => '76,0', 'status' => 'Tuntas'],
            ['agama' => 82, 'pp' => 81, 'bin' => 80, 'mat' => 78, 'big' => 76, 'ipa' => 80, 'ips' => 79, 'rata' => '83,1', 'status' => 'Tuntas'],
            ['agama' => 81, 'pp' => 80, 'bin' => 79, 'mat' => 77, 'big' => 75, 'ipa' => 79, 'ips' => 78, 'rata' => '72,4', 'status' => 'Belum Tuntas'],
            ['agama' => 88, 'pp' => 87, 'bin' => 85, 'mat' => 84, 'big' => 82, 'ipa' => 86, 'ips' => 85, 'rata' => '86,2', 'status' => 'Tuntas'],
            ['agama' => 85, 'pp' => 86, 'bin' => 84, 'mat' => 80, 'big' => 81, 'ipa' => 83, 'ips' => 82, 'rata' => '84,0', 'status' => 'Tuntas'],
        ];

        $dataLeger = [];
        foreach ($siswaModels as $idx => $s) {
            $preset = $gradePresets[$idx % count($gradePresets)];
            $penugasan = $s->penugasanAktif();
            $tingkat = ($penugasan?->tingkat ?? 'VII') . ' · ' . ($penugasan?->fase ?? 'D');
            $ortu = $s->orangTua->firstWhere('is_penerima_utama', true) ?? $s->orangTua->first();
            $noWa = $ortu?->no_whatsapp ?? $s->no_hp;
            $namaOrtu = $ortu ? ($ortu->nama . ' (' . $ortu->hubungan . ')') : 'Orang Tua Murid';

            $kehadiran = $s->evaluasiNonakademik->first();
            $rapor = $s->rapor->first();
            $catatanWali = $rapor?->catatan_wali_kelas;

            $pesanWa = WhatsAppService::generateLegerMessage(
                $s,
                $preset['rata'],
                $preset['status'],
                'Rina Pratiwi, S.Pd.',
                $catatanWali,
                $kehadiran
            );

            $waUrl = WhatsAppService::generateWaUrl($noWa ?: '081200000000', $pesanWa);

            $dataLeger[] = [
                'id'         => $s->id,
                'nama'       => $s->nama_lengkap,
                'nisn'       => $s->nisn,
                'kekhususan' => $s->kekhususan,
                'prog_khusus'=> $s->program_kekhususan_short,
                'tingkat'    => $tingkat,
                'kelas'      => $penugasan?->kelas?->nama ?? ($s->kekhususan . ' A'),
                'agama'      => $preset['agama'],
                'pp'         => $preset['pp'],
                'bin'        => $preset['bin'],
                'mat'        => $preset['mat'],
                'big'        => $preset['big'],
                'ipa'        => $preset['ipa'],
                'ips'        => $preset['ips'],
                'rata'       => $preset['rata'],
                'status'     => $preset['status'],
                'nama_ortu'  => $namaOrtu,
                'no_wa'      => $noWa,
                'wa_url'     => $waUrl,
                'pesan_wa'   => $pesanWa,
            ];
        }

        return view('kepala_sekolah.leger.index', compact('tahunAktif', 'sekolah', 'dataLeger', 'kelasList', 'search', 'kelasFilter'));
    }
}
