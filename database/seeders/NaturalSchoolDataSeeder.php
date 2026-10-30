<?php

namespace Database\Seeders;

use App\Models\{DistribusiRapor, Ekstrakurikuler, EkstrakurikulerSiswa, EvaluasiNonakademik, Guru, Kelas, KelasMataPelajaran, NilaiAsesmen, OrangTua, PenugasanSiswaKelas, PenugasanWaliKelas, PrestasiSiswa, Rapor, Siswa, TahunAjaran};
use App\Services\RaporService;
use Illuminate\Database\Seeder;

class NaturalSchoolDataSeeder extends Seeder
{
    public function run(): void
    {
        $periods = TahunAjaran::all()->sortBy(fn ($period) => $period->tahun.'-'.($period->semester === 'Ganjil' ? '1' : '2'));
        if ($periods->isEmpty()) throw new \RuntimeException('Tahun ajaran harus tersedia sebelum data contoh dibuat.');
        $service = app(RaporService::class);

        // NISN lama dipakai hanya untuk memperbarui baris contoh sebelumnya tanpa duplikat.
        $students = [
            ['TU-A', 'Tunarungu', 7, 'Dira Maharani', '0999001001', '0160003001', 'P', '2012-04-18', 'Rita Maharani'],
            ['TU-A', 'Tunarungu', 8, 'Elin Safitri', '0999001002', '0150003002', 'P', '2011-09-02', 'Yanti Safitri'],
            ['TU-A', 'Tunarungu', 9, 'Faris Ramadhan', '0999001003', '0140003003', 'L', '2010-02-23', 'Sulastri'],
            ['TU-A', 'Tunarungu', 10, 'Gina Permata', '0999001004', '0130003004', 'P', '2009-12-08', 'Maya Permata'],
            ['TU-A', 'Tunarungu', 10, 'Raka Pradana', '0120003015', '0120003015', 'L', '2009-05-17', 'Nina Pradana'],
            ['TG-A', 'Tunagrahita', 7, 'Hanifa Zahra', '0999001005', '0160003005', 'P', '2012-06-10', 'Nurhayati'],
            ['TG-A', 'Tunagrahita', 8, 'Iqbal Maulana', '0999001006', '0150003006', 'L', '2011-03-14', 'Rini Maulana'],
            ['TG-A', 'Tunagrahita', 9, 'Jihan Amelia', '0999001007', '0140003007', 'P', '2010-10-21', 'Desi Amelia'],
            ['TG-A', 'Tunagrahita', 10, 'Kamil Pratama', '0999001008', '0130003008', 'L', '2009-07-04', 'Sri Wahyuni'],
            ['TG-A', 'Tunagrahita', 10, 'Salma Azzahra', '0120003016', '0120003016', 'P', '2009-03-22', 'Dina Azzahra'],
        ];
        $statuses = [Rapor::STATUS_DRAFT, Rapor::STATUS_DIKEMBALIKAN, Rapor::STATUS_DIAJUKAN, Rapor::STATUS_DISETUJUI, Rapor::STATUS_DISETUJUI];
        $delivery = ['0130003004' => 'terkirim', '0120003015' => 'pending', '0130003008' => 'gagal', '0120003016' => 'terkirim'];

        $this->retirePreviousSamples();

        foreach ($periods as $period) {
            $this->prepareClasses($period);
            foreach ($students as $index => [$code, $specialty, $baseLevel, $name, $oldNisn, $nisn, $gender, $birth, $parentName]) {
                $class = Kelas::where('kode', $code)->firstOrFail();
                $wali = PenugasanWaliKelas::where('kelas_id', $class->id)->where('tahun_ajaran_id', $period->id)->firstOrFail();
                $student = Siswa::where('nisn', $nisn)->orWhere('nisn', $oldNisn)->first() ?? new Siswa();
                $student->fill([
                    'nisn' => $nisn, 'nama_lengkap' => $name, 'jenis_kelamin' => $gender,
                    'tempat_lahir' => 'Pekanbaru', 'tgl_lahir' => $birth,
                    'kekhususan' => $specialty, 'is_aktif' => true,
                ])->save();
                $level = min(12, $baseLevel + max(0, (int) substr($period->tahun, 0, 4) - 2025));
                $phase = $level <= 9 ? 'D' : ($level <= 11 ? 'E' : 'F');
                PenugasanSiswaKelas::updateOrCreate(
                    ['siswa_id' => $student->id, 'tahun_ajaran_id' => $period->id],
                    ['kelas_id' => $class->id, 'tingkat' => $this->roman($level), 'fase' => $phase]
                );
                $parent = OrangTua::where('siswa_id', $student->id)->first() ?? new OrangTua();
                $parent->fill([
                    'siswa_id' => $student->id, 'nama' => $parentName, 'hubungan' => 'Ibu',
                    'no_whatsapp' => '081268'.substr($nisn, -6), 'is_penerima_utama' => true,
                ])->save();
                $report = Rapor::firstOrCreate(
                    ['siswa_id' => $student->id, 'tahun_ajaran_id' => $period->id],
                    ['wali_kelas_id' => $wali->guru_id, 'status' => Rapor::STATUS_DRAFT]
                );
                $status = $statuses[$index % 5];
                $report->update(['wali_kelas_id' => $wali->guru_id, 'tgl_rapor' => $this->reportDate($period)]);

                if ($status === Rapor::STATUS_DRAFT) {
                    $subject = $service->mapel($class, $period->id)->first();
                    if ($subject) NilaiAsesmen::updateOrCreate(
                        ['siswa_id' => $student->id, 'tahun_ajaran_id' => $period->id, 'mata_pelajaran_id' => $subject->id],
                        ['nilai_akhir' => 85, 'narasi_capaian' => null, 'is_locked' => false]
                    );
                    $report->update(['status' => $status, 'tgl_pengajuan' => null]);
                    continue;
                }

                $this->completeReport($student, $class, $period, $report, $service, $index);
                if ($status === Rapor::STATUS_DISETUJUI) {
                    if (!$report->snapshot || !$report->pdf_path || str_contains(json_encode($report->snapshot), 'Demo ')) {
                        $report->update(['status' => Rapor::STATUS_DIAJUKAN, 'tgl_pengajuan' => $this->reportDate($period)]);
                        $service->decide($report, true);
                    }
                    DistribusiRapor::updateOrCreate(
                        ['rapor_id' => $report->id, 'orang_tua_id' => $parent->id],
                        ['status_kirim' => $delivery[$nisn],
                            'tgl_kirim' => $delivery[$nisn] === 'terkirim' ? $this->reportDate($period) : null,
                            'catatan' => $delivery[$nisn] === 'gagal' ? 'Nomor penerima perlu diperiksa kembali.' : 'Pengiriman rapor tercatat.']
                    );
                } else {
                    $report->update([
                        'status' => $status, 'tgl_pengajuan' => $this->reportDate($period),
                        'catatan_revisi' => $status === Rapor::STATUS_DIKEMBALIKAN
                            ? 'Perjelas narasi capaian pada mata pelajaran Bahasa Indonesia.' : null,
                    ]);
                    $locked = $status === Rapor::STATUS_DIAJUKAN;
                    NilaiAsesmen::where('siswa_id', $student->id)->where('tahun_ajaran_id', $period->id)->update(['is_locked' => $locked]);
                    EvaluasiNonakademik::where('siswa_id', $student->id)->where('tahun_ajaran_id', $period->id)->update(['is_locked' => $locked]);
                }
            }
        }
        $this->otherSchoolRecords();
    }

    private function completeReport(Siswa $student, Kelas $class, TahunAjaran $period, Rapor $report, RaporService $service, int $index): void
    {
        foreach ($service->mapel($class, $period->id) as $subject) {
            NilaiAsesmen::updateOrCreate(
                ['siswa_id' => $student->id, 'tahun_ajaran_id' => $period->id, 'mata_pelajaran_id' => $subject->id],
                ['nilai_akhir' => 85,
                    'narasi_capaian' => $student->nama_lengkap.' menunjukkan perkembangan bertahap dalam '.$subject->nama.' melalui latihan di kelas.']
            );
        }
        EvaluasiNonakademik::updateOrCreate(
            ['siswa_id' => $student->id, 'tahun_ajaran_id' => $period->id],
            ['sakit' => $index % 3, 'izin' => ($index + 1) % 2, 'alpa' => 0,
                'perkembangan' => $student->nama_lengkap.' semakin mandiri mengikuti kegiatan belajar dan berinteraksi dengan teman.',
                'tidak_ada_prestasi' => $index !== 3]
        );
        if ($index === 3) PrestasiSiswa::updateOrCreate(
            ['siswa_id' => $student->id, 'tahun_ajaran_id' => $period->id, 'kegiatan' => 'Pameran karya siswa sekolah'],
            ['keterangan' => 'Menampilkan karya dengan percaya diri.']
        );
        if ($extra = Ekstrakurikuler::first()) EkstrakurikulerSiswa::updateOrCreate(
            ['siswa_id' => $student->id, 'tahun_ajaran_id' => $period->id, 'ekstrakurikuler_id' => $extra->id],
            ['keterangan' => 'Aktif mengikuti kegiatan dan mampu bekerja sama.']
        );
        $report->update([
            'catatan_wali_kelas' => 'Pertahankan kemajuan belajar dan lanjutkan latihan mandiri di rumah.',
            'tgl_rapor' => $this->reportDate($period),
        ]);
    }

    private function prepareClasses(TahunAjaran $period): void
    {
        $template = TahunAjaran::where('tahun', '2026/2027')->where('semester', 'Ganjil')->firstOrFail();
        foreach (['TU-A', 'TG-A'] as $code) {
            $class = Kelas::where('kode', $code)->firstOrFail();
            if (!PenugasanWaliKelas::where('kelas_id', $class->id)->where('tahun_ajaran_id', $period->id)->exists()) {
                $guruId = PenugasanWaliKelas::where('kelas_id', $class->id)->where('tahun_ajaran_id', $template->id)->value('guru_id');
                if (!$guruId) throw new \RuntimeException("Wali kelas untuk {$code} belum tersedia.");
                PenugasanWaliKelas::firstOrCreate(
                    ['kelas_id' => $class->id, 'tahun_ajaran_id' => $period->id], ['guru_id' => $guruId]
                );
            }
            $subjectIds = KelasMataPelajaran::where('kelas_id', $class->id)
                ->where('tahun_ajaran_id', $template->id)->pluck('mata_pelajaran_id');
            foreach ($subjectIds as $subjectId) KelasMataPelajaran::firstOrCreate([
                'kelas_id' => $class->id, 'mata_pelajaran_id' => $subjectId, 'tahun_ajaran_id' => $period->id,
            ]);
        }
    }

    private function retirePreviousSamples(): void
    {
        // Sembunyikan hanya empat siswa contoh Autis dari seeder lama; arsip fisiknya tetap ada.
        Siswa::whereIn('nisn', [
            '0160003009', '0150003010', '0140003011', '0130003012',
            '0999001009', '0999001010', '0999001011', '0999001012',
        ])->where(function ($query) {
            $query->where('nama_lengkap', 'like', 'Demo %')
                ->orWhereIn('nama_lengkap', ['Lila Anindya', 'Malik Akbar', 'Nara Kirana', 'Oki Saputra']);
        })->delete();

        $classId = Kelas::where('kode', 'AU-A')->value('id');
        $guruId = Guru::where('nip', '199411082019032009')->value('id');
        if ($classId && $guruId) {
            PenugasanWaliKelas::where('kelas_id', $classId)->where('guru_id', $guruId)->delete();
        }
    }

    private function otherSchoolRecords(): void
    {
        foreach ([
            ['0999001013', '0160003013', 'Nabila Fauziah', 'P', 'Tunarungu', true],
            ['0999001014', '0150003014', 'Rizky Ramadhan', 'L', 'Autis', false],
        ] as [$oldNisn, $nisn, $name, $gender, $specialty, $active]) {
            $student = Siswa::where('nisn', $nisn)->orWhere('nisn', $oldNisn)->first() ?? new Siswa();
            $student->fill(['nisn' => $nisn, 'nama_lengkap' => $name, 'jenis_kelamin' => $gender,
                'kekhususan' => $specialty, 'is_aktif' => $active])->save();
        }
        $guru = Guru::where('nip', '198912082015032004')->orWhere('nip', '099900000000000001')->first() ?? new Guru();
        $guru->fill(['nip' => '198912082015032004', 'nama_lengkap' => 'Mira Susanti, S.Pd.',
            'jenis_kelamin' => 'P', 'jabatan' => 'Guru Pendamping', 'no_whatsapp' => '081268553344'])->save();
    }

    private function reportDate(TahunAjaran $period): string
    {
        [$start, $end] = explode('/', $period->tahun);
        return $period->semester === 'Ganjil' ? $start.'-12-19' : $end.'-06-20';
    }

    private function roman(int $level): string
    {
        return [7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'][$level];
    }
}
