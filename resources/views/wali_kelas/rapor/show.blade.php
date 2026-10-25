<x-layouts.wali-kelas
    title="Wali Kelas · Pratinjau Rapor"
    periode="2026/2027 · Semester Ganjil"
    userName="Rina Pratiwi · Wali Kelas">

    @php
        $ketunaan = $siswa->kekhususan ?? 'Tunarungu';
        $namaSiswa = $siswa->nama_lengkap ?? 'Alya Putri';
        $nisn = $siswa->nisn ?? '0160001001';
        $nis = '26' . str_pad($siswa->id, 3, '0', STR_PAD_LEFT);
        $penugasan = $siswa->penugasanAktif();
        $kelasNama = $penugasan?->kelas?->nama ?? ($ketunaan . ' A');
        $tingkat = $penugasan?->tingkat ?? 'VII';
        $fase = $penugasan?->fase ?? 'D';

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

        $semester = $tahunAktif->semester ?? 'Ganjil';
        $semesterRomawi = ($semester === 'Ganjil') ? 'I (Ganjil)' : 'II (Genap)';
        $tahunPelajaran = $tahunAktif->tahun ?? '2026/2027';

        $statusRapor = $rapor?->status ?? 'draft';
        $isDisetujui = ($statusRapor === 'disetujui');

        // Tentukan Program Kekhususan berdasarkan 3 Ketunaan SLB Insan Mutiara
        if (strcasecmp($ketunaan, 'Tunarungu') === 0) {
            $programKekhususanNama = 'Program Kekhususan (PKPBI)';
            $programKekhususanCapaian = 'Ananda sangat baik dalam mengidentifikasi langsung sumber bunyi dan irama lingkungan menggunakan sisa pendengaran secara optimal serta komunikasi isyarat.';
            $programKekhususanNilai = 98;
        } elseif (strcasecmp($ketunaan, 'Tunagrahita') === 0) {
            $programKekhususanNama = 'Program Kekhususan (Bina Diri)';
            $programKekhususanCapaian = 'Ananda mampu melakukan proses bina diri dan perawatan kebersihan diri secara mandiri serta disiplin merapikan perlengkapan belajar.';
            $programKekhususanNilai = 95;
        } else { // Autis
            $programKekhususanNama = 'Program Kekhususan (Pengembangan Komunikasi dan Interaksi)';
            $programKekhususanCapaian = 'Ananda mampu membangun interaksi sosial timbal balik, mempertahankan kontak mata terarah, serta menyampaikan ekspresi kebutuhan dasar secara teratur.';
            $programKekhususanNilai = 96;
        }

        // Orang Tua
        $ayah = $siswa->orangTua?->where('hubungan', 'Ayah')->first();
        $ibu = $siswa->orangTua?->where('hubungan', 'Ibu')->first();
        $namaAyah = $ayah?->nama ?? 'Andi Putra';
        $namaIbu = $ibu?->nama ?? 'Siti Aminah';

        // Evaluasi Non-Akademik
        $evaluasi = $siswa->evaluasiNonakademik?->first();
        $sakit = $evaluasi?->sakit ?? 0;
        $izin = $evaluasi?->izin ?? 3;
        $alpa = $evaluasi?->alpa ?? 0;
        $perkembangan = $evaluasi?->perkembangan ?? 'Pertumbuhan fisik dan kesehatan jasmani berkembang sangat baik. Ananda aktif dan mandiri dalam mengikuti kegiatan belajar di sekolah.';

        // Wali Kelas & Kepala Sekolah
        $waliKelas = $rapor?->waliKelas ?? $penugasan?->kelas?->waliKelasAktif()?->guru;
        $namaWaliKelas = $waliKelas?->nama_lengkap ?? 'Rina Pratiwi, S.Pd.';
        $nipWaliKelas = $waliKelas?->nip ?? '198803152011012014';
        $namaKepsek = $sekolah->kepala_sekolah ?? 'Novia Parenri, S.Si, M.Si';
        $nipKepsek = $sekolah->nip_kepala ?? '198005122005012003';
    @endphp

    <style>
        /* Standar Ukuran Kertas A4 & Cetak Gabungan */
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }

        .serif-title {
            font-family: 'Times New Roman', Times, serif;
        }

        @media print {
            body {
                background: white !important;
                color: black !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .screen-preview-container {
                display: none !important;
            }
            .print-combined-all-pages {
                display: block !important;
            }
            .print-page-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                page-break-after: always !important;
                break-after: page !important;
            }
            .print-page-sheet:last-of-type {
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

    <!-- Top Action Bar (Screen Only - Persis Gambar) -->
    <div class="no-print space-y-1 mb-5">
        <div class="text-[13px] font-normal text-gray-500">
            Wali Kelas / Rapor Saya / {{ $isDisetujui ? 'Pratinjau Rapor' : 'Preview Rapor' }}
        </div>
        <a href="{{ route('wali.rapor.index') }}" class="inline-flex items-center text-xs font-semibold text-[#555783] hover:underline mb-1">
            ← Kembali ke Rapor Saya
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-1">
            <div>
                <h1 class="text-[26px] font-bold text-[#1A1A2E] tracking-tight leading-snug">
                    {{ $isDisetujui ? 'Pratinjau Rapor' : 'Preview Rapor' }}
                </h1>
                <p class="text-[13px] text-gray-500">
                    {{ $namaSiswa }} · {{ $isDisetujui ? 'Arsip 2025/2026 Genap' : 'NISN ' . $nisn }} · {{ $tingkat }} / {{ $fase }} · {{ $isDisetujui ? 'Lulus' : $kelasNama . ' · ' . $tahunPelajaran . ' ' . $semester }}
                </p>
                @if($isDisetujui)
                    <div class="flex items-center gap-2 mt-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#ECFDF5] text-[#065F46] border border-[#A7F3D0]">
                            Disetujui
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                            Arsip · Lulus
                        </span>
                    </div>
                @endif
            </div>

            <!-- Right Buttons: Zoom & Unduh PDF / Ajukan Rapor -->
            <div class="flex items-center gap-2.5">
                <div class="hidden sm:flex items-center px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs font-semibold text-gray-600 shadow-xs">
                    100%
                </div>

                @if(!$rapor || $rapor->status === 'draft' || $rapor->status === 'dikembalikan')
                    <form method="POST" action="{{ route('wali.rapor.submit', $siswa->id) }}">
                        @csrf
                        <button
                            type="submit"
                            class="px-5 py-2.5 rounded-xl bg-[#555783] hover:bg-[#46486E] text-white text-xs font-semibold transition-colors shadow-sm flex items-center gap-2 cursor-pointer"
                        >
                            <span>Ajukan rapor</span>
                        </button>
                    </form>
                @elseif($rapor->status === 'diajukan')
                    <span class="px-3.5 py-2 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        Menunggu Persetujuan
                    </span>
                @endif

                <!-- Tombol Unduh PDF Gabungan Lengkap (Mencetak Semua Halaman Sekaligus) -->
                <button
                    type="button"
                    onclick="window.print()"
                    class="px-5 py-2.5 rounded-xl bg-[#555783] hover:bg-[#46486E] text-white text-xs font-bold transition-all shadow-sm flex items-center gap-2 cursor-pointer active:scale-95"
                    title="Unduh seluruh buku rapor lengkap sekaligus (Cover, Identitas, Nilai, Evaluasi)"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Unduh PDF</span>
                </button>
            </div>
        </div>

        <!-- Banner Progress Status (Persis Gambar 1 & Gambar 2) -->
        <div class="mt-4 p-3.5 rounded-2xl border border-gray-200 bg-white shadow-xs flex items-center justify-between text-xs">
            <div class="flex items-center gap-2.5 font-medium text-gray-700">
                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold shrink-0">✓</span>
                @if($isDisetujui)
                    <span><strong>Rapor historis · Disetujui</strong> {{ $namaSiswa }} · 2025/2026 Genap · {{ $tingkat }} / {{ $fase }} · Lulus</span>
                @else
                    <span>Periksa kelengkapan &nbsp;→&nbsp; <strong class="text-[#555783]">Preview</strong> &nbsp;→&nbsp; Ajukan</span>
                @endif
            </div>

            <div>
                @if($isDisetujui)
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#ECFDF5] text-[#065F46] border border-[#A7F3D0]">
                        Disetujui
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-medium bg-gray-100 text-gray-600 border border-gray-200">
                        Belum diajukan
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- INTERACTIVE 2-COLUMN LAYOUT (PERSIS SESUAI 3 GAMBAR UI)     -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div x-data="{ activeTab: 'cover', subPage: 1 }" class="screen-preview-container grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- ── KOLOM KIRI (SIDEBAR CARDS) ────────────────────────── -->
        <div class="lg:col-span-4 space-y-4">

            @if($isDisetujui)
                <!-- Card 1: Identitas Rapor (Persis Gambar 1) -->
                <div class="p-5 rounded-2xl bg-[#F7F6F2] border border-[#E8E7ED] space-y-3">
                    <h3 class="text-xs font-bold text-[#1A1A2E]">Identitas rapor</h3>
                    <div>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                            Disetujui · Arsip
                        </span>
                    </div>
                    <div class="text-xs text-gray-600 space-y-1 pt-1 leading-relaxed">
                        <p class="font-semibold text-gray-900">{{ $namaSiswa }}</p>
                        <p>NISN: {{ $nisn }}</p>
                        <p>Kelas {{ $tingkat }} · Fase {{ $fase }}</p>
                        <p>Semester II (Genap)</p>
                        <p>Tahun Pelajaran 2025/2026</p>
                        <p>Keputusan: Lulus</p>
                    </div>
                </div>
            @else
                <!-- Card 1: Kelengkapan (Persis Gambar 2 & 3) -->
                <div class="p-5 rounded-2xl bg-[#F7F6F2] border border-[#E8E7ED] space-y-3">
                    <h3 class="text-xs font-bold text-[#1A1A2E]">Kelengkapan</h3>
                    <div>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                            Lengkap
                        </span>
                    </div>
                    <div class="space-y-2 text-xs text-gray-700 pt-1 font-medium">
                        <div class="flex items-center gap-2 text-emerald-600">
                            <span>✓</span>
                            <span class="text-gray-700">Identitas dan periode</span>
                        </div>
                        <div class="flex items-center gap-2 text-emerald-600">
                            <span>✓</span>
                            <span class="text-gray-700">13 nilai & capaian</span>
                        </div>
                        <div class="flex items-center gap-2 text-emerald-600">
                            <span>✓</span>
                            <span class="text-gray-700">Kehadiran & kegiatan</span>
                        </div>
                        <div class="flex items-center gap-2 text-emerald-600">
                            <span>✓</span>
                            <span class="text-gray-700">Prestasi: tidak ada</span>
                        </div>
                        <div class="flex items-center gap-2 text-emerald-600">
                            <span>✓</span>
                            <span class="text-gray-700">Fisik & kesehatan</span>
                        </div>
                        <div class="flex items-center gap-2 text-emerald-600">
                            <span>✓</span>
                            <span class="text-gray-700">Catatan wali kelas</span>
                        </div>
                        <div class="flex items-center gap-2 text-emerald-600">
                            <span>✓</span>
                            <span class="text-gray-700">Tanggal rapor</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Card 2: Identitas Sekolah (Persis Gambar 1, 2 & 3) -->
            <div class="p-5 rounded-2xl bg-[#F7F6F2] border border-[#E8E7ED] space-y-2 text-xs text-gray-600 leading-relaxed">
                <h3 class="font-bold text-[#1A1A2E]">Identitas sekolah</h3>
                <p class="font-bold text-[#1A1A2E] pt-1">SLB Insan Mutiara Pekanbaru</p>
                <p>Jl. Cendana/ Adi Sucipto No. 33<br>Sidomulyo Timur, Marpoyan Damai<br>Pekanbaru, Riau</p>
                <p class="text-gray-500 pt-1">NPSN / NSS: —</p>
            </div>

            <!-- Card 3: Navigasi Sub-Halaman (Persis Gambar 1) -->
            <div class="p-5 rounded-2xl bg-[#F7F6F2] border border-[#E8E7ED] space-y-3 text-xs">
                <h3 class="font-bold text-[#1A1A2E]">3 halaman</h3>
                <div class="space-y-1.5 font-medium">
                    <button
                        type="button"
                        @click="activeTab = 'nilai'; subPage = 1"
                        class="w-full text-left py-1 text-xs transition-colors flex items-center justify-between cursor-pointer"
                        :class="activeTab === 'nilai' && subPage === 1 ? 'text-[#555783] font-bold' : 'text-gray-600 hover:text-gray-900'"
                    >
                        <span>Hasil belajar · aktif</span>
                        <span x-show="activeTab === 'nilai' && subPage === 1" class="w-1.5 h-1.5 rounded-full bg-[#555783]"></span>
                    </button>
                    <button
                        type="button"
                        @click="activeTab = 'evaluasi'; subPage = 2"
                        class="w-full text-left py-1 text-xs transition-colors flex items-center justify-between cursor-pointer"
                        :class="activeTab === 'evaluasi' && subPage === 2 ? 'text-[#555783] font-bold' : 'text-gray-600 hover:text-gray-900'"
                    >
                        <span>Hasil lanjutan dan evaluasi</span>
                        <span x-show="activeTab === 'evaluasi' && subPage === 2" class="w-1.5 h-1.5 rounded-full bg-[#555783]"></span>
                    </button>
                    <button
                        type="button"
                        @click="activeTab = 'evaluasi'; subPage = 3"
                        class="w-full text-left py-1 text-xs transition-colors flex items-center justify-between cursor-pointer"
                        :class="activeTab === 'evaluasi' && subPage === 3 ? 'text-[#555783] font-bold' : 'text-gray-600 hover:text-gray-900'"
                    >
                        <span>Keputusan & tanda tangan</span>
                        <span x-show="activeTab === 'evaluasi' && subPage === 3" class="w-1.5 h-1.5 rounded-full bg-[#555783]"></span>
                    </button>
                </div>
                <div class="pt-3 border-t border-gray-200 text-[11px] text-gray-500 flex items-center justify-between">
                    <span>Halaman <span x-text="subPage"></span> dari 3</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" @click="subPage = Math.max(1, subPage - 1)" class="hover:text-gray-800 cursor-pointer">← Sebelumnya</button>
                        <span>•</span>
                        <button type="button" @click="subPage = Math.min(3, subPage + 1)" class="hover:text-gray-800 cursor-pointer">Berikutnya →</button>
                    </div>
                </div>
            </div>

            <!-- Tombol Cepat Buka Halaman Cetak Bersih Standalone -->
            <a
                href="{{ route('wali.rapor.cetak', $siswa->id) }}"
                target="_blank"
                class="w-full py-2.5 px-4 rounded-xl border border-gray-300 bg-white hover:bg-gray-50 text-xs font-semibold text-gray-700 flex items-center justify-center gap-2 transition-colors shadow-xs"
            >
                <svg class="w-4 h-4 text-[#555783]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                <span>Buka Lembar Cetak Penuh (Tab Baru)</span>
            </a>

        </div>

        <!-- ── KOLOM KANAN (TAB BAR & SIMULATOR KERTAS A4) ────────── -->
        <div class="lg:col-span-8 space-y-3">

            <!-- Tab Navigation Bar (Persis Gambar: Cover | Identitas | Hasil Belajar | Evaluasi) -->
            <div class="bg-gray-100 p-1 rounded-xl flex items-center border border-gray-200 text-xs font-medium text-gray-600">
                <button
                    type="button"
                    @click="activeTab = 'cover'"
                    class="flex-1 py-2 text-center rounded-lg transition-all cursor-pointer"
                    :class="activeTab === 'cover' ? 'bg-[#555783]/15 text-[#1A1A2E] font-bold shadow-xs bg-white' : 'hover:text-gray-900'"
                >
                    Cover
                </button>
                <button
                    type="button"
                    @click="activeTab = 'identitas'"
                    class="flex-1 py-2 text-center rounded-lg transition-all cursor-pointer"
                    :class="activeTab === 'identitas' ? 'bg-[#555783]/15 text-[#1A1A2E] font-bold shadow-xs bg-white' : 'hover:text-gray-900'"
                >
                    Identitas
                </button>
                <button
                    type="button"
                    @click="activeTab = 'nilai'; subPage = 1"
                    class="flex-1 py-2 text-center rounded-lg transition-all cursor-pointer"
                    :class="activeTab === 'nilai' ? 'bg-[#555783]/15 text-[#1A1A2E] font-bold shadow-xs bg-white' : 'hover:text-gray-900'"
                >
                    Hasil Belajar
                </button>
                <button
                    type="button"
                    @click="activeTab = 'evaluasi'; subPage = 2"
                    class="flex-1 py-2 text-center rounded-lg transition-all cursor-pointer"
                    :class="activeTab === 'evaluasi' ? 'bg-[#555783]/15 text-[#1A1A2E] font-bold shadow-xs bg-white' : 'hover:text-gray-900'"
                >
                    Evaluasi
                </button>
            </div>

            <!-- Subtitle Header Bar di Atas Kertas (Persis Gambar 1, 2 & 3) -->
            <div class="flex items-center justify-between px-1 text-xs text-gray-500 font-medium">
                <div>
                    <span x-text="activeTab === 'cover' ? 'Cover' : (activeTab === 'identitas' ? 'Identitas' : (activeTab === 'nilai' ? 'Hasil Belajar' : 'Evaluasi'))"></span> · {{ $namaSiswa }}
                </div>
                <div>
                    A4 · 100%
                </div>
            </div>

            <!-- ── SIMULASI KANVAS KERTAS A4 (PERSIS GAMBAR) ────────── -->
            <div class="bg-[#E4E6ED] rounded-2xl p-6 sm:p-10 flex flex-col items-center shadow-inner overflow-x-auto min-h-[920px]">
                
                <!-- 1. KERTAS: COVER (Persis Gambar 2) -->
                <div x-show="activeTab === 'cover'" class="bg-white border border-gray-300 shadow-md w-full max-w-[680px] min-h-[860px] p-10 sm:p-14 text-center flex flex-col justify-between text-black">
                    <div class="space-y-4 pt-6">
                        <h2 class="serif-title text-xl font-bold tracking-wider uppercase text-black leading-snug">
                            LAPORAN HASIL BELAJAR<br>PESERTA DIDIK
                        </h2>
                        <h3 class="serif-title text-base font-bold tracking-wider uppercase text-black">
                            {{ $jenjangPanjang }}
                        </h3>
                        <div class="w-16 h-0.5 bg-black mx-auto my-3"></div>
                    </div>

                    <div class="space-y-3 my-auto py-8">
                        <p class="text-xs text-gray-600">Nama Peserta Didik</p>
                        <h4 class="serif-title text-2xl font-bold tracking-wider uppercase text-black">
                            {{ $namaSiswa }}
                        </h4>
                        <p class="serif-title text-sm tracking-widest text-black">
                            NISN {{ $nisn }}
                        </p>

                        <div class="pt-8 text-xs text-gray-800 space-y-1">
                            <p>Kelas {{ $tingkat }} / Fase {{ $fase }}</p>
                            <p>{{ $kelasNama }}</p>
                            <p>Semester {{ $semesterRomawi }}</p>
                            <p>Tahun Pelajaran {{ $tahunPelajaran }}</p>
                        </div>
                    </div>

                    <div class="space-y-1.5 pb-4">
                        <h5 class="serif-title text-sm font-bold uppercase tracking-wider text-black">
                            SLB INSAN MUTIARA PEKANBARU
                        </h5>
                        <p class="text-[11px] text-gray-700 leading-tight">
                            Jl. Cendana/ Adi Sucipto No. 33<br>
                            Sidomulyo Timur, Marpoyan Damai<br>
                            Pekanbaru, Riau
                        </p>
                    </div>
                </div>

                <!-- 2. KERTAS: IDENTITAS (Persis Gambar 3) -->
                <div x-show="activeTab === 'identitas'" class="bg-white border border-gray-300 shadow-md w-full max-w-[680px] min-h-[860px] p-8 sm:p-12 text-black text-xs leading-relaxed space-y-6">
                    <div class="space-y-3">
                        <h3 class="serif-title text-center font-bold text-sm uppercase tracking-wider mb-4">
                            IDENTITAS PESERTA DIDIK
                        </h3>
                        <div class="space-y-2 text-xs">
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

                    <div class="space-y-3 pt-6 border-t border-gray-200">
                        <h3 class="serif-title text-center font-bold text-sm uppercase tracking-wider mb-4">
                            IDENTITAS SEKOLAH
                        </h3>
                        <div class="space-y-2 text-xs">
                            <div class="flex"><span class="w-48 text-gray-700">Nama sekolah</span><span class="w-4">:</span><span class="font-bold text-black">SLB Insan Mutiara Pekanbaru</span></div>
                            <div class="flex"><span class="w-48 text-gray-700">NPSN / NSS</span><span class="w-4">:</span><span>10497554 / —</span></div>
                            <div class="flex"><span class="w-48 text-gray-700">Alamat sekolah</span><span class="w-4">:</span><span>Jl. Cendana/ Adi Sucipto No. 33</span></div>
                            <div class="flex"><span class="w-48 text-gray-700">Kelurahan / Kecamatan</span><span class="w-4">:</span><span>Sidomulyo Timur / Marpoyan Damai</span></div>
                            <div class="flex"><span class="w-48 text-gray-700">Kota / Provinsi</span><span class="w-4">:</span><span>Pekanbaru / Riau</span></div>
                            <div class="flex"><span class="w-48 text-gray-700">Kepala sekolah</span><span class="w-4">:</span><span class="font-bold uppercase">{{ $namaKepsek }}</span></div>
                        </div>
                    </div>
                </div>

                <!-- 3. KERTAS: HASIL BELAJAR (Persis Gambar 1) -->
                <div x-show="activeTab === 'nilai'" class="bg-white border border-gray-300 shadow-md w-full max-w-[680px] min-h-[860px] p-6 sm:p-10 text-black text-xs leading-relaxed space-y-4">
                    <div class="text-center mb-3">
                        <h2 class="serif-title text-sm font-bold tracking-wider uppercase text-black">
                            LAPORAN HASIL BELAJAR<br>(RAPORT)
                        </h2>
                    </div>

                    <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-[11px] border-b border-gray-300 pb-2.5">
                        <div class="space-y-0.5">
                            <p class="flex"><span class="w-32 text-gray-600">Nama Peserta Didik</span><span class="w-3">:</span><strong>{{ $namaSiswa }}</strong></p>
                            <p class="flex"><span class="w-32 text-gray-600">NISN</span><span class="w-3">:</span><span>{{ $nisn }}</span></p>
                            <p class="flex"><span class="w-32 text-gray-600">Sekolah</span><span class="w-3">:</span><span>SLB Insan Mutiara Pekanbaru</span></p>
                            <p class="flex"><span class="w-32 text-gray-600">Alamat</span><span class="w-3">:</span><span>Jalan Cendana No. 33A</span></p>
                        </div>
                        <div class="space-y-0.5">
                            <p class="flex"><span class="w-28 text-gray-600">Kelas</span><span class="w-3">:</span><span>{{ $tingkat }} (Tingkat {{ $tingkat }})</span></p>
                            <p class="flex"><span class="w-28 text-gray-600">Fase</span><span class="w-3">:</span><span>{{ $fase }}</span></p>
                            <p class="flex"><span class="w-28 text-gray-600">Semester</span><span class="w-3">:</span><span>{{ $semesterRomawi }}</span></p>
                            <p class="flex"><span class="w-28 text-gray-600">Tahun Pelajaran</span><span class="w-3">:</span><span>{{ $tahunPelajaran }}</span></p>
                        </div>
                    </div>

                    <table class="w-full text-left border-collapse text-[10px] sm:text-[11px] border border-black">
                        <thead>
                            <tr class="bg-gray-100 border-b border-black font-bold text-center">
                                <th class="py-1.5 px-1.5 border-r border-black w-7">No</th>
                                <th class="py-1.5 px-2.5 border-r border-black w-48 text-left">Muatan Pelajaran</th>
                                <th class="py-1.5 px-2 border-r border-black w-16">Nilai Akhir</th>
                                <th class="py-1.5 px-2.5 text-left">Capaian Kompetensi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black">
                            <tr>
                                <td class="py-1 px-1.5 text-center border-r border-black align-top">1</td>
                                <td class="py-1 px-2.5 border-r border-black font-semibold align-top">Pendidikan Agama dan Budi Pekerti</td>
                                <td class="py-1 px-2 text-center font-bold border-r border-black align-top">90</td>
                                <td class="py-1 px-2.5 align-top leading-tight">Ananda menunjukkan banyak perubahan dalam berinteraksi dan bertingkah laku sesuai dengan prinsip ajaran keagamaan.</td>
                            </tr>
                            <tr>
                                <td class="py-1 px-1.5 text-center border-r border-black align-top">2</td>
                                <td class="py-1 px-2.5 border-r border-black font-semibold align-top">Pendidikan Pancasila</td>
                                <td class="py-1 px-2 text-center font-bold border-r border-black align-top">98</td>
                                <td class="py-1 px-2.5 align-top leading-tight">Ananda mampu menghargai keberagaman suku dan agama. Perlu bimbingan lanjutan dalam memahami makna semboyan Bhinneka Tunggal Ika melalui visual.</td>
                            </tr>
                            <tr>
                                <td class="py-1 px-1.5 text-center border-r border-black align-top">3</td>
                                <td class="py-1 px-2.5 border-r border-black font-semibold align-top">Bahasa Indonesia</td>
                                <td class="py-1 px-2 text-center font-bold border-r border-black align-top">98</td>
                                <td class="py-1 px-2.5 align-top leading-tight">Ananda menunjukkan kemampuan yang sangat baik dalam mempresentasikan teks secara visual dan/atau isyarat berdasarkan hasil tulisan dongeng Lancang Kuning.</td>
                            </tr>
                            <tr>
                                <td class="py-1 px-1.5 text-center border-r border-black align-top">4</td>
                                <td class="py-1 px-2.5 border-r border-black font-semibold align-top">Matematika</td>
                                <td class="py-1 px-2 text-center font-bold border-r border-black align-top">95</td>
                                <td class="py-1 px-2.5 align-top leading-tight">Ananda terampil menentukan satuan waktu (jam dan menit) dalam kehidupan sehari-hari dan membaca jam analog secara mandiri.</td>
                            </tr>
                            <tr>
                                <td class="py-1 px-1.5 text-center border-r border-black align-top">5</td>
                                <td class="py-1 px-2.5 border-r border-black font-semibold align-top">Bahasa Inggris</td>
                                <td class="py-1 px-2 text-center font-bold border-r border-black align-top">86</td>
                                <td class="py-1 px-2.5 align-top leading-tight">Ananda mampu mengidentifikasi teks inti sederhana melalui pengenalan diri (self introduction).</td>
                            </tr>
                            <tr>
                                <td class="py-1 px-1.5 text-center border-r border-black align-top">6</td>
                                <td class="py-1 px-2.5 border-r border-black font-semibold align-top">Ilmu Pengetahuan Alam (IPA)</td>
                                <td class="py-1 px-2 text-center font-bold border-r border-black align-top">95</td>
                                <td class="py-1 px-2.5 align-top leading-tight">Ananda mampu menceritakan kembali fenomena perubahan iklim dan cara menjaga kebersihan lingkungan dari banjir.</td>
                            </tr>
                            <tr>
                                <td class="py-1 px-1.5 text-center border-r border-black align-top">7</td>
                                <td class="py-1 px-2.5 border-r border-black font-semibold align-top">Ilmu Pengetahuan Sosial (IPS)</td>
                                <td class="py-1 px-2 text-center font-bold border-r border-black align-top">90</td>
                                <td class="py-1 px-2.5 align-top leading-tight">Ananda mampu mengidentifikasi upaya pengurangan risiko bencana lingkungan sekitar sekolah.</td>
                            </tr>
                            <tr>
                                <td class="py-1 px-1.5 text-center border-r border-black align-top">8</td>
                                <td class="py-1 px-2.5 border-r border-black font-semibold align-top">Seni Budaya (Seni Rupa)</td>
                                <td class="py-1 px-2 text-center font-bold border-r border-black align-top">98</td>
                                <td class="py-1 px-2.5 align-top leading-tight">Ananda mampu menciptakan karya seni rupa dua dimensi dan kerajinan tangan dari bahan ramah lingkungan secara mandiri.</td>
                            </tr>
                            <tr>
                                <td class="py-1 px-1.5 text-center border-r border-black align-top">9</td>
                                <td class="py-1 px-2.5 border-r border-black font-semibold align-top">PJOK</td>
                                <td class="py-1 px-2 text-center font-bold border-r border-black align-top">98</td>
                                <td class="py-1 px-2.5 align-top leading-tight">Ananda sangat aktif berpartisipasi dalam aktivitas kebugaran fisik dan motorik luar ruang.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- 4. KERTAS: EVALUASI & PENGESAHAN (Persis Halaman 2 & 3 PDF) -->
                <div x-show="activeTab === 'evaluasi'" class="bg-white border border-gray-300 shadow-md w-full max-w-[680px] min-h-[860px] p-6 sm:p-10 text-black text-xs leading-relaxed space-y-4">
                    <div class="border-b border-black pb-2 flex justify-between items-center text-[11px]">
                        <span class="font-bold uppercase">{{ $namaSiswa }}</span>
                        <span>NISN: <strong>{{ $nisn }}</strong> · Kelas: <strong>{{ $tingkat }}</strong></span>
                    </div>

                    <!-- Program Kekhususan & Mapel Lanjutan -->
                    <table class="w-full text-left border-collapse text-[10px] sm:text-[11px] border border-black">
                        <thead>
                            <tr class="bg-gray-100 border-b border-black font-bold text-center">
                                <th class="py-1.5 px-1.5 border-r border-black w-7">No</th>
                                <th class="py-1.5 px-2.5 border-r border-black w-48 text-left">Muatan Pelajaran Lanjutan</th>
                                <th class="py-1.5 px-2 border-r border-black w-16">Nilai Akhir</th>
                                <th class="py-1.5 px-2.5 text-left">Capaian Kompetensi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black">
                            <tr>
                                <td class="py-1 px-1.5 text-center border-r border-black align-top">10</td>
                                <td class="py-1 px-2.5 border-r border-black font-semibold align-top">
                                    Keterampilan Pilihan:
                                    <div class="text-[10px] text-gray-700 font-normal mt-0.5">• Suvenir<br>• Tata Kecantikan</div>
                                </td>
                                <td class="py-1 px-2 text-center font-bold border-r border-black align-top">95<br><span class="text-[10px] font-normal">98</span></td>
                                <td class="py-1 px-2.5 align-top leading-tight">Ananda mampu membuat cinderamata dari daur ulang serta mengenal alat kebersihan dan penataan diri dengan aman.</td>
                            </tr>
                            <tr class="bg-indigo-50/40">
                                <td class="py-1 px-1.5 text-center border-r border-black align-top font-bold text-[#555783]">11</td>
                                <td class="py-1 px-2.5 border-r border-black font-bold text-[#555783] align-top">
                                    {{ $programKekhususanNama }}
                                    <span class="block text-[9px] font-normal text-gray-600">Khusus ketunaan {{ $ketunaan }}</span>
                                </td>
                                <td class="py-1 px-2 text-center font-bold border-r border-black align-top text-[#555783]">{{ $programKekhususanNilai }}</td>
                                <td class="py-1 px-2.5 align-top leading-tight font-medium">{{ $programKekhususanCapaian }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 px-1.5 text-center border-r border-black align-top">12</td>
                                <td class="py-1 px-2.5 border-r border-black font-semibold align-top">Muatan Lokal (Budaya Melayu Riau)</td>
                                <td class="py-1 px-2 text-center font-bold border-r border-black align-top">95</td>
                                <td class="py-1 px-2.5 align-top leading-tight">Ananda menunjukkan pemahaman yang sangat baik tentang cerita rakyat Melayu Riau (Lancang Kuning).</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Ekstrakurikuler -->
                    <div class="space-y-1">
                        <p class="font-bold text-[11px] uppercase">Kegiatan Ekstrakurikuler</p>
                        <table class="w-full text-left border-collapse text-[10px] sm:text-[11px] border border-black">
                            <tbody class="divide-y divide-black">
                                <tr>
                                    <td class="py-1 px-2 text-center border-r border-black w-7">1</td>
                                    <td class="py-1 px-2.5 border-r border-black font-semibold w-48">Pramuka Luar Biasa</td>
                                    <td class="py-1 px-2.5">Aktif dan disiplin dalam baris-berbaris pramuka khusus.</td>
                                </tr>
                                <tr>
                                    <td class="py-1 px-2 text-center border-r border-black">2</td>
                                    <td class="py-1 px-2.5 border-r border-black font-semibold">Seni Pantomim & Tari Isyarat</td>
                                    <td class="py-1 px-2.5">Ekspresi gerak visual dan penghayatan sangat baik.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Ketidakhadiran & Catatan -->
                    <div class="grid grid-cols-2 gap-2 text-[10px] sm:text-[11px]">
                        <div class="border border-black p-2 space-y-0.5">
                            <p class="font-bold border-b border-black pb-0.5 uppercase">Ketidakhadiran</p>
                            <div class="flex justify-between"><span>Sakit</span><span>: {{ $sakit }} hari</span></div>
                            <div class="flex justify-between"><span>Izin</span><span>: {{ $izin }} hari</span></div>
                            <div class="flex justify-between"><span>Tanpa Keterangan</span><span>: {{ $alpa }} hari</span></div>
                        </div>
                        <div class="border border-black p-2 space-y-0.5">
                            <p class="font-bold border-b border-black pb-0.5 uppercase">Prestasi yang Diraih</p>
                            <p>• Juara 1 Hari Disabilitas DINSOS</p>
                            <p>• Juara Harapan 1 Mendongeng FKIP UNRI</p>
                        </div>
                    </div>

                    <!-- Catatan Wali Kelas -->
                    <div class="border border-black p-2 text-[10px] sm:text-[11px] space-y-0.5">
                        <p class="font-bold uppercase">Catatan Wali Kelas</p>
                        <p class="italic leading-snug">
                            "Prestasi ananda pada semester ini sangat luar biasa! Ananda menunjukkan kemandirian belajar dan sopan santun yang terpuji. Pertahankan semangat belajar."
                        </p>
                    </div>

                    <!-- Keputusan Kelulusan / Kenaikan -->
                    <div class="border border-black p-2 text-[11px] text-center font-medium bg-gray-50">
                        <p class="font-bold uppercase mb-0.5">Keputusan :</p>
                        <p>Berdasarkan hasil yang dicapai, peserta didik dinyatakan: <strong>{{ in_array($tingkat, ['IX', 'XII']) ? 'LULUS' : 'NAIK KE KELAS BERIKUTNYA' }}</strong>.</p>
                    </div>

                    <!-- Tanda Tangan 3 Pihak -->
                    <div class="pt-3 grid grid-cols-2 text-center text-[10px] sm:text-[11px] gap-y-8">
                        <div>
                            <p class="mb-10">Orang Tua/ Wali</p>
                            <p class="font-bold underline underline-offset-4">....................................</p>
                        </div>
                        <div>
                            <p class="mb-1">Pekanbaru, 19 Desember 2026</p>
                            <p class="mb-10">Wali Kelas,</p>
                            <p class="font-bold underline underline-offset-4 uppercase">{{ $namaWaliKelas }}</p>
                            <p class="text-[9px] text-gray-700">NIP. {{ $nipWaliKelas }}</p>
                        </div>

                        <div class="col-span-2 text-center -mt-2">
                            <p class="mb-10">Mengetahui,<br>Kepala Sekolah</p>
                            <p class="font-bold underline underline-offset-4 uppercase">{{ $namaKepsek }}</p>
                            <p class="text-[9px] text-gray-700">NIP. {{ $nipKepsek }}</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>


    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- PRINT-ONLY CONTAINER: GABUNGAN SEMUA HALAMAN SEKALIGUS      -->
    <!-- Ketika tombol 'Unduh PDF' diklik, SEMUA HALAMAN INI DICETAK -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="hidden print-combined-all-pages space-y-0 text-black">
        
        <!-- HALAMAN 1: COVER DEPAN -->
        <div class="print-page-sheet text-center flex flex-col justify-between py-6">
            <div class="space-y-3 pt-6">
                <h1 class="serif-title text-xl font-bold tracking-wider uppercase text-black">
                    LAPORAN HASIL BELAJAR<br>PESERTA DIDIK
                </h1>
                <h2 class="serif-title text-base font-bold tracking-wider uppercase text-black">
                    {{ $jenjangPanjang }}
                </h2>
                <div class="w-16 h-0.5 bg-black mx-auto my-3"></div>
            </div>

            <div class="space-y-4 my-auto py-12">
                <p class="text-xs text-gray-700">Nama Peserta Didik</p>
                <h3 class="serif-title text-2xl font-bold tracking-wider uppercase text-black">
                    {{ $namaSiswa }}
                </h3>
                <p class="serif-title text-sm tracking-widest text-black">
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
                <h4 class="serif-title text-sm font-bold uppercase tracking-wider text-black">
                    SLB INSAN MUTIARA PEKANBARU
                </h4>
                <p class="text-xs text-gray-700 leading-tight">
                    Jl. Cendana/ Adi Sucipto No. 33<br>
                    Sidomulyo Timur, Marpoyan Damai<br>
                    Pekanbaru, Riau
                </p>
            </div>
        </div>

        <!-- HALAMAN 2: IDENTITAS PESERTA DIDIK & SEKOLAH -->
        <div class="print-page-sheet text-xs leading-relaxed space-y-8 py-6">
            <div class="space-y-4">
                <h3 class="serif-title text-center font-bold text-sm uppercase tracking-wider mb-4 border-b border-black pb-2">
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
                <h3 class="serif-title text-center font-bold text-sm uppercase tracking-wider mb-4 border-b border-black pb-2">
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

        <!-- HALAMAN 3: NILAI ASESMEN & CAPAIAN KOMPETENSI -->
        <div class="print-page-sheet text-xs leading-relaxed space-y-4 py-6">
            <div class="text-center mb-3">
                <h2 class="serif-title text-sm font-bold tracking-wider uppercase text-black">
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

        <!-- HALAMAN 4: EVALUASI NONAKADEMIK, PROGRAM KHUSUS & PENGESAHAN -->
        <div class="print-page-sheet text-xs leading-relaxed space-y-4 py-6">
            <div class="border-b-2 border-black pb-2 flex justify-between items-center text-[11px]">
                <span class="font-bold uppercase">{{ $namaSiswa }}</span>
                <span>NISN: <strong>{{ $nisn }}</strong> · Kelas: <strong>{{ $tingkat }}</strong></span>
            </div>

            <!-- Muatan Pelajaran Lanjutan & Program Kekhususan -->
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

    </div>

</x-layouts.wali-kelas>
