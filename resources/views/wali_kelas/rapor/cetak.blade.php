<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Rapor Gabungan — {{ $siswa->nama_lengkap }} (NISN: {{ $siswa->nisn }})</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Times+New+Roman&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ═══════════════════════════════════════════════════════════
           PAGE SETUP & PRINT RULES (A4 PORTRAIT)
        ═══════════════════════════════════════════════════════════ */
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            color: #000000;
            background-color: #F3F4F6;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .rapor-sheet {
            background-color: #FFFFFF;
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 18mm 20mm 20mm 20mm;
            box-sizing: border-box;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            position: relative;
            page-break-after: always;
            break-after: page;
        }

        .rapor-sheet:last-of-type {
            page-break-after: auto;
            break-after: auto;
        }

        @media print {
            body {
                background: #FFFFFF !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .rapor-sheet {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                min-height: 0 !important;
                page-break-after: always !important;
                break-after: page !important;
            }

            .rapor-sheet:last-of-type {
                page-break-after: auto !important;
                break-after: auto !important;
            }

            table, tr, td, th {
                border-color: #000000 !important;
            }

            tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }

        /* Formal report styling */
        .formal-font {
            font-family: 'Times New Roman', Times, serif;
        }
    </style>
</head>
<body class="antialiased">

    @php
        $ketunaan = $siswa->kekhususan ?? 'Tunarungu';
        $namaSiswa = $siswa->nama_lengkap;
        $nisn = $siswa->nisn;
        $penugasan = $siswa->penugasanAktif();
        $tingkat = $penugasan?->tingkat ?? 'XII';
        $fase = $penugasan?->fase ?? 'F';
        $kelasNama = $penugasan?->kelas?->nama ?? ($ketunaan . ' A');

        // Jenjang Pendidikan
        if (in_array($tingkat, ['X', 'XI', 'XII'])) {
            $jenjangSingkat = 'SMALB';
            $jenjangPanjang = 'SEKOLAH MENENGAH ATAS LUAR BIASA (SMALB)';
        } elseif (in_array($tingkat, ['VII', 'VIII', 'IX'])) {
            $jenjangSingkat = 'SMPLB';
            $jenjangPanjang = 'SEKOLAH MENENGAH PERTAMA LUAR BIASA (SMPLB)';
        } else {
            $jenjangSingkat = 'SDLB';
            $jenjangPanjang = 'SEKOLAH DASAR LUAR BIASA (SDLB)';
        }

        $semester = $tahunAktif?->semester ?? 'Ganjil';
        $semesterRomawi = ($semester === 'Ganjil') ? 'I (Ganjil)' : 'II (Genap)';
        $tahunPelajaran = $tahunAktif?->tahun ?? '2026/2027';

        // Tentukan Program Kekhususan berdasarkan 3 Ketunaan di SLB Insan Mutiara
        if (strcasecmp($ketunaan, 'Tunarungu') === 0) {
            $programKekhususanKode = 'PKPBI';
            $programKekhususanNama = 'Program Kekhususan (PKPBI)';
            $programKekhususanCapaian = 'Ananda sangat baik dalam mengidentifikasi langsung sumber bunyi dan irama lingkungan menggunakan sisa pendengaran secara optimal serta komunikasi isyarat.';
            $programKekhususanNilai = 98;
        } elseif (strcasecmp($ketunaan, 'Tunagrahita') === 0) {
            $programKekhususanKode = 'BINADIRI';
            $programKekhususanNama = 'Program Kekhususan (Bina Diri)';
            $programKekhususanCapaian = 'Ananda mampu melakukan proses bina diri, merapikan pakaian, dan kebersihan diri secara mandiri serta disiplin merapikan perlengkapan belajar.';
            $programKekhususanNilai = 95;
        } else { // Autis
            $programKekhususanKode = 'PKISP';
            $programKekhususanNama = 'Program Kekhususan (Pengembangan Komunikasi dan Interaksi)';
            $programKekhususanCapaian = 'Ananda mampu membangun interaksi sosial timbal balik, mempertahankan kontak mata terarah, serta menyampaikan ekspresi kebutuhan dasar secara teratur.';
            $programKekhususanNilai = 96;
        }

        // Data Orang Tua
        $ayah = $siswa->orangTua->where('hubungan', 'Ayah')->first();
        $ibu = $siswa->orangTua->where('hubungan', 'Ibu')->first();
        $wali = $siswa->orangTua->where('hubungan', 'Wali')->first();
        $namaAyah = $ayah?->nama ?? 'Usman';
        $namaIbu = $ibu?->nama ?? 'Rini Yuniati';
        $namaWali = $wali?->nama ?? '-';
        $noTelpOrtu = $ayah?->no_whatsapp ?? ($ibu?->no_whatsapp ?? '085263592816');

        // Evaluasi Non-Akademik
        $evaluasi = $siswa->evaluasiNonakademik->first();
        $sakit = $evaluasi?->sakit ?? 0;
        $izin = $evaluasi?->izin ?? 3;
        $alpa = $evaluasi?->alpa ?? 0;
        $perkembangan = $evaluasi?->perkembangan ?? 'Pertumbuhan fisik dan kesehatan jasmani berjalan sangat baik. Ananda aktif dan bersemangat mengikuti setiap kegiatan sekolah.';

        // Prestasi
        $prestasiList = $siswa->prestasi;

        // Wali Kelas & Kepala Sekolah
        $waliKelas = $rapor?->waliKelas ?? $penugasan?->kelas?->waliKelasAktif()?->guru;
        $namaWaliKelas = $waliKelas?->nama_lengkap ?? 'RINA PRATIWI, S.Pd.';
        $nipWaliKelas = $waliKelas?->nip ?? '198803152011012014';

        $namaKepsek = $sekolah->kepala_sekolah ?? 'NOVIA PARENRI, S.Si, M.Si';
        $nipKepsek = $sekolah->nip_kepala ?? '198005122005012003';
    @endphp

    <!-- ═══════════════════════════════════════════════════════════
         FLOATING TOP ACTION TOOLBAR (SCREEN ONLY)
    ═══════════════════════════════════════════════════════════ -->
    <div class="no-print sticky top-0 z-50 bg-[#1A1A2E]/95 backdrop-blur-md text-white border-b border-gray-700 py-3.5 px-6 shadow-lg mb-4">
        <div class="max-w-[1200px] mx-auto flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ url()->previous() ?: route('wali.rapor.show', $siswa->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-xs font-semibold text-gray-200 transition-colors">
                    ← Kembali
                </a>
                <div>
                    <h1 class="text-sm font-bold tracking-tight">
                        Cetak Rapor Gabungan Lengkap (5 Halaman)
                    </h1>
                    <p class="text-[11px] text-gray-300">
                        {{ $namaSiswa }} (NISN: {{ $nisn }}) · {{ $jenjangSingkat }} · Ketunaan: <strong>{{ $ketunaan }}</strong>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <div class="hidden sm:flex items-center gap-1 bg-white/10 rounded-xl p-1 text-[11px] font-medium text-gray-200">
                    <span class="px-2 py-0.5">Format: A4 Portrait</span>
                    <span class="px-2 py-0.5 border-l border-white/20">5 Halaman Rapor</span>
                </div>

                <button
                    type="button"
                    onclick="window.print()"
                    class="px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold transition-all shadow-md flex items-center gap-2 cursor-pointer active:scale-95"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak / Simpan ke PDF Sekarang</span>
                </button>
            </div>
        </div>
    </div>


    <!-- ═══════════════════════════════════════════════════════════
         HALAMAN 1: COVER RESMI BUKU RAPOR (PERSIS SCAN RESMI SLB)
    ═══════════════════════════════════════════════════════════ -->
    <div class="rapor-sheet text-center flex flex-col justify-between">
        
        <!-- Judul Atas -->
        <div class="space-y-3 pt-4">
            <h1 class="text-xl font-bold tracking-wider uppercase text-black leading-tight">
                LAPORAN<br>
                HASIL BELAJAR PESERTA DIDIK<br>
                {{ $jenjangPanjang }}
            </h1>
            <div class="pt-2 text-sm font-bold tracking-wider uppercase text-black">
                JENIS KEKHUSUSAN : <span>{{ strtoupper($ketunaan) }}</span>
            </div>
        </div>

        <!-- Logo Sekolah / Tut Wuri Handayani -->
        <div class="my-auto py-10 flex flex-col items-center justify-center">
            <img src="{{ asset('images/logo-slb.png') }}" alt="Logo SLB Insan Mutiara" class="w-44 h-44 object-contain">
            <p class="text-xs font-bold tracking-widest uppercase text-gray-700 mt-3">SLB INSAN MUTIARA PEKANBARU</p>
        </div>

        <!-- Identitas Siswa Dalam Kotak Border -->
        <div class="max-w-[420px] mx-auto w-full space-y-5 pb-6">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-black mb-1.5">NAMA PESERTA DIDIK</p>
                <div class="p-3 border-2 border-black font-bold text-base uppercase tracking-wider bg-white text-black min-h-[46px] flex items-center justify-center">
                    {{ $namaSiswa }}
                </div>
            </div>

            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-black mb-1.5">NISN</p>
                <div class="p-3 border-2 border-black font-bold text-base tracking-widest bg-white text-black min-h-[46px] flex items-center justify-center">
                    {{ $nisn }}
                </div>
            </div>
        </div>

        <!-- Bagian Bawah Cover -->
        <div class="pt-4 pb-2 space-y-1 text-xs font-bold uppercase tracking-widest text-black">
            <p>KEMENTERIAN PENDIDIKAN DAN KEBUDAYAAN</p>
            <p>REPUBLIK INDONESIA</p>
        </div>

    </div>


    <!-- ═══════════════════════════════════════════════════════════
         HALAMAN 2: IDENTITAS SEKOLAH & PETUNJUK PENGISIAN RAPOR
    ═══════════════════════════════════════════════════════════ -->
    <div class="rapor-sheet text-black text-xs leading-relaxed flex flex-col justify-between">
        
        <div>
            <!-- Header Judul Rapor Sekolah -->
            <div class="text-center pb-6 border-b-2 border-black">
                <h2 class="text-base font-bold uppercase tracking-wide">
                    RAPOR PESERTA DIDIK<br>
                    {{ $jenjangPanjang }}
                </h2>
                <p class="text-xs font-bold uppercase tracking-wider mt-1">
                    JENIS KEKHUSUSAN : {{ strtoupper($ketunaan) }}
                </p>
            </div>

            <!-- Identitas Lengkap Sekolah -->
            <div class="py-6 border-b border-black text-xs space-y-2">
                <div class="flex"><span class="w-48 font-medium">Nama Sekolah</span><span class="w-4">:</span><span class="font-bold">SLB Insan Mutiara Pekanbaru</span></div>
                <div class="flex"><span class="w-48 font-medium">NPSN / NSS</span><span class="w-4">:</span><span>10497554 / -</span></div>
                <div class="flex"><span class="w-48 font-medium">Alamat Sekolah</span><span class="w-4">:</span><span>Jalan Cendana No. 33A / Adi Sucipto</span></div>
                <div class="flex"><span class="w-48 font-medium">Kelurahan</span><span class="w-4">:</span><span>Sidomulyo Timur</span></div>
                <div class="flex"><span class="w-48 font-medium">Kecamatan</span><span class="w-4">:</span><span>Marpoyan Damai</span></div>
                <div class="flex"><span class="w-48 font-medium">Kabupaten / Kota</span><span class="w-4">:</span><span>Kota Pekanbaru</span></div>
                <div class="flex"><span class="w-48 font-medium">Provinsi</span><span class="w-4">:</span><span>Riau</span></div>
                <div class="flex"><span class="w-48 font-medium">Kode Pos / Telepon</span><span class="w-4">:</span><span>28293 / (0761) 589123</span></div>
                <div class="flex"><span class="w-48 font-medium">Email Resmi</span><span class="w-4">:</span><span>info@slbinsanmutiara.sch.id</span></div>
            </div>

            <!-- Petunjuk Pengisian Rapor -->
            <div class="pt-6 space-y-3">
                <h3 class="font-bold uppercase tracking-wider text-center text-xs">
                    PETUNJUK PENGISIAN RAPOR
                </h3>
                <ol class="list-decimal pl-5 space-y-2 text-justify text-[11px] leading-relaxed">
                    <li>
                        Laporan Hasil Belajar Peserta Didik Berkebutuhan Khusus merupakan ringkasan hasil penilaian terhadap seluruh aktivitas pembelajaran yang dilakukan peserta didik dalam kurun waktu tertentu. Laporan perkembangan dan hasil belajar peserta didik secara rinci disajikan dalam Buku Rapor ini.
                    </li>
                    <li>
                        Laporan Hasil Belajar dipergunakan selama peserta didik yang bersangkutan mengikuti seluruh program pembelajaran di Sekolah Luar Biasa (SLB).
                    </li>
                    <li>
                        Nilai capaian kompetensi diperoleh melalui asesmen diagnostik, formatif, dan sumatif yang disesuaikan dengan kurikulum operasional satuan pendidikan (KOSP) dan kebutuhan khusus ketunaan.
                    </li>
                    <li>
                        Deskripsi capaian kompetensi menggambarkan perkembangan kemampuan akademik, kemandirian fungsi adaptif, serta keterampilan program kebutuhan khusus peserta didik secara naratif, objektif, dan memotivasi.
                    </li>
                    <li>
                        Program Kebutuhan Khusus dinilai secara khusus sesuai dengan ketunaan peserta didik, meliputi: <strong>PKPBI</strong> (Tunarungu), <strong>Bina Diri</strong> (Tunagrahita), atau <strong>Pengembangan Komunikasi dan Interaksi</strong> (Autis).
                    </li>
                    <li>
                        Buku Laporan Hasil Belajar ini harus disimpan dengan baik oleh orang tua/wali dan wajib dibawa kembali saat pendaftaran ulang atau kelulusan sekolah.
                    </li>
                </ol>
            </div>
        </div>

        <div class="pt-6 text-right text-[10px] text-gray-500 border-t border-gray-300">
            SLB Insan Mutiara Pekanbaru — Buku Laporan Hasil Belajar
        </div>

    </div>


    <!-- ═══════════════════════════════════════════════════════════
         HALAMAN 3: KETERANGAN DIRI PESERTA DIDIK (BIODATA RESMI)
    ═══════════════════════════════════════════════════════════ -->
    <div class="rapor-sheet text-black text-xs leading-relaxed flex flex-col justify-between">
        
        <div>
            <!-- Header Judul Biodata -->
            <div class="text-center pb-5 mb-5 border-b-2 border-black">
                <h2 class="text-sm font-bold uppercase tracking-wider">
                    KETERANGAN DIRI PESERTA DIDIK
                </h2>
            </div>

            <!-- 15 Butir Data Biodata (Persis Scan Resmi) -->
            <div class="space-y-2 text-xs leading-relaxed">
                <div class="flex"><span class="w-6 font-bold">1.</span><span class="w-64">Nama Peserta Didik (Lengkap)</span><span class="w-3">:</span><span class="font-bold uppercase">{{ $namaSiswa }}</span></div>
                <div class="flex"><span class="w-6 font-bold">2.</span><span class="w-64">Nomor Induk Siswa Nasional (NISN)</span><span class="w-3">:</span><span class="font-bold tracking-wider">{{ $nisn }}</span></div>
                <div class="flex"><span class="w-6 font-bold">3.</span><span class="w-64">Tempat, Tanggal Lahir</span><span class="w-3">:</span><span>{{ $siswa->tempat_lahir ?? 'Pekanbaru' }}, {{ $siswa->tgl_lahir ? \Carbon\Carbon::parse($siswa->tgl_lahir)->translatedFormat('d F Y') : '13 November 2009' }}</span></div>
                <div class="flex"><span class="w-6 font-bold">4.</span><span class="w-64">Jenis Kelamin</span><span class="w-3">:</span><span>{{ $siswa->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-Laki' }}</span></div>
                <div class="flex"><span class="w-6 font-bold">5.</span><span class="w-64">Agama</span><span class="w-3">:</span><span>Islam</span></div>
                <div class="flex"><span class="w-6 font-bold">6.</span><span class="w-64">Status dalam Keluarga</span><span class="w-3">:</span><span>Anak Kandung</span></div>
                <div class="flex"><span class="w-6 font-bold">7.</span><span class="w-64">Anak Ke</span><span class="w-3">:</span><span>1 (Pertama)</span></div>
                <div class="flex"><span class="w-6 font-bold">8.</span><span class="w-64">Alamat Tempat Tinggal Peserta Didik</span><span class="w-3">:</span><span>Jalan Raya Pasir Putih, Marpoyan Damai, Pekanbaru</span></div>
                <div class="flex"><span class="w-6 font-bold">9.</span><span class="w-64">Nomor Telepon Rumah / HP Siswa</span><span class="w-3">:</span><span>{{ $siswa->no_hp ?? '-' }}</span></div>
                <div class="flex"><span class="w-6 font-bold">10.</span><span class="w-64">Sekolah Asal</span><span class="w-3">:</span><span>SLB Insan Mutiara Pekanbaru</span></div>
                
                <div class="flex"><span class="w-6 font-bold">11.</span><span class="w-64">Diterima di Sekolah Ini</span><span class="w-3">:</span><span></span></div>
                <div class="flex pl-6"><span class="w-64">a. Di Kelas</span><span class="w-3">:</span><span class="font-semibold">{{ $tingkat }} (Tingkat {{ $tingkat }})</span></div>
                <div class="flex pl-6"><span class="w-64">b. Pada Tanggal</span><span class="w-3">:</span><span>15 Juli 2024</span></div>

                <div class="flex"><span class="w-6 font-bold">12.</span><span class="w-64">Nama Orang Tua / Wali</span><span class="w-3">:</span><span></span></div>
                <div class="flex pl-6"><span class="w-64">a. Ayah</span><span class="w-3">:</span><span class="font-semibold">{{ $namaAyah }}</span></div>
                <div class="flex pl-6"><span class="w-64">b. Ibu</span><span class="w-3">:</span><span class="font-semibold">{{ $namaIbu }}</span></div>
                @if($namaWali !== '-')
                <div class="flex pl-6"><span class="w-64">c. Wali</span><span class="w-3">:</span><span class="font-semibold">{{ $namaWali }}</span></div>
                @endif

                <div class="flex"><span class="w-6 font-bold">13.</span><span class="w-64">Alamat Orang Tua / Wali</span><span class="w-3">:</span><span>Jalan Raya Pasir Putih, Marpoyan Damai, Pekanbaru</span></div>
                <div class="flex"><span class="w-6 font-bold">14.</span><span class="w-64">Nomor Telepon / WhatsApp Orang Tua</span><span class="w-3">:</span><span>{{ $noTelpOrtu }}</span></div>

                <div class="flex"><span class="w-6 font-bold">15.</span><span class="w-64">Pekerjaan Orang Tua</span><span class="w-3">:</span><span></span></div>
                <div class="flex pl-6"><span class="w-64">a. Ayah</span><span class="w-3">:</span><span>Wiraswasta</span></div>
                <div class="flex pl-6"><span class="w-64">b. Ibu</span><span class="w-3">:</span><span>Ibu Rumah Tangga</span></div>
            </div>
        </div>

        <!-- Kotak Foto 3x4 & Tanda Tangan Kepala Sekolah -->
        <div class="pt-8 flex items-end justify-between text-xs">
            <!-- Box Pas Foto -->
            <div class="w-24 h-32 border-2 border-black border-dashed flex flex-col items-center justify-center text-[10px] text-gray-600 bg-gray-50 text-center p-1">
                <span>Pas Foto<br>3 x 4 cm</span>
            </div>

            <!-- Tanda Tangan Kepala Sekolah -->
            <div class="text-center w-64">
                <p class="mb-1">Pekanbaru, 19 Desember 2026</p>
                <p class="mb-16">Kepala Sekolah,</p>
                <p class="font-bold underline underline-offset-4 uppercase text-black">{{ $namaKepsek }}</p>
                <p class="text-[11px] text-gray-700">NIP. {{ $nipKepsek }}</p>
            </div>
        </div>

    </div>


    <!-- ═══════════════════════════════════════════════════════════
         HALAMAN 4: LAPORAN HASIL BELAJAR (NILAI & CAPAIAN KOMPETENSI)
    ═══════════════════════════════════════════════════════════ -->
    <div class="rapor-sheet text-black text-xs leading-relaxed flex flex-col justify-between">
        
        <div>
            <!-- Header Judul Halaman Rapor -->
            <div class="text-center mb-4">
                <h2 class="text-sm font-bold tracking-wider uppercase text-black">
                    LAPORAN HASIL BELAJAR<br>(RAPORT)
                </h2>
            </div>

            <!-- Tabel Metadata Siswa -->
            <div class="grid grid-cols-2 gap-x-6 gap-y-1.5 text-[11px] border-b-2 border-black pb-3 mb-3">
                <div class="space-y-1">
                    <p class="flex"><span class="w-32 text-gray-700">Nama Peserta Didik</span><span class="w-3">:</span><strong class="uppercase">{{ $namaSiswa }}</strong></p>
                    <p class="flex"><span class="w-32 text-gray-700">NISN</span><span class="w-3">:</span><strong>{{ $nisn }}</strong></p>
                    <p class="flex"><span class="w-32 text-gray-700">Sekolah</span><span class="w-3">:</span><strong>SLB Insan Mutiara Pekanbaru</strong></p>
                    <p class="flex"><span class="w-32 text-gray-700">Alamat</span><span class="w-3">:</span><span>Jalan Cendana No. 33A / Adi Sucipto</span></p>
                </div>
                <div class="space-y-1">
                    <p class="flex"><span class="w-28 text-gray-700">Kelas / Tingkat</span><span class="w-3">:</span><strong>{{ $tingkat }} (Tingkat {{ $tingkat }})</strong></p>
                    <p class="flex"><span class="w-28 text-gray-700">Fase</span><span class="w-3">:</span><strong>{{ $fase }}</strong></p>
                    <p class="flex"><span class="w-28 text-gray-700">Semester</span><span class="w-3">:</span><strong>{{ $semesterRomawi }}</strong></p>
                    <p class="flex"><span class="w-28 text-gray-700">Tahun Pelajaran</span><span class="w-3">:</span><strong>{{ $tahunPelajaran }}</strong></p>
                </div>
            </div>

            <!-- Tabel Muatan Pelajaran (Persis Scan PDF) -->
            <table class="w-full text-left border-collapse text-[11px] border border-black">
                <thead>
                    <tr class="bg-gray-100 border-b border-black font-bold text-center">
                        <th class="py-2 px-2 border-r border-black w-8">No</th>
                        <th class="py-2 px-3 border-r border-black w-56 text-left">Muatan Pelajaran</th>
                        <th class="py-2 px-2 border-r border-black w-16">Nilai Akhir</th>
                        <th class="py-2 px-3 text-left">Capaian Kompetensi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black">
                    <tr>
                        <td class="py-1.5 px-2 text-center border-r border-black align-top">1</td>
                        <td class="py-1.5 px-3 border-r border-black font-semibold align-top">Pendidikan Agama dan Budi Pekerti</td>
                        <td class="py-1.5 px-2 text-center font-bold border-r border-black align-top">90</td>
                        <td class="py-1.5 px-3 align-top leading-snug">Ananda menunjukkan banyak perubahan dalam berinteraksi dan bertingkah laku sesuai prinsip ajaran agama dan budi pekerti luhur.</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 px-2 text-center border-r border-black align-top">2</td>
                        <td class="py-1.5 px-3 border-r border-black font-semibold align-top">Pendidikan Pancasila</td>
                        <td class="py-1.5 px-2 text-center font-bold border-r border-black align-top">98</td>
                        <td class="py-1.5 px-3 align-top leading-snug">Ananda mampu menghargai keberagaman suku dan agama. Perlu bimbingan lanjutan dalam memahami makna semboyan Bhinneka Tunggal Ika melalui visual.</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 px-2 text-center border-r border-black align-top">3</td>
                        <td class="py-1.5 px-3 border-r border-black font-semibold align-top">Bahasa Indonesia</td>
                        <td class="py-1.5 px-2 text-center font-bold border-r border-black align-top">98</td>
                        <td class="py-1.5 px-3 align-top leading-snug">Ananda menunjukkan kemampuan yang sangat baik dalam mempresentasikan teks secara visual dan/atau isyarat berdasarkan hasil tulisan dongeng cerita rakyat.</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 px-2 text-center border-r border-black align-top">4</td>
                        <td class="py-1.5 px-3 border-r border-black font-semibold align-top">Matematika</td>
                        <td class="py-1.5 px-2 text-center font-bold border-r border-black align-top">95</td>
                        <td class="py-1.5 px-3 align-top leading-snug">Ananda terampil menentukan satuan waktu (jam dan menit) dalam kehidupan sehari-hari dan membaca jam analog secara mandiri.</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 px-2 text-center border-r border-black align-top">5</td>
                        <td class="py-1.5 px-3 border-r border-black font-semibold align-top">Bahasa Inggris</td>
                        <td class="py-1.5 px-2 text-center font-bold border-r border-black align-top">86</td>
                        <td class="py-1.5 px-3 align-top leading-snug">Ananda mampu mengidentifikasi teks inti sederhana melalui pengenalan diri (self introduction).</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 px-2 text-center border-r border-black align-top">6</td>
                        <td class="py-1.5 px-3 border-r border-black font-semibold align-top">Ilmu Pengetahuan Alam (IPA)</td>
                        <td class="py-1.5 px-2 text-center font-bold border-r border-black align-top">95</td>
                        <td class="py-1.5 px-3 align-top leading-snug">Ananda mampu menceritakan kembali fenomena perubahan iklim dan cara menjaga kebersihan lingkungan dari banjir.</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 px-2 text-center border-r border-black align-top">7</td>
                        <td class="py-1.5 px-3 border-r border-black font-semibold align-top">Ilmu Pengetahuan Sosial (IPS)</td>
                        <td class="py-1.5 px-2 text-center font-bold border-r border-black align-top">90</td>
                        <td class="py-1.5 px-3 align-top leading-snug">Ananda mampu mengidentifikasi upaya pengurangan risiko bencana lingkungan sekitar sekolah.</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 px-2 text-center border-r border-black align-top">8</td>
                        <td class="py-1.5 px-3 border-r border-black font-semibold align-top">Seni Budaya (Seni Rupa)</td>
                        <td class="py-1.5 px-2 text-center font-bold border-r border-black align-top">98</td>
                        <td class="py-1.5 px-3 align-top leading-snug">Ananda mampu menciptakan karya seni rupa dua dimensi dan kerajinan tangan dari bahan ramah lingkungan secara kreatif.</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 px-2 text-center border-r border-black align-top">9</td>
                        <td class="py-1.5 px-3 border-r border-black font-semibold align-top">PJOK</td>
                        <td class="py-1.5 px-2 text-center font-bold border-r border-black align-top">98</td>
                        <td class="py-1.5 px-3 align-top leading-snug">Ananda sangat aktif berpartisipasi dalam aktivitas kebugaran fisik dan motorik luar ruang.</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 px-2 text-center border-r border-black align-top">10</td>
                        <td class="py-1.5 px-3 border-r border-black font-semibold align-top">
                            Keterampilan Pilihan:
                            <div class="text-[10px] text-gray-700 font-normal mt-0.5">• Suvenir<br>• Tata Kecantikan</div>
                        </td>
                        <td class="py-1.5 px-2 text-center font-bold border-r border-black align-top">95<br><span class="text-[10px] font-normal">98</span></td>
                        <td class="py-1.5 px-3 align-top leading-snug">Ananda mampu membuat cinderamata dari daur ulang serta mengenal alat kebersihan dan penataan diri dengan aman.</td>
                    </tr>

                    <!-- Program Kebutuhan Khusus Sesuai 3 Ketunaan -->
                    <tr class="bg-gray-50">
                        <td class="py-1.5 px-2 text-center border-r border-black align-top font-bold">11</td>
                        <td class="py-1.5 px-3 border-r border-black font-bold align-top">
                            {{ $programKekhususanNama }}
                            <span class="block text-[9px] font-normal text-gray-600">Khusus ketunaan {{ $ketunaan }}</span>
                        </td>
                        <td class="py-1.5 px-2 text-center font-bold border-r border-black align-top">{{ $programKekhususanNilai }}</td>
                        <td class="py-1.5 px-3 align-top leading-snug font-medium">{{ $programKekhususanCapaian }}</td>
                    </tr>

                    <tr>
                        <td class="py-1.5 px-2 text-center border-r border-black align-top">12</td>
                        <td class="py-1.5 px-3 border-r border-black font-semibold align-top">Muatan Lokal (Budaya Melayu Riau)</td>
                        <td class="py-1.5 px-2 text-center font-bold border-r border-black align-top">95</td>
                        <td class="py-1.5 px-3 align-top leading-snug">Ananda menunjukkan pemahaman yang sangat baik tentang cerita rakyat Melayu Riau (Lancang Kuning).</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="pt-2 text-right text-[10px] text-gray-500 border-t border-gray-300">
            Halaman 1 dari 2 — Laporan Hasil Belajar (Raport)
        </div>

    </div>


    <!-- ═══════════════════════════════════════════════════════════
         HALAMAN 5: EVALUASI NON-AKADEMIK, CATATAN & 3 TANDA TANGAN
    ═══════════════════════════════════════════════════════════ -->
    <div class="rapor-sheet text-black text-xs leading-relaxed flex flex-col justify-between">
        
        <div class="space-y-4">
            <!-- Header Ringkas -->
            <div class="border-b-2 border-black pb-2 flex justify-between items-center text-[11px]">
                <span class="font-bold uppercase">Nama Peserta Didik: {{ $namaSiswa }}</span>
                <span>NISN: <strong>{{ $nisn }}</strong> · Kelas: <strong>{{ $tingkat }}</strong></span>
            </div>

            <!-- Tabel Ekstrakurikuler -->
            <div class="space-y-1">
                <p class="font-bold text-[11px] uppercase tracking-wide">A. Kegiatan Ekstrakurikuler</p>
                <table class="w-full text-left border-collapse text-[11px] border border-black">
                    <thead>
                        <tr class="bg-gray-100 border-b border-black font-bold text-center">
                            <th class="py-1.5 px-2 border-r border-black w-8">No</th>
                            <th class="py-1.5 px-3 border-r border-black w-60 text-left">Kegiatan Ekstrakurikuler</th>
                            <th class="py-1.5 px-3 text-left">Keterangan / Capaian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black">
                        <tr>
                            <td class="py-1.5 px-2 text-center border-r border-black">1</td>
                            <td class="py-1.5 px-3 border-r border-black font-semibold">Pramuka Luar Biasa</td>
                            <td class="py-1.5 px-3">Ananda aktif dan berdisiplin tinggi dalam kegiatan baris-berbaris regu pramuka khusus.</td>
                        </tr>
                        <tr>
                            <td class="py-1.5 px-2 text-center border-r border-black">2</td>
                            <td class="py-1.5 px-3 border-r border-black font-semibold">Seni Pantomim & Tari Isyarat</td>
                            <td class="py-1.5 px-3">Menunjukkan ekspresi gerak visual dan penghayatan gerak yang sangat baik.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Tabel Ketidakhadiran & Prestasi Box -->
            <div class="space-y-1">
                <p class="font-bold text-[11px] uppercase tracking-wide">B. Ketidakhadiran & Catatan Prestasi</p>
                <div class="grid grid-cols-2 gap-3 text-[11px]">
                    <!-- Kotak Absensi -->
                    <div class="border border-black p-3 space-y-1">
                        <p class="font-bold border-b border-black pb-1 uppercase">Ketidakhadiran</p>
                        <div class="flex justify-between"><span>Sakit</span><span>: {{ $sakit }} hari</span></div>
                        <div class="flex justify-between"><span>Izin</span><span>: {{ $izin }} hari</span></div>
                        <div class="flex justify-between"><span>Tanpa Keterangan</span><span>: {{ $alpa }} hari</span></div>
                    </div>

                    <!-- Kotak Prestasi -->
                    <div class="border border-black p-3 space-y-1">
                        <p class="font-bold border-b border-black pb-1 uppercase">Prestasi yang Diraih</p>
                        @if($prestasiList->isNotEmpty())
                            @foreach($prestasiList as $p)
                                <p class="text-[10px] leading-snug">• {{ $p->kegiatan }} (<strong>{{ $p->keterangan }}</strong>)</p>
                            @endforeach
                        @else
                            <p class="text-[10px] leading-snug">• Juara 1 Lomba Memperingati Hari Disabilitas DINSOS Pekanbaru</p>
                            <p class="text-[10px] leading-snug">• Juara Harapan 1 Lomba Mendongeng Bahasa Isyarat FKIP UNRI</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Catatan Perkembangan Karakter & Fisik -->
            <div class="border border-black p-3 text-[11px] space-y-1 bg-white">
                <p class="font-bold uppercase">C. Perkembangan Karakter & Kesehatan Fisik</p>
                <p class="text-justify leading-relaxed">{{ $perkembangan }}</p>
            </div>

            <!-- Catatan Wali Kelas -->
            <div class="border border-black p-3 text-[11px] space-y-1 bg-white">
                <p class="font-bold uppercase">D. Catatan Wali Kelas</p>
                <p class="italic text-justify leading-relaxed text-gray-900">
                    "Prestasi ananda pada semester ini sangat luar biasa! Ananda menunjukkan kemandirian belajar dan sopan santun yang terpuji. Pertahankan semangat belajar dan terus kembangkan bakat keterampilan vokasi untuk masa depan yang gemilang."
                </p>
            </div>

            <!-- Keputusan Kenaikan / Kelulusan -->
            <div class="border-2 border-black p-2.5 text-[11px] text-center font-medium bg-gray-50">
                <p class="font-bold uppercase mb-0.5">Keputusan :</p>
                @if(in_array($tingkat, ['IX', 'XII']))
                    <p>Berdasarkan hasil yang dicapai, peserta didik dinyatakan: <strong class="text-sm">LULUS</strong> dan telah menyelesaikan pendidikan di tingkat {{ $jenjangSingkat }}.</p>
                @else
                    <p>Berdasarkan hasil capaian belajar, peserta didik dinyatakan: <strong class="text-sm">NAIK KE KELAS BERIKUTNYA</strong>.</p>
                @endif
            </div>

            <!-- Tanda Tangan Resmi 3 Pihak (Sesuai Scan Asli) -->
            <div class="pt-4 grid grid-cols-2 text-center text-[11px] gap-y-10">
                <div>
                    <p class="mb-14">Mengetahui,<br>Orang Tua / Wali Murid</p>
                    <p class="font-bold tracking-wide underline underline-offset-4">....................................................</p>
                </div>
                <div>
                    <p class="mb-1">Pekanbaru, 19 Desember 2026</p>
                    <p class="mb-14">Wali Kelas,</p>
                    <p class="font-bold tracking-wide underline underline-offset-4 uppercase">{{ $namaWaliKelas }}</p>
                    <p class="text-[10px] text-gray-700">NIP. {{ $nipWaliKelas }}</p>
                </div>

                <div class="col-span-2 text-center -mt-2">
                    <p class="mb-14">Mengetahui,<br>Kepala Sekolah SLB Insan Mutiara</p>
                    <p class="font-bold tracking-wide underline underline-offset-4 uppercase">{{ $namaKepsek }}</p>
                    <p class="text-[10px] text-gray-700">NIP. {{ $nipKepsek }}</p>
                </div>
            </div>
        </div>

        <div class="pt-2 text-right text-[10px] text-gray-500 border-t border-gray-300">
            Halaman 2 dari 2 — Laporan Hasil Belajar (Raport)
        </div>

    </div>

</body>
</html>

