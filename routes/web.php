<?php

use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\ArsipController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EkstrakurikulerController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\OrangTuaController;
use App\Http\Controllers\Admin\PenugasanController;
use App\Http\Controllers\Admin\SekolahController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\KepalaSekolah\ArsipController as KepalaArsipController;
use App\Http\Controllers\KepalaSekolah\DashboardController as KepalaDashboardController;
use App\Http\Controllers\KepalaSekolah\LegerController as KepalaLegerController;
use App\Http\Controllers\KepalaSekolah\PersetujuanController as KepalaPersetujuanController;
use App\Http\Controllers\WaliKelas\ArsipController as WaliArsipController;
use App\Http\Controllers\WaliKelas\DashboardController as WaliDashboardController;
use App\Http\Controllers\WaliKelas\DistribusiController as WaliDistribusiController;
use App\Http\Controllers\WaliKelas\EvaluasiNonakademikController as WaliNonakademikController;
use App\Http\Controllers\WaliKelas\NilaiAsesmenController as WaliAsesmenController;
use App\Http\Controllers\WaliKelas\RaporSayaController as WaliRaporController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard if logged in, else to login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route(Auth::user()->getDashboardRoute());
    }
    return redirect()->route('login');
});

// Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Modul Admin ──────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Data Siswa
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/tambah', [SiswaController::class, 'create'])->name('siswa.create');
    Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
    Route::get('/siswa/{id}/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
    Route::put('/siswa/{id}', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{id}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
    Route::get('/siswa/{id}', [SiswaController::class, 'show'])->name('siswa.show');

    // Data Guru
    Route::get('/guru', [GuruController::class, 'index'])->name('guru.index');
    Route::get('/guru/tambah', [GuruController::class, 'create'])->name('guru.create');
    Route::post('/guru', [GuruController::class, 'store'])->name('guru.store');
    Route::get('/guru/{id}/edit', [GuruController::class, 'edit'])->name('guru.edit');
    Route::put('/guru/{id}', [GuruController::class, 'update'])->name('guru.update');
    Route::delete('/guru/{id}', [GuruController::class, 'destroy'])->name('guru.destroy');

    // Data Kelas & Penugasan
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::put('/kelas/{id}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{id}', [KelasController::class, 'destroy'])->name('kelas.destroy');
    Route::get('/penugasan', [\App\Http\Controllers\Admin\AssignmentController::class, 'index'])->name('penugasan.index');
    Route::post('/penugasan', [\App\Http\Controllers\Admin\AssignmentController::class, 'store'])->name('penugasan.store');

    // Tahun Ajaran
    Route::get('/tahun-ajaran', [\App\Http\Controllers\Admin\SettingsController::class, 'years'])->name('tahun-ajaran.index');
    Route::post('/tahun-ajaran', [\App\Http\Controllers\Admin\SettingsController::class, 'saveYear'])->name('tahun-ajaran.store');
    Route::post('/tahun-ajaran/{id}/aktifkan', [TahunAjaranController::class, 'setAktif'])->name('tahun-ajaran.aktifkan');
    Route::post('/tahun-ajaran/{id}/tutup-pengisian', [\App\Http\Controllers\Admin\ReportEntryController::class, 'close'])->name('tahun-ajaran.tutup-pengisian');
    Route::post('/tahun-ajaran/{id}/buka-pengisian', [\App\Http\Controllers\Admin\ReportEntryController::class, 'reopen'])->name('tahun-ajaran.buka-pengisian');

    // Orang Tua / Wali
    Route::get('/orang-tua', [OrangTuaController::class, 'index'])->name('orang-tua.index');
    Route::post('/orang-tua', [OrangTuaController::class, 'store'])->name('orang-tua.store');
    Route::put('/orang-tua/{id}', [OrangTuaController::class, 'update'])->name('orang-tua.update');
    Route::delete('/orang-tua/{id}', [OrangTuaController::class, 'destroy'])->name('orang-tua.destroy');

    // Mata Pelajaran
    Route::get('/mata-pelajaran', [MataPelajaranController::class, 'index'])->name('mapel.index');
    Route::post('/mata-pelajaran', [MataPelajaranController::class, 'store'])->name('mapel.store');
    Route::put('/mata-pelajaran/{id}', [MataPelajaranController::class, 'update'])->name('mapel.update');
    Route::delete('/mata-pelajaran/{id}', [MataPelajaranController::class, 'destroy'])->name('mapel.destroy');

    // Ekstrakurikuler
    Route::get('/ekstrakurikuler', [EkstrakurikulerController::class, 'index'])->name('ekskul.index');
    Route::post('/ekstrakurikuler', [EkstrakurikulerController::class, 'store'])->name('ekskul.store');
    Route::put('/ekstrakurikuler/{id}', [EkstrakurikulerController::class, 'update'])->name('ekskul.update');
    Route::delete('/ekstrakurikuler/{id}', [EkstrakurikulerController::class, 'destroy'])->name('ekskul.destroy');

    // Data Sekolah
    Route::get('/sekolah', [\App\Http\Controllers\Admin\SettingsController::class, 'school'])->name('sekolah.index');
    Route::put('/sekolah', [\App\Http\Controllers\Admin\SettingsController::class, 'saveSchool'])->name('sekolah.update');

    // Akun & Hak Akses
    Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');
    Route::post('/akun', [AkunController::class, 'store'])->name('akun.store');
    Route::delete('/akun/{id}', [AkunController::class, 'destroy'])->name('akun.destroy');
    Route::post('/akun/{id}/reset-password', [AkunController::class, 'resetPassword'])->name('akun.reset-password');
    Route::post('/akun/{id}/toggle-status', [AkunController::class, 'toggleStatus'])->name('akun.toggle-status');

    // Arsip
    Route::get('/arsip', [\App\Http\Controllers\ReportController::class, 'arsip'])->name('arsip.index');
    Route::get('/data-terhapus', [\App\Http\Controllers\Admin\DeletedDataController::class, 'index'])->name('data-terhapus.index');
    Route::post('/data-terhapus/{type}/{id}/restore', [\App\Http\Controllers\Admin\DeletedDataController::class, 'restore'])->name('data-terhapus.restore');

    // Log Aktivitas
    Route::get('/log-aktivitas', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('log.index');
});

// ── Modul Kepala Sekolah ─────────────────────────────────────
Route::middleware(['auth', 'role:kepala_sekolah'])->prefix('kepala-sekolah')->name('kepala.')->group(function () {
    Route::get('/dashboard', [KepalaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/persetujuan', [\App\Http\Controllers\KepalaSekolah\ReviewController::class, 'index'])->name('persetujuan.index');
    Route::get('/persetujuan/{id}', [\App\Http\Controllers\ReportController::class, 'show'])->name('persetujuan.show');
    Route::get('/persetujuan/{id}/cetak', [\App\Http\Controllers\ReportController::class, 'cetak'])->name('persetujuan.cetak');
    Route::post('/persetujuan/{id}/approve', [\App\Http\Controllers\KepalaSekolah\ReviewController::class, 'approve'])->name('persetujuan.approve');
    Route::post('/persetujuan/{id}/reject', [\App\Http\Controllers\KepalaSekolah\ReviewController::class, 'reject'])->name('persetujuan.reject');
    Route::get('/leger', [\App\Http\Controllers\KepalaSekolah\ExportController::class, 'index'])->name('leger.index');
    Route::get('/arsip', [\App\Http\Controllers\ReportController::class, 'arsip'])->name('arsip.index');
});

// ── Modul Wali Kelas ─────────────────────────────────────────
Route::middleware(['auth', 'role:wali_kelas'])->prefix('wali-kelas')->name('wali.')->group(function () {
    Route::get('/dashboard', [WaliDashboardController::class, 'index'])->name('dashboard');
    Route::get('/nilai-asesmen', [\App\Http\Controllers\WaliKelas\AcademicController::class, 'asesmen'])->name('asesmen.index');
    Route::post('/nilai-asesmen', [\App\Http\Controllers\WaliKelas\AcademicController::class, 'saveScores'])->name('asesmen.store');
    Route::get('/evaluasi-nonakademik', [\App\Http\Controllers\WaliKelas\AcademicController::class, 'nonakademik'])->name('nonakademik.index');
    Route::post('/evaluasi-nonakademik', [\App\Http\Controllers\WaliKelas\AcademicController::class, 'saveEvaluation'])->name('nonakademik.store');
    Route::get('/rapor-saya', [\App\Http\Controllers\WaliKelas\AcademicController::class, 'rapor'])->name('rapor.index');
    Route::get('/rapor-saya/{id}', [\App\Http\Controllers\WaliKelas\AcademicController::class, 'show'])->name('rapor.show');
    Route::get('/rapor-saya/{id}/cetak', [\App\Http\Controllers\WaliKelas\AcademicController::class, 'cetak'])->name('rapor.cetak');
    Route::post('/rapor-saya/{id}/ajukan', [\App\Http\Controllers\WaliKelas\AcademicController::class, 'submit'])->name('rapor.submit');
    Route::get('/distribusi', [\App\Http\Controllers\WaliKelas\AcademicController::class, 'distribusi'])->name('distribusi.index');
    Route::post('/distribusi/{id}/mark-sent', [\App\Http\Controllers\WaliKelas\AcademicController::class, 'markSent'])->name('distribusi.mark-sent');
    Route::get('/arsip', [\App\Http\Controllers\ReportController::class, 'arsip'])->name('arsip.index');
});

// Document IDs always identify reports.
Route::middleware(['auth', 'role:admin,kepala_sekolah,wali_kelas'])->group(function () {
    Route::get('/dokumen-rapor/{id}', [\App\Http\Controllers\ReportController::class, 'show'])->name('reports.show');
    Route::get('/dokumen-rapor/{id}/cetak', [\App\Http\Controllers\ReportController::class, 'cetak'])->name('reports.cetak');
});
Route::get('/rapor-penerima/{token}', [\App\Http\Controllers\ReportController::class, 'shared'])
    ->where('token', '[A-Za-z0-9]{64}')->middleware('throttle:30,1')->name('reports.shared');
Route::middleware(['auth', 'role:kepala_sekolah'])->group(function () {
    Route::get('/kepala-sekolah/leger/export', [\App\Http\Controllers\KepalaSekolah\ExportController::class, 'export'])->name('kepala.leger.export');
    Route::post('/kepala-sekolah/persetujuan/{id}/return-legacy', [\App\Http\Controllers\KepalaSekolah\ReviewController::class, 'returnLegacy'])->name('kepala.persetujuan.return-legacy');
    Route::post('/kepala-sekolah/persetujuan/{id}/finalize', [\App\Http\Controllers\KepalaSekolah\ReviewController::class, 'finalize'])->name('kepala.persetujuan.finalize');
});

Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/admin/ekskul-penugasan', [\App\Http\Controllers\Admin\AssignmentController::class,'ekskul'])->name('admin.ekskul.penugasan');
    Route::post('/admin/ekskul-penugasan', [\App\Http\Controllers\Admin\AssignmentController::class,'saveEkskul'])->name('admin.ekskul.penugasan.store');
    Route::put('/admin/tahun-ajaran/{id}', [\App\Http\Controllers\Admin\SettingsController::class,'saveYear'])->name('admin.tahun-ajaran.update');
    Route::get('/admin/akun/{id}/edit', [\App\Http\Controllers\Admin\SettingsController::class,'account'])->name('admin.akun.edit');
    Route::put('/admin/akun/{id}', [\App\Http\Controllers\Admin\SettingsController::class,'saveAccount'])->name('admin.akun.update');
});
Route::get('/logo-sekolah', function () {
    $path = \App\Models\Sekolah::instance()->logo;
    if ($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        return response()->file(\Illuminate\Support\Facades\Storage::disk('public')->path($path));
    }
    return response()->file(public_path('images/logo-slb.png'));
})->name('school.logo');
