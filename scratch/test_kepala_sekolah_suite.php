<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\KepalaSekolah\DashboardController;
use App\Http\Controllers\KepalaSekolah\PersetujuanController;
use App\Http\Controllers\KepalaSekolah\LegerController;
use App\Http\Controllers\KepalaSekolah\ArsipController;
use Illuminate\Http\Request;

echo "=== TEST KEPALA SEKOLAH FULL SUITE ===" . PHP_EOL;

// 1. Dashboard
$dashController = new DashboardController();
$dashView = $dashController->index();
assert(isset($dashView['stats']['menunggu_tinjauan']), 'Dashboard stats missing');
echo " [PASS] Dashboard Kepala Sekolah memuat metrik dinamis: " . $dashView['stats']['menunggu_tinjauan'] . " menunggu tinjauan" . PHP_EOL;

// 2. Persetujuan Filter
$persetujuanController = new PersetujuanController();
$reqPersetujuan = Request::create('/kepala-sekolah/persetujuan', 'GET', ['status' => 'diajukan']);
$viewPersetujuan = $persetujuanController->index($reqPersetujuan);
assert(count($viewPersetujuan['antrean']) >= 1, 'Persetujuan antrean missing');
echo " [PASS] Persetujuan status filter berhasil memuat " . count($viewPersetujuan['antrean']) . " rapor diajukan" . PHP_EOL;

// 3. Leger Filter
$legerController = new LegerController();
$reqLeger = Request::create('/kepala-sekolah/leger', 'GET', ['kelas' => 'Tunarungu A']);
$viewLeger = $legerController->index($reqLeger);
assert(count($viewLeger['dataLeger']) >= 1, 'Leger data missing');
echo " [PASS] Leger filter kelas Tunarungu A berhasil memuat " . count($viewLeger['dataLeger']) . " siswa" . PHP_EOL;

// 4. Arsip Filter
$arsipController = new ArsipController();
$reqArsip = Request::create('/kepala-sekolah/arsip', 'GET', ['kekhususan' => 'Tunagrahita']);
$viewArsip = $arsipController->index($reqArsip);
assert(count($viewArsip['arsip']) >= 1, 'Kepsek arsip data missing');
echo " [PASS] Arsip Kepala Sekolah filter Tunagrahita berhasil memuat " . count($viewArsip['arsip']) . " data" . PHP_EOL;

echo "🎉 ALL KEPALA SEKOLAH TESTS PASSED!" . PHP_EOL;
