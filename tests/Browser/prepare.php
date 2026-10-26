<?php
// Rebuild only the dedicated browser-test SQLite database.
$root = dirname(__DIR__, 2);
$database = $root.'/storage/framework/testing/browser.sqlite';
if (!is_dir(dirname($database))) mkdir(dirname($database), 0777, true);
foreach (['APP_ENV'=>'testing','DB_CONNECTION'=>'sqlite','DB_DATABASE'=>$database,'DB_URL'=>'','CACHE_STORE'=>'array','SESSION_DRIVER'=>'file','APP_CONFIG_CACHE'=>$root.'/storage/framework/testing/browser-config.php'] as $key=>$value) {
    putenv("$key=$value"); $_ENV[$key]=$value; $_SERVER[$key]=$value;
}
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== $database) {
    throw new RuntimeException('Refusing to reset a database other than the browser fixture.');
}
if (!file_exists($database)) touch($database);
\Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force'=>true]);
\Illuminate\Support\Facades\Artisan::call('db:seed', ['--force'=>true]);
$student = \App\Models\Siswa::whereHas('penugasanKelas', fn($q)=>$q->where('kelas_id',1))->firstOrFail();
\App\Models\Rapor::where('siswa_id',$student->id)->update(['status'=>'draft']);
\App\Models\NilaiAsesmen::where('siswa_id',$student->id)->update(['is_locked'=>false]);
$readyStudent = \App\Models\Siswa::whereHas('penugasanKelas', fn($q)=>$q->where('kelas_id',1))->orderBy('id')->skip(1)->firstOrFail();
$period = \App\Models\TahunAjaran::getAktif();
$assignment = $readyStudent->penugasanKelas()->where('tahun_ajaran_id', $period->id)->firstOrFail();
$wali = \App\Models\PenugasanWaliKelas::where('kelas_id', $assignment->kelas_id)->where('tahun_ajaran_id', $period->id)->firstOrFail();
$subjects = \App\Models\KelasMataPelajaran::where('kelas_id', $assignment->kelas_id)->where('tahun_ajaran_id', $period->id)->pluck('mata_pelajaran_id');
foreach ($subjects as $subjectId) {
    \App\Models\NilaiAsesmen::updateOrCreate(['siswa_id'=>$readyStudent->id,'tahun_ajaran_id'=>$period->id,'mata_pelajaran_id'=>$subjectId], ['nilai_akhir'=>80,'narasi_capaian'=>'Capaian pembelajaran lengkap untuk pengujian dialog persetujuan.']);
}
\App\Models\EvaluasiNonakademik::updateOrCreate(['siswa_id'=>$readyStudent->id,'tahun_ajaran_id'=>$period->id], ['sakit'=>0,'izin'=>0,'alpa'=>0,'perkembangan'=>'Perkembangan individual terisi.','tidak_ada_prestasi'=>true]);
\App\Models\EkstrakurikulerSiswa::where('siswa_id',$readyStudent->id)->where('tahun_ajaran_id',$period->id)->update(['keterangan'=>'Aktif mengikuti kegiatan.']);
\App\Models\Rapor::updateOrCreate(['siswa_id'=>$readyStudent->id,'tahun_ajaran_id'=>$period->id], ['wali_kelas_id'=>$wali->guru_id,'status'=>'diajukan','catatan_wali_kelas'=>'Catatan wali untuk pengujian persetujuan.','tgl_rapor'=>'2026-12-19']);
echo "Browser fixture ready in storage/framework/testing/browser.sqlite\n";
