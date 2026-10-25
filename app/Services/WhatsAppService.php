<?php

namespace App\Services;

class WhatsAppService
{
    /**
     * Format Indonesian phone number to standard international format (62...)
     */
    public static function formatPhoneNumber(?string $number): string
    {
        if (!$number) {
            return '';
        }
        $cleaned = preg_replace('/[^0-9]/', '', $number);
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '8')) {
            $cleaned = '62' . $cleaned;
        }
        return $cleaned;
    }

    /**
     * Generate Leger summary message for parents
     */
    public static function generateLegerMessage($siswa, $rataRata, $status, $waliKelasNama = 'Rina Pratiwi, S.Pd.', $catatan = null, $kehadiran = null): string
    {
        $progKhusus = $siswa->program_kekhususan ?? 'Program Kekhususan';
        $penugasan = $siswa->penugasanAktif();
        $kelasNama = $penugasan?->kelas?->nama ?? ($siswa->kekhususan . ' A');
        $tingkat = $penugasan?->tingkat ?? 'VII';
        $fase = $penugasan?->fase ?? 'D';
        $sakit = $kehadiran->sakit ?? 0;
        $izin = $kehadiran->izin ?? 0;
        $alpa = $kehadiran->alpa ?? 0;
        $catatanText = $catatan ?: 'Ananda menunjukkan antusiasme belajar dan perkembangan kemandirian yang sangat baik di sekolah.';

        return "*LAPORAN HASIL BELAJAR (LEGER AKADEMIK)*\n"
             . "*SLB INSAN MUTIARA PEKANBARU*\n"
             . "Tahun Ajaran: 2026/2027 · Semester Ganjil\n\n"
             . "Yth. Bapak/Ibu Orang Tua / Wali Murid,\n"
             . "Berikut kami sampaikan ringkasan hasil belajar ananda:\n\n"
             . "👤 *Nama Siswa:* " . $siswa->nama_lengkap . "\n"
             . "🔢 *NISN:* " . $siswa->nisn . "\n"
             . "🏫 *Kelas / Fase:* " . $kelasNama . " (" . $tingkat . " · Fase " . $fase . ")\n"
             . "🎯 *Ketunaan:* " . $siswa->kekhususan . "\n"
             . "📚 *Program Kekhususan:* " . $progKhusus . "\n\n"
             . "📊 *Rekap Akademik:*\n"
             . "- Rata-rata Nilai: *" . $rataRata . "*\n"
             . "- Status: *" . $status . "*\n\n"
             . "📅 *Kehadiran:*\n"
             . "- Sakit: " . $sakit . " hari\n"
             . "- Izin: " . $izin . " hari\n"
             . "- Tanpa Keterangan: " . $alpa . " hari\n\n"
             . "📝 *Catatan Wali Kelas:*\n"
             . "\"" . $catatanText . "\"\n\n"
             . "Kepala Sekolah: *Novia Parenri, S.Si, M.Si*\n"
             . "Wali Kelas: *" . $waliKelasNama . "*\n\n"
             . "_Pesan ini dikirim resmi dari Sistem e-Rapor SLB Insan Mutiara._";
    }

    /**
     * Generate clickable https://wa.me deep link
     */
    public static function generateWaUrl(string $phone, string $message): string
    {
        $formattedPhone = self::formatPhoneNumber($phone);
        return 'https://wa.me/' . $formattedPhone . '?text=' . rawurlencode($message);
    }
}

