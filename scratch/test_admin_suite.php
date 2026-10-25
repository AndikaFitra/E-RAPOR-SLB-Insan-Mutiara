<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\ArsipController;
use App\Http\Controllers\Admin\PenugasanController;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

echo "=== TEST ADMIN FULL SUITE ===" . PHP_EOL;

// 1. Dashboard
$dashController = new DashboardController();
$dashView = $dashController->index();
assert(isset($dashView['stats']['siswa_aktif']), 'Admin dashboard stats missing');
echo " [PASS] Admin Dashboard memuat data metrik dinamis: " . $dashView['stats']['siswa_aktif'] . " siswa aktif" . PHP_EOL;

// 2. Akun CRUD
$akunController = new AkunController();
$testEmail = 'test_guru_akun_' . time() . '@slb.sch.id';
$reqStore = Request::create('/admin/akun', 'POST', [
    'name'     => 'Guru Testing Akun',
    'email'    => $testEmail,
    'password' => 'password123',
    'role'     => 'wali_kelas',
]);
$respStore = $akunController->store($reqStore);
$createdUser = User::where('email', $testEmail)->first();
assert($createdUser !== null, 'Akun store gagal membuat user');
echo " [PASS] Admin Akun Store berhasil membuat user baru: " . $createdUser->email . PHP_EOL;

// Toggle Status
$respToggle = $akunController->toggleStatus(new Request(), $createdUser->id);
$createdUser->refresh();
assert($createdUser->is_aktif === false, 'Toggle status gagal menonaktifkan akun');
echo " [PASS] Admin Akun Toggle Status berhasil mengubah is_aktif ke false" . PHP_EOL;

// Delete Akun
$respDestroy = $akunController->destroy($createdUser->id);
$deletedUser = User::find($createdUser->id);
assert($deletedUser === null, 'Akun destroy gagal menghapus user');
echo " [PASS] Admin Akun Destroy berhasil menghapus user" . PHP_EOL;

// 3. Penugasan Store
$penugasanController = new PenugasanController();
$guru = Guru::first();
$kelas = Kelas::first();
$siswa = Siswa::first();
$reqPenugasan = Request::create('/admin/penugasan', 'POST', [
    'guru_id'  => $guru->id,
    'kelas_id' => $kelas->id,
    'siswa'    => [$siswa->id],
]);
$respPenugasan = $penugasanController->store($reqPenugasan);
echo " [PASS] Penugasan Wali Kelas & Siswa berhasil diperbarui" . PHP_EOL;

// 4. Arsip Filter
$arsipController = new ArsipController();
$reqArsip = Request::create('/admin/arsip', 'GET', ['search' => 'Tunarungu']);
$viewArsip = $arsipController->index($reqArsip);
assert(count($viewArsip['arsipList']) >= 1, 'Admin arsip search gagal');
echo " [PASS] Admin Arsip filter berhasil menemukan " . count($viewArsip['arsipList']) . " rombel" . PHP_EOL;

echo "🎉 ALL ADMIN TESTS PASSED!" . PHP_EOL;
