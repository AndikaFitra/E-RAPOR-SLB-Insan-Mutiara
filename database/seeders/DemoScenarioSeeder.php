<?php

namespace Database\Seeders;

use App\Models\DistribusiRapor;
use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerSiswa;
use App\Models\EvaluasiNonakademik;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\KelasMataPelajaran;
use App\Models\NilaiAsesmen;
use App\Models\OrangTua;
use App\Models\PenugasanSiswaKelas;
use App\Models\PenugasanWaliKelas;
use App\Models\PrestasiSiswa;
use App\Models\Rapor;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\RaporService;
use Illuminate\Database\Seeder;

class DemoScenarioSeeder extends Seeder
{
    public function run(): void
    {
        $periode = TahunAjaran::getAktif() ?? throw new \RuntimeException('Aktifkan tahun ajaran sebelum membuat data demo.');
        $service = app(RaporService::class);
        $this->prepareActiveClasses($periode);

        // Setiap wali kelas mendapat empat kondisi rapor untuk didemokan.
        $scenarios = [
            ['TU-A', 'Tunarungu', 'VII', 'D', 'Demo Dira Tunarungu', '0999001001', 'draft', null],
            ['TU-A', 'Tunarungu', 'VIII', 'D', 'Demo Elin Tunarungu', '0999001002', 'dikembalikan', null],
            ['TU-A', 'Tunarungu', 'IX', 'D', 'Demo Faris Tunarungu', '0999001003', 'diajukan', null],
            ['TU-A', 'Tunarungu', 'XII', 'F', 'Demo Gina Tunarungu', '0999001004', 'disetujui', 'terkirim'],
            ['TG-A', 'Tunagrahita', 'VII', 'D', 'Demo Hani Tunagrahita', '0999001005', 'draft', null],
            ['TG-A', 'Tunagrahita', 'VIII', 'D', 'Demo Iqbal Tunagrahita', '0999001006', 'dikembalikan', null],
            ['TG-A', 'Tunagrahita', 'IX', 'D', 'Demo Jihan Tunagrahita', '0999001007', 'diajukan', null],
            ['TG-A', 'Tunagrahita', 'X', 'E', 'Demo Kamil Tunagrahita', '0999001008', 'disetujui', 'gagal'],
            ['AU-A', 'Autis', 'VII', 'D', 'Demo Lila Autis', '0999001009', 'draft', null],
            ['AU-A', 'Autis', 'VIII', 'D', 'Demo Malik Autis', '0999001010', 'dikembalikan', null],
            ['AU-A', 'Autis', 'IX', 'D', 'Demo Nara Autis', '0999001011', 'diajukan', null],
            ['AU-A', 'Autis', 'X', 'E', 'Demo Oki Autis', '0999001012', 'disetujui', 'pending'],
        ];

        foreach ($scenarios as [$kode, $kekhususan, $tingkat, $fase, $nama, $nisn, $status, $distribusi]) {
            $kelas = Kelas::where('kode', $kode)->firstOrFail();
            $wali = PenugasanWaliKelas::where('kelas_id', $kelas->id)
                ->where('tahun_ajaran_id', $periode->id)->firstOrFail();
            $siswa = Siswa::updateOrCreate(['nisn' => $nisn], [
                'nama_lengkap' => $nama,
                'jenis_kelamin' => in_array($nisn, ['0999001003', '0999001006', '0999001008', '0999001010', '0999001012']) ? 'L' : 'P',
                'tempat_lahir' => 'Pekanbaru',
                'tgl_lahir' => '2011-05-12',
                'kekhususan' => $kekhususan,
                'is_aktif' => true,
            ]);
            PenugasanSiswaKelas::updateOrCreate(
                ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $periode->id],
                ['kelas_id' => $kelas->id, 'tingkat' => $tingkat, 'fase' => $fase]
            );
            $orangTua = OrangTua::updateOrCreate(
                ['siswa_id' => $siswa->id, 'nama' => 'Wali '.$nama],
                ['hubungan' => 'Wali', 'no_whatsapp' => '0812000'.substr($nisn, -6), 'is_penerima_utama' => true]
            );
            $rapor = Rapor::firstOrCreate(
                ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $periode->id],
                ['wali_kelas_id' => $wali->guru_id, 'status' => Rapor::STATUS_DRAFT]
            );

            if ($status === Rapor::STATUS_DRAFT) {
                // Satu nilai tanpa narasi membuat contoh rapor yang perlu dilengkapi.
                $mapel = $service->mapel($kelas, $periode->id)->first();
                if ($mapel) {
                    NilaiAsesmen::updateOrCreate(
                        ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $periode->id, 'mata_pelajaran_id' => $mapel->id],
                        ['nilai_akhir' => 76, 'narasi_capaian' => null, 'is_locked' => false]
                    );
                }
                $rapor->update(['status' => $status, 'wali_kelas_id' => $wali->guru_id]);
                continue;
            }

            $this->completeReport($siswa, $kelas, $periode, $rapor, $service);
            if ($status === Rapor::STATUS_DISETUJUI) {
                if (!$rapor->snapshot || !$rapor->pdf_path) {
                    $rapor->update(['status' => Rapor::STATUS_DIAJUKAN, 'wali_kelas_id' => $wali->guru_id]);
                    $service->decide($rapor, true);
                }
                DistribusiRapor::updateOrCreate(
                    ['rapor_id' => $rapor->id, 'orang_tua_id' => $orangTua->id],
                    ['status_kirim' => $distribusi, 'tgl_kirim' => $distribusi === 'terkirim' ? now()->subDay() : null,
                        'catatan' => $distribusi === 'gagal' ? 'Nomor penerima perlu diverifikasi ulang.' : 'Data demo distribusi rapor.']
                );
            } else {
                $rapor->update([
                    'status' => $status,
                    'wali_kelas_id' => $wali->guru_id,
                    'tgl_pengajuan' => now()->subDay(),
                    'catatan_revisi' => $status === Rapor::STATUS_DIKEMBALIKAN
                        ? 'Perjelas narasi capaian sebelum diajukan ulang.' : null,
                ]);
                $locked = $status === Rapor::STATUS_DIAJUKAN;
                NilaiAsesmen::where('siswa_id', $siswa->id)->where('tahun_ajaran_id', $periode->id)->update(['is_locked' => $locked]);
                EvaluasiNonakademik::where('siswa_id', $siswa->id)->where('tahun_ajaran_id', $periode->id)->update(['is_locked' => $locked]);
            }
        }

        // Dua kondisi admin yang tidak mengganggu penugasan kelas dan rapor.
        Siswa::updateOrCreate(['nisn' => '0999001013'], [
            'nama_lengkap' => 'Demo Siswa Belum Ditugaskan', 'jenis_kelamin' => 'P',
            'kekhususan' => 'Tunarungu', 'is_aktif' => true,
        ]);
        Siswa::updateOrCreate(['nisn' => '0999001014'], [
            'nama_lengkap' => 'Demo Siswa Nonaktif', 'jenis_kelamin' => 'L',
            'kekhususan' => 'Autis', 'is_aktif' => false,
        ]);
        Guru::updateOrCreate(['nip' => '099900000000000001'], [
            'nama_lengkap' => 'Demo Guru Tanpa Penugasan', 'jenis_kelamin' => 'P',
            'jabatan' => 'Guru Demo', 'no_whatsapp' => '081200000000',
        ]);

        $this->historicalReport($periode, $service);
    }

    private function completeReport(Siswa $siswa, Kelas $kelas, TahunAjaran $periode, Rapor $rapor, RaporService $service): void
    {
        foreach ($service->mapel($kelas, $periode->id) as $index => $mapel) {
            NilaiAsesmen::updateOrCreate(
                ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $periode->id, 'mata_pelajaran_id' => $mapel->id],
                ['nilai_akhir' => 78 + ($index % 16),
                    'narasi_capaian' => 'Menunjukkan kemajuan dalam '.$mapel->nama.' melalui latihan yang terarah.']
            );
        }
        EvaluasiNonakademik::updateOrCreate(
            ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $periode->id],
            ['sakit' => 1, 'izin' => 1, 'alpa' => 0,
                'perkembangan' => 'Semakin mandiri mengikuti kegiatan belajar dan berinteraksi di kelas.',
                'tidak_ada_prestasi' => $siswa->nisn !== '0999001004']
        );
        if ($siswa->nisn === '0999001004') {
            PrestasiSiswa::updateOrCreate(
                ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $periode->id,
                    'kegiatan' => 'Pameran karya siswa sekolah'],
                ['keterangan' => 'Menampilkan karya dengan percaya diri.']
            );
        }
        $ekskul = Ekstrakurikuler::first();
        if ($ekskul) {
            EkstrakurikulerSiswa::updateOrCreate(
                ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $periode->id, 'ekstrakurikuler_id' => $ekskul->id],
                ['keterangan' => 'Baik: aktif mengikuti kegiatan dan mampu bekerja sama.']
            );
        }
        $rapor->update([
            'catatan_wali_kelas' => 'Teruskan latihan mandiri di rumah dan pertahankan kemajuan belajar.',
            'tgl_rapor' => $this->reportDate($periode),
        ]);
    }

    private function prepareActiveClasses(TahunAjaran $periode): void
    {
        $template = TahunAjaran::where('tahun', '2026/2027')->where('semester', 'Ganjil')->firstOrFail();
        foreach (['TU-A', 'TG-A', 'AU-A'] as $kode) {
            $kelas = Kelas::where('kode', $kode)->firstOrFail();
            $wali = PenugasanWaliKelas::where('kelas_id', $kelas->id)
                ->where('tahun_ajaran_id', $periode->id)->first();
            if (!$wali) {
                $guruId = PenugasanWaliKelas::where('kelas_id', $kelas->id)
                    ->where('tahun_ajaran_id', $template->id)->value('guru_id');
                if (!$guruId) throw new \RuntimeException("Wali kelas contoh untuk {$kode} belum tersedia.");
                PenugasanWaliKelas::firstOrCreate(
                    ['kelas_id' => $kelas->id, 'tahun_ajaran_id' => $periode->id],
                    ['guru_id' => $guruId]
                );
            }
            if (!KelasMataPelajaran::where('kelas_id', $kelas->id)->where('tahun_ajaran_id', $periode->id)->exists()) {
                $mapelIds = KelasMataPelajaran::where('kelas_id', $kelas->id)
                    ->where('tahun_ajaran_id', $template->id)->pluck('mata_pelajaran_id');
                foreach ($mapelIds as $mapelId) {
                    KelasMataPelajaran::firstOrCreate([
                        'kelas_id' => $kelas->id,
                        'mata_pelajaran_id' => $mapelId,
                        'tahun_ajaran_id' => $periode->id,
                    ]);
                }
            }
        }
    }

    private function reportDate(TahunAjaran $periode): string
    {
        [$mulai, $akhir] = explode('/', $periode->tahun);
        return $periode->semester === 'Ganjil' ? $mulai.'-12-19' : $akhir.'-06-20';
    }

    private function historicalReport(TahunAjaran $aktif, RaporService $service): void
    {
        $lama = TahunAjaran::where('tahun', '2025/2026')->where('semester', 'Genap')->firstOrFail();
        $siswa = Siswa::where('nisn', '0999001004')->firstOrFail();
        $kelas = Kelas::where('kode', 'TU-A')->firstOrFail();
        $wali = PenugasanWaliKelas::where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $aktif->id)->firstOrFail();
        PenugasanWaliKelas::updateOrCreate(
            ['kelas_id' => $kelas->id, 'tahun_ajaran_id' => $lama->id], ['guru_id' => $wali->guru_id]
        );
        PenugasanSiswaKelas::updateOrCreate(
            ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $lama->id],
            ['kelas_id' => $kelas->id, 'tingkat' => 'XI', 'fase' => 'F']
        );
        foreach ($service->mapel($kelas, $aktif->id) as $mapel) {
            KelasMataPelajaran::firstOrCreate([
                'kelas_id' => $kelas->id, 'mata_pelajaran_id' => $mapel->id, 'tahun_ajaran_id' => $lama->id,
            ]);
        }
        $rapor = Rapor::firstOrCreate(
            ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $lama->id],
            ['wali_kelas_id' => $wali->guru_id, 'status' => Rapor::STATUS_DIAJUKAN]
        );
        if ($rapor->snapshot && $rapor->pdf_path) return;
        $this->completeReport($siswa, $kelas, $lama, $rapor, $service);
        $rapor->update(['status' => Rapor::STATUS_DIAJUKAN, 'tgl_rapor' => '2026-06-20']);
        $service->decide($rapor, true);
    }
}
