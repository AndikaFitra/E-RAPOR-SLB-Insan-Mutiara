<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Siswa;

$wali = User::where('role', 'wali_kelas')->first();
$kepsek = User::where('role', 'kepala_sekolah')->first();
$siswa = Siswa::first();

echo "Testing as Wali Kelas: " . $wali->email . PHP_EOL;
auth()->login($wali);

$response1 = app()->handle(Illuminate\Http\Request::create('/wali-kelas/rapor-saya/' . $siswa->id, 'GET'));
echo "GET /wali-kelas/rapor-saya/{$siswa->id} -> Status: " . $response1->getStatusCode() . PHP_EOL;

$response2 = app()->handle(Illuminate\Http\Request::create('/wali-kelas/rapor-saya/' . $siswa->id . '/cetak', 'GET'));
echo "GET /wali-kelas/rapor-saya/{$siswa->id}/cetak -> Status: " . $response2->getStatusCode() . PHP_EOL;

auth()->login($kepsek);
$response3 = app()->handle(Illuminate\Http\Request::create('/kepala-sekolah/persetujuan/' . $siswa->id . '/cetak', 'GET'));
echo "GET /kepala-sekolah/persetujuan/{$siswa->id}/cetak -> Status: " . $response3->getStatusCode() . PHP_EOL;

$content = $response2->getContent();
$hasCover = str_contains($content, 'LAPORAN') && str_contains($content, 'JENIS KEKHUSUSAN');
$hasPetunjuk = str_contains($content, 'PETUNJUK PENGISIAN RAPOR');
$hasBiodata = str_contains($content, 'KETERANGAN DIRI PESERTA DIDIK');
$hasNilai = str_contains($content, 'Capaian Kompetensi') && str_contains($content, 'Program Kekhususan');
$hasPengesahan = (stripos($content, 'Novia Parenri') !== false || stripos($content, 'NOVIA PARENRI') !== false) && stripos($content, 'Wali Kelas') !== false;

echo "Verifikasi Komponen Gabungan Rapor:" . PHP_EOL;
echo "- Cover Depan Resmi: " . ($hasCover ? "OK" : "FAILED") . PHP_EOL;
echo "- Petunjuk & Identitas Sekolah: " . ($hasPetunjuk ? "OK" : "FAILED") . PHP_EOL;
echo "- Biodata Peserta Didik: " . ($hasBiodata ? "OK" : "FAILED") . PHP_EOL;
echo "- Nilai & Asesmen (PKPBI/Bina Diri/PKISP): " . ($hasNilai ? "OK" : "FAILED") . PHP_EOL;
echo "- Non-akademik & Tanda Tangan Resmi: " . ($hasPengesahan ? "OK" : "FAILED") . PHP_EOL;

if ($response1->getStatusCode() === 200 && $response2->getStatusCode() === 200 && $response3->getStatusCode() === 200 && $hasCover && $hasPetunjuk && $hasBiodata && $hasNilai && $hasPengesahan) {
    echo "🎉 SEMUA PENGUJIAN CETAK GABUNGAN LULUS DENGAN SEMPURNA!" . PHP_EOL;
} else {
    echo "❌ ADA PENGUJIAN YANG GAGAL" . PHP_EOL;
    exit(1);
}
