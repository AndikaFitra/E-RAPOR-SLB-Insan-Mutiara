<x-layouts.wali-kelas
    title="Wali Kelas · Distribusi Rapor"
    periode="2026/2027 · Semester Ganjil"
    userName="Rina Pratiwi · Wali Kelas">

    <!-- Flash Alert -->
    @if(session('success'))
        <div class="mb-4">
            <x-alert-banner type="success" title="Distribusi Berhasil" :message="session('success')" />
        </div>
    @endif

    <!-- Page Header -->
    <x-page-header
        breadcrumb="Wali Kelas / Distribusi Rapor"
        title="Distribusi Rapor & Leger"
        subtitle="Kirimkan hasil belajar dan leger resmi langsung ke nomor WhatsApp orang tua/wali murid menggunakan wa.me."
    />

    <!-- Filter Card -->
    <x-card class="p-5 space-y-4">
        <form method="GET" action="{{ route('wali.distribusi.index') }}" class="space-y-4">
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
                        placeholder="Cari nama siswa atau penerima..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-[#1A1A2E] placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#555783] text-white text-xs font-semibold hover:bg-[#46486E] transition-colors shadow-xs">
                        Cari
                    </button>
                    @if(request('search') || request('status_kirim'))
                        <a href="{{ route('wali.distribusi.index') }}" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 bg-white hover:bg-[#F7F6F2] transition-colors flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
                <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option>Tahun: 2026/2027</option>
                </select>
                <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option>Semester: Ganjil</option>
                </select>
                <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option>Kelas: Tunarungu A</option>
                </select>
                <select name="status_kirim" onchange="this.form.submit()" class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option value="">Status kirim: Semua</option>
                    <option value="Ditandai terkirim" {{ request('status_kirim') === 'Ditandai terkirim' ? 'selected' : '' }}>Ditandai terkirim</option>
                    <option value="Belum dikirim" {{ request('status_kirim') === 'Belum dikirim' ? 'selected' : '' }}>Belum dikirim</option>
                </select>
            </div>
        </form>
    </x-card>

    <!-- Operational Banner WhatsApp -->
    <x-callout-note
        title="Distribusi WhatsApp Terintegrasi via wa.me"
        description="Rapor yang telah Disetujui Kepala Sekolah dapat langsung dikirim ke WhatsApp orang tua dengan klik tombol 'Kirim WA'. Format pesan memuat identitas 3 ketunaan, Program Kekhususan, rekapitulasi capaian, dan catatan wali kelas."
    />

    <!-- Data Table Card -->
    <div x-data="{
        showModal: false,
        modalTitle: '',
        modalNamaOrtu: '',
        modalNoWa: '',
        modalWaUrl: '',
        modalPesan: '',
        openWaModal(nama, ortu, noWa, waUrl, pesan) {
            this.modalTitle = nama;
            this.modalNamaOrtu = ortu;
            this.modalNoWa = noWa;
            this.modalWaUrl = waUrl;
            this.modalPesan = pesan;
            this.showModal = true;
        }
    }">
        <x-card class="overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#F7F6F2] border-b border-[#E8E7ED] font-semibold text-gray-600">
                            <th class="py-3.5 px-6">Siswa & Ketunaan</th>
                            <th class="py-3.5 px-4">Penerima (Orang Tua)</th>
                            <th class="py-3.5 px-4">Nomor WhatsApp</th>
                            <th class="py-3.5 px-4">Status Rapor</th>
                            <th class="py-3.5 px-4">Status Pengiriman</th>
                            <th class="py-3.5 px-6 text-right">Aksi Distribusi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($dataDistribusi as $row)
                            <tr class="hover:bg-[#FBFBFA]">
                                <td class="py-4 px-6">
                                    <p class="font-bold text-[#1A1A2E] text-sm">{{ $row['siswa'] }}</p>
                                    <div class="flex items-center gap-1.5 text-[11px] text-gray-400 mt-0.5">
                                        <span>NISN: {{ $row['nisn'] }}</span>
                                        <span>·</span>
                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-700">
                                            {{ $row['kekhususan'] }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-gray-700 font-medium">{{ $row['penerima'] }}</td>
                                <td class="py-4 px-4 font-mono text-gray-600">{{ $row['nomor'] ?: '—' }}</td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $row['is_approved'] ? 'bg-emerald-50 text-[#16A34A] border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ $row['status_rapor'] }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 font-semibold">
                                    @if($row['status'] === 'Ditandai terkirim')
                                        <span class="inline-flex items-center gap-1 text-[#16A34A]">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            {{ $row['status'] }}
                                        </span>
                                    @else
                                        <span class="text-[#D97706]">{{ $row['status'] }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Tombol WhatsApp wa.me -->
                                        <a
                                            href="{{ $row['wa_url'] }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-[#25D366] hover:bg-[#1EBE5D] text-white text-xs font-bold shadow-xs transition-colors"
                                            title="Buka WhatsApp ke {{ $row['penerima'] }}"
                                        >
                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                            </svg>
                                            <span>Kirim WA</span>
                                        </a>

                                        <!-- Preview Button -->
                                        <button
                                            type="button"
                                            @click="openWaModal(
                                                '{{ addslashes($row['siswa']) }}',
                                                '{{ addslashes($row['penerima']) }}',
                                                '{{ $row['nomor'] }}',
                                                '{{ $row['wa_url'] }}',
                                                `{{ addslashes($row['pesan_wa']) }}`
                                            )"
                                            class="px-2.5 py-1.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-[#F7F6F2] hover:text-[#555783] text-xs font-semibold transition-colors"
                                        >
                                            Preview
                                        </button>

                                        <!-- Tandai Terkirim -->
                                        @if($row['rapor_id'] && $row['status'] !== 'Ditandai terkirim')
                                            <form method="POST" action="{{ route('wali.distribusi.mark-sent', $row['rapor_id']) }}" class="inline">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="px-2.5 py-1.5 rounded-xl border border-emerald-300 text-emerald-700 hover:bg-emerald-50 text-[11px] font-semibold transition-colors"
                                                    title="Tandai sudah dikirim"
                                                >
                                                    ✓ Tandai
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    Tidak ada data distribusi ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :current="1" :total="1" :totalItems="count($dataDistribusi)" :perPage="10" />
        </x-card>

        <!-- Modal Preview WhatsApp -->
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
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-[#25D366]/10 flex items-center justify-center text-[#25D366]">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#1A1A2E]">Format Pesan WhatsApp (wa.me)</h3>
                            <p class="text-xs text-gray-500" x-text="'Penerima: ' + modalNamaOrtu + ' (' + modalNoWa + ')'"></p>
                        </div>
                    </div>
                    <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="bg-[#F7F6F2] p-4 rounded-xl border border-gray-200">
                    <pre class="font-sans text-xs text-[#1A1A2E] whitespace-pre-wrap leading-relaxed max-h-64 overflow-y-auto" x-text="modalPesan"></pre>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button
                        type="button"
                        @click="showModal = false"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Tutup
                    </button>
                    <a
                        :href="modalWaUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="px-5 py-2.5 rounded-xl bg-[#25D366] hover:bg-[#1EBE5D] text-white text-xs font-bold flex items-center gap-2 shadow-xs transition-colors"
                    >
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        <span>Kirim via WhatsApp (wa.me)</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

</x-layouts.wali-kelas>
