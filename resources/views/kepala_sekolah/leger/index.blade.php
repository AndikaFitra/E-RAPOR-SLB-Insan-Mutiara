<x-layouts.kepala-sekolah
    title="Kepala Sekolah · Leger Rekap Nilai"
    periode="2026/2027 · Semester Ganjil"
    userName="Novia增 Parenri · Kepala Sekolah">

    <!-- Page Header -->
    <x-page-header
        breadcrumb="Kepala Sekolah / Leger / Rekap Nilai"
        title="Leger / Rekap Nilai"
        subtitle="Rekap penilaian siswa sesuai kelas, tingkat individual, dan distribusi leger via WhatsApp ke orang tua."
    />

    <!-- Filter Card -->
    <x-card class="p-5 space-y-4" x-data="{
        selectedMessage: '',
        selectedWaUrl: '',
        selectedNama: '',
        selectedOrtu: '',
        selectedNoWa: '',
        showPreviewModal: false,
        openPreview(nama, ortu, noWa, waUrl, pesan) {
            this.selectedNama = nama;
            this.selectedOrtu = ortu;
            this.selectedNoWa = noWa;
            this.selectedWaUrl = waUrl;
            this.selectedMessage = pesan;
            this.showPreviewModal = true;
        }
    }">
        <form method="GET" action="{{ route('kepala.leger.index') }}" class="space-y-4">
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
                        placeholder="Cari nama siswa atau NISN..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-[#1A1A2E] placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#555783] text-white text-xs font-semibold hover:bg-[#46486E] transition-colors shadow-xs">
                        Cari
                    </button>
                    @if(request('search') || request('kelas'))
                        <a href="{{ route('kepala.leger.index') }}" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 bg-white hover:bg-[#F7F6F2] transition-colors flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
                <select name="kelas" onchange="this.form.submit()" class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->nama }}" {{ request('kelas') === $k->nama ? 'selected' : '' }}>
                            Kelas: {{ $k->nama }}
                        </option>
                    @endforeach
                </select>
                <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option>Tahun: 2026/2027</option>
                </select>
                <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option>Semester: Ganjil</option>
                </select>
                <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option>3 Ketunaan: Tunarungu, Tunagrahita, Autis</option>
                </select>
            </div>
        </form>
    </x-card>

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-gray-500">
        <p>
            Menampilkan <strong>{{ count($dataLeger) }}</strong> siswa terdaftar. Klik tombol WhatsApp untuk mengirimkan ringkasan nilai langsung ke nomor orang tua/wali via <code>wa.me</code>.
        </p>
        <button
            type="button"
            onclick="window.print()"
            class="px-4 py-2 rounded-xl bg-[#555783] hover:bg-[#46486E] text-white font-medium flex items-center gap-2 transition-colors self-start sm:self-auto cursor-pointer"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak Leger / PDF
        </button>
    </div>

    <!-- Table Leger Akademik -->
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
                <table class="dashboard-table dashboard-table--wide text-left">
                    <thead>
                        <tr class="bg-[#F7F6F2] border-b border-[#E8E7ED] font-semibold text-gray-600">
                            <th class="py-3 px-4">Siswa & Ketunaan</th>
                            <th class="py-3 px-3">Tingkat / Fase</th>
                            <th class="py-3 px-2 text-center">Agama</th>
                            <th class="py-3 px-2 text-center">PP</th>
                            <th class="py-3 px-2 text-center">BIN</th>
                            <th class="py-3 px-2 text-center">MAT</th>
                            <th class="py-3 px-2 text-center">BIG</th>
                            <th class="py-3 px-2 text-center">IPA</th>
                            <th class="py-3 px-2 text-center">IPS</th>
                            <th class="py-3 px-3 text-center">Rata-rata</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Kirim Leger WA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($dataLeger as $row)
                            <tr class="hover:bg-[#FBFBFA]">
                                <td class="py-3.5 px-4">
                                    <p class="font-bold text-[#1A1A2E] text-sm">{{ $row['nama'] }}</p>
                                    <div class="flex items-center gap-2 mt-0.5 text-[11px] text-gray-500">
                                        <span>NISN: {{ $row['nisn'] }}</span>
                                        <span>·</span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded font-medium {{ $row['kekhususan'] === 'Tunarungu' ? 'bg-blue-50 text-blue-700' : ($row['kekhususan'] === 'Tunagrahita' ? 'bg-amber-50 text-amber-700' : 'bg-purple-50 text-purple-700') }}">
                                            {{ $row['kekhususan'] }}
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-0.5">
                                        Khusus: <strong>{{ $row['prog_khusus'] }}</strong>
                                    </p>
                                </td>
                                <td class="py-3 px-3 text-gray-600">
                                    <p class="font-medium text-[#1A1A2E]">{{ $row['kelas'] }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $row['tingkat'] }}</p>
                                </td>
                                <td class="py-3 px-2 text-center text-gray-700">{{ $row['agama'] }}</td>
                                <td class="py-3 px-2 text-center text-gray-700">{{ $row['pp'] }}</td>
                                <td class="py-3 px-2 text-center text-gray-700">{{ $row['bin'] }}</td>
                                <td class="py-3 px-2 text-center text-gray-700">{{ $row['mat'] }}</td>
                                <td class="py-3 px-2 text-center text-gray-700">{{ $row['big'] }}</td>
                                <td class="py-3 px-2 text-center text-gray-700">{{ $row['ipa'] }}</td>
                                <td class="py-3 px-2 text-center text-gray-700">{{ $row['ips'] }}</td>
                                <td class="py-3 px-3 text-center font-bold text-sm text-[#1A1A2E]">{{ $row['rata'] }}</td>
                                <td class="py-3 px-3 text-center font-semibold {{ $row['status'] === 'Tuntas' ? 'text-[#16A34A]' : 'text-[#DC2626]' }}">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $row['status'] === 'Tuntas' ? 'bg-emerald-50 text-[#16A34A]' : 'bg-rose-50 text-[#DC2626]' }}">
                                        {{ $row['status'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Tombol Direct WA -->
                                        <a
                                            href="{{ $row['wa_url'] }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#25D366] hover:bg-[#1EBE5D] text-white text-[11px] font-bold shadow-xs transition-colors"
                                            title="Kirim ke {{ $row['nama_ortu'] }} ({{ $row['no_wa'] }})"
                                        >
                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                            </svg>
                                            <span>Kirim WA</span>
                                        </a>

                                        <!-- Preview / Info Button -->
                                        <button
                                            type="button"
                                            @click="openWaModal(
                                                '{{ addslashes($row['nama']) }}',
                                                '{{ addslashes($row['nama_ortu']) }}',
                                                '{{ $row['no_wa'] }}',
                                                '{{ $row['wa_url'] }}',
                                                `{{ addslashes($row['pesan_wa']) }}`
                                            )"
                                            class="p-1.5 rounded-xl border border-gray-200 text-gray-500 hover:bg-[#F7F6F2] hover:text-[#555783] transition-colors"
                                            title="Lihat pesan ringkasan"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="py-8 text-center text-gray-400">
                                    Tidak ada data siswa ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :current="1" :total="1" :totalItems="count($dataLeger)" :perPage="10" />
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
                            <h3 class="text-sm font-bold text-[#1A1A2E]">Preview Kirim Leger via WhatsApp</h3>
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
                        <span>Buka WhatsApp (wa.me)</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Callout Note -->
    <x-callout-note
        title="Distribusi WhatsApp Terhubung Otomatis via wa.me"
        description="Fitur Leger telah dilengkapi deep link WhatsApp resmi (wa.me) yang memuat nama siswa, NISN, 3 ketunaan, Program Kekhususan, rekapitulasi nilai, presensi, serta catatan wali kelas."
    />

</x-layouts.kepala-sekolah>
