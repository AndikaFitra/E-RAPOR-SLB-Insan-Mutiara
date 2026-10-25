<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\OrangTua;
use App\Models\Rapor;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

echo "=== SUITE TEST SELURUH FITUR & WA.ME INTEGRATION ===\n\n";

$pass = 0;
$fail = 0;

function assertCondition($cond, $desc) {
    global $pass, $fail;
    if ($cond) {
        echo " [PASS] $desc\n";
        $pass++;
    } else {
        echo " [FAIL] $desc\n";
        $fail++;
    }
}

// 1. Test WhatsAppService
$phone1 = WhatsAppService::formatPhoneNumber('08127001001');
assertCondition($phone1 === '628127001001', "formatPhoneNumber 0812... -> 62812...");

$phone2 = WhatsAppService::formatPhoneNumber('+62 813-7788-2211');
assertCondition($phone2 === '6281377882211', "formatPhoneNumber +62 813... -> 62813...");

$siswaAlya = Siswa::where('nama_lengkap', 'Alya Putri')->first();
$msg = WhatsAppService::generateLegerMessage($siswaAlya, '84,6', 'Tuntas', 'Rina Pratiwi, S.Pd.');
assertCondition(str_contains($msg, 'Alya Putri') && str_contains($msg, 'PKPBI') && str_contains($msg, 'Tunarungu'), "Pesan WhatsApp memuat Nama, PKPBI, dan Tunarungu");

$waUrl = WhatsAppService::generateWaUrl('08127001001', $msg);
assertCondition(str_starts_with($waUrl, 'https://wa.me/628127001001?text='), "wa.me link format sesuai standar WhatsApp");

// 2. Test Kepala Sekolah Leger Controller
$kepsekUser = User::where('role', 'kepala_sekolah')->first();
auth()->login($kepsekUser);

$legerCtrl = new App\Http\Controllers\KepalaSekolah\LegerController();
$legerView = $legerCtrl->index(new Request());
$dataLeger = $legerView->getData()['dataLeger'];
assertCondition(count($dataLeger) >= 5, "Leger Kepala Sekolah memuat " . count($dataLeger) . " data siswa dari DB");
assertCondition(isset($dataLeger[0]['wa_url']) && str_contains($dataLeger[0]['wa_url'], 'https://wa.me/'), "Data Leger memuat wa_url untuk setiap siswa");

// 3. Test Wali Kelas Distribusi Controller & markSent
$waliUser = User::where('role', 'wali_kelas')->first();
auth()->login($waliUser);

$distCtrl = new App\Http\Controllers\WaliKelas\DistribusiController();
$distView = $distCtrl->index(new Request());
$dataDist = $distView->getData()['dataDistribusi'];
assertCondition(count($dataDist) >= 5, "Distribusi Wali Kelas memuat " . count($dataDist) . " data distribusi");
assertCondition(isset($dataDist[0]['wa_url']) && str_contains($dataDist[0]['wa_url'], 'https://wa.me/'), "Data Distribusi memuat wa_url untuk orang tua");

$raporSample = Rapor::first();
$respMark = $distCtrl->markSent(new Request(), $raporSample->id);
assertCondition($respMark->isRedirect(), "markSent berhasil redirect dengan status terkirim");

// 4. Test Kepala Sekolah Persetujuan Approve & Reject
auth()->login($kepsekUser);
$persetujuanCtrl = new App\Http\Controllers\KepalaSekolah\PersetujuanController();

// Test Approve
$raporTest = Rapor::updateOrCreate(
    [
        'siswa_id'        => $siswaAlya->id,
        'tahun_ajaran_id' => TahunAjaran::getAktif()->id,
    ],
    [
        'wali_kelas_id'   => Guru::first()->id,
        'status'          => Rapor::STATUS_DIAJUKAN,
        'tgl_pengajuan'   => now(),
    ]
);

$respApprove = $persetujuanCtrl->approve(new Request(), $raporTest->id);
$raporTest->refresh();
assertCondition($raporTest->status === Rapor::STATUS_DISETUJUI && $raporTest->tgl_persetujuan !== null, "Approve Rapor berhasil mengubah status ke disetujui & timestamp persetujuan");

// Test Reject
$reqReject = new Request(['catatan_revisi' => 'Perbaiki capaian kompetensi Bahasa Indonesia']);
$respReject = $persetujuanCtrl->reject($reqReject, $raporTest->id);
$raporTest->refresh();
assertCondition($raporTest->status === Rapor::STATUS_DIKEMBALIKAN && str_contains($raporTest->catatan_revisi, 'Perbaiki'), "Reject Rapor berhasil mengubah status ke dikembalikan & menyimpan catatan revisi");

// Restore status to diajukan
$raporTest->update(['status' => Rapor::STATUS_DIAJUKAN]);

// 5. Test Admin Guru CRUD
$adminUser = User::where('role', 'admin')->first();
auth()->login($adminUser);
$guruCtrl = new App\Http\Controllers\Admin\GuruController();

$testNip = '1995' . substr(strval(time()), -6) . rand(10, 99);
$reqGuruStore = new Request([
    'nama_lengkap' => 'Guru Penguji, S.Pd',
    'nip' => $testNip,
    'jenis_kelamin' => 'L',
    'kekhususan' => 'Tunarungu, Tunagrahita, Autis',
    'no_whatsapp' => '081299998888',
    'email' => 'gurupenguji_' . time() . rand(100, 999) . '@slbinsanmutiara.sch.id',
    'status' => 'Aktif',
]);
$respGuruStore = $guruCtrl->store($reqGuruStore);
$createdGuru = Guru::where('nip', $testNip)->first();
assertCondition($createdGuru !== null, "Admin Guru Store berhasil membuat Guru baru di database");

$reqGuruUpdate = new Request([
    'nama_lengkap' => 'Guru Penguji Diedit, S.Pd, M.Pd',
    'nip' => $testNip,
    'jenis_kelamin' => 'L',
    'kekhususan' => 'Autis',
    'no_whatsapp' => '081299997777',
    'status' => 'Aktif',
]);
$guruCtrl->update($reqGuruUpdate, $createdGuru->id);
$createdGuru->refresh();
assertCondition(str_contains($createdGuru->nama_lengkap, 'M.Pd') && $createdGuru->jabatan === 'Autis', "Admin Guru Update berhasil memperbarui nama & spesialisasi Autis");

$guruCtrl->destroy($createdGuru->id);
assertCondition(Guru::find($createdGuru->id) === null, "Admin Guru Destroy berhasil soft delete guru");
$createdGuru->forceDelete();

// 6. Test Admin Kelas CRUD
$kelasCtrl = new App\Http\Controllers\Admin\KelasController();
$reqKelasStore = new Request([
    'nama' => 'Autis B ' . time(),
    'kekhususan' => 'Autis',
    'kode' => 'AU-B' . rand(10, 99),
]);
$kelasCtrl->store($reqKelasStore);
$createdKelas = Kelas::where('nama', $reqKelasStore->nama)->first();
assertCondition($createdKelas !== null && $createdKelas->kekhususan === 'Autis', "Admin Kelas Store berhasil membuat kelas dengan ketunaan Autis");

$reqKelasUpdate = new Request([
    'nama' => $createdKelas->nama . ' Updated',
    'kekhususan' => 'Autis',
    'kode' => $createdKelas->kode,
]);
$kelasCtrl->update($reqKelasUpdate, $createdKelas->id);
$createdKelas->refresh();
assertCondition(str_contains($createdKelas->nama, 'Updated'), "Admin Kelas Update berhasil memperbarui kelas");

$kelasCtrl->destroy($createdKelas->id);
assertCondition(Kelas::find($createdKelas->id) === null, "Admin Kelas Destroy berhasil menghapus kelas");

// 7. Test Admin Mapel CRUD
$mapelCtrl = new App\Http\Controllers\Admin\MataPelajaranController();
$reqMapelStore = new Request([
    'kode' => 'TES' . rand(10, 99),
    'nama' => 'Mapel Pengujian SLB',
    'kelompok' => 'Program Kekhususan',
]);
$mapelCtrl->store($reqMapelStore);
$createdMapel = MataPelajaran::where('kode', $reqMapelStore->kode)->first();
assertCondition($createdMapel !== null && $createdMapel->kelompok === 'Program Kekhususan', "Admin Mapel Store berhasil");

$mapelCtrl->destroy($createdMapel->id);
assertCondition(MataPelajaran::find($createdMapel->id) === null, "Admin Mapel Destroy berhasil");

// 8. Test Admin Orang Tua CRUD
$otCtrl = new App\Http\Controllers\Admin\OrangTuaController();
$reqOtStore = new Request([
    'siswa_id' => $siswaAlya->id,
    'nama' => 'Orang Tua Uji',
    'hubungan' => 'Wali',
    'no_whatsapp' => '081288887777',
]);
$otCtrl->store($reqOtStore);
$createdOt = OrangTua::where('no_whatsapp', '081288887777')->first();
assertCondition($createdOt !== null && $createdOt->nama === 'Orang Tua Uji', "Admin Orang Tua Store berhasil");

$otCtrl->destroy($createdOt->id);
assertCondition(OrangTua::find($createdOt->id) === null, "Admin Orang Tua Destroy berhasil");

// 9. Test Admin Sekolah Update
$sekolahCtrl = new App\Http\Controllers\Admin\SekolahController();
$reqSekolah = new Request([
    'nama' => 'SLB Insan Mutiara Pekanbaru',
    'npsn' => '69900001',
    'kepala_sekolah' => 'Novia Parenri, S.Si, M.Si',
    'nip_kepala' => '198005122005012003',
    'alamat' => 'Jl. Adi Sucipto / Cendana No. 12',
    'email' => 'info@slbinsanmutiara.sch.id',
    'telepon' => '0761-123456',
]);
$sekolahCtrl->update($reqSekolah);
$sekolah = Sekolah::instance();
assertCondition($sekolah->kepala_sekolah === 'Novia Parenri, S.Si, M.Si', "Admin Sekolah Update berhasil memperbarui Novia Parenri, S.Si, M.Si");

// 10. Test Wali Kelas Rapor Submit
auth()->login($waliUser);
$raporSayaCtrl = new App\Http\Controllers\WaliKelas\RaporSayaController();
$raporSayaCtrl->submit(new Request(), $siswaAlya->id);
$raporAlya = Rapor::where('siswa_id', $siswaAlya->id)->first();
assertCondition($raporAlya->status === Rapor::STATUS_DIAJUKAN, "Wali Kelas submit rapor berhasil mengubah status ke Diajukan");

echo "\nHASIL PENGUJIAN: $pass LULUS, $fail GAGAL\n";
if ($fail === 0) {
    echo "🎉 SEMUA PENGUJIAN LULUS DENGAN SEMPURNA!\n";
}
