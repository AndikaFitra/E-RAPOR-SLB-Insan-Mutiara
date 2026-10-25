<x-layouts.wali-kelas
    title="Wali Kelas · Laporan Hasil Belajar (Raport)"
    periode="2026/2027 · Semester Ganjil"
    userName="Rina Pratiwi · Wali Kelas">

    @php
        $ketunaan = $siswa->kekhususan ?? 'Tunarungu';
        $namaSiswa = $siswa->nama_lengkap ?? 'Friska Carolin Monalisa';
        $nisn = $siswa->nisn ?? '0053935552';
        $penugasan = $siswa->penugasanAktif();
        $kelasNama = $penugasan?->kelas?->nama ?? ($ketunaan . ' A');
        $tingkat = $penugasan?->tingkat ?? 'XII';
        $fase = $penugasan?->fase ?? 'F';
        
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
        $wali = $siswa->orangTua?->where('hubungan', 'Wali')->first();
        $namaAyah = $ayah?->nama ?? 'Usman';
        $namaIbu = $ibu?->nama ?? 'Rini Yuniati';
        $namaWali = $wali?->nama ?? '-';
        $noTelpOrtu = $ayah?->no_whatsapp ?? ($ibu?->no_whatsapp ?? '085263592816');

        // Evaluasi Non-Akademik
        $evaluasi = $siswa->evaluasiNonakademik?->first();
        $sakit = $evaluasi?->sakit ?? 0;
        $izin = $evaluasi?->izin ?? 3;
        $alpa = $evaluasi?->alpa ?? 0;
        $perkembangan = $evaluasi?->perkembangan ?? 'Pertumbuhan fisik dan kesehatan jasmani berkembang sangat baik. Ananda aktif dan mandiri dalam mengikuti kegiatan belajar di sekolah.';

        // Wali Kelas & Kepala Sekolah
        $waliKelas = $rapor?->waliKelas ?? $penugasan?->kelas?->waliKelasAktif()?->guru;
        $namaWaliKelas = $waliKelas?->nama_lengkap ?? 'RINA PRATIWI, S.Pd.';
        $nipWaliKelas = $waliKelas?->nip ?? '198803152011012014';
        $namaKepsek = $sekolah->kepala_sekolah ?? 'NOVIA PARENRI, S.Si, M.Si';
        $nipKepsek = $sekolah->nip_kepala ?? '198005122005012003';
    @endphp

    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
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
            .print-only-combined {
                display: block !important;
            }
            .screen-only-tabs {
                display: none !important;
            }
            .print-sheet {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                min-height: 0 !important;
                page-break-after: always !important;
                break-after: page !important;
            }
            .print-sheet:last-of-type {
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
    </style>

    <!-- Top Action Bar (Screen Only) -->
    <div class="no-print space-y-1 mb-6">
        <div class="text-[13px] font-normal text-gray-500">
            Wali Kelas / Rapor Saya / Laporan Hasil Belajar
        </div>
        <a href="{{ route('wali.rapor.index') }}" class="inline-flex items-center text-xs font-semibold text-[#555783] hover:underline mb-2">
            ← Kembali ke Rapor Saya
        </a>
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pt-1">
            <div>
                <h1 class="text-[26px] font-bold text-[#1A1A2E] tracking-tight leading-snug">
                    Laporan Hasil Belajar (Raport)
                </h1>
                <p class="text-[13px] text-gray-500">
                    {{ $namaSiswa }} · NISN {{ $nisn }} · Kelas {{ $kelasNama }} (Ketunaan: <strong>{{ $ketunaan }}</strong>)
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5">
                @if(!$rapor || $rapor->status === 'draft' || $rapor->status === 'dikembalikan')
                    <form method="POST" action="{{ route('wali.rapor.submit', $siswa->id) }}">
                        @csrf
                        <button
                            type="submit"
                            class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors shadow-sm flex items-center gap-2 cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Ajukan ke Kepala Sekolah</span>
                        </button>
                    </form>
                @elseif($rapor->status === 'diajukan')
                    <span class="px-3 py-2 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        Menunggu Persetujuan
                    </span>
                @elseif($rapor->status === 'disetujui')
                    <span class="px-3 py-2 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Rapor Disetujui
                    </span>
                @endif

                <!-- Buka Lembar Cetak Bersih (New Tab) -->
                <a
                    href="{{ route('wali.rapor.cetak', $siswa->id) }}"
                    target="_blank"
                    class="px-3.5 py-2.5 rounded-xl bg-white border border-gray-300 hover:bg-gray-50 text-[#1A1A2E] text-xs font-semibold transition-colors shadow-xs flex items-center gap-1.5"
                    title="Buka halaman pratinjau cetak penuh di tab baru"
                >
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span>Halaman Cetak Bersih</span>
                </a>

                <!-- Unduh / Cetak Gabungan Langsung -->
                <button
                    type="button"
                    onclick="window.print()"
                    class="px-4 py-2.5 rounded-xl bg-[#555783] hover:bg-[#46486E] text-white text-xs font-semibold transition-all shadow-sm flex items-center gap-2 cursor-pointer active:scale-95"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak / Unduh Rapor Lengkap (PDF)</span>
                </button>
            </div>
        </div>

        @if($rapor && $rapor->status === 'dikembalikan' && $rapor->catatan_revisi)
            <div class="mt-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs">
                <p class="font-bold text-rose-800 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Catatan Revisi dari Kepala Sekolah (Novia Parenri, S.Si, M.Si):
                </p>
                <p class="text-rose-700 mt-1 pl-5.5 leading-relaxed">{{ $rapor->catatan_revisi }}</p>
            </div>
        @endif
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- INTERACTIVE SCREEN CONTAINER (TAB SELECTION)                -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div x-data="{ activeTab: 'all' }" class="screen-only-tabs space-y-6">

        <!-- Navigation Tabs (Screen Only) -->
        <div class="no-print flex flex-wrap items-center gap-2 border-b border-[#E8E7ED] pb-3 text-xs font-semibold">
            <!-- Tab Gabungan Semua Halaman -->
            <button
                type="button"
                @click="activeTab = 'all'"
                class="px-4 py-2 rounded-xl transition-all flex items-center gap-1.5 cursor-pointer"
                :class="activeTab === 'all' ? 'bg-[#555783] text-white shadow-xs' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>📖 Gabungan Semua Halaman (Buku Rapor Lengkap)</span>
            </button>

            <!-- Per-Page Tabs -->
            <button
                type="button"
                @click="activeTab = 'cover'"
                class="px-3.5 py-2 rounded-xl transition-all cursor-pointer"
                :class="activeTab === 'cover' ? 'bg-[#555783] text-white shadow-xs' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'"
            >
                1. Cover Depan
            </button>
            <button
                type="button"
                @click="activeTab = 'petunjuk'"
                class="px-3.5 py-2 rounded-xl transition-all cursor-pointer"
                :class="activeTab === 'petunjuk' ? 'bg-[#555783] text-white shadow-xs' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'"
            >
                2. Identitas & Petunjuk
            </button>
            <button
                type="button"
                @click="activeTab = 'biodata'"
                class="px-3.5 py-2 rounded-xl transition-all cursor-pointer"
                :class="activeTab === 'biodata' ? 'bg-[#555783] text-white shadow-xs' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'"
            >
                3. Biodata Siswa
            </button>
            <button
                type="button"
                @click="activeTab = 'nilai'"
                class="px-3.5 py-2 rounded-xl transition-all cursor-pointer"
                :class="activeTab === 'nilai' ? 'bg-[#555783] text-white shadow-xs' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'"
            >
                4. Capaian Nilai & Asesmen
            </button>
            <button
                type="button"
                @click="activeTab = 'pengesahan'"
                class="px-3.5 py-2 rounded-xl transition-all cursor-pointer"
                :class="activeTab === 'pengesahan' ? 'bg-[#555783] text-white shadow-xs' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'"
            >
                5. Nonakademik & Pengesahan
            </button>
        </div>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- LEMBAR 1: COVER DEPAN RESMI                                 -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <div x-show="activeTab === 'all' || activeTab === 'cover'" class="bg-white border border-[#E8E7ED] rounded-2xl p-8 sm:p-14 shadow-xs max-w-4xl mx-auto text-center space-y-8 text-[#1A1A2E]">
            <div class="space-y-3 pt-4">
                <h2 class="text-xl font-bold tracking-wider uppercase">LAPORAN</h2>
                <h3 class="text-base font-bold tracking-wider uppercase">HASIL BELAJAR PESERTA DIDIK</h3>
                <h4 class="text-base font-bold tracking-wider uppercase">{{ $jenjangPanjang }}</h4>
                <div class="text-sm font-bold tracking-wide uppercase pt-2">
                    JENIS KEKHUSUSAN : <span class="text-[#555783]">{{ strtoupper($ketunaan) }}</span>
                </div>
            </div>

            <!-- Logo SLB -->
            <div class="py-10 flex flex-col items-center justify-center">
                <img src="{{ asset('images/logo-slb.png') }}" alt="Logo SLB" class="w-40 h-40 object-contain">
                <p class="text-xs font-bold tracking-widest uppercase text-gray-600 mt-2">SLB INSAN MUTIARA PEKANBARU</p>
            </div>

            <div class="space-y-4 max-w-md mx-auto pt-2">
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-500 mb-1">NAMA PESERTA DIDIK</p>
                    <div class="p-3 border-2 border-black font-bold text-lg uppercase tracking-wider bg-gray-50">
                        {{ $namaSiswa }}
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-gray-500 mb-1 pt-2">NISN</p>
                    <div class="p-3 border-2 border-black font-bold text-lg tracking-widest bg-gray-50">
                        {{ $nisn }}
                    </div>
                </div>
            </div>

            <div class="pt-12 space-y-1 text-xs font-bold uppercase tracking-wider">
                <p>KEMENTERIAN PENDIDIKAN DAN KEBUDAYAAN</p>
                <p>REPUBLIK INDONESIA</p>
            </div>
        </div>

        <div x-show="activeTab === 'all'" class="no-print my-6 flex items-center justify-center gap-3 text-xs text-gray-400">
            <span class="h-px bg-gray-300 w-24"></span>
            <span>BATAS HALAMAN CETAK 1 & 2</span>
            <span class="h-px bg-gray-300 w-24"></span>
        </div>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- LEMBAR 2: IDENTITAS SEKOLAH & PETUNJUK PENGISIAN RAPOR     -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <div x-show="activeTab === 'all' || activeTab === 'petunjuk'" class="bg-white border border-[#E8E7ED] rounded-2xl p-8 sm:p-12 shadow-xs max-w-4xl mx-auto space-y-6 text-[#1A1A2E] text-xs">
            <div class="text-center pb-5 border-b-2 border-black">
                <h2 class="text-sm font-bold uppercase tracking-wide">
                    RAPOR PESERTA DIDIK {{ $jenjangPanjang }}
                </h2>
                <p class="text-xs font-bold uppercase tracking-wider mt-0.5">
                    JENIS KEKHUSUSAN : {{ strtoupper($ketunaan) }}
                </p>
            </div>

            <div class="py-4 border-b border-gray-300 text-xs space-y-2">
                <div class="flex"><span class="w-48 font-medium text-gray-600">Nama Sekolah</span><span class="w-4">:</span><span class="font-bold">SLB Insan Mutiara Pekanbaru</span></div>
                <div class="flex"><span class="w-48 font-medium text-gray-600">NPSN / NSS</span><span class="w-4">:</span><span>10497554 / -</span></div>
                <div class="flex"><span class="w-48 font-medium text-gray-600">Alamat Sekolah</span><span class="w-4">:</span><span>Jalan Cendana No. 33A / Adi Sucipto</span></div>
                <div class="flex"><span class="w-48 font-medium text-gray-600">Kelurahan / Kecamatan</span><span class="w-4">:</span><span>Sidomulyo Timur / Marpoyan Damai</span></div>
                <div class="flex"><span class="w-48 font-medium text-gray-600">Kota / Provinsi</span><span class="w-4">:</span><span>Kota Pekanbaru / Riau</span></div>
                <div class="flex"><span class="w-48 font-medium text-gray-600">Telepon / Email</span><span class="w-4">:</span><span>(0761) 589123 / info@slbinsanmutiara.sch.id</span></div>
            </div>

            <div class="pt-4 space-y-3">
                <h3 class="font-bold uppercase tracking-wider text-center text-xs">
                    PETUNJUK PENGISIAN RAPOR
                </h3>
                <ol class="list-decimal pl-5 space-y-2 text-justify text-xs leading-relaxed">
                    <li>Laporan hasil Belajar Peserta Didik Berkebutuhan Khusus merupakan ringkasan hasil penilaian terhadap seluruh aktivitas pembelajaran yang dilakukan peserta didik dalam kurun waktu tertentu.</li>
                    <li>Laporan hasil Belajar dipergunakan selama peserta didik yang bersangkutan mengikuti seluruh program pembelajaran di Sekolah Luar Biasa (SLB).</li>
                    <li>Nilai capaian kompetensi diperoleh melalui asesmen diagnostik, formatif, dan sumatif yang disesuaikan dengan kurikulum operasional dan kebutuhan khusus ketunaan.</li>
                    <li>Deskripsi capaian kompetensi menggambarkan perkembangan kemampuan akademik, kemandirian fungsi adaptif, dan keterampilan program kekhususan peserta didik secara naratif dan memotivasi.</li>
                    <li>Program Kebutuhan Khusus dinilai secara spesifik berdasarkan ketunaan peserta didik: <strong>PKPBI</strong> (Tunarungu), <strong>Bina Diri</strong> (Tunagrahita), atau <strong>Pengembangan Komunikasi & Interaksi</strong> (Autis).</li>
                </ol>
            </div>
        </div>

        <div x-show="activeTab === 'all'" class="no-print my-6 flex items-center justify-center gap-3 text-xs text-gray-400">
            <span class="h-px bg-gray-300 w-24"></span>
            <span>BATAS HALAMAN CETAK 2 & 3</span>
            <span class="h-px bg-gray-300 w-24"></span>
        </div>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- LEMBAR 3: KETERANGAN DIRI PESERTA DIDIK (BIODATA RESMI)    -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <div x-show="activeTab === 'all' || activeTab === 'biodata'" class="bg-white border border-[#E8E7ED] rounded-2xl p-8 sm:p-12 shadow-xs max-w-4xl mx-auto space-y-6 text-[#1A1A2E] text-xs">
            <h3 class="text-center font-bold text-sm uppercase tracking-wider pb-3 border-b-2 border-black">
                KETERANGAN DIRI PESERTA DIDIK
            </h3>

            <div class="space-y-2.5 text-xs leading-relaxed max-w-2xl mx-auto">
                <div class="flex"><span class="w-7 font-bold">1.</span><span class="w-60 text-gray-700">Nama Peserta Didik (Lengkap)</span><span class="w-3">:</span><span class="font-bold uppercase">{{ $namaSiswa }}</span></div>
                <div class="flex"><span class="w-7 font-bold">2.</span><span class="w-60 text-gray-700">Nomor Induk Siswa Nasional (NISN)</span><span class="w-3">:</span><span class="font-bold tracking-wider">{{ $nisn }}</span></div>
                <div class="flex"><span class="w-7 font-bold">3.</span><span class="w-60 text-gray-700">Tempat, Tanggal Lahir</span><span class="w-3">:</span><span>{{ $siswa->tempat_lahir ?? 'Pekanbaru' }}, {{ $siswa->tgl_lahir ? \Carbon\Carbon::parse($siswa->tgl_lahir)->translatedFormat('d F Y') : '13 November 2009' }}</span></div>
                <div class="flex"><span class="w-7 font-bold">4.</span><span class="w-60 text-gray-700">Jenis Kelamin</span><span class="w-3">:</span><span>{{ $siswa->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-Laki' }}</span></div>
                <div class="flex"><span class="w-7 font-bold">5.</span><span class="w-60 text-gray-700">Agama</span><span class="w-3">:</span><span>Islam</span></div>
                <div class="flex"><span class="w-7 font-bold">6.</span><span class="w-60 text-gray-700">Status Dalam Keluarga</span><span class="w-3">:</span><span>Anak Kandung</span></div>
                <div class="flex"><span class="w-7 font-bold">7.</span><span class="w-60 text-gray-700">Anak Ke</span><span class="w-3">:</span><span>1 (Pertama)</span></div>
                <div class="flex"><span class="w-7 font-bold">8.</span><span class="w-60 text-gray-700">Alamat Peserta Didik</span><span class="w-3">:</span><span>Jalan Raya Pasir Putih, Marpoyan Damai, Pekanbaru</span></div>
                <div class="flex"><span class="w-7 font-bold">9.</span><span class="w-60 text-gray-700">Nomor Telepon Rumah / HP</span><span class="w-3">:</span><span>{{ $siswa->no_hp ?? '-' }}</span></div>
                <div class="flex"><span class="w-7 font-bold">10.</span><span class="w-60 text-gray-700">Sekolah Asal</span><span class="w-3">:</span><span>SLB Insan Mutiara Pekanbaru</span></div>
                <div class="flex"><span class="w-7 font-bold">11.</span><span class="w-60 text-gray-700">Di Terima Di Sekolah Ini</span><span class="w-3">:</span><span></span></div>
                <div class="flex pl-7"><span class="w-56 text-gray-600">a. Dikelas</span><span class="w-3">:</span><span class="font-semibold">{{ $tingkat }} (Tingkat {{ $tingkat }})</span></div>
                <div class="flex pl-7"><span class="w-56 text-gray-600">b. Pada Tanggal</span><span class="w-3">:</span><span>15 Juli 2024</span></div>
                <div class="flex"><span class="w-7 font-bold">12.</span><span class="w-60 text-gray-700">Nama Orang Tua</span><span class="w-3">:</span><span></span></div>
                <div class="flex pl-7"><span class="w-56 text-gray-600">a. Ayah</span><span class="w-3">:</span><span class="font-semibold">{{ $namaAyah }}</span></div>
                <div class="flex pl-7"><span class="w-56 text-gray-600">b. Ibu</span><span class="w-3">:</span><span class="font-semibold">{{ $namaIbu }}</span></div>
                <div class="flex"><span class="w-7 font-bold">13.</span><span class="w-60 text-gray-700">Alamat Orang Tua</span><span class="w-3">:</span><span>Jalan Raya Pasir Putih, Marpoyan Damai, Pekanbaru</span></div>
                <div class="flex"><span class="w-7 font-bold">14.</span><span class="w-60 text-gray-700">Nomor Telepon Orang Tua</span><span class="w-3">:</span><span>{{ $noTelpOrtu }}</span></div>
                <div class="flex"><span class="w-7 font-bold">15.</span><span class="w-60 text-gray-700">Pekerjaan Orang Tua</span><span class="w-3">:</span><span></span></div>
                <div class="flex pl-7"><span class="w-56 text-gray-600">a. Ayah</span><span class="w-3">:</span><span>Wiraswasta</span></div>
                <div class="flex pl-7"><span class="w-56 text-gray-600">b. Ibu</span><span class="w-3">:</span><span>Ibu Rumah Tangga</span></div>
            </div>

            <!-- Pas Foto & Tanda Tangan Kepala Sekolah -->
            <div class="pt-8 flex items-end justify-between text-xs max-w-2xl mx-auto">
                <div class="w-24 h-32 border-2 border-black border-dashed flex flex-col items-center justify-center text-[10px] text-gray-500 bg-gray-50 text-center p-1">
                    <span>Pas Foto<br>3 x 4 cm</span>
                </div>
                <div class="text-center w-64">
                    <p class="mb-1">Pekanbaru, 19 Desember 2026</p>
                    <p class="mb-16">Kepala Sekolah,</p>
                    <p class="font-bold underline underline-offset-4 uppercase text-[#1A1A2E]">{{ $namaKepsek }}</p>
                    <p class="text-[11px] text-gray-600">NIP. {{ $nipKepsek }}</p>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'all'" class="no-print my-6 flex items-center justify-center gap-3 text-xs text-gray-400">
            <span class="h-px bg-gray-300 w-24"></span>
            <span>BATAS HALAMAN CETAK 3 & 4</span>
            <span class="h-px bg-gray-300 w-24"></span>
        </div>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- LEMBAR 4: LAPORAN HASIL BELAJAR (NILAI & CAPAIAN KOMPETENSI)-->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <div x-show="activeTab === 'all' || activeTab === 'nilai'" class="bg-white border border-[#E8E7ED] rounded-2xl p-8 sm:p-12 shadow-xs space-y-6 max-w-4xl mx-auto text-[#1A1A2E]">
            
            <!-- Document Title -->
            <div class="text-center space-y-1 mb-4">
                <h2 class="text-base font-bold tracking-wider uppercase">
                    LAPORAN HASIL BELAJAR<br>(RAPORT)
                </h2>
            </div>

            <!-- Student Metadata Table -->
            <div class="grid grid-cols-2 gap-x-6 gap-y-1.5 text-xs border-b-2 border-black pb-3 font-normal">
                <div class="space-y-1">
                    <p><span class="inline-block w-36 text-gray-600">Nama Peserta Didik</span> : <strong>{{ $namaSiswa }}</strong></p>
                    <p><span class="inline-block w-36 text-gray-600">NISN</span> : <strong>{{ $nisn }}</strong></p>
                    <p><span class="inline-block w-36 text-gray-600">Sekolah</span> : <strong>SLB Insan Mutiara Pekanbaru</strong></p>
                    <p><span class="inline-block w-36 text-gray-600">Alamat</span> : <span>Jalan Cendana No. 33A / Adi Sucipto</span></p>
                </div>
                <div class="space-y-1">
                    <p><span class="inline-block w-32 text-gray-600">Kelas</span> : <strong>{{ $tingkat }} (Tingkat {{ $tingkat }})</strong></p>
                    <p><span class="inline-block w-32 text-gray-600">Fase</span> : <strong>{{ $fase }}</strong></p>
                    <p><span class="inline-block w-32 text-gray-600">Semester</span> : <strong>{{ $semesterRomawi }}</strong></p>
                    <p><span class="inline-block w-32 text-gray-600">Tahun Pelajaran</span> : <strong>{{ $tahunPelajaran }}</strong></p>
                </div>
            </div>

            <!-- Table Muatan Pelajaran (Persis Scan Asli) -->
            <div class="overflow-x-auto pt-1">
                <table class="w-full text-left border-collapse text-xs border border-black">
                    <thead>
                        <tr class="bg-gray-100 border-b border-black font-bold text-center">
                            <th class="py-2 px-2 border-r border-black w-8">No</th>
                            <th class="py-2 px-3 border-r border-black w-60 text-left">Muatan Pelajaran</th>
                            <th class="py-2 px-2 border-r border-black w-20">Nilai Akhir</th>
                            <th class="py-2 px-3 text-left">Capaian Kompetensi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black text-xs">
                        <tr>
                            <td class="py-2 px-2 text-center border-r border-black align-top">1</td>
                            <td class="py-2 px-3 border-r border-black font-semibold align-top">Pendidikan Agama dan Budi Pekerti</td>
                            <td class="py-2 px-2 text-center font-bold border-r border-black align-top">90</td>
                            <td class="py-2 px-3 align-top leading-relaxed">Ananda menunjukkan banyak perubahan dalam berinteraksi dan bertingkah laku sesuai dengan prinsip ajaran keagamaan dan budi pekerti luhur.</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2 text-center border-r border-black align-top">2</td>
                            <td class="py-2 px-3 border-r border-black font-semibold align-top">Pendidikan Pancasila</td>
                            <td class="py-2 px-2 text-center font-bold border-r border-black align-top">98</td>
                            <td class="py-2 px-3 align-top leading-relaxed">Ananda mampu menghargai keberagaman suku, agama dan kepercayaan. Perlu bimbingan lanjutan dalam memahami semboyan Bhinneka Tunggal Ika melalui visual.</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2 text-center border-r border-black align-top">3</td>
                            <td class="py-2 px-3 border-r border-black font-semibold align-top">Bahasa Indonesia</td>
                            <td class="py-2 px-2 text-center font-bold border-r border-black align-top">98</td>
                            <td class="py-2 px-3 align-top leading-relaxed">Ananda menunjukkan kemampuan yang sangat baik dalam mempresentasikan teks secara lisan dan/atau isyarat berdasarkan hasil tulisan dongeng Lancang Kuning.</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2 text-center border-r border-black align-top">4</td>
                            <td class="py-2 px-3 border-r border-black font-semibold align-top">Matematika</td>
                            <td class="py-2 px-2 text-center font-bold border-r border-black align-top">95</td>
                            <td class="py-2 px-3 align-top leading-relaxed">Ananda menunjukkan pemahaman yang sangat baik dalam menentukan satuan waktu (jam dan menit) dalam kehidupan sehari-hari dan membaca jam analog secara mandiri.</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2 text-center border-r border-black align-top">5</td>
                            <td class="py-2 px-3 border-r border-black font-semibold align-top">Bahasa Inggris</td>
                            <td class="py-2 px-2 text-center font-bold border-r border-black align-top">86</td>
                            <td class="py-2 px-3 align-top leading-relaxed">Ananda mampu mengidentifikasi informasi atau teks inti sederhana melalui teks perkenalan diri (self introduction).</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2 text-center border-r border-black align-top">6</td>
                            <td class="py-2 px-3 border-r border-black font-semibold align-top">Ilmu Pengetahuan Alam (IPA)</td>
                            <td class="py-2 px-2 text-center font-bold border-r border-black align-top">95</td>
                            <td class="py-2 px-3 align-top leading-relaxed">Ananda mampu menceritakan kembali fenomena perubahan iklim dan cara menjaga kebersihan lingkungan dari banjir.</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2 text-center border-r border-black align-top">7</td>
                            <td class="py-2 px-3 border-r border-black font-semibold align-top">Ilmu Pengetahuan Sosial (IPS)</td>
                            <td class="py-2 px-2 text-center font-bold border-r border-black align-top">90</td>
                            <td class="py-2 px-3 align-top leading-relaxed">Ananda mampu mengidentifikasi upaya mengurangi risiko bencana di lingkungan tempat tinggal.</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2 text-center border-r border-black align-top">8</td>
                            <td class="py-2 px-3 border-r border-black font-semibold align-top">Seni Budaya (Seni Rupa)</td>
                            <td class="py-2 px-2 text-center font-bold border-r border-black align-top">98</td>
                            <td class="py-2 px-3 align-top leading-relaxed">Ananda mampu menciptakan karya seni rupa dua dimensi dan kerajinan tangan berbahan ramah lingkungan secara kreatif.</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2 text-center border-r border-black align-top">9</td>
                            <td class="py-2 px-3 border-r border-black font-semibold align-top">PJOK</td>
                            <td class="py-2 px-2 text-center font-bold border-r border-black align-top">98</td>
                            <td class="py-2 px-3 align-top leading-relaxed">Ananda sangat baik dan aktif berpartisipasi dalam aktivitas kebugaran fisik dan motorik luar ruang.</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2 text-center border-r border-black align-top">10</td>
                            <td class="py-2 px-3 border-r border-black font-semibold align-top">
                                Keterampilan Pilihan:
                                <div class="text-[11px] text-gray-600 font-normal mt-0.5">• Suvenir<br>• Tata Kecantikan</div>
                            </td>
                            <td class="py-2 px-2 text-center font-bold border-r border-black align-top">95<br><span class="text-[11px] font-normal text-gray-600">98</span></td>
                            <td class="py-2 px-3 align-top leading-relaxed">Ananda mampu melakukan proses pembuatan suvenir dari barang daur ulang serta mengenal jenis peralatan penataan diri dengan aman.</td>
                        </tr>

                        <!-- Baris Program Kekhususan Berdasarkan 3 Ketunaan -->
                        <tr class="bg-indigo-50/40">
                            <td class="py-2 px-2 text-center border-r border-black align-top font-bold text-[#555783]">11</td>
                            <td class="py-2 px-3 border-r border-black font-bold text-[#555783] align-top">
                                {{ $programKekhususanNama }}
                                <span class="block text-[10px] font-normal text-gray-500 mt-0.5">Program khusus sesuai ketunaan {{ $ketunaan }}</span>
                            </td>
                            <td class="py-2 px-2 text-center font-bold border-r border-black align-top text-[#555783]">
                                {{ $programKekhususanNilai }}
                            </td>
                            <td class="py-2 px-3 align-top leading-relaxed font-medium">
                                {{ $programKekhususanCapaian }}
                            </td>
                        </tr>

                        <tr>
                            <td class="py-2 px-2 text-center border-r border-black align-top">12</td>
                            <td class="py-2 px-3 border-r border-black font-semibold align-top">Muatan Lokal (Budaya Melayu Riau)</td>
                            <td class="py-2 px-2 text-center font-bold border-r border-black align-top">95</td>
                            <td class="py-2 px-3 align-top leading-relaxed">Ananda telah menunjukkan pemahamannya dengan sangat baik tentang cerita rakyat masyarakat Melayu Riau (Lancang Kuning).</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="text-right text-[11px] text-gray-500 pt-2 border-t border-gray-200">
                Halaman 1 dari 2 — Laporan Hasil Belajar (Raport)
            </div>
        </div>

        <div x-show="activeTab === 'all'" class="no-print my-6 flex items-center justify-center gap-3 text-xs text-gray-400">
            <span class="h-px bg-gray-300 w-24"></span>
            <span>BATAS HALAMAN CETAK 4 & 5</span>
            <span class="h-px bg-gray-300 w-24"></span>
        </div>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- LEMBAR 5: EVALUASI NONAKADEMIK & 3 TANDA TANGAN RESMI      -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <div x-show="activeTab === 'all' || activeTab === 'pengesahan'" class="bg-white border border-[#E8E7ED] rounded-2xl p-8 sm:p-12 shadow-xs space-y-5 max-w-4xl mx-auto text-[#1A1A2E] text-xs">
            
            <div class="border-b-2 border-black pb-2 flex justify-between items-center text-xs">
                <span class="font-bold uppercase">Nama Peserta Didik: {{ $namaSiswa }}</span>
                <span>NISN: <strong>{{ $nisn }}</strong> · Kelas: <strong>{{ $tingkat }}</strong></span>
            </div>

            <!-- Ekstrakurikuler Table -->
            <div class="space-y-1.5">
                <p class="font-bold text-xs uppercase tracking-wide">A. Kegiatan Ekstrakurikuler</p>
                <table class="w-full text-left border-collapse text-xs border border-black">
                    <thead>
                        <tr class="bg-gray-100 border-b border-black font-bold text-center">
                            <th class="py-2 px-2 border-r border-black w-8">No</th>
                            <th class="py-2 px-3 border-r border-black w-60 text-left">Ekstrakurikuler</th>
                            <th class="py-2 px-3 text-left">Keterangan / Capaian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black">
                        <tr>
                            <td class="py-2 px-2 text-center border-r border-black">1</td>
                            <td class="py-2 px-3 border-r border-black font-semibold">Pramuka Luar Biasa</td>
                            <td class="py-2 px-3">Ananda menunjukkan pemahaman yang baik dalam keterampilan baris-berbaris dan ketangkasan regu pramuka khusus.</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2 text-center border-r border-black">2</td>
                            <td class="py-2 px-3 border-r border-black font-semibold">Seni Pantomim & Tari Isyarat</td>
                            <td class="py-2 px-3">Menunjukkan ekspresi visual dan penghayatan gerak yang luar biasa.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Ketidakhadiran & Prestasi Box -->
            <div class="space-y-1.5">
                <p class="font-bold text-xs uppercase tracking-wide">B. Ketidakhadiran & Catatan Prestasi</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                    <div class="border border-black rounded-lg p-3 space-y-1.5 bg-white">
                        <p class="font-bold border-b border-gray-300 pb-1 uppercase">Ketidakhadiran</p>
                        <p>Sakit : {{ $sakit }} hari</p>
                        <p>Izin : {{ $izin }} hari</p>
                        <p>Tanpa Keterangan : {{ $alpa }} hari</p>
                    </div>
                    <div class="border border-black rounded-lg p-3 space-y-1 bg-white">
                        <p class="font-bold border-b border-gray-300 pb-1 uppercase">Prestasi</p>
                        <p class="leading-relaxed">• Juara 1 Lomba Memperingati Hari Disabilitas oleh DINSOS Pekanbaru</p>
                        <p class="leading-relaxed">• Juara Harapan 1 Lomba Mendongeng Bahasa Isyarat Bulan Bahasa PBSI FKIP UNRI</p>
                    </div>
                </div>
            </div>

            <!-- Catatan Perkembangan Karakter & Fisik -->
            <div class="border border-black rounded-lg p-3 text-xs space-y-1 bg-white">
                <p class="font-bold uppercase">C. Perkembangan Karakter & Kesehatan Fisik</p>
                <p class="leading-relaxed">{{ $perkembangan }}</p>
            </div>

            <!-- Catatan Wali Kelas -->
            <div class="border border-black rounded-lg p-4 text-xs space-y-1.5 bg-[#FDFCF9]">
                <p class="font-bold text-[#1A1A2E] uppercase">D. Catatan Wali Kelas</p>
                <p class="italic text-gray-800 leading-relaxed">
                    "Prestasi akademis ananda pada semester ini sangat luar biasa! Ananda adalah siswa yang cerdas, rajin, dan mandiri dalam belajar. Kemampuan dalam memahami materi pelajaran serta penguasaan kosa kata baru berkembang pesat. Pertahankan semangat ini sebagai modal berharga untuk melanjutkan pendidikan atau memasuki dunia kerja setelah lulus sekolah. Excellent!"
                </p>
            </div>

            <!-- Keputusan Kelulusan / Kenaikan Kelas -->
            <div class="border-2 border-black rounded-lg p-3 text-xs text-center font-medium bg-gray-50">
                <p class="font-bold uppercase mb-0.5">Keputusan :</p>
                @if(in_array($tingkat, ['IX', 'XII']))
                    <p>Berdasarkan hasil yang dicapai pada Kelas X, XI dan XII peserta didik dinyatakan <strong>LULUS</strong> serta telah menyelesaikan pendidikan di tingkat {{ $jenjangSingkat }}.</p>
                @else
                    <p>Berdasarkan hasil capaian belajar semester ini, peserta didik dinyatakan <strong>NAIK KE TINGKAT KELAS BERIKUTNYA</strong>.</p>
                @endif
            </div>

            <!-- Tanda Tangan Resmi (Sesuai Scan PDF) -->
            <div class="pt-6 grid grid-cols-2 text-center text-xs gap-y-12">
                <div>
                    <p class="mb-14">Mengetahui,<br>Orang Tua/ Wali</p>
                    <p class="font-bold tracking-wide underline underline-offset-4">....................................................</p>
                </div>
                <div>
                    <p class="mb-1">Pekanbaru, 19 Desember 2026</p>
                    <p class="mb-14">Wali Kelas</p>
                    <p class="font-bold tracking-wide underline underline-offset-4 uppercase">{{ $namaWaliKelas }}</p>
                    <p class="text-[10px] text-gray-500">NIP. {{ $nipWaliKelas }}</p>
                </div>

                <div class="col-span-2 text-center -mt-4">
                    <p class="mb-14">Mengetahui,<br>Kepala Sekolah SLB Insan Mutiara</p>
                    <p class="font-bold tracking-wide underline underline-offset-4 uppercase">{{ $namaKepsek }}</p>
                    <p class="text-[10px] text-gray-500">NIP. {{ $nipKepsek }}</p>
                </div>
            </div>

            <div class="text-right text-[11px] text-gray-500 pt-2 border-t border-gray-200">
                Halaman 2 dari 2 — Laporan Hasil Belajar (Raport)
            </div>
        </div>

    </div>


    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- PRINT-ONLY CONTAINER: GABUNGAN SEMUA HALAMAN (5 HALAMAN)     -->
    <!-- Menjamin saat dicetak/PDF, SEMUA HALAMAN LANGSUNG TERGABUNG -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="hidden print:block print-only-combined space-y-0 text-black">
        
        <!-- HALAMAN PRINT 1: COVER -->
        <div class="print-sheet text-center flex flex-col justify-between py-6">
            <div class="space-y-3 pt-6">
                <h1 class="text-xl font-bold tracking-wider uppercase text-black">
                    LAPORAN<br>
                    HASIL BELAJAR PESERTA DIDIK<br>
                    {{ $jenjangPanjang }}
                </h1>
                <p class="text-sm font-bold tracking-wider uppercase pt-2">
                    JENIS KEKHUSUSAN : {{ strtoupper($ketunaan) }}
                </p>
            </div>

            <div class="py-12 flex flex-col items-center justify-center">
                <img src="{{ asset('images/logo-slb.png') }}" alt="Logo SLB" class="w-44 h-44 object-contain">
                <p class="text-xs font-bold tracking-widest uppercase text-gray-700 mt-2">SLB INSAN MUTIARA PEKANBARU</p>
            </div>

            <div class="max-w-[420px] mx-auto w-full space-y-5 pb-6">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider mb-1">NAMA PESERTA DIDIK</p>
                    <div class="p-3 border-2 border-black font-bold text-base uppercase tracking-wider bg-white flex items-center justify-center">
                        {{ $namaSiswa }}
                    </div>
                </div>

                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider mb-1">NISN</p>
                    <div class="p-3 border-2 border-black font-bold text-base tracking-widest bg-white flex items-center justify-center">
                        {{ $nisn }}
                    </div>
                </div>
            </div>

            <div class="pt-8 space-y-1 text-xs font-bold uppercase tracking-widest">
                <p>KEMENTERIAN PENDIDIKAN DAN KEBUDAYAAN</p>
                <p>REPUBLIK INDONESIA</p>
            </div>
        </div>

        <!-- HALAMAN PRINT 2: IDENTITAS & PETUNJUK -->
        <div class="print-sheet text-xs leading-relaxed flex flex-col justify-between py-6">
            <div>
                <div class="text-center pb-5 border-b-2 border-black">
                    <h2 class="text-sm font-bold uppercase tracking-wide">
                        RAPOR PESERTA DIDIK {{ $jenjangPanjang }}
                    </h2>
                    <p class="text-xs font-bold uppercase tracking-wider mt-1">
                        JENIS KEKHUSUSAN : {{ strtoupper($ketunaan) }}
                    </p>
                </div>

                <div class="py-5 border-b border-black text-xs space-y-2">
                    <div class="flex"><span class="w-48 font-medium">Nama Sekolah</span><span class="w-4">:</span><span class="font-bold">SLB Insan Mutiara Pekanbaru</span></div>
                    <div class="flex"><span class="w-48 font-medium">NPSN / NSS</span><span class="w-4">:</span><span>10497554 / -</span></div>
                    <div class="flex"><span class="w-48 font-medium">Alamat Sekolah</span><span class="w-4">:</span><span>Jalan Cendana No. 33A / Adi Sucipto</span></div>
                    <div class="flex"><span class="w-48 font-medium">Kelurahan / Kecamatan</span><span class="w-4">:</span><span>Sidomulyo Timur / Marpoyan Damai</span></div>
                    <div class="flex"><span class="w-48 font-medium">Kota / Provinsi</span><span class="w-4">:</span><span>Kota Pekanbaru / Riau</span></div>
                    <div class="flex"><span class="w-48 font-medium">Kode Pos / Telepon</span><span class="w-4">:</span><span>28293 / (0761) 589123</span></div>
                    <div class="flex"><span class="w-48 font-medium">Email Resmi</span><span class="w-4">:</span><span>info@slbinsanmutiara.sch.id</span></div>
                </div>

                <div class="pt-6 space-y-3">
                    <h3 class="font-bold uppercase tracking-wider text-center text-xs">
                        PETUNJUK PENGISIAN RAPOR
                    </h3>
                    <ol class="list-decimal pl-5 space-y-2 text-justify text-[11px] leading-relaxed">
                        <li>Laporan hasil Belajar Peserta Didik Berkebutuhan Khusus merupakan ringkasan hasil penilaian terhadap seluruh aktivitas pembelajaran yang dilakukan peserta didik dalam kurun waktu tertentu.</li>
                        <li>Laporan hasil Belajar dipergunakan selama peserta didik yang bersangkutan mengikuti seluruh program pembelajaran di Sekolah Luar Biasa (SLB).</li>
                        <li>Nilai capaian kompetensi diperoleh melalui asesmen diagnostik, formatif, dan sumatif yang disesuaikan dengan kurikulum operasional dan kebutuhan khusus ketunaan.</li>
                        <li>Deskripsi capaian kompetensi menggambarkan perkembangan kemampuan akademik, kemandirian fungsi adaptif, dan keterampilan program kekhususan peserta didik secara naratif, objektif, dan memotivasi.</li>
                        <li>Program Kebutuhan Khusus dinilai secara spesifik: <strong>PKPBI</strong> (Tunarungu), <strong>Bina Diri</strong> (Tunagrahita), atau <strong>Pengembangan Komunikasi & Interaksi</strong> (Autis).</li>
                        <li>Buku Laporan Hasil Belajar ini harus disimpan dengan baik oleh orang tua/wali dan wajib dibawa saat pendaftaran ulang atau kelulusan sekolah.</li>
                    </ol>
                </div>
            </div>

            <div class="pt-6 text-right text-[10px] text-gray-600 border-t border-black">
                SLB Insan Mutiara Pekanbaru — Buku Laporan Hasil Belajar
            </div>
        </div>

        <!-- HALAMAN PRINT 3: BIODATA SISWA -->
        <div class="print-sheet text-xs leading-relaxed flex flex-col justify-between py-6">
            <div>
                <div class="text-center pb-4 mb-4 border-b-2 border-black">
                    <h2 class="text-sm font-bold uppercase tracking-wider">
                        KETERANGAN DIRI PESERTA DIDIK
                    </h2>
                </div>

                <div class="space-y-2 text-[11px] leading-relaxed">
                    <div class="flex"><span class="w-6 font-bold">1.</span><span class="w-60">Nama Peserta Didik (Lengkap)</span><span class="w-3">:</span><span class="font-bold uppercase">{{ $namaSiswa }}</span></div>
                    <div class="flex"><span class="w-6 font-bold">2.</span><span class="w-60">Nomor Induk Siswa Nasional (NISN)</span><span class="w-3">:</span><span class="font-bold tracking-wider">{{ $nisn }}</span></div>
                    <div class="flex"><span class="w-6 font-bold">3.</span><span class="w-60">Tempat, Tanggal Lahir</span><span class="w-3">:</span><span>{{ $siswa->tempat_lahir ?? 'Pekanbaru' }}, {{ $siswa->tgl_lahir ? \Carbon\Carbon::parse($siswa->tgl_lahir)->translatedFormat('d F Y') : '13 November 2009' }}</span></div>
                    <div class="flex"><span class="w-6 font-bold">4.</span><span class="w-60">Jenis Kelamin</span><span class="w-3">:</span><span>{{ $siswa->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-Laki' }}</span></div>
                    <div class="flex"><span class="w-6 font-bold">5.</span><span class="w-60">Agama</span><span class="w-3">:</span><span>Islam</span></div>
                    <div class="flex"><span class="w-6 font-bold">6.</span><span class="w-60">Status dalam Keluarga</span><span class="w-3">:</span><span>Anak Kandung</span></div>
                    <div class="flex"><span class="w-6 font-bold">7.</span><span class="w-60">Anak Ke</span><span class="w-3">:</span><span>1 (Pertama)</span></div>
                    <div class="flex"><span class="w-6 font-bold">8.</span><span class="w-60">Alamat Tempat Tinggal Peserta Didik</span><span class="w-3">:</span><span>Jalan Raya Pasir Putih, Marpoyan Damai, Pekanbaru</span></div>
                    <div class="flex"><span class="w-6 font-bold">9.</span><span class="w-60">Nomor Telepon Rumah / HP Siswa</span><span class="w-3">:</span><span>{{ $siswa->no_hp ?? '-' }}</span></div>
                    <div class="flex"><span class="w-6 font-bold">10.</span><span class="w-60">Sekolah Asal</span><span class="w-3">:</span><span>SLB Insan Mutiara Pekanbaru</span></div>
                    
                    <div class="flex"><span class="w-6 font-bold">11.</span><span class="w-60">Diterima di Sekolah Ini</span><span class="w-3">:</span><span></span></div>
                    <div class="flex pl-6"><span class="w-56">a. Di Kelas</span><span class="w-3">:</span><span class="font-semibold">{{ $tingkat }} (Tingkat {{ $tingkat }})</span></div>
                    <div class="flex pl-6"><span class="w-56">b. Pada Tanggal</span><span class="w-3">:</span><span>15 Juli 2024</span></div>

                    <div class="flex"><span class="w-6 font-bold">12.</span><span class="w-60">Nama Orang Tua / Wali</span><span class="w-3">:</span><span></span></div>
                    <div class="flex pl-6"><span class="w-56">a. Ayah</span><span class="w-3">:</span><span class="font-semibold">{{ $namaAyah }}</span></div>
                    <div class="flex pl-6"><span class="w-56">b. Ibu</span><span class="w-3">:</span><span class="font-semibold">{{ $namaIbu }}</span></div>
                    @if($namaWali !== '-')
                    <div class="flex pl-6"><span class="w-56">c. Wali</span><span class="w-3">:</span><span class="font-semibold">{{ $namaWali }}</span></div>
                    @endif

                    <div class="flex"><span class="w-6 font-bold">13.</span><span class="w-60">Alamat Orang Tua / Wali</span><span class="w-3">:</span><span>Jalan Raya Pasir Putih, Marpoyan Damai, Pekanbaru</span></div>
                    <div class="flex"><span class="w-6 font-bold">14.</span><span class="w-60">Nomor Telepon / WhatsApp Orang Tua</span><span class="w-3">:</span><span>{{ $noTelpOrtu }}</span></div>

                    <div class="flex"><span class="w-6 font-bold">15.</span><span class="w-60">Pekerjaan Orang Tua</span><span class="w-3">:</span><span></span></div>
                    <div class="flex pl-6"><span class="w-56">a. Ayah</span><span class="w-3">:</span><span>Wiraswasta</span></div>
                    <div class="flex pl-6"><span class="w-56">b. Ibu</span><span class="w-3">:</span><span>Ibu Rumah Tangga</span></div>
                </div>
            </div>

            <!-- Pas Foto & Tanda Tangan Kepala Sekolah -->
            <div class="pt-8 flex items-end justify-between text-xs">
                <div class="w-24 h-32 border-2 border-black border-dashed flex flex-col items-center justify-center text-[10px] text-gray-600 bg-gray-50 text-center p-1">
                    <span>Pas Foto<br>3 x 4 cm</span>
                </div>
                <div class="text-center w-64">
                    <p class="mb-1">Pekanbaru, 19 Desember 2026</p>
                    <p class="mb-16">Kepala Sekolah,</p>
                    <p class="font-bold underline underline-offset-4 uppercase">{{ $namaKepsek }}</p>
                    <p class="text-[11px] text-gray-700">NIP. {{ $nipKepsek }}</p>
                </div>
            </div>
        </div>

        <!-- HALAMAN PRINT 4: NILAI & ASESMEN (HALAMAN 1 RAPOR) -->
        <div class="print-sheet text-xs leading-relaxed flex flex-col justify-between py-6">
            <div>
                <div class="text-center mb-3">
                    <h2 class="text-sm font-bold tracking-wider uppercase">
                        LAPORAN HASIL BELAJAR<br>(RAPORT)
                    </h2>
                </div>

                <div class="grid grid-cols-2 gap-x-6 gap-y-1 text-[11px] border-b-2 border-black pb-2.5 mb-2.5">
                    <div class="space-y-0.5">
                        <p class="flex"><span class="w-32">Nama Peserta Didik</span><span class="w-3">:</span><strong class="uppercase">{{ $namaSiswa }}</strong></p>
                        <p class="flex"><span class="w-32">NISN</span><span class="w-3">:</span><strong>{{ $nisn }}</strong></p>
                        <p class="flex"><span class="w-32">Sekolah</span><span class="w-3">:</span><strong>SLB Insan Mutiara Pekanbaru</strong></p>
                        <p class="flex"><span class="w-32">Alamat</span><span class="w-3">:</span><span>Jalan Cendana No. 33A / Adi Sucipto</span></p>
                    </div>
                    <div class="space-y-0.5">
                        <p class="flex"><span class="w-28">Kelas / Tingkat</span><span class="w-3">:</span><strong>{{ $tingkat }} (Tingkat {{ $tingkat }})</strong></p>
                        <p class="flex"><span class="w-28">Fase</span><span class="w-3">:</span><strong>{{ $fase }}</strong></p>
                        <p class="flex"><span class="w-28">Semester</span><span class="w-3">:</span><strong>{{ $semesterRomawi }}</strong></p>
                        <p class="flex"><span class="w-28">Tahun Pelajaran</span><span class="w-3">:</span><strong>{{ $tahunPelajaran }}</strong></p>
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
                            <td class="py-1.5 px-3 align-top leading-snug">Ananda mampu menghargai keberagaman suku dan agama. Perlu bimbingan dalam memahami semboyan Bhinneka Tunggal Ika melalui visual.</td>
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
                        <tr>
                            <td class="py-1.5 px-2 text-center border-r border-black align-top">10</td>
                            <td class="py-1.5 px-3 border-r border-black font-semibold align-top">
                                Keterampilan Pilihan:
                                <div class="text-[10px] text-gray-700 font-normal mt-0.5">• Suvenir<br>• Tata Kecantikan</div>
                            </td>
                            <td class="py-1.5 px-2 text-center font-bold border-r border-black align-top">95<br><span class="text-[10px] font-normal">98</span></td>
                            <td class="py-1.5 px-3 align-top leading-snug">Ananda mampu membuat cinderamata dari daur ulang serta mengenal alat kebersihan dan penataan diri dengan aman.</td>
                        </tr>

                        <!-- Program Kebutuhan Khusus -->
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

            <div class="pt-2 text-right text-[10px] text-gray-600 border-t border-black">
                Halaman 1 dari 2 — Laporan Hasil Belajar (Raport)
            </div>
        </div>

        <!-- HALAMAN PRINT 5: PENGESAHAN & NONAKADEMIK (HALAMAN 2 RAPOR) -->
        <div class="print-sheet text-xs leading-relaxed flex flex-col justify-between py-6">
            <div class="space-y-3.5">
                <div class="border-b-2 border-black pb-1.5 flex justify-between items-center text-[11px]">
                    <span class="font-bold uppercase">Nama Peserta Didik: {{ $namaSiswa }}</span>
                    <span>NISN: <strong>{{ $nisn }}</strong> · Kelas: <strong>{{ $tingkat }}</strong></span>
                </div>

                <!-- Ekstrakurikuler -->
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

                <!-- Ketidakhadiran & Prestasi -->
                <div class="space-y-1">
                    <p class="font-bold text-[11px] uppercase tracking-wide">B. Ketidakhadiran & Catatan Prestasi</p>
                    <div class="grid grid-cols-2 gap-3 text-[11px]">
                        <div class="border border-black p-2.5 space-y-1">
                            <p class="font-bold border-b border-black pb-0.5 uppercase">Ketidakhadiran</p>
                            <div class="flex justify-between"><span>Sakit</span><span>: {{ $sakit }} hari</span></div>
                            <div class="flex justify-between"><span>Izin</span><span>: {{ $izin }} hari</span></div>
                            <div class="flex justify-between"><span>Tanpa Keterangan</span><span>: {{ $alpa }} hari</span></div>
                        </div>

                        <div class="border border-black p-2.5 space-y-0.5">
                            <p class="font-bold border-b border-black pb-0.5 uppercase">Prestasi yang Diraih</p>
                            <p class="text-[10px] leading-snug">• Juara 1 Lomba Memperingati Hari Disabilitas DINSOS Pekanbaru</p>
                            <p class="text-[10px] leading-snug">• Juara Harapan 1 Lomba Mendongeng Bahasa Isyarat FKIP UNRI</p>
                        </div>
                    </div>
                </div>

                <!-- Perkembangan Karakter -->
                <div class="border border-black p-2.5 text-[11px] space-y-0.5">
                    <p class="font-bold uppercase">C. Perkembangan Karakter & Kesehatan Fisik</p>
                    <p class="text-justify leading-relaxed">{{ $perkembangan }}</p>
                </div>

                <!-- Catatan Wali Kelas -->
                <div class="border border-black p-2.5 text-[11px] space-y-0.5">
                    <p class="font-bold uppercase">D. Catatan Wali Kelas</p>
                    <p class="italic text-justify leading-relaxed">
                        "Prestasi ananda pada semester ini sangat luar biasa! Ananda menunjukkan kemandirian belajar dan sopan santun yang terpuji. Pertahankan semangat belajar dan terus kembangkan bakat keterampilan vokasi untuk masa depan yang gemilang."
                    </p>
                </div>

                <!-- Keputusan -->
                <div class="border-2 border-black p-2 text-[11px] text-center font-medium bg-gray-50">
                    <p class="font-bold uppercase mb-0.5">Keputusan :</p>
                    @if(in_array($tingkat, ['IX', 'XII']))
                        <p>Berdasarkan hasil yang dicapai, peserta didik dinyatakan: <strong class="text-sm">LULUS</strong> dan telah menyelesaikan pendidikan di tingkat {{ $jenjangSingkat }}.</p>
                    @else
                        <p>Berdasarkan hasil capaian belajar, peserta didik dinyatakan: <strong class="text-sm">NAIK KE KELAS BERIKUTNYA</strong>.</p>
                    @endif
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
            </div>

            <div class="pt-2 text-right text-[10px] text-gray-600 border-t border-black">
                Halaman 2 dari 2 — Laporan Hasil Belajar (Raport)
            </div>
        </div>

    </div>

</x-layouts.wali-kelas>
