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
        /* Standar A4 Portrait & Cetak Gabungan */
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

        .serif-font {
            font-family: 'Times New Roman', Times, serif;
        }

        .rapor-sheet {
            background-color: #FFFFFF;
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 16mm 20mm 20mm 20mm;
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
                color: #000000 !important;
            }

            tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</head>
<body class="antialiased">

    @php
        $ketunaan = $siswa->kekhususan ?? 'Tunarungu';
        $namaSiswa = $siswa->nama_lengkap;
        $nisn = $siswa->nisn;
        $nis = '26' . str_pad($siswa->id, 3, '0', STR_PAD_LEFT);
        $penugasan = $siswa->penugasanAktif();
        $tingkat = $penugasan?->tingkat ?? 'VII';
        $fase = $penugasan?->fase ?? 'D';
        $kelasNama = $penugasan?->kelas?->nama ?? ($ketunaan . ' A');

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

        if (strcasecmp($ketunaan, 'Tunarungu') === 0) {
            $programKekhususanNama = 'Program Kekhususan (PKPBI)';
            $programKekhususanCapaian = 'Ananda sangat baik dalam mengidentifikasi langsung sumber bunyi dan irama lingkungan menggunakan sisa pendengaran secara optimal serta komunikasi isyarat.';
            $programKekhususanNilai = 98;
        } elseif (strcasecmp($ketunaan, 'Tunagrahita') === 0) {
            $programKekhususanNama = 'Program Kekhususan (Bina Diri)';
            $programKekhususanCapaian = 'Ananda mampu melakukan proses bina diri, merapikan pakaian, dan kebersihan diri secara mandiri serta disiplin merapikan perlengkapan belajar.';
            $programKekhususanNilai = 95;
        } else { // Autis
            $programKekhususanNama = 'Program Kekhususan (Pengembangan Komunikasi dan Interaksi)';
            $programKekhususanCapaian = 'Ananda mampu membangun interaksi sosial timbal balik, mempertahankan kontak mata terarah, serta menyampaikan ekspresi kebutuhan dasar secara teratur.';
            $programKekhususanNilai = 96;
        }

        $ayah = $siswa->orangTua?->where('hubungan', 'Ayah')->first();
        $ibu = $siswa->orangTua?->where('hubungan', 'Ibu')->first();
        $namaAyah = $ayah?->nama ?? 'Andi Putra';
        $namaIbu = $ibu?->nama ?? 'Siti Aminah';

        $evaluasi = $siswa->evaluasiNonakademik?->first();
        $sakit = $evaluasi?->sakit ?? 0;
        $izin = $evaluasi?->izin ?? 3;
        $alpa = $evaluasi?->alpa ?? 0;
        $perkembangan = $evaluasi?->perkembangan ?? 'Pertumbuhan fisik dan kesehatan jasmani berkembang sangat baik. Ananda aktif dan mandiri dalam mengikuti kegiatan belajar di sekolah.';

        $waliKelas = $rapor?->waliKelas ?? $penugasan?->kelas?->waliKelasAktif()?->guru;
        $namaWaliKelas = $waliKelas?->nama_lengkap ?? 'Rina Pratiwi, S.Pd.';
        $nipWaliKelas = $waliKelas?->nip ?? '198803152011012014';
        $namaKepsek = $sekolah->kepala_sekolah ?? 'Novia Parenri, S.Si, M.Si';
        $nipKepsek = $sekolah->nip_kepala ?? '198005122005012003';
    @endphp

    <!-- Floating Top Bar (Screen Only) -->
    <div class="no-print sticky top-0 z-50 bg-[#1A1A2E]/95 backdrop-blur-md text-white border-b border-gray-700 py-3 px-6 shadow-lg mb-4">
        <div class="max-w-[1200px] mx-auto flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ url()->previous() ?: route('wali.rapor.show', $siswa->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-xs font-semibold text-gray-200 transition-colors">
                    ← Kembali
                </a>
                <div>
                    <h1 class="text-sm font-bold tracking-tight">
                        Cetak Rapor Gabungan Lengkap (Semua Halaman Sekaligus)
                    </h1>
                    <p class="text-[11px] text-gray-300">
                        {{ $namaSiswa }} (NISN: {{ $nisn }}) · {{ $jenjangSingkat }} · Ketunaan: <strong>{{ $ketunaan }}</strong>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <span class="hidden sm:inline-block px-3 py-1 bg-white/10 rounded-lg text-xs text-gray-200">Format: A4 Portrait</span>
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

    <!-- HALAMAN 1: COVER DEPAN -->
    <div class="rapor-sheet text-center flex flex-col justify-between py-6">
        <div class="space-y-3 pt-6">
            <h1 class="serif-font text-xl font-bold tracking-wider uppercase text-black">
                LAPORAN HASIL BELAJAR<br>PESERTA DIDIK
            </h1>
            <h2 class="serif-font text-base font-bold tracking-wider uppercase text-black">
                {{ $jenjangPanjang }}
            </h2>
            <div class="w-16 h-0.5 bg-black mx-auto my-3"></div>
        </div>

        <div class="space-y-4 my-auto py-12">
            <p class="text-xs text-gray-700">Nama Peserta Didik</p>
            <h3 class="serif-font text-2xl font-bold tracking-wider uppercase text-black">
                {{ $namaSiswa }}
            </h3>
            <p class="serif-font text-sm tracking-widest text-black">
                NISN {{ $nisn }}
            </p>

            <div class="pt-10 text-xs text-gray-800 space-y-1">
                <p>Kelas {{ $tingkat }} / Fase {{ $fase }}</p>
                <p>{{ $kelasNama }}</p>
                <p>Semester {{ $semesterRomawi }}</p>
                <p>Tahun Pelajaran {{ $tahunPelajaran }}</p>
            </div>
        </div>

        <div class="space-y-1.5 pb-6">
            <h4 class="serif-font text-sm font-bold uppercase tracking-wider text-black">
                SLB INSAN MUTIARA PEKANBARU
            </h4>
            <p class="text-xs text-gray-700 leading-tight">
                Jl. Cendana/ Adi Sucipto No. 33<br>
                Sidomulyo Timur, Marpoyan Damai<br>
                Pekanbaru, Riau
            </p>
        </div>
    </div>

    <!-- HALAMAN 2: IDENTITAS & SEKOLAH -->
    <div class="rapor-sheet text-xs leading-relaxed space-y-8 py-6">
        <div class="space-y-4">
            <h3 class="serif-font text-center font-bold text-sm uppercase tracking-wider mb-4 border-b border-black pb-2">
                IDENTITAS PESERTA DIDIK
            </h3>
            <div class="space-y-2.5 text-xs">
                <div class="flex"><span class="w-48 text-gray-700">Nama peserta didik</span><span class="w-4">:</span><span class="font-bold text-black">{{ $namaSiswa }}</span></div>
                <div class="flex"><span class="w-48 text-gray-700">NIS / NISN</span><span class="w-4">:</span><span>{{ $nis }} / {{ $nisn }}</span></div>
                <div class="flex"><span class="w-48 text-gray-700">Jenis kelamin</span><span class="w-4">:</span><span>{{ $siswa->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-Laki' }}</span></div>
                <div class="flex"><span class="w-48 text-gray-700">Tempat, tanggal lahir</span><span class="w-4">:</span><span>{{ $siswa->tempat_lahir ?? 'Pekanbaru' }}, {{ $siswa->tgl_lahir ? \Carbon\Carbon::parse($siswa->tgl_lahir)->translatedFormat('d F Y') : '12 Februari 2013' }}</span></div>
                <div class="flex"><span class="w-48 text-gray-700">Agama</span><span class="w-4">:</span><span>Islam</span></div>
                <div class="flex"><span class="w-48 text-gray-700">Jenis kekhususan</span><span class="w-4">:</span><span>{{ $ketunaan }}</span></div>
                <div class="flex"><span class="w-48 text-gray-700">Alamat peserta didik</span><span class="w-4">:</span><span>Jl. Melati No. 12, Pekanbaru</span></div>
                <div class="flex"><span class="w-48 text-gray-700">Kelas / Fase</span><span class="w-4">:</span><span>{{ $tingkat }} / {{ $fase }} · {{ $kelasNama }}</span></div>
                <div class="flex"><span class="w-48 text-gray-700">Semester / Tahun pelajaran</span><span class="w-4">:</span><span>{{ $semesterRomawi }} / {{ $tahunPelajaran }}</span></div>
                <div class="flex"><span class="w-48 text-gray-700">Tanggal diterima</span><span class="w-4">:</span><span>13 Juli 2026</span></div>
                <div class="flex"><span class="w-48 text-gray-700">Kelas saat diterima</span><span class="w-4">:</span><span>{{ $tingkat }}</span></div>
                <div class="flex"><span class="w-48 text-gray-700">Status dalam keluarga</span><span class="w-4">:</span><span>Anak kandung · Anak ke 1</span></div>
                <div class="flex"><span class="w-48 text-gray-700">Nama ayah</span><span class="w-4">:</span><span>{{ $namaAyah }}</span></div>
                <div class="flex"><span class="w-48 text-gray-700">Nama ibu</span><span class="w-4">:</span><span>{{ $namaIbu }}</span></div>
            </div>
        </div>

        <div class="space-y-4 pt-6 border-t-2 border-black">
            <h3 class="serif-font text-center font-bold text-sm uppercase tracking-wider mb-4 border-b border-black pb-2">
                IDENTITAS SEKOLAH
            </h3>
            <div class="space-y-2.5 text-xs">
                <div class="flex"><span class="w-48 text-gray-700">Nama sekolah</span><span class="w-4">:</span><span class="font-bold text-black">SLB Insan Mutiara Pekanbaru</span></div>
                <div class="flex"><span class="w-48 text-gray-700">NPSN / NSS</span><span class="w-4">:</span><span>10497554 / —</span></div>
                <div class="flex"><span class="w-48 text-gray-700">Alamat sekolah</span><span class="w-4">:</span><span>Jl. Cendana/ Adi Sucipto No. 33</span></div>
                <div class="flex"><span class="w-48 text-gray-700">Kelurahan / Kecamatan</span><span class="w-4">:</span><span>Sidomulyo Timur / Marpoyan Damai</span></div>
                <div class="flex"><span class="w-48 text-gray-700">Kota / Provinsi</span><span class="w-4">:</span><span>Pekanbaru / Riau</span></div>
                <div class="flex"><span class="w-48 text-gray-700">Kepala sekolah</span><span class="w-4">:</span><span class="font-bold uppercase">{{ $namaKepsek }}</span></div>
            </div>
        </div>
    </div>

    <!-- HALAMAN 3: NILAI ASESMEN -->
    <div class="rapor-sheet text-xs leading-relaxed space-y-4 py-6">
        <div class="text-center mb-3">
            <h2 class="serif-font text-sm font-bold tracking-wider uppercase text-black">
                LAPORAN HASIL BELAJAR<br>(RAPORT)
            </h2>
        </div>

        <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-[11px] border-b-2 border-black pb-2.5">
            <div class="space-y-0.5">
                <p class="flex"><span class="w-32 text-gray-700">Nama Peserta Didik</span><span class="w-3">:</span><strong class="uppercase">{{ $namaSiswa }}</strong></p>
                <p class="flex"><span class="w-32 text-gray-700">NISN</span><span class="w-3">:</span><span>{{ $nisn }}</span></p>
                <p class="flex"><span class="w-32 text-gray-700">Sekolah</span><span class="w-3">:</span><span>SLB Insan Mutiara Pekanbaru</span></p>
                <p class="flex"><span class="w-32 text-gray-700">Alamat</span><span class="w-3">:</span><span>Jalan Cendana No. 33A</span></p>
            </div>
            <div class="space-y-0.5">
                <p class="flex"><span class="w-28 text-gray-700">Kelas</span><span class="w-3">:</span><span>{{ $tingkat }} (Tingkat {{ $tingkat }})</span></p>
                <p class="flex"><span class="w-28 text-gray-700">Fase</span><span class="w-3">:</span><span>{{ $fase }}</span></p>
                <p class="flex"><span class="w-28 text-gray-700">Semester</span><span class="w-3">:</span><span>{{ $semesterRomawi }}</span></p>
                <p class="flex"><span class="w-28 text-gray-700">Tahun Pelajaran</span><span class="w-3">:</span><span>{{ $tahunPelajaran }}</span></p>
            </div>
        </div>

        <table class="w-full text-left border-collapse text-[11px] border border-black">
            <thead>
                <tr class="bg-gray-100 border-b border-black font-bold text-center">
                    <th class="py-1.5 px-2 border-r border-black w-8">No</th>
                    <th class="py-1.5 px-3 border-r border-black w-56 text-left">Muatan Pelajaran</th>
                    <th class="py-1.5 px-2 border-r border-black w-16">Nilai Akhir</th>
                    <th class="py-1.5 px-3 text-left">Capaian Kompetensi</th>
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
                    <td class="py-1.5 px-3 align-top leading-snug">Ananda menunjukkan kemampuan yang sangat baik dalam mempresentasikan teks secara visual dan/atau isyarat berdasarkan hasil tulisan dongeng Lancang Kuning.</td>
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
                    <td class="py-1.5 px-3 align-top leading-snug">Ananda mampu menciptakan karya seni rupa dua dimensi dan kerajinan tangan dari bahan ramah lingkungan secara mandiri.</td>
                </tr>
                <tr>
                    <td class="py-1.5 px-2 text-center border-r border-black align-top">9</td>
                    <td class="py-1.5 px-3 border-r border-black font-semibold align-top">PJOK</td>
                    <td class="py-1.5 px-2 text-center font-bold border-r border-black align-top">98</td>
                    <td class="py-1.5 px-3 align-top leading-snug">Ananda sangat aktif berpartisipasi dalam aktivitas kebugaran fisik dan motorik luar ruang.</td>
                </tr>
            </tbody>
        </table>

        <div class="text-right text-[10px] text-gray-600 pt-2 border-t border-black">
            Halaman 1 dari 2 — Laporan Hasil Belajar (Raport)
        </div>
    </div>

    <!-- HALAMAN 4: EVALUASI & PENGESAHAN -->
    <div class="rapor-sheet text-xs leading-relaxed space-y-4 py-6">
        <div class="border-b-2 border-black pb-2 flex justify-between items-center text-[11px]">
            <span class="font-bold uppercase">{{ $namaSiswa }}</span>
            <span>NISN: <strong>{{ $nisn }}</strong> · Kelas: <strong>{{ $tingkat }}</strong></span>
        </div>

        <table class="w-full text-left border-collapse text-[11px] border border-black">
            <thead>
                <tr class="bg-gray-100 border-b border-black font-bold text-center">
                    <th class="py-1.5 px-2 border-r border-black w-8">No</th>
                    <th class="py-1.5 px-3 border-r border-black w-56 text-left">Muatan Pelajaran Lanjutan</th>
                    <th class="py-1.5 px-2 border-r border-black w-16">Nilai Akhir</th>
                    <th class="py-1.5 px-3 text-left">Capaian Kompetensi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black">
                <tr>
                    <td class="py-1.5 px-2 text-center border-r border-black align-top">10</td>
                    <td class="py-1.5 px-3 border-r border-black font-semibold align-top">
                        Keterampilan Pilihan:
                        <div class="text-[10px] text-gray-700 font-normal mt-0.5">• Suvenir<br>• Tata Kecantikan</div>
                    </td>
                    <td class="py-1.5 px-2 text-center font-bold border-r border-black align-top">95<br><span class="text-[10px] font-normal">98</span></td>
                    <td class="py-1.5 px-3 align-top leading-snug">Ananda mampu membuat cinderamata dari daur ulang serta mengenal alat kebersihan dan penataan diri dengan aman.</td>
                </tr>
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

        <!-- Ekstrakurikuler -->
        <div class="space-y-1">
            <p class="font-bold text-[11px] uppercase">Kegiatan Ekstrakurikuler</p>
            <table class="w-full text-left border-collapse text-[11px] border border-black">
                <tbody class="divide-y divide-black">
                    <tr>
                        <td class="py-1.5 px-2 text-center border-r border-black w-8">1</td>
                        <td class="py-1.5 px-3 border-r border-black font-semibold w-56">Pramuka Luar Biasa</td>
                        <td class="py-1.5 px-3">Ananda aktif dan berdisiplin tinggi dalam kegiatan baris-berbaris pramuka khusus.</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 px-2 text-center border-r border-black">2</td>
                        <td class="py-1.5 px-3 border-r border-black font-semibold">Seni Pantomim & Tari Isyarat</td>
                        <td class="py-1.5 px-3">Menunjukkan ekspresi visual dan penghayatan gerak yang luar biasa.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Ketidakhadiran & Prestasi -->
        <div class="grid grid-cols-2 gap-3 text-[11px]">
            <div class="border border-black p-2.5 space-y-1">
                <p class="font-bold border-b border-black pb-0.5 uppercase">Ketidakhadiran</p>
                <div class="flex justify-between"><span>Sakit</span><span>: {{ $sakit }} hari</span></div>
                <div class="flex justify-between"><span>Izin</span><span>: {{ $izin }} hari</span></div>
                <div class="flex justify-between"><span>Tanpa Keterangan</span><span>: {{ $alpa }} hari</span></div>
            </div>

            <div class="border border-black p-2.5 space-y-0.5">
                <p class="font-bold border-b border-black pb-0.5 uppercase">Prestasi yang Diraih</p>
                <p class="text-[10px] leading-snug">• Juara 1 Lomba Hari Disabilitas DINSOS Pekanbaru</p>
                <p class="text-[10px] leading-snug">• Juara Harapan 1 Mendongeng Isyarat FKIP UNRI</p>
            </div>
        </div>

        <!-- Catatan Wali Kelas -->
        <div class="border border-black p-2.5 text-[11px] space-y-0.5">
            <p class="font-bold uppercase">Catatan Wali Kelas</p>
            <p class="italic text-justify leading-relaxed">
                "Prestasi ananda pada semester ini sangat luar biasa! Ananda menunjukkan kemandirian belajar dan sopan santun yang terpuji. Pertahankan semangat belajar dan terus kembangkan bakat keterampilan vokasi untuk masa depan yang gemilang."
            </p>
        </div>

        <!-- Keputusan -->
        <div class="border-2 border-black p-2 text-[11px] text-center font-medium bg-gray-50">
            <p class="font-bold uppercase mb-0.5">Keputusan :</p>
            <p>Berdasarkan hasil yang dicapai, peserta didik dinyatakan: <strong>{{ in_array($tingkat, ['IX', 'XII']) ? 'LULUS' : 'NAIK KE KELAS BERIKUTNYA' }}</strong>.</p>
        </div>

        <!-- Tanda Tangan 3 Pihak -->
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

        <div class="pt-2 text-right text-[10px] text-gray-600 border-t border-black">
            Halaman 2 dari 2 — Laporan Hasil Belajar (Raport)
        </div>
    </div>

</body>
</html>

