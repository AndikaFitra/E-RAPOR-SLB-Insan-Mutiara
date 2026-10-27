<x-layouts.wali-kelas
    title="Wali Kelas · Evaluasi Non-Akademik"
    periode="2026/2027 · Semester Ganjil"
    userName="Rina Pratiwi · Wali Kelas">

    <!-- Flash Alert -->
    @if(session('success'))
        <div class="mb-4">
            <x-alert-banner type="success" title="Evaluasi Disimpan" :message="session('success')" />
        </div>
    @endif

    <div x-data="{
        showModal: false,
        modalSiswaId: '',
        modalNama: '',
        modalSakit: 0,
        modalIzin: 0,
        modalAlpa: 0,
        modalPerkembangan: '',
        modalCatatanWali: '',
        openEdit(row) {
            this.modalSiswaId = row.id;
            this.modalNama = row.nama;
            this.modalSakit = row.sakit;
            this.modalIzin = row.izin;
            this.modalAlpa = row.alpa;
            this.modalPerkembangan = row.perkembangan;
            this.modalCatatanWali = row.catatan_wali;
            this.showModal = true;
        }
    }">

        <!-- Page Header -->
        <x-page-header
            breadcrumb="Wali Kelas / Evaluasi Non-Akademik"
            title="Evaluasi Non-Akademik"
            subtitle="Lengkapi kehadiran, kegiatan, dan perkembangan siswa sebelum mengajukan rapor ke Kepala Sekolah."
        />

        <!-- Filter Card -->
        <x-card class="p-5 space-y-4">
            <form method="GET" action="{{ route('wali.nonakademik.index') }}" class="flex items-center justify-between gap-4">
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
                        placeholder="Cari nama siswa atau NISN..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-[#1A1A2E] placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#555783] text-white text-xs font-semibold hover:bg-[#46486E] transition-colors shadow-xs">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('wali.nonakademik.index') }}" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 bg-white hover:bg-[#F7F6F2] transition-colors flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </x-card>

        <!-- Data Table Card -->
        <x-card class="overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#F7F6F2] border-b border-[#E8E7ED] font-semibold text-gray-600">
                            <th class="py-3.5 px-6">Siswa</th>
                            <th class="py-3.5 px-4">Tingkat / Fase</th>
                            <th class="py-3.5 px-4">Kehadiran (S / I / A)</th>
                            <th class="py-3.5 px-4">Kegiatan / Prestasi</th>
                            <th class="py-3.5 px-4">Perkembangan</th>
                            <th class="py-3.5 px-4">Kelengkapan</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($dataNonAkademik as $row)
                            <tr class="hover:bg-[#FBFBFA]">
                                <td class="py-4 px-6 font-bold text-[#1A1A2E] text-sm">{{ $row['nama'] }}</td>
                                <td class="py-4 px-4 text-gray-600">{{ $row['tingkat'] }}</td>
                                <td class="py-4 px-4 font-mono text-gray-700">{{ $row['kehadiran'] }}</td>
                                <td class="py-4 px-4 text-gray-700">{{ $row['kegiatan'] }}</td>
                                <td class="py-4 px-4 text-gray-700 max-w-xs truncate" title="{{ $row['perkembangan'] }}">
                                    {{ $row['perkembangan'] }}
                                </td>
                                <td class="py-4 px-4 font-semibold">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-semibold {{ $row['status'] === 'Lengkap' ? 'bg-emerald-50 text-[#16A34A]' : 'bg-amber-50 text-[#D97706]' }}">
                                        {{ $row['status'] }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <button
                                        type="button"
                                        @click="openEdit({{ json_encode($row) }})"
                                        class="font-semibold text-[#555783] hover:underline cursor-pointer"
                                    >
                                        Input / Edit Evaluasi
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-pagination :current="1" :total="1" :totalItems="count($dataNonAkademik)" :perPage="10" />
        </x-card>

        <!-- Modal Input / Edit Evaluasi Non-Akademik -->
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
                        <h3 class="text-base font-bold text-[#1A1A2E]">Evaluasi Non-Akademik Siswa</h3>
                        <p class="text-xs text-gray-500" x-text="modalNama"></p>
                    </div>
                    <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('wali.nonakademik.store') }}" class="space-y-4 text-xs">
                    @csrf
                    <input type="hidden" name="siswa_id" :value="modalSiswaId" />

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Kehadiran (Hari) *</label>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <span class="text-[11px] text-gray-500">Sakit:</span>
                                <input type="number" name="sakit" x-model="modalSakit" min="0" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-center font-bold text-gray-800 focus:outline-none focus:border-[#555783]" />
                            </div>
                            <div>
                                <span class="text-[11px] text-gray-500">Izin:</span>
                                <input type="number" name="izin" x-model="modalIzin" min="0" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-center font-bold text-gray-800 focus:outline-none focus:border-[#555783]" />
                            </div>
                            <div>
                                <span class="text-[11px] text-gray-500">Alpa / Tanpa Ket.:</span>
                                <input type="number" name="alpa" x-model="modalAlpa" min="0" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-center font-bold text-gray-800 focus:outline-none focus:border-[#555783]" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Perkembangan Fisik & Kesehatan *</label>
                        <textarea
                            name="perkembangan"
                            x-model="modalPerkembangan"
                            rows="2"
                            required
                            placeholder="Deskripsi pertumbuhan fisik, kebersihan diri, dan adaptasi..."
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Catatan Wali Kelas untuk Rapor</label>
                        <textarea
                            name="catatan_wali"
                            x-model="modalCatatanWali"
                            rows="3"
                            placeholder="Pesan motivasi atau catatan khusus yang tercetak pada halaman rapor..."
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="showModal = false" class="px-4 py-2.5 rounded-xl border border-gray-200 font-semibold text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#555783] hover:bg-[#46486E] text-white font-bold shadow-xs transition-colors">
                            Simpan Evaluasi
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</x-layouts.wali-kelas>
