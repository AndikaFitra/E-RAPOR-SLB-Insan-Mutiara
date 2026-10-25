<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\WaliKelas\NilaiAsesmenController;
use App\Http\Controllers\WaliKelas\EvaluasiNonakademikController;
use App\Http\Controllers\WaliKelas\RaporSayaController;
use App\Http\Controllers\WaliKelas\DistribusiController;
use App\Http\Controllers\WaliKelas\ArsipController;
use App\Http\Controllers\WaliKelas\DashboardController;
use Illuminate\Http\Request;

echo "=== TEST WALI KELAS FULL SUITE ===" . PHP_EOL;

// 1. Dashboard
$dashController = new DashboardController();
$dashView = $dashController->index();
assert(isset($dashView['stats']['siswa_di_kelas']), 'Dashboard stats missing');
echo " [PASS] Dashboard wali kelas render dengan data statistik dinamis" . PHP_EOL;

// 2. Nilai Asesmen Index & Filter
$asesmenController = new NilaiAsesmenController();
$reqAsesmen = Request::create('/wali-kelas/nilai-asesmen', 'GET', ['search' => 'Matematika']);
$viewAsesmen = $asesmenController->index($reqAsesmen);
assert(count($viewAsesmen['listAsesmen']) >= 1, 'Search mapel gagal');
echo " [PASS] Asesmen search filter berhasil menemukan " . count($viewAsesmen['listAsesmen']) . " mapel" . PHP_EOL;

// 3. Evaluasi Non-akademik Filter
$nonAkademikController = new EvaluasiNonakademikController();
$reqNonAkad = Request::create('/wali-kelas/evaluasi-nonakademik', 'GET', ['search' => 'Alya']);
$viewNonAkad = $nonAkademikController->index($reqNonAkad);
assert(count($viewNonAkad['dataNonAkademik']) >= 1, 'Search nonakademik gagal');
echo " [PASS] Evaluasi non-akademik filter berhasil mencari siswa" . PHP_EOL;

// 4. Rapor Saya Filter Status
$raporController = new RaporSayaController();
$reqRaporDraft = Request::create('/wali-kelas/rapor-saya', 'GET', ['status' => 'diajukan']);
$viewRaporDraft = $raporController->index($reqRaporDraft);
assert(isset($viewRaporDraft['counts']['diajukan']), 'Counts diajukan missing');
echo " [PASS] Rapor Saya filter status & counts berhasil (" . $viewRaporDraft['counts']['diajukan'] . " diajukan)" . PHP_EOL;

// 5. Arsip Filter
$arsipController = new ArsipController();
$reqArsip = Request::create('/wali-kelas/arsip', 'GET', ['kekhususan' => 'Tunarungu']);
$viewArsip = $arsipController->index($reqArsip);
assert(count($viewArsip['dataArsip']) >= 1, 'Arsip filter gagal');
echo " [PASS] Arsip filter kekhususan Tunarungu memuat " . count($viewArsip['dataArsip']) . " data" . PHP_EOL;

// 6. Distribusi Filter
$distController = new DistribusiController();
$reqDist = Request::create('/wali-kelas/distribusi', 'GET', ['status_kirim' => 'Belum dikirim']);
$viewDist = $distController->index($reqDist);
assert(isset($viewDist['dataDistribusi']), 'Data distribusi missing');
echo " [PASS] Distribusi filter status_kirim berhasil memuat " . count($viewDist['dataDistribusi']) . " data" . PHP_EOL;

echo "🎉 ALL WALI KELAS TESTS PASSED!" . PHP_EOL;
