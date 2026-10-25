<x-layouts.kepala-sekolah
    title="Kepala Sekolah · Review Detail Rapor"
    periode="2026/2027 · Semester Ganjil"
    userName="Novia Parenri · Kepala Sekolah">

    @php
        $namaSiswa = $siswa->nama_lengkap ?? 'Alya Putri';
        $nisn = $siswa->nisn ?? '0160001001';
        $penugasan = $siswa->penugasanAktif();
        $kelasNama = $penugasan?->kelas?->nama ?? (($siswa->kekhususan ?? 'Tunarungu') . ' A');
        $tingkat = $penugasan?->tingkat ?? 'VII';
        $fase = $penugasan?->fase ?? 'D';
        $statusRapor = $rapor?->status ?? 'diajukan';
        $raporId = $rapor?->id ?? $id;

        $progKhususNama = 'Program Kekhususan · PKPBI';
        $progKhususCapaian = 'Ananda mampu merespons bunyi lingkungan dengan bantuan visual sesuai kebutuhan komunikasinya.';
        if (isset($siswa) && strcasecmp($siswa->kekhususan, 'Tunagrahita') === 0) {
            $progKhususNama = 'Program Kekhususan · Bina Diri';
            $progKhususCapaian = 'Ananda mampu melakukan proses bina diri dan kebersihan diri secara mandiri.';
        } elseif (isset($siswa) && strcasecmp($siswa->kekhususan, 'Autis') === 0) {
            $progKhususNama = 'Program Kekhususan · Pengembangan Komunikasi & Interaksi';
            $progKhususCapaian = 'Ananda mampu merespons interaksi sosial timbal balik dan menjaga kontak mata terarah.';
        }

        $mapelList = [
            ['nama' => 'Pendidikan Agama Islam dan Budi Pekerti', 'nilai' => 86, 'capaian' => 'Ananda mampu mengenali perilaku baik dan menerapkan sikap saling menghormati dalam kegiatan sehari-hari.'],
            ['nama' => 'Pendidikan Pancasila', 'nilai' => 85, 'capaian' => 'Ananda mampu mengidentifikasi aturan di sekolah dan memberi contoh sikap bekerja sama.'],
            ['nama' => 'Bahasa Indonesia', 'nilai' => 84, 'capaian' => 'Ananda mampu memahami isi bacaan sederhana dan menjawab pertanyaan tentang tokoh serta peristiwa melalui bahasa isyarat. Ananda mulai menyusun kalimat pendek secara mandiri.'],
            ['nama' => 'Matematika', 'nilai' => 82, 'capaian' => 'Ananda mampu menerapkan penjumlahan dan pengurangan dalam situasi sehari-hari dengan bantuan visual.'],
            ['nama' => 'Bahasa Inggris', 'nilai' => 80, 'capaian' => 'Ananda mampu memperkenalkan diri dan mengenali kosakata benda di lingkungan sekolah.'],
            ['nama' => 'IPA', 'nilai' => 84, 'capaian' => 'Ananda mampu mengenali bagian tubuh dan memahami kebiasaan menjaga kesehatan.'],
            ['nama' => 'IPS', 'nilai' => 83, 'capaian' => 'Ananda mampu mengenali lingkungan sekolah serta peran anggota keluarga.'],
            ['nama' => 'Seni Budaya · Seni Rupa', 'nilai' => 90, 'capaian' => 'Ananda mampu membuat karya dua dimensi dengan pilihan bentuk dan warna yang semakin mandiri.'],
            ['nama' => 'PJOK', 'nilai' => 85, 'capaian' => 'Ananda mampu berpartisipasi dalam aktivitas gerak sederhana dan mengikuti aturan keselamatan.'],
            ['nama' => 'Keterampilan Pilihan · Suvenir', 'nilai' => 88, 'capaian' => 'Ananda mampu mengikuti urutan kerja membuat suvenir sederhana dengan rapi.'],
            ['nama' => 'Keterampilan Pilihan · Tata Kecantikan', 'nilai' => 82, 'capaian' => 'Ananda mampu mengenali peralatan kebersihan diri dan menggunakannya secara aman dengan pendampingan.'],
            ['nama' => $progKhususNama, 'nilai' => 86, 'capaian' => $progKhususCapaian],
            ['nama' => 'Muatan Lokal · Budaya Melayu Riau', 'nilai' => 85, 'capaian' => 'Ananda mampu mengenali cerita rakyat Riau dan menyampaikan kembali pesan cerita.'],
        ];
    @endphp

    <div x-data="{ showApproveModal: false, showReturnModal: false }">

        <!-- Page Header & Back Button -->
        <div class="space-y-1 mb-6">
            <div class="text-[13px] font-normal text-gray-500">
                Kepala Sekolah / Persetujuan Rapor / Review Detail Rapor
            </div>
            <a href="{{ route('kepala.persetujuan.index') }}" class="inline-flex items-center text-xs font-semibold text-[#555783] hover:underline mb-2">
                ← Kembali ke Persetujuan Rapor
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-1">
                <div>
                    <h1 class="text-[28px] font-bold text-[#1A1A2E] tracking-tight leading-snug">
                        Review Detail Rapor
                    </h1>
                    <p class="text-[14px] text-gray-500">
                        {{ $namaSiswa }} · {{ $tingkat }} / {{ $fase }} · {{ $kelasNama }} (Ketunaan: <strong>{{ $siswa->kekhususan }}</strong>)
                    </p>
                    <div class="flex items-center gap-2 mt-2">
                        @if($statusRapor === 'disetujui')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#ECFDF5] text-[#065F46] border border-[#A7F3D0]">
                                Disetujui
                            </span>
                        @elseif($statusRapor === 'dikembalikan')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                Dikembalikan untuk revisi
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                Diajukan
                            </span>
                        @endif
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#ECFDF5] text-[#065F46] border border-[#A7F3D0]">
                            13 Capaian Lengkap
                        </span>
                    </div>
                </div>

                <!-- Top Action Buttons -->
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="showReturnModal = true"
                        class="px-5 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 bg-white hover:bg-[#F7F6F2] transition-colors shadow-xs cursor-pointer"
                    >
                        Kembalikan
                    </button>
                    @if($statusRapor !== 'disetujui')
                        <button
                            type="button"
                            @click="showApproveModal = true"
                            class="px-5 py-2.5 rounded-xl bg-[#555783] hover:bg-[#46486E] text-white text-xs font-semibold transition-colors shadow-sm cursor-pointer"
                        >
                            ✓ Setujui rapor
                        </button>
                    @else
                        <span class="px-4 py-2.5 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Telah Disetujui
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Two-Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left Side Cards (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Kelengkapan -->
                <x-card title="Kelengkapan Penilaian">
                    <div class="space-y-3 text-xs text-[#1A1A2E]">
                        <div class="flex items-center gap-2.5 text-[#16A34A] font-medium">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-gray-700">Identitas & Ketunaan Terverifikasi</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-[#16A34A] font-medium">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-gray-700">13 Nilai & Capaian Lengkap</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-[#16A34A] font-medium">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-gray-700">Program Khusus: {{ $siswa->program_kekhususan_short }}</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-[#16A34A] font-medium">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-gray-700">Kehadiran & Catatan Terisi</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-[#16A34A] font-medium">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-gray-700">Kepala Sekolah: Novia Parenri, S.Si, M.Si</span>
                        </div>
                    </div>
                </x-card>

                <!-- Riwayat Pengajuan -->
                <x-card title="Riwayat pengajuan">
                    <div class="space-y-2 text-xs">
                        <p class="font-bold text-[#1A1A2E]">5 Okt 2026 · 09.20</p>
                        <p class="text-gray-600">Diajukan oleh wali kelas untuk verifikasi persetujuan.</p>
                        @if($rapor?->catatan_revisi)
                            <div class="mt-2 p-2.5 bg-rose-50 border border-rose-200 rounded-xl">
                                <p class="font-bold text-rose-800 text-[11px]">Catatan Revisi Terakhir:</p>
                                <p class="text-rose-700 text-xs mt-0.5">{{ $rapor->catatan_revisi }}</p>
                            </div>
                        @endif
                    </div>
                </x-card>
            </div>

            <!-- Right Side: Official Report Document Sheet (8 cols) -->
            <div class="lg:col-span-8 bg-white rounded-2xl border border-[#E8E7ED] p-8 sm:p-12 shadow-xs space-y-8">
                <!-- Header Document -->
                <div class="text-center space-y-1">
                    <h2 class="text-base sm:text-lg font-bold tracking-wider text-[#1A1A2E] uppercase">
                        LAPORAN HASIL BELAJAR<br>(RAPORT)
                    </h2>
                </div>

                <!-- Identity Metadata -->
                <div class="grid grid-cols-2 gap-y-2 text-xs text-[#1A1A2E] border-b border-gray-100 pb-6">
                    <div>
                        <span class="text-gray-500">Nama Peserta Didik</span> : <span class="font-bold">{{ $namaSiswa }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Kelas / Fase</span> : <span class="font-medium">{{ $tingkat }} / {{ $fase }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">NISN</span> : <span class="font-medium">{{ $nisn }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Semester / Tahun</span> : <span class="font-medium">I (Ganjil) / 2026/2027</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Ketunaan</span> : <span class="font-medium font-semibold text-[#555783]">{{ $siswa->kekhususan }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Sekolah</span> : <span class="font-medium">SLB Insan Mutiara Pekanbaru</span>
                    </div>
                </div>

                <!-- Subjects & Assessments Table -->
                <div class="space-y-4">
                    <div class="grid grid-cols-12 text-xs font-bold text-gray-700 border-b border-gray-200 pb-2">
                        <div class="col-span-4">Muatan Pelajaran</div>
                        <div class="col-span-2 text-center">Nilai Akhir</div>
                        <div class="col-span-6">Capaian Kompetensi</div>
                    </div>

                    <div class="space-y-4 divide-y divide-gray-100 text-xs">
                        @foreach($mapelList as $m)
                            <div class="grid grid-cols-12 gap-3 pt-3">
                                <div class="col-span-4 font-semibold text-[#1A1A2E]">
                                    {{ $m['nama'] }}
                                    <p class="text-[11px] font-normal text-gray-500 mt-0.5">Nilai akhir: {{ $m['nilai'] }}</p>
                                </div>
                                <div class="col-span-2 text-center font-bold text-[#1A1A2E]">
                                    {{ $m['nilai'] }}
                                </div>
                                <div class="col-span-6 text-gray-600 leading-relaxed">
                                    {{ $m['capaian'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Setujui Rapor -->
        <div
            x-show="showApproveModal"
            style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-black/40 backdrop-blur-xs flex items-center justify-center p-4"
            @keydown.escape.window="showApproveModal = false"
        >
            <div
                @click.away="showApproveModal = false"
                class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-[#E8E7ED] space-y-4"
            >
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-[#1A1A2E]">Setujui Rapor Resmi?</h3>
                        <p class="text-xs text-gray-500">Kepala Sekolah: Novia Parenri, S.Si, M.Si</p>
                    </div>
                </div>

                <p class="text-xs text-gray-600 leading-relaxed">
                    Dengan menyetujui, rapor untuk <strong>{{ $namaSiswa }}</strong> (NISN: {{ $nisn }}) akan berstatus <strong>Disetujui</strong> dan dapat diunduh serta didistribusikan kepada orang tua murid via WhatsApp (wa.me).
                </p>

                <form method="POST" action="{{ route('kepala.persetujuan.approve', $raporId) }}" class="pt-2 flex items-center justify-end gap-3">
                    @csrf
                    <button
                        type="button"
                        @click="showApproveModal = false"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-xl bg-[#555783] hover:bg-[#46486E] text-white text-xs font-bold shadow-sm transition-colors cursor-pointer"
                    >
                        Ya, Setujui Sekarang
                    </button>
                </form>
            </div>
        </div>

        <!-- Modal Kembalikan Rapor -->
        <div
            x-show="showReturnModal"
            style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-black/40 backdrop-blur-xs flex items-center justify-center p-4"
            @keydown.escape.window="showReturnModal = false"
        >
            <div
                @click.away="showReturnModal = false"
                class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-[#E8E7ED] space-y-4"
            >
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-[#1A1A2E]">Kembalikan Rapor untuk Revisi</h3>
                        <p class="text-xs text-gray-500">{{ $namaSiswa }} · {{ $kelasNama }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('kepala.persetujuan.reject', $raporId) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Catatan Revisi / Masukan untuk Wali Kelas *
                        </label>
                        <textarea
                            name="catatan_revisi"
                            rows="4"
                            required
                            placeholder="Tuliskan poin perbaikan, misalnya: 'Perbaiki deskripsi narasi capaian pada mata pelajaran Bahasa Indonesia agar mencerminkan kemajuan secara spesifik...'"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-xs text-[#1A1A2E] placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button
                            type="button"
                            @click="showReturnModal = false"
                            class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition-colors cursor-pointer"
                        >
                            Kembalikan Rapor
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</x-layouts.kepala-sekolah>
