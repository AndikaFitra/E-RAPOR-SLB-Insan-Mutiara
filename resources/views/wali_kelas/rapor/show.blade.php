<x-layouts.wali-kelas
    title="Wali Kelas · Laporan Hasil Belajar (Raport)"
    periode="2026/2027 · Semester Ganjil"
    userName="Rina Pratiwi · Wali Kelas">

    @php
        $ketunaan = $siswa->kekhususan ?? 'Tunarungu';
        $namaSiswa = $siswa->nama_lengkap ?? 'Friska Carolin Monalisa';
        $nisn = $siswa->nisn ?? '0053935552';
        $kelasNama = optional($siswa->penugasanAktif()?->kelas)->nama ?? ($ketunaan . ' A');
        $tingkat = $siswa->penugasanAktif()?->tingkat ?? 'XII';
        $fase = $siswa->penugasanAktif()?->fase ?? 'F';
        $jenjang = in_array($tingkat, ['X', 'XI', 'XII']) ? 'SMALB' : (in_array($tingkat, ['VII', 'VIII', 'IX']) ? 'SMPLB' : 'SDLB');
        $semester = $tahunAktif->semester ?? 'Ganjil';
        $tahunPelajaran = $tahunAktif->tahun ?? '2026/2027';

        // Tentukan Program Kekhususan berdasarkan 3 Ketunaan SLB Insan Mutiara
        if (strcasecmp($ketunaan, 'Tunarungu') === 0) {
            $programKekhususanNama = 'Program Kekhususan (PKPBI)';
            $programKekhususanCapaian = 'Ananda sangat baik dalam mengidentifikasi langsung mengenai pubertas melalui teks bacaan serta merespons irama dan persepsi bunyi lingkungan.';
            $programKekhususanNilai = 98;
        } elseif (strcasecmp($ketunaan, 'Tunagrahita') === 0) {
            $programKekhususanNama = 'Program Kekhususan (Bina Diri)';
            $programKekhususanCapaian = 'Ananda mampu melakukan proses bina diri dan perawatan kebersihan diri secara mandiri serta disiplin merapikan perlengkapan belajar.';
            $programKekhususanNilai = 95;
        } else { // Autis
            $programKekhususanNama = 'Program Kekhususan (Pengembangan Komunikasi dan Interaksi)';
            $programKekhususanCapaian = 'Ananda mampu membangun interaksi sosial timbal balik, mempertahankan kontak mata terarah, serta menyampaikan ekspresi dan kebutuhan dasar secara teratur.';
            $programKekhususanNilai = 96;
        }
    @endphp

    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            .no-print {
                display: none !important;
            }
            .print-sheet {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .page-break {
                page-break-after: always;
                break-after: page;
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
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-1">
            <div>
                <h1 class="text-[26px] font-bold text-[#1A1A2E] tracking-tight leading-snug">
                    Laporan Hasil Belajar (Raport)
                </h1>
                <p class="text-[13px] text-gray-500">
                    {{ $namaSiswa }} · NISN {{ $nisn }} · Kelas {{ $kelasNama }} (Ketunaan: <strong>{{ $ketunaan }}</strong>)
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2.5">
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

                <button
                    type="button"
                    onclick="window.print()"
                    class="px-4 py-2.5 rounded-xl bg-[#555783] hover:bg-[#46486E] text-white text-xs font-semibold transition-colors shadow-sm flex items-center gap-2 cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak Rapor / PDF</span>
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

    <div x-data="{ activeTab: 'nilai' }" class="space-y-6">

        <!-- Navigation Tabs (Screen Only) -->
        <div class="no-print flex items-center gap-2 border-b border-[#E8E7ED] pb-3 text-xs font-semibold">
            <button
                type="button"
                @click="activeTab = 'nilai'"
                class="px-4 py-2 rounded-xl transition-all"
                :class="activeTab === 'nilai' ? 'bg-[#555783] text-white shadow-xs' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'"
            >
                1. Lembar Nilai & Capaian (Sesuai Scan PDF)
            </button>
            <button
                type="button"
                @click="activeTab = 'cover'"
                class="px-4 py-2 rounded-xl transition-all"
                :class="activeTab === 'cover' ? 'bg-[#555783] text-white shadow-xs' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'"
            >
                2. Cover Resmi & Petunjuk ({{ $jenjang }})
            </button>
            <button
                type="button"
                @click="activeTab = 'biodata'"
                class="px-4 py-2 rounded-xl transition-all"
                :class="activeTab === 'biodata' ? 'bg-[#555783] text-white shadow-xs' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'"
            >
                3. Keterangan Diri Siswa (Biodata)
            </button>
        </div>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- TAB 1: LEMBAR NILAI & CAPAIAN (PERSIS SCAN PDF SLB INSAN MUTIARA) -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <div x-show="activeTab === 'nilai'" class="print-sheet bg-white border border-[#E8E7ED] rounded-2xl p-8 sm:p-12 shadow-xs space-y-6 max-w-4xl mx-auto text-[#1A1A2E]">
            
            <!-- Document Title -->
            <div class="text-center space-y-1 mb-6">
                <h2 class="text-lg font-bold tracking-wider uppercase">
                    LAPORAN HASIL BELAJAR<br>(RAPORT)
                </h2>
            </div>

            <!-- Student Metadata Table (Exact layout from PDF) -->
            <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-xs border-b border-gray-200 pb-4 font-normal">
                <div class="space-y-1">
                    <p><span class="inline-block w-36 text-gray-600">Nama Peserta Didik</span> : <strong>{{ $namaSiswa }}</strong></p>
                    <p><span class="inline-block w-36 text-gray-600">NISN</span> : <strong>{{ $nisn }}</strong></p>
                    <p><span class="inline-block w-36 text-gray-600">Sekolah</span> : <strong>SLB Insan Mutiara Pekanbaru</strong></p>
                    <p><span class="inline-block w-36 text-gray-600">Alamat</span> : <span>Jalan Cendana No. 33A / Adi Sucipto</span></p>
                </div>
                <div class="space-y-1">
                    <p><span class="inline-block w-32 text-gray-600">Kelas</span> : <strong>{{ $tingkat }} (Tingkat {{ $tingkat }})</strong></p>
                    <p><span class="inline-block w-32 text-gray-600">Fase</span> : <strong>{{ $fase }}</strong></p>
                    <p><span class="inline-block w-32 text-gray-600">Semester</span> : <strong>{{ $semester === 'Ganjil' ? 'I (Ganjil)' : 'II (Genap)' }}</strong></p>
                    <p><span class="inline-block w-32 text-gray-600">Tahun Pelajaran</span> : <strong>{{ $tahunPelajaran }}</strong></p>
                </div>
            </div>

            <!-- Table Muatan Pelajaran (Exact Table from PDF) -->
            <div class="overflow-x-auto pt-2">
                <table class="w-full text-left border-collapse text-xs border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-300 font-bold text-center">
                            <th class="py-2.5 px-3 border-r border-gray-300 w-10">No</th>
                            <th class="py-2.5 px-4 border-r border-gray-300 w-64 text-left">Muatan Pelajaran</th>
                            <th class="py-2.5 px-3 border-r border-gray-300 w-24">Nilai Akhir</th>
                            <th class="py-2.5 px-4 text-left">Capaian Kompetensi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300 text-xs">
                        <tr>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 align-top">1</td>
                            <td class="py-2.5 px-4 border-r border-gray-300 font-semibold align-top">Pendidikan Agama dan Budi Pekerti</td>
                            <td class="py-2.5 px-3 text-center font-bold border-r border-gray-300 align-top">90</td>
                            <td class="py-2.5 px-4 align-top leading-relaxed">Ananda menunjukkan banyak perubahan dalam berinteraksi dan bertingkah laku sesuai dengan prinsip ajaran keagamaan dan budi pekerti luhur.</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 align-top">2</td>
                            <td class="py-2.5 px-4 border-r border-gray-300 font-semibold align-top">Pendidikan Pancasila</td>
                            <td class="py-2.5 px-3 text-center font-bold border-r border-gray-300 align-top">98</td>
                            <td class="py-2.5 px-4 align-top leading-relaxed">Ananda mampu menghargai keberagaman suku, agama dan kepercayaan, di lingkungan, satuan pendidikan, rumah dan masyarakat. Namun Perlu bimbingan dalam memahami semboyan Bhinneka Tunggal Ika melalui teks dengan bahasa isyarat/visual.</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 align-top">3</td>
                            <td class="py-2.5 px-4 border-r border-gray-300 font-semibold align-top">Bahasa Indonesia</td>
                            <td class="py-2.5 px-3 text-center font-bold border-r border-gray-300 align-top">98</td>
                            <td class="py-2.5 px-4 align-top leading-relaxed">Ananda menunjukkan kemampuan yang sangat baik dalam mempresentasikan teks secara lisan dan/atau isyarat berdasarkan hasil tulisan yang telah dibuat melalui dongeng lancang kuning.</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 align-top">4</td>
                            <td class="py-2.5 px-4 border-r border-gray-300 font-semibold align-top">Matematika</td>
                            <td class="py-2.5 px-3 text-center font-bold border-r border-gray-300 align-top">95</td>
                            <td class="py-2.5 px-4 align-top leading-relaxed">Ananda menunjukkan pemahaman yang sangat baik dalam menentukan dan menerapkan satuan waktu (jam dan menit) dalam kehidupan sehari-hari. Ananda sudah bisa membaca jam analog dan digital dengan lebih percaya diri.</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 align-top">5</td>
                            <td class="py-2.5 px-4 border-r border-gray-300 font-semibold align-top">Bahasa Inggris</td>
                            <td class="py-2.5 px-3 text-center font-bold border-r border-gray-300 align-top">86</td>
                            <td class="py-2.5 px-4 align-top leading-relaxed">Ananda mampu mengidentifikasi informasi atau teks inti sederhana melalui teks self introduction (teks perkenalan diri).</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 align-top">6</td>
                            <td class="py-2.5 px-4 border-r border-gray-300 font-semibold align-top">Ilmu Pengetahuan Alam (IPA)</td>
                            <td class="py-2.5 px-3 text-center font-bold border-r border-gray-300 align-top">95</td>
                            <td class="py-2.5 px-4 align-top leading-relaxed">Ananda mampu menceritakan kembali dampak perubahan iklim bagi kehidupan manusia serta penanggulangannya dari teks sederhana tentang banjir.</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 align-top">7</td>
                            <td class="py-2.5 px-4 border-r border-gray-300 font-semibold align-top">Ilmu Pengetahuan Sosial (IPS)</td>
                            <td class="py-2.5 px-3 text-center font-bold border-r border-gray-300 align-top">90</td>
                            <td class="py-2.5 px-4 align-top leading-relaxed">Ananda mampu dalam mengidentifikasi upaya mengurangi resiko bencana melalui teks sederhana tentang pencegahan bencana lingkungan.</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 align-top">8</td>
                            <td class="py-2.5 px-4 border-r border-gray-300 font-semibold align-top">Seni Budaya (Seni Rupa)</td>
                            <td class="py-2.5 px-3 text-center font-bold border-r border-gray-300 align-top">98</td>
                            <td class="py-2.5 px-4 align-top leading-relaxed">Ananda mampu menciptakan karya sendiri berupa karya dua atau tiga dimensi atas dasar perasaan, minat, nalar, dan sesuai minatnya (membuat kerajinan dari bahan ramah lingkungan).</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 align-top">9</td>
                            <td class="py-2.5 px-4 border-r border-gray-300 font-semibold align-top">Pendidikan Jasmani Olahraga dan Kesehatan</td>
                            <td class="py-2.5 px-3 text-center font-bold border-r border-gray-300 align-top">98</td>
                            <td class="py-2.5 px-4 align-top leading-relaxed">Ananda menunjukkan kemampuan yang sangat baik berpartisipasi dalam aktivitas kebugaran di luar ruang dan/atau lingkungan alam.</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 align-top">10</td>
                            <td class="py-2.5 px-4 border-r border-gray-300 font-semibold align-top">
                                Keterampilan Pilihan
                                <div class="text-[11px] text-gray-600 font-normal mt-1">
                                    • Suvenir<br>
                                    • Tata Kecantikan
                                </div>
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold border-r border-gray-300 align-top">
                                95<br><span class="text-[11px] font-normal text-gray-600">98</span>
                            </td>
                            <td class="py-2.5 px-4 align-top leading-relaxed">
                                Ananda mampu melakukan proses pembuatan suvenir dari barang daur ulang. Pada tata kecantikan, ananda menunjukkan pemahaman yang sangat baik dalam mengenal jenis peralatan perawatan diri secara aman.
                            </td>
                        </tr>

                        <!-- Baris Program Kekhususan Berdasarkan 3 Ketunaan Sesuai Permintaan User -->
                        <tr class="bg-indigo-50/30">
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 align-top font-bold text-[#555783]">11</td>
                            <td class="py-2.5 px-4 border-r border-gray-300 font-bold text-[#555783] align-top">
                                {{ $programKekhususanNama }}
                                <span class="block text-[10px] font-normal text-gray-500 mt-0.5">Program khusus sesuai ketunaan {{ $ketunaan }}</span>
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold border-r border-gray-300 align-top text-[#555783]">
                                {{ $programKekhususanNilai }}
                            </td>
                            <td class="py-2.5 px-4 align-top leading-relaxed">
                                {{ $programKekhususanCapaian }}
                            </td>
                        </tr>

                        <tr>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 align-top">12</td>
                            <td class="py-2.5 px-4 border-r border-gray-300 font-semibold align-top">Muatan Lokal (Budaya Melayu Riau)</td>
                            <td class="py-2.5 px-3 text-center font-bold border-r border-gray-300 align-top">95</td>
                            <td class="py-2.5 px-4 align-top leading-relaxed">Ananda telah menunjukkan pemahamannya dengan sangat baik tentang cerita rakyat masyarakat Riau (Lancang Kuning).</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Page Break for Clean Print -->
            <div class="page-break pt-4"></div>

            <!-- Ekstrakurikuler Table -->
            <div class="space-y-2 pt-2">
                <table class="w-full text-left border-collapse text-xs border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-300 font-bold text-center">
                            <th class="py-2 px-3 border-r border-gray-300 w-10">No</th>
                            <th class="py-2 px-4 border-r border-gray-300 w-64 text-left">Ekstrakurikuler</th>
                            <th class="py-2 px-4 text-left">Keterangan / Capaian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        <tr>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300">1</td>
                            <td class="py-2.5 px-4 border-r border-gray-300 font-semibold">Pramuka Luar Biasa</td>
                            <td class="py-2.5 px-4">Ananda menunjukkan pemahaman yang baik dalam keterampilan baris-berbaris dan ketangkasan pramuka.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Ketidakhadiran & Prestasi Box -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs pt-2">
                <div class="border border-gray-300 rounded-lg p-3 space-y-1.5">
                    <p class="font-bold border-b border-gray-200 pb-1">Ketidakhadiran</p>
                    <p>Sakit : - hari</p>
                    <p>Izin : 3 hari</p>
                    <p>Tanpa Keterangan : - hari</p>
                </div>
                <div class="border border-gray-300 rounded-lg p-3 space-y-1.5">
                    <p class="font-bold border-b border-gray-200 pb-1">Prestasi</p>
                    <p>• Juara 1 Lomba Memperingati Hari Disabilitas oleh DINSOS Pekanbaru</p>
                    <p>• Juara Harapan 1 Lomba Mendongeng Bahasa Isyarat Bulan Bahasa PBSI FKIP UNRI</p>
                </div>
            </div>

            <!-- Catatan Wali Kelas -->
            <div class="border border-gray-300 rounded-lg p-4 text-xs space-y-1.5 bg-[#FDFCF9]">
                <p class="font-bold text-[#1A1A2E]">Catatan Wali Kelas</p>
                <p class="italic text-gray-700 leading-relaxed">
                    "Prestasi akademis ananda pada semester ini sangat luar biasa! Ananda adalah siswa yang cerdas, rajin, dan mandiri dalam belajar. Kemampuan dalam memahami materi pelajaran serta penguasaan kosa kata baru berkembang pesat. Pertahankan semangat ini sebagai modal berharga untuk melanjutkan pendidikan atau memasuki dunia kerja setelah lulus sekolah. Excellent!"
                </p>
            </div>

            <!-- Keputusan Kelulusan / Kenaikan Kelas -->
            <div class="border border-gray-300 rounded-lg p-3 text-xs text-center font-medium bg-gray-50">
                <p class="font-bold uppercase mb-0.5">Keputusan :</p>
                <p>Berdasarkan hasil yang dicapai pada Kelas X, XI dan XII peserta didik dinyatakan <strong>LULUS</strong> serta telah menyelesaikan pendidikan di tingkat SMALB.</p>
            </div>

            <!-- Tanda Tangan Resmi (Sesuai Scan PDF) -->
            <div class="pt-8 grid grid-cols-2 text-center text-xs gap-y-16">
                <div>
                    <p class="mb-16">Orang Tua/ Wali</p>
                    <p class="font-bold tracking-wide underline underline-offset-4">..................................</p>
                </div>
                <div>
                    <p class="mb-1">Pekanbaru, 04 Mei 2026</p>
                    <p class="mb-16">Wali Kelas</p>
                    <p class="font-bold tracking-wide underline underline-offset-4">NOVA YANI, S.Pd, Gr</p>
                    <p class="text-[10px] text-gray-500">NIP. 199002142014022005</p>
                </div>

                <div class="col-span-2 text-center -mt-6">
                    <p class="mb-16">Mengetahui,<br>Kepala Sekolah</p>
                    <p class="font-bold tracking-wide underline underline-offset-4">NOVIA PARENRI, S.Si, M.Si</p>
                    <p class="text-[10px] text-gray-500">NIP. 198005122005012003</p>
                </div>
            </div>

        </div>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- TAB 2: COVER RESMI & PETUNJUK (SESUAI PDF SCAN 2)           -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <div x-show="activeTab === 'cover'" class="print-sheet bg-white border border-[#E8E7ED] rounded-2xl p-8 sm:p-16 shadow-xs max-w-4xl mx-auto text-center space-y-8 text-[#1A1A2E]">
            <div class="space-y-3 pt-6">
                <h2 class="text-xl font-bold tracking-wider uppercase">LAPORAN</h2>
                <h3 class="text-base font-bold tracking-wider uppercase">HASIL BELAJAR PESERTA DIDIK</h3>
                <h4 class="text-base font-bold tracking-wider uppercase">SEKOLAH MENENGAH PERTAMA LUAR BIASA ({{ $jenjang }})</h4>
                <div class="text-sm font-bold tracking-wide uppercase pt-2">
                    JENIS KEKHUSUSAN : <span class="text-[#555783]">{{ strtoupper($ketunaan) }}</span>
                </div>
            </div>

            <!-- Logo Tut Wuri Handayani / SLB -->
            <div class="py-10 flex justify-center">
                <img src="{{ asset('images/logo-slb.png') }}" alt="Logo SLB" class="w-36 h-36 object-contain">
            </div>

            <div class="space-y-4 max-w-md mx-auto pt-4">
                <p class="text-xs font-semibold uppercase text-gray-500">NAMA PESERTA DIDIK</p>
                <div class="p-3 border-2 border-black font-bold text-lg uppercase tracking-wider bg-gray-50">
                    {{ $namaSiswa }}
                </div>

                <p class="text-xs font-semibold uppercase text-gray-500 pt-3">NISN</p>
                <div class="p-3 border-2 border-black font-bold text-lg tracking-widest bg-gray-50">
                    {{ $nisn }}
                </div>
            </div>

            <div class="pt-16 space-y-1 text-xs font-bold uppercase tracking-wider">
                <p>KEMENTERIAN PENDIDIKAN DAN KEBUDAYAAN</p>
                <p>REPUBLIK INDONESIA</p>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- TAB 3: KETERANGAN DIRI SISWA (BIODATA RESMI)                -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <div x-show="activeTab === 'biodata'" class="print-sheet bg-white border border-[#E8E7ED] rounded-2xl p-8 sm:p-12 shadow-xs max-w-4xl mx-auto space-y-6 text-[#1A1A2E]">
            <h3 class="text-center font-bold text-base uppercase tracking-wider mb-6">
                KETERANGAN DIRI PESERTA DIDIK
            </h3>

            <div class="space-y-2 text-xs leading-relaxed max-w-2xl mx-auto">
                <div class="flex"><span class="w-8">1.</span><span class="w-56">Nama Peserta Didik (Lengkap)</span><span>: <strong>{{ $namaSiswa }}</strong></span></div>
                <div class="flex"><span class="w-8">2.</span><span class="w-56">NISN</span><span>: {{ $nisn }}</span></div>
                <div class="flex"><span class="w-8">3.</span><span class="w-56">Tempat, Tanggal Lahir</span><span>: Pekanbaru, 13 November 2009</span></div>
                <div class="flex"><span class="w-8">4.</span><span class="w-56">Jenis Kelamin</span><span>: {{ $siswa->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-Laki' }}</span></div>
                <div class="flex"><span class="w-8">5.</span><span class="w-56">Agama</span><span>: Islam</span></div>
                <div class="flex"><span class="w-8">6.</span><span class="w-56">Status Dalam Keluarga</span><span>: Anak Kandung</span></div>
                <div class="flex"><span class="w-8">7.</span><span class="w-56">Anak Ke</span><span>: 1 (Pertama)</span></div>
                <div class="flex"><span class="w-8">8.</span><span class="w-56">Alamat Peserta Didik</span><span>: Jalan Raya Pasir Putih, Marpoyan Damai, Pekanbaru</span></div>
                <div class="flex"><span class="w-8">9.</span><span class="w-56">Nomor Telepon Rumah</span><span>: -</span></div>
                <div class="flex"><span class="w-8">10.</span><span class="w-56">Sekolah Asal</span><span>: SLB Insan Mutiara</span></div>
                <div class="flex"><span class="w-8">11.</span><span class="w-56">Di Terima Di Sekolah Ini</span><span>:</span></div>
                <div class="flex pl-8"><span class="w-52">a. Dikelas</span><span>: VII (Tujuh)</span></div>
                <div class="flex pl-8"><span class="w-52">b. Pada Tanggal</span><span>: Juni 2025</span></div>
                <div class="flex"><span class="w-8">12.</span><span class="w-56">Nama Orang Tua</span><span>:</span></div>
                <div class="flex pl-8"><span class="w-52">a. Ayah</span><span>: Usman</span></div>
                <div class="flex pl-8"><span class="w-52">b. Ibu</span><span>: Rini Yuniati</span></div>
                <div class="flex"><span class="w-8">13.</span><span class="w-56">Alamat Orang Tua</span><span>: Jalan Raya Pasir Putih, Marpoyan Damai, Pekanbaru</span></div>
                <div class="flex"><span class="w-8">14.</span><span class="w-56">Nomor Telepon Orang Tua</span><span>: 085263592816</span></div>
                <div class="flex"><span class="w-8">15.</span><span class="w-56">Pekerjaan Orang Tua</span><span>:</span></div>
                <div class="flex pl-8"><span class="w-52">a. Ayah</span><span>: Wiraswasta</span></div>
                <div class="flex pl-8"><span class="w-52">b. Ibu</span><span>: Ibu Rumah Tangga</span></div>
            </div>

            <!-- Tanda Tangan Kepala Sekolah -->
            <div class="pt-10 flex justify-end text-xs text-right pr-12">
                <div class="text-center">
                    <p class="mb-1">Pekanbaru, 19 Desember 2025</p>
                    <p class="mb-16">Kepala Sekolah</p>
                    <p class="font-bold underline underline-offset-4">NOVIA PARENRI, S.Si, M.Si</p>
                    <p class="text-[10px] text-gray-500">NIP. 198005122005012003</p>
                </div>
            </div>
        </div>

    </div>

</x-layouts.wali-kelas>

