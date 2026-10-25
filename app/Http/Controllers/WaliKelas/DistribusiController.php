<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\DistribusiRapor;
use App\Models\Kelas;
use App\Models\Rapor;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DistribusiController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $sekolah = Sekolah::instance();
        $kelasList = Kelas::all();

        $search = $request->query('search');
        $statusKirim = $request->query('status_kirim');

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

        $siswaModels = $query->get();

        $dataDistribusi = [];
        foreach ($siswaModels as $s) {
            $penugasan = $s->penugasanAktif();
            $rapor = $s->rapor->first();
            $ortu = $s->orangTua->firstWhere('is_penerima_utama', true) ?? $s->orangTua->first();
            $noWa = $ortu?->no_whatsapp ?? $s->no_hp;
            $namaOrtu = $ortu ? ($ortu->nama . ' · ' . $ortu->hubungan) : 'Orang Tua Murid';

            $statusRapor = $rapor?->status ?? 'draft';
            $isApproved = ($statusRapor === Rapor::STATUS_DISETUJUI);

            // Tentukan status kirim
            $statusKirimRow = 'Belum dikirim';
            if ($isApproved && $rapor) {
                $distRecord = DistribusiRapor::where('rapor_id', $rapor->id)->first();
                if ($distRecord && $distRecord->status_kirim === 'terkirim') {
                    $statusKirimRow = 'Ditandai terkirim';
                }
            }

            if ($statusKirim && $statusKirim !== 'Semua' && $statusKirimRow !== $statusKirim) {
                continue;
            }

            $kehadiran = $s->evaluasiNonakademik->first();
            $pesanWa = WhatsAppService::generateLegerMessage(
                $s,
                '85,2',
                'Tuntas',
                'Rina Pratiwi, S.Pd.',
                $rapor?->catatan_wali_kelas,
                $kehadiran
            );

            $waUrl = WhatsAppService::generateWaUrl($noWa ?: '081200000000', $pesanWa);

            $dataDistribusi[] = [
                'id'           => $s->id,
                'rapor_id'     => $rapor?->id,
                'siswa'        => $s->nama_lengkap,
                'nisn'         => $s->nisn,
                'kekhususan'   => $s->kekhususan,
                'periode'      => ($tahunAktif?->tahun ?? '2026/2027') . ' ' . ($tahunAktif?->semester ?? 'Ganjil'),
                'penerima'     => $namaOrtu,
                'nomor'        => $noWa,
                'is_approved'  => $isApproved,
                'status_rapor' => ucfirst($statusRapor),
                'pdf'          => $isApproved ? 'PDF Disetujui' : ('PDF ' . ucfirst($statusRapor)),
                'status'       => $statusKirimRow,
                'wa_url'       => $waUrl,
                'pesan_wa'     => $pesanWa,
            ];
        }

        return view('wali_kelas.distribusi.index', compact('tahunAktif', 'sekolah', 'dataDistribusi', 'kelasList', 'search', 'statusKirim'));
    }

    public function markSent(Request $request, int $id): RedirectResponse
    {
        $rapor = Rapor::with('siswa.orangTua')->find($id);
        if ($rapor) {
            $ortu = $rapor->siswa?->orangTua->firstWhere('is_penerima_utama', true) 
                    ?? $rapor->siswa?->orangTua->first();

            if ($ortu) {
                DistribusiRapor::updateOrCreate(
                    [
                        'rapor_id'     => $rapor->id,
                        'orang_tua_id' => $ortu->id,
                    ],
                    [
                        'tgl_kirim'    => now(),
                        'status_kirim' => 'terkirim',
                        'catatan'      => 'Dikirim via WhatsApp wa.me ke ' . $ortu->nama . ' (' . $ortu->no_whatsapp . ')',
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Status distribusi rapor berhasil diperbarui menjadi Ditandai terkirim.');
    }
}
