<x-layouts.wali-kelas
    title="Wali Kelas · Nilai Asesmen"
    periode="2026/2027 · Semester Ganjil"
    userName="Rina Pratiwi · Wali Kelas">

    <!-- Flash Alert -->
    @if(session('success'))
        <div class="mb-4">
            <x-alert-banner type="success" title="Penilaian Disimpan" :message="session('success')" />
        </div>
    @endif

    <div x-data="{
        showModal: false,
        selectedMapelId: null,
        selectedMapel: '',
        selectedSiswaId: '{{ $siswaList->first()?->id ?? '' }}',
        nilaiAkhir: 85,
        narasiCapaian: '',
        existingScores: {{ json_encode($existingScores ?? []) }},
        openInput(id, mapel) {
            this.selectedMapelId = id;
            this.selectedMapel = mapel;
            this.updateSiswaScore();
            this.showModal = true;
        },
        updateSiswaScore() {
            if (this.existingScores[this.selectedMapelId] && this.existingScores[this.selectedMapelId][this.selectedSiswaId]) {
                const s = this.existingScores[this.selectedMapelId][this.selectedSiswaId];
                this.nilaiAkhir = s.nilai_akhir;
                this.narasiCapaian = s.narasi_capaian;
            } else {
                this.nilaiAkhir = 85;
                this.narasiCapaian = 'Ananda mampu mengenali materi pembelajaran dengan bantuan visual dan isyarat, serta merespons tugas secara tertib.';
            }
        }
    }">

        <!-- Page Header -->
        <x-page-header
            breadcrumb="Wali Kelas / Nilai Asesmen"
            title="Nilai Asesmen & Capaian"
            subtitle="Pantau kelengkapan asesmen per mata pelajaran di kelas {{ $selectedKelas?->nama ?? 'Tunarungu A' }} yang Anda ampu."
        />

        <!-- Filter Card -->
        <x-card class="p-5 space-y-4">
            <form method="GET" action="{{ route('wali.asesmen.index') }}" class="space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari mata pelajaran atau kode..."
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-[#1A1A2E] placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#555783] text-white text-xs font-semibold hover:bg-[#46486E] transition-colors shadow-xs">
                            Cari
                        </button>
                        @if(request('search') || request('kelas_id'))
                            <a href="{{ route('wali.asesmen.index') }}" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 bg-white hover:bg-[#F7F6F2] transition-colors flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Reset
                            </a>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
                    <select name="kelas_id" onchange="this.form.submit()" class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ ($selectedKelas?->id === $k->id) ? 'selected' : '' }}>Kelas: {{ $k->nama }} ({{ $k->kekhususan }})</option>
                        @endforeach
                    </select>
                    <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]" disabled>
                        <option>Total: {{ count($listAsesmen) }} Muatan Pembelajaran</option>
                    </select>
                    <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]" disabled>
                        <option>Tahun: {{ $tahunAktif?->tahun ?? '2026/2027' }}</option>
                    </select>
                    <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]" disabled>
                        <option>Semester: {{ $tahunAktif?->semester ?? 'Ganjil' }}</option>
                    </select>
                </div>
            </form>
        </x-card>

        <!-- Callout Note Info -->
        <x-callout-note
            title="Penilaian Kurikulum Merdeka SLB Berbasis Capaian Individual"
            description="Capaian kompetensi dinilai sesuai tingkat setiap siswa (Fase D). Program Kekhususan disesuaikan dengan jenis ketunaan: Tunarungu (PKPBI), Tunagrahita (Bina Diri), atau Autis (PKISP)."
        />

        <!-- Data Table Card -->
        <x-card class="overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="dashboard-table dashboard-table--wide text-left">
                    <thead>
                        <tr class="bg-[#F7F6F2] border-b border-[#E8E7ED] font-semibold text-gray-600">
                            <th class="py-3.5 px-6">Mata pelajaran</th>
                            <th class="py-3.5 px-4">Tingkat / Fase</th>
                            <th class="py-3.5 px-4">Nilai terisi</th>
                            <th class="py-3.5 px-4">Narasi terisi</th>
                            <th class="py-3.5 px-4">Kelengkapan</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($listAsesmen as $row)
                            <tr class="hover:bg-[#FBFBFA]">
                                <td class="py-4 px-6 font-bold text-[#1A1A2E]">
                                    {{ $row['mapel'] }}
                                    <span class="ml-1 text-[11px] font-normal text-gray-400">({{ $row['kode'] }})</span>
                                </td>
                                <td class="py-4 px-4 text-gray-600">{{ $row['tingkat'] }}</td>
                                <td class="py-4 px-4 text-gray-700 font-medium">{{ $row['nilai'] }}</td>
                                <td class="py-4 px-4 text-gray-700 font-medium">{{ $row['narasi'] }}</td>
                                <td class="py-4 px-4 font-semibold {{ $row['status'] === 'Lengkap' ? 'text-[#16A34A]' : 'text-[#D97706]' }}">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-semibold {{ $row['status'] === 'Lengkap' ? 'bg-emerald-50 text-[#16A34A]' : 'bg-amber-50 text-[#D97706]' }}">
                                        {{ $row['status'] }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <button
                                        type="button"
                                        @click="openInput({{ $row['id'] }}, '{{ addslashes($row['mapel']) }}')"
                                        class="font-semibold text-[#555783] hover:underline cursor-pointer"
                                    >
                                        Input / Edit Nilai
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    Tidak ada mata pelajaran yang sesuai kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :current="1" :total="1" :totalItems="count($listAsesmen)" :perPage="15" />
        </x-card>

        <!-- Modal Input / Edit Nilai -->
        <div
            x-show="showModal"
            style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-black/40 backdrop-blur-xs flex items-center justify-center p-4"
            @keydown.escape.window="showModal = false"
        >
            <div
                @click.away="showModal = false"
                class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-[#E8E7ED] space-y-4"
            >
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="text-base font-bold text-[#1A1A2E]">Input Nilai & Narasi Capaian</h3>
                        <p class="text-xs text-[#555783] font-medium" x-text="selectedMapel"></p>
                    </div>
                    <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('wali.asesmen.store') }}" class="space-y-4 text-xs">
                    @csrf
                    <input type="hidden" name="mapel_id" :value="selectedMapelId" />
                    <input type="hidden" name="mapel_nama" :value="selectedMapel" />

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Pilih Siswa *</label>
                        <select
                            name="siswa_id"
                            x-model="selectedSiswaId"
                            @change="updateSiswaScore()"
                            required
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]"
                        >
                            @foreach($siswaList as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_lengkap }} (NISN: {{ $s->nisn }}) - {{ $s->kekhususan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Nilai Akhir (Skala 0 - 100) *</label>
                        <input
                            type="number"
                            name="nilai_akhir"
                            required
                            min="0"
                            max="100"
                            x-model="nilaiAkhir"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Deskripsi Capaian Kompetensi *</label>
                        <textarea
                            name="narasi_capaian"
                            rows="4"
                            required
                            x-model="narasiCapaian"
                            placeholder="Tuliskan capaian yang dicapai siswa dengan baik dan aspek yang perlu peningkatan..."
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="showModal = false" class="px-4 py-2.5 rounded-xl border border-gray-200 font-semibold text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#555783] hover:bg-[#46486E] text-white font-bold shadow-xs transition-colors">
                            Simpan Penilaian
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</x-layouts.wali-kelas>
