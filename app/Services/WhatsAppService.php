<?php

namespace App\Services;

use App\Models\Rapor;

class WhatsAppService
{
    public static function formatPhoneNumber(?string $number): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $number ?? '');
        if (str_starts_with($cleaned, '0')) $cleaned = '62'.substr($cleaned, 1);
        elseif (str_starts_with($cleaned, '8')) $cleaned = '62'.$cleaned;
        return $cleaned;
    }

    public static function validPhone(?string $number): bool
    {
        return (bool) preg_match('/^628[0-9]{8,11}$/', self::formatPhoneNumber($number));
    }

    public static function generateWaUrl(string $phone, string $message): string
    {
        return 'https://wa.me/'.self::formatPhoneNumber($phone).'?text='.rawurlencode($message);
    }

    public static function raporMessage(Rapor $rapor, string $recipient): string
    {
        $data = $rapor->snapshot;
        if (!$rapor->hasValidShareLink()) return '';
        return "Yth. Bapak/Ibu {$recipient},\n\nRapor {$data['siswa']['nama_lengkap']} untuk semester {$data['semester']} {$data['tahun']} telah disetujui.\n"
            ."Silakan unduh dokumen melalui tautan berikut:\n".route('reports.shared', $rapor->share_token)
            ."\n\nMohon menjaga kerahasiaan tautan rapor ini.\n{$data['sekolah']['nama']}\nWali kelas: {$data['wali']}";
    }

    public static function generateLegerMessage($siswa, $rataRata, $status, $waliKelasNama = '', $catatan = null, $kehadiran = null): string
    {
        $periode = \App\Models\TahunAjaran::getAktif();
        return "Ringkasan hasil belajar {$siswa->nama_lengkap}\nPeriode: ".($periode?->label ?? '—')
            ."\nProgram: {$siswa->program_kekhususan}\nKekhususan: {$siswa->kekhususan}\nRata-rata: {$rataRata}\nStatus: {$status}\nWali kelas: {$waliKelasNama}\nCatatan: ".($catatan ?? '—');
    }
}
