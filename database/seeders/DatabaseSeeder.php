<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerSiswa;
use App\Models\EvaluasiNonakademik;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\KelasMataPelajaran;
use App\Models\MataPelajaran;
use App\Models\NilaiAsesmen;
use App\Models\OrangTua;
use App\Models\PenugasanPembimbing;
use App\Models\PenugasanSiswaKelas;
use App\Models\PenugasanWaliKelas;
use App\Models\PrestasiSiswa;
use App\Models\Rapor;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data Sekolah (Sesuai Scan PDF Resmi)
        $sekolah = Sekolah::updateOrCreate(
            ['id' => 1],
            [
                'nama'           => 'SLB Insan Mutiara Pekanbaru',
                'npsn'           => '10497554',
                'alamat'         => 'Jalan Cendana No. 33A / Adi Sucipto',
                'kota'           => 'Pekanbaru',
                'provinsi'       => 'Riau',
                'kode_pos'       => '28293',
                'telepon'        => '(0761) 589123',
                'email'          => 'info@slbinsanmutiara.sch.id',
                'kepala_sekolah' => 'Novia Parenri, S.Si, M.Si',
                'nip_kepala'     => '198005122005012003',
                'logo'           => 'images/logo-slb.png',
            ]
        );

        // 2. Tahun Ajaran
        $thAktif = TahunAjaran::create([
            'tahun'    => '2026/2027',
            'semester' => 'Ganjil',
            'is_aktif' => true,
        ]);

        $thLalu = TahunAjaran::create([
            'tahun'    => '2025/2026',
            'semester' => 'Genap',
            'is_aktif' => false,
        ]);

        // 3. Data Guru
        $guruKepsek = Guru::create([
            'nip'           => '198005122005012003',
            'nama_lengkap'  => 'Novia Parenri, S.Si, M.Si',
            'jenis_kelamin' => 'P',
            'jabatan'       => 'Kepala Sekolah',
            'no_whatsapp'   => '081268112233',
        ]);

        $guruRina = Guru::create([
            'nip'           => '198803152011012014',
            'nama_lengkap'  => 'Rina Pratiwi, S.Pd.',
            'jenis_kelamin' => 'P',
            'jabatan'       => 'Guru Kelas Tunarungu',
            'no_whatsapp'   => '081371445566',
        ]);

        $guruAhmad = Guru::create([
            'nip'           => '199107242015021002',
            'nama_lengkap'  => 'Ahmad Fauzi, S.Pd.',
            'jenis_kelamin' => 'L',
            'jabatan'       => 'Guru Kelas Tunagrahita',
            'no_whatsapp'   => '082199887766',
        ]);

        $guruSiti = Guru::create([
            'nip'           => '199411082019032009',
            'nama_lengkap'  => 'Siti Nurhaliza, S.Pd.',
            'jenis_kelamin' => 'P',
            'jabatan'       => 'Guru Kelas Autis',
            'no_whatsapp'   => '085233445566',
        ]);

        $guruNova = Guru::create([
            'nip'           => '199002142014022005',
            'nama_lengkap'  => 'Nova Yani, S.Pd, Gr',
            'jenis_kelamin' => 'P',
            'jabatan'       => 'Guru Kelas SMALB',
            'no_whatsapp'   => '081277112288',
        ]);

        // 4. Akun Pengguna (Users)
        User::create([
            'name'     => 'Administrator Sistem',
            'email'    => 'admin@slbinsanmutiara.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'is_aktif' => true,
            'guru_id'  => null,
        ]);

        User::create([
            'name'     => 'Novia Parenri, S.Si, M.Si',
            'email'    => 'kepsek@slbinsanmutiara.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'kepala_sekolah',
            'is_aktif' => true,
            'guru_id'  => $guruKepsek->id,
        ]);

        User::create([
            'name'     => 'Rina Pratiwi, S.Pd.',
            'email'    => 'rinapratiwi@slbinsanmutiara.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'wali_kelas',
            'is_aktif' => true,
            'guru_id'  => $guruRina->id,
        ]);

        User::create([
            'name'     => 'Ahmad Fauzi, S.Pd.',
            'email'    => 'ahmadfauzi@slbinsanmutiara.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'wali_kelas',
            'is_aktif' => true,
            'guru_id'  => $guruAhmad->id,
        ]);

        User::create([
            'name'     => 'Akun Nonaktif Demo',
            'email'    => 'nonaktif@slbinsanmutiara.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'wali_kelas',
            'is_aktif' => false,
            'guru_id'  => null,
        ]);

        // 5. Data Kelas (3 Ketunaan di SLB Insan Mutiara: Tunarungu, Tunagrahita, Autis)
        $kelasTunarunguA = Kelas::create([
            'nama'       => 'Tunarungu A',
            'kekhususan' => 'Tunarungu',
            'kode'       => 'TU-A',
        ]);

        $kelasTunagrahitaA = Kelas::create([
            'nama'       => 'Tunagrahita A',
            'kekhususan' => 'Tunagrahita',
            'kode'       => 'TG-A',
        ]);

        $kelasAutisA = Kelas::create([
            'nama'       => 'Autis A',
            'kekhususan' => 'Autis',
            'kode'       => 'AU-A',
        ]);

        // 6. Mata Pelajaran (Kurikulum Merdeka Khusus SLB)
        $mapelList = [
            ['nama' => 'Pendidikan Agama dan Budi Pekerti', 'kode' => 'PABP', 'kelompok' => 'Umum'],
            ['nama' => 'Pendidikan Pancasila', 'kode' => 'PPKN', 'kelompok' => 'Umum'],
            ['nama' => 'Bahasa Indonesia', 'kode' => 'BIND', 'kelompok' => 'Umum'],
            ['nama' => 'Matematika', 'kode' => 'MTK', 'kelompok' => 'Umum'],
            ['nama' => 'Bahasa Inggris', 'kode' => 'BING', 'kelompok' => 'Umum'],
            ['nama' => 'Ilmu Pengetahuan Alam (IPA)', 'kode' => 'IPA', 'kelompok' => 'Umum'],
            ['nama' => 'Ilmu Pengetahuan Sosial (IPS)', 'kode' => 'IPS', 'kelompok' => 'Umum'],
            ['nama' => 'Seni Budaya (Seni Rupa)', 'kode' => 'SBR', 'kelompok' => 'Umum'],
            ['nama' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan', 'kode' => 'PJOK', 'kelompok' => 'Umum'],
            ['nama' => 'Keterampilan Pilihan: Suvenir', 'kode' => 'K-SUV', 'kelompok' => 'Keterampilan Pilihan'],
            ['nama' => 'Keterampilan Pilihan: Tata Kecantikan', 'kode' => 'K-TKC', 'kelompok' => 'Keterampilan Pilihan'],

            // 3 Program Kekhususan Berdasarkan 3 Ketunaan:
            ['nama' => 'Program Kekhususan (PKPBI)', 'kode' => 'PKPBI', 'kelompok' => 'Program Kekhususan'],
            ['nama' => 'Program Kekhususan (Bina Diri)', 'kode' => 'BINADIRI', 'kelompok' => 'Program Kekhususan'],
            ['nama' => 'Program Kekhususan (Pengembangan Komunikasi dan Interaksi)', 'kode' => 'PKISP', 'kelompok' => 'Program Kekhususan'],

            ['nama' => 'Muatan Lokal: Budaya Melayu Riau', 'kode' => 'BMR', 'kelompok' => 'Muatan Lokal'],
        ];

        $mapelModels = collect();
        foreach ($mapelList as $m) {
            $mapelModels->push(MataPelajaran::create($m));
        }

        // Hubungkan Mapel ke Kelas
        foreach ($mapelModels as $mapel) {
            KelasMataPelajaran::create([
                'kelas_id'          => $kelasTunarunguA->id,
                'mata_pelajaran_id' => $mapel->id,
                'tahun_ajaran_id'   => $thAktif->id,
            ]);
            KelasMataPelajaran::create([
                'kelas_id'          => $kelasTunagrahitaA->id,
                'mata_pelajaran_id' => $mapel->id,
                'tahun_ajaran_id'   => $thAktif->id,
            ]);
            KelasMataPelajaran::create([
                'kelas_id'          => $kelasAutisA->id,
                'mata_pelajaran_id' => $mapel->id,
                'tahun_ajaran_id'   => $thAktif->id,
            ]);
        }

        // 7. Ekstrakurikuler
        $ekskulPramuka = Ekstrakurikuler::create(['nama' => 'Pramuka']);
        $ekskulPantomim = Ekstrakurikuler::create(['nama' => 'Seni Pantomim & Tari Isyarat']);
        $ekskulBocce = Ekstrakurikuler::create(['nama' => 'Olahraga Bocce']);

        // 8. Data Siswa (3 Ketunaan di SLB Insan Mutiara)
        // Tunarungu
        $siswaAlya = Siswa::create([
            'nisn'          => '0160001001',
            'nama_lengkap'  => 'Alya Putri',
            'jenis_kelamin' => 'P',
            'tempat_lahir'  => 'Pekanbaru',
            'tgl_lahir'     => '2012-05-14',
            'kekhususan'    => 'Tunarungu',
            'no_hp'         => '082177889901',
            'is_aktif'      => true,
        ]);

        $siswaBima = Siswa::create([
            'nisn'          => '0150001002',
            'nama_lengkap'  => 'Bima Pratama',
            'jenis_kelamin' => 'L',
            'tempat_lahir'  => 'Kampar',
            'tgl_lahir'     => '2011-08-20',
            'kekhususan'    => 'Tunarungu',
            'no_hp'         => '082177889902',
            'is_aktif'      => true,
        ]);

        $siswaCitra = Siswa::create([
            'nisn'          => '0140001003',
            'nama_lengkap'  => 'Citra Lestari',
            'jenis_kelamin' => 'P',
            'tempat_lahir'  => 'Dumai',
            'tgl_lahir'     => '2010-03-12',
            'kekhususan'    => 'Tunarungu',
            'no_hp'         => '082177889903',
            'is_aktif'      => true,
        ]);

        $siswaDimas = Siswa::create([
            'nisn'          => '0160001004',
            'nama_lengkap'  => 'Dimas Saputra',
            'jenis_kelamin' => 'L',
            'tempat_lahir'  => 'Pekanbaru',
            'tgl_lahir'     => '2012-11-09',
            'kekhususan'    => 'Tunarungu',
            'no_hp'         => '082177889904',
            'is_aktif'      => true,
        ]);

        $siswaElsa = Siswa::create([
            'nisn'          => '0150001005',
            'nama_lengkap'  => 'Elsa Rahma',
            'jenis_kelamin' => 'P',
            'tempat_lahir'  => 'Siak',
            'tgl_lahir'     => '2011-01-25',
            'kekhususan'    => 'Tunarungu',
            'no_hp'         => '082177889905',
            'is_aktif'      => true,
        ]);

        $siswaFajar = Siswa::create([
            'nisn'          => '0140001006',
            'nama_lengkap'  => 'Fajar Ramadhan',
            'jenis_kelamin' => 'L',
            'tempat_lahir'  => 'Pekanbaru',
            'tgl_lahir'     => '2010-09-18',
            'kekhususan'    => 'Tunarungu',
            'no_hp'         => '082177889906',
            'is_aktif'      => true,
        ]);

        // Tunagrahita
        $siswaNadia = Siswa::create([
            'nisn'          => '0160001007',
            'nama_lengkap'  => 'Nadia Safira',
            'jenis_kelamin' => 'P',
            'tempat_lahir'  => 'Pekanbaru',
            'tgl_lahir'     => '2012-07-22',
            'kekhususan'    => 'Tunagrahita',
            'no_hp'         => '082177889907',
            'is_aktif'      => true,
        ]);

        $siswaMaya = Siswa::create([
            'nisn'          => '0098765434',
            'nama_lengkap'  => 'Maya Anggraini',
            'jenis_kelamin' => 'P',
            'tempat_lahir'  => 'Siak',
            'tgl_lahir'     => '2011-06-30',
            'kekhususan'    => 'Tunagrahita',
            'no_hp'         => '082177889908',
            'is_aktif'      => true,
        ]);

        // Autis
        $siswaRafi = Siswa::create([
            'nisn'          => '0150002001',
            'nama_lengkap'  => 'Rafi Al-Fathir',
            'jenis_kelamin' => 'L',
            'tempat_lahir'  => 'Pekanbaru',
            'tgl_lahir'     => '2011-10-15',
            'kekhususan'    => 'Autis',
            'no_hp'         => '082177889909',
            'is_aktif'      => true,
        ]);

        // Siswa dari Scan Rapor Asli
        $siswaFriska = Siswa::create([
            'nisn'          => '0053935552',
            'nama_lengkap'  => 'Friska Carolin Monalisa',
            'jenis_kelamin' => 'P',
            'tempat_lahir'  => 'Pekanbaru',
            'tgl_lahir'     => '2005-04-12',
            'kekhususan'    => 'Tunarungu',
            'no_hp'         => '082177889910',
            'is_aktif'      => true,
        ]);

        $siswaArumi = Siswa::create([
            'nisn'          => '0095723244',
            'nama_lengkap'  => 'Arumi Nur Azizah',
            'jenis_kelamin' => 'P',
            'tempat_lahir'  => 'Padang',
            'tgl_lahir'     => '2009-11-13',
            'kekhususan'    => 'Tunarungu',
            'no_hp'         => '085263592816',
            'is_aktif'      => true,
        ]);

        // 9. Orang Tua Siswa
        OrangTua::create([
            'siswa_id'          => $siswaAlya->id,
            'nama'              => 'Bambang Santoso',
            'hubungan'          => 'Ayah',
            'no_whatsapp'       => '08127001001',
            'is_penerima_utama' => true,
        ]);

        OrangTua::create([
            'siswa_id'          => $siswaBima->id,
            'nama'              => 'Ratna Sari',
            'hubungan'          => 'Ibu',
            'no_whatsapp'       => '081377882211',
            'is_penerima_utama' => true,
        ]);

        OrangTua::create([
            'siswa_id'          => $siswaCitra->id,
            'nama'              => 'H. Ridwan',
            'hubungan'          => 'Wali',
            'no_whatsapp'       => '082155667788',
            'is_penerima_utama' => true,
        ]);

        OrangTua::create([
            'siswa_id'          => $siswaDimas->id,
            'nama'              => 'Agus Saputra',
            'hubungan'          => 'Ayah',
            'no_whatsapp'       => '081266778899',
            'is_penerima_utama' => true,
        ]);

        OrangTua::create([
            'siswa_id'          => $siswaElsa->id,
            'nama'              => 'Nurhalimah',
            'hubungan'          => 'Ibu',
            'no_whatsapp'       => '081388990011',
            'is_penerima_utama' => true,
        ]);

        OrangTua::create([
            'siswa_id'          => $siswaFajar->id,
            'nama'              => 'Hendri Ramadhan',
            'hubungan'          => 'Ayah',
            'no_whatsapp'       => '085277889922',
            'is_penerima_utama' => true,
        ]);

        OrangTua::create([
            'siswa_id'          => $siswaNadia->id,
            'nama'              => 'Siti Aminah',
            'hubungan'          => 'Ibu',
            'no_whatsapp'       => '081277665544',
            'is_penerima_utama' => true,
        ]);

        OrangTua::create([
            'siswa_id'          => $siswaMaya->id,
            'nama'              => 'Kusnadi',
            'hubungan'          => 'Ayah',
            'no_whatsapp'       => '081366554433',
            'is_penerima_utama' => true,
        ]);

        OrangTua::create([
            'siswa_id'          => $siswaRafi->id,
            'nama'              => 'Dewi Lestari',
            'hubungan'          => 'Ibu',
            'no_whatsapp'       => '082266778811',
            'is_penerima_utama' => true,
        ]);

        OrangTua::create([
            'siswa_id'          => $siswaFriska->id,
            'nama'              => 'Ibu Monalisa',
            'hubungan'          => 'Ibu',
            'no_whatsapp'       => '081277112233',
            'is_penerima_utama' => true,
        ]);

        OrangTua::create([
            'siswa_id'          => $siswaArumi->id,
            'nama'              => 'Usman',
            'hubungan'          => 'Ayah',
            'no_whatsapp'       => '085263592816',
            'is_penerima_utama' => true,
        ]);

        // 10. Penugasan Wali Kelas
        PenugasanWaliKelas::create([
            'guru_id'         => $guruRina->id,
            'kelas_id'        => $kelasTunarunguA->id,
            'tahun_ajaran_id' => $thAktif->id,
        ]);

        PenugasanWaliKelas::create([
            'guru_id'         => $guruAhmad->id,
            'kelas_id'        => $kelasTunagrahitaA->id,
            'tahun_ajaran_id' => $thAktif->id,
        ]);

        PenugasanWaliKelas::create([
            'guru_id'         => $guruSiti->id,
            'kelas_id'        => $kelasAutisA->id,
            'tahun_ajaran_id' => $thAktif->id,
        ]);

        // 11. Penugasan Siswa ke Kelas
        PenugasanSiswaKelas::create([
            'siswa_id'        => $siswaAlya->id,
            'kelas_id'        => $kelasTunarunguA->id,
            'tahun_ajaran_id' => $thAktif->id,
            'tingkat'         => 'VII',
            'fase'            => 'D',
        ]);

        PenugasanSiswaKelas::create([
            'siswa_id'        => $siswaBima->id,
            'kelas_id'        => $kelasTunarunguA->id,
            'tahun_ajaran_id' => $thAktif->id,
            'tingkat'         => 'VIII',
            'fase'            => 'D',
        ]);

        PenugasanSiswaKelas::create([
            'siswa_id'        => $siswaCitra->id,
            'kelas_id'        => $kelasTunarunguA->id,
            'tahun_ajaran_id' => $thAktif->id,
            'tingkat'         => 'IX',
            'fase'            => 'D',
        ]);

        PenugasanSiswaKelas::create([
            'siswa_id'        => $siswaNadia->id,
            'kelas_id'        => $kelasTunagrahitaA->id,
            'tahun_ajaran_id' => $thAktif->id,
            'tingkat'         => 'VII',
            'fase'            => 'D',
        ]);

        PenugasanSiswaKelas::create([
            'siswa_id'        => $siswaRafi->id,
            'kelas_id'        => $kelasAutisA->id,
            'tahun_ajaran_id' => $thAktif->id,
            'tingkat'         => 'VII',
            'fase'            => 'D',
        ]);

        PenugasanSiswaKelas::create([
            'siswa_id'        => $siswaFriska->id,
            'kelas_id'        => $kelasTunarunguA->id,
            'tahun_ajaran_id' => $thAktif->id,
            'tingkat'         => 'XII',
            'fase'            => 'F',
        ]);

        // 12. Rapor & Nilai Asesmen Sampel
        $raporAlya = Rapor::create([
            'siswa_id'           => $siswaAlya->id,
            'tahun_ajaran_id'    => $thAktif->id,
            'wali_kelas_id'      => $guruRina->id,
            'status'             => Rapor::STATUS_DIAJUKAN,
            'catatan_wali_kelas' => 'Alya menunjukkan kemampuan luar biasa dalam pembelajaran visual dan komunikasi isyarat. Pertahankan semangat belajar yang tinggi.',
            'tgl_rapor'          => '2026-12-19',
            'tgl_pengajuan'      => now()->subHours(2),
        ]);

        // Nilai Asesmen Alya (Tunarungu: Menggunakan PKPBI)
        NilaiAsesmen::create([
            'siswa_id'          => $siswaAlya->id,
            'mata_pelajaran_id' => $mapelModels->firstWhere('kode', 'PABP')->id,
            'tahun_ajaran_id'   => $thAktif->id,
            'nilai_akhir'       => 90,
            'narasi_capaian'    => 'Ananda menunjukkan banyak perubahan positif dalam berinteraksi dan bertingkah laku terpuji sesuai nilai keagamaan.',
        ]);

        NilaiAsesmen::create([
            'siswa_id'          => $siswaAlya->id,
            'mata_pelajaran_id' => $mapelModels->firstWhere('kode', 'PKPBI')->id,
            'tahun_ajaran_id'   => $thAktif->id,
            'nilai_akhir'       => 98,
            'narasi_capaian'    => 'Ananda sangat baik dalam mengidentifikasi langsung sumber bunyi dan irama lingkungan menggunakan sisa pendengaran secara optimal.',
        ]);

        EvaluasiNonakademik::create([
            'siswa_id'           => $siswaAlya->id,
            'tahun_ajaran_id'    => $thAktif->id,
            'sakit'              => 0,
            'izin'               => 3,
            'alpa'               => 0,
            'perkembangan'       => 'Pertumbuhan fisik sangat baik, pendengaran terlatih dengan konsisten.',
            'tidak_ada_prestasi' => false,
        ]);

        PrestasiSiswa::create([
            'siswa_id'        => $siswaAlya->id,
            'tahun_ajaran_id' => $thAktif->id,
            'kegiatan'        => 'Lomba Memperingati Hari Disabilitas oleh DINSOS Pekanbaru',
            'keterangan'      => 'Juara 1',
        ]);

        PrestasiSiswa::create([
            'siswa_id'        => $siswaAlya->id,
            'tahun_ajaran_id' => $thAktif->id,
            'kegiatan'        => 'Lomba Mendongeng Bahasa Isyarat Bulan Bahasa FKIP UNRI',
            'keterangan'      => 'Juara Harapan 1',
        ]);

        // Rapor Bima (Disetujui)
        Rapor::create([
            'siswa_id'           => $siswaBima->id,
            'tahun_ajaran_id'    => $thAktif->id,
            'wali_kelas_id'      => $guruRina->id,
            'status'             => Rapor::STATUS_DISETUJUI,
            'catatan_wali_kelas' => 'Bima menunjukkan ketekunan tinggi dalam setiap sesi bina komunikasi.',
            'tgl_rapor'          => '2026-12-19',
            'tgl_pengajuan'      => now()->subDays(2),
            'tgl_persetujuan'    => now()->subDay(),
        ]);

        // Rapor Citra (Dikembalikan untuk revisi)
        Rapor::create([
            'siswa_id'           => $siswaCitra->id,
            'tahun_ajaran_id'    => $thAktif->id,
            'wali_kelas_id'      => $guruRina->id,
            'status'             => Rapor::STATUS_DIKEMBALIKAN,
            'catatan_revisi'     => 'Mohon perbaiki narasi capaian pada mata pelajaran Bahasa Indonesia agar lebih spesifik.',
            'catatan_wali_kelas' => 'Citra memiliki potensi artistik yang kuat dalam seni visual.',
            'tgl_rapor'          => '2026-12-19',
            'tgl_pengajuan'      => now()->subDays(3),
        ]);

        // Rapor Elsa (Diajukan)
        Rapor::create([
            'siswa_id'           => $siswaElsa->id,
            'tahun_ajaran_id'    => $thAktif->id,
            'wali_kelas_id'      => $guruRina->id,
            'status'             => Rapor::STATUS_DIAJUKAN,
            'catatan_wali_kelas' => 'Elsa sangat aktif dalam kegiatan kelas dan cepat memahami instruksi visual.',
            'tgl_rapor'          => '2026-12-19',
            'tgl_pengajuan'      => now()->subHours(5),
        ]);

        // Rapor Nadia (Tunagrahita - Diajukan)
        Rapor::create([
            'siswa_id'           => $siswaNadia->id,
            'tahun_ajaran_id'    => $thAktif->id,
            'wali_kelas_id'      => $guruAhmad->id,
            'status'             => Rapor::STATUS_DIAJUKAN,
            'catatan_wali_kelas' => 'Nadia mengalami kemajuan signifikan dalam kemandirian bina diri dan merapikan pakaian.',
            'tgl_rapor'          => '2026-12-19',
            'tgl_pengajuan'      => now()->subHours(4),
        ]);

        // Rapor Rafi (Autis - Diajukan)
        Rapor::create([
            'siswa_id'           => $siswaRafi->id,
            'tahun_ajaran_id'    => $thAktif->id,
            'wali_kelas_id'      => $guruSiti->id,
            'status'             => Rapor::STATUS_DIAJUKAN,
            'catatan_wali_kelas' => 'Rafi semakin nyaman berinteraksi sosial dan menunjukkan kontak mata yang lebih fokus.',
            'tgl_rapor'          => '2026-12-19',
            'tgl_pengajuan'      => now()->subHours(3),
        ]);

        // Evaluasi Nonakademik untuk siswa lainnya
        foreach ([$siswaBima, $siswaCitra, $siswaDimas, $siswaElsa, $siswaFajar, $siswaNadia, $siswaRafi, $siswaFriska, $siswaArumi] as $s) {
            EvaluasiNonakademik::create([
                'siswa_id'           => $s->id,
                'tahun_ajaran_id'    => $thAktif->id,
                'sakit'              => rand(0, 2),
                'izin'               => rand(0, 3),
                'alpa'               => 0,
                'perkembangan'       => 'Perkembangan fisik dan adaptasi lingkungan berjalan sangat baik.',
                'tidak_ada_prestasi' => true,
            ]);
        }

        ActivityLog::log('Inisialisasi sistem database selesai', null, 'Sukses');
    }
}
