<x-layouts.admin
    title="Admin · Mata Pelajaran"
    periode="2026/2027 · Semester Ganjil"
    status="Aktif"
    userName="Admin Sekolah">

    <!-- Flash Alert -->
    @if(session('success'))
        <div class="mb-6">
            <x-alert-banner type="success" title="Operasi Berhasil" :message="session('success')" />
        </div>
    @endif

    <div x-data="{
        showCreateModal: false,
        showEditModal: false,
        editId: null,
        editKode: '',
        editNama: '',
        editKelompok: '',
        openEdit(m) {
            this.editId = m.id;
            this.editKode = m.kode;
            this.editNama = m.nama;
            this.editKelompok = m.kelompok;
            this.showEditModal = true;
        }
    }">

        <!-- Page Header -->
        <x-page-header
            breadcrumb="Admin / Mata Pelajaran"
            title="Mata Pelajaran & Program Kekhususan"
            subtitle="Kurikulum Merdeka Khusus SLB dengan 3 program kekhususan resmi: PKPBI, Bina Diri, dan Pengembangan Komunikasi & Interaksi.">
            <x-slot:action>
                <button
                    type="button"
                    @click="showCreateModal = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#555783] hover:bg-[#47496E] text-white text-[14px] font-semibold rounded-xl shadow-sm transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    <span>Tambah Mata Pelajaran</span>
                </button>
            </x-slot:action>
        </x-page-header>

        <!-- Filter Card -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('admin.mapel.index') }}" class="space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="relative flex-1 max-w-lg">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari kode atau nama mata pelajaran..."
                               class="w-full pl-10 pr-4 py-2 border border-[#D5D7E2] rounded-xl text-[13px] text-[#1A1A2E] placeholder-gray-400 focus:outline-none focus:border-[#555783] transition-colors" />
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-4 py-2 bg-[#555783] hover:bg-[#47496E] text-white text-[13px] font-semibold rounded-xl transition-colors shadow-xs">
                            Cari
                        </button>
                        @if(request('search') || request('kelompok'))
                            <a href="{{ route('admin.mapel.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-[#D5D7E2] rounded-xl text-[13px] font-medium text-gray-600 hover:bg-gray-50 transition-colors cursor-pointer">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span>Reset filter</span>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 pt-2 border-t border-gray-100">
                    <div>
                        <select name="kelompok" onchange="this.form.submit()" class="w-full px-3 py-2 border border-[#D5D7E2] rounded-xl text-[13px] text-gray-700 focus:outline-none focus:border-[#555783] bg-white cursor-pointer">
                            <option value="">Kelompok: Semua</option>
                            <option value="Umum" {{ request('kelompok') === 'Umum' ? 'selected' : '' }}>Umum</option>
                            <option value="Program Kekhususan" {{ request('kelompok') === 'Program Kekhususan' ? 'selected' : '' }}>Program Kekhususan (3 Ketunaan)</option>
                            <option value="Keterampilan Pilihan" {{ request('kelompok') === 'Keterampilan Pilihan' ? 'selected' : '' }}>Keterampilan Pilihan</option>
                            <option value="Muatan Lokal" {{ request('kelompok') === 'Muatan Lokal' ? 'selected' : '' }}>Muatan Lokal</option>
                        </select>
                    </div>
                    <div>
                        <div class="w-full px-3 py-2 rounded-xl text-[12px] text-gray-500 bg-gray-50 flex items-center justify-between">
                            <span>Total Muatan</span>
                            <span class="font-bold text-[#555783]">{{ count($mapelList) }} Mapel</span>
                        </div>
                    </div>
                </div>
            </form>
        </x-card>

        <!-- Table of Mata Pelajaran -->
        <x-card class="mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#F7F6F2] text-[#1A1A2E] text-[13px] font-semibold">
                            <th class="py-3 px-4 rounded-l-lg font-semibold">Kode</th>
                            <th class="py-3 px-4 font-semibold">Nama mata pelajaran</th>
                            <th class="py-3 px-4 font-semibold">Kelompok</th>
                            <th class="py-3 px-4 font-semibold">Fase</th>
                            <th class="py-3 px-4 font-semibold">Status</th>
                            <th class="py-3 px-4 rounded-r-lg font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-[13px]">
                        @forelse($mapelList as $m)
                            <tr class="hover:bg-gray-50/40 transition-colors">
                                <td class="py-4.5 px-4 font-mono text-xs font-semibold text-[#555783] whitespace-nowrap">
                                    {{ $m['kode'] }}
                                </td>
                                <td class="py-4.5 px-4 font-semibold text-[#1A1A2E] whitespace-nowrap">
                                    {{ $m['nama'] }}
                                </td>
                                <td class="py-4.5 px-4 text-gray-600 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold {{ $m['kelompok'] === 'Program Kekhususan' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $m['kelompok'] }}
                                    </span>
                                </td>
                                <td class="py-4.5 px-4 text-gray-600 whitespace-nowrap">
                                    {{ $m['fase'] }}
                                </td>
                                <td class="py-4.5 px-4 whitespace-nowrap font-medium text-[#16A34A]">
                                    {{ $m['status'] }}
                                </td>
                                <td class="py-4.5 px-4 whitespace-nowrap text-right text-gray-500">
                                    <div class="flex items-center justify-end gap-1.5 text-[13px]">
                                        <button
                                            type="button"
                                            @click="openEdit({{ json_encode($m) }})"
                                            class="text-gray-700 hover:text-[#555783] font-semibold transition-colors cursor-pointer"
                                        >
                                            Edit
                                        </button>
                                        <span class="text-gray-300">/</span>
                                        <button 
                                            type="button" 
                                            @click="$dispatch('open-delete-modal', { 
                                                name: '{{ addslashes($m['nama']) }}', 
                                                details: 'Kode: {{ $m['kode'] }} · Kelompok: {{ $m['kelompok'] }}',
                                                action: '{{ route('admin.mapel.destroy', $m['id']) }}'
                                            })"
                                            class="text-rose-600 hover:text-rose-800 font-semibold transition-colors cursor-pointer"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    Tidak ada mata pelajaran ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :total="count($mapelList)" :count="count($mapelList)" :perPage="10" />
        </x-card>

        <x-callout-note
            title="3 Program Kekhususan Resmi Berdasarkan Ketunaan"
            subtitle="SLB Insan Mutiara memiliki 3 ketunaan: PKPBI untuk Tunarungu, Bina Diri untuk Tunagrahita, dan Pengembangan Komunikasi & Interaksi untuk Autis." />

        <!-- Modal Tambah Mapel -->
        <div
            x-show="showCreateModal"
            style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-black/40 backdrop-blur-xs flex items-center justify-center p-4"
            @keydown.escape.window="showCreateModal = false"
        >
            <div
                @click.away="showCreateModal = false"
                class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-[#E8E7ED] space-y-4"
            >
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-[#1A1A2E]">Tambah Mata Pelajaran</h3>
                    <button type="button" @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.mapel.store') }}" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Kode Mapel *</label>
                        <input type="text" name="kode" required placeholder="contoh: PKPBI, BIND, MTK" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 uppercase focus:outline-none focus:border-[#555783]" />
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Nama Mata Pelajaran *</label>
                        <input type="text" name="nama" required placeholder="contoh: Program Kekhususan (PKPBI)" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]" />
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Kelompok Muatan *</label>
                        <select name="kelompok" required class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]">
                            <option value="Umum">Umum</option>
                            <option value="Program Kekhususan">Program Kekhususan (3 Ketunaan)</option>
                            <option value="Keterampilan Pilihan">Keterampilan Pilihan</option>
                            <option value="Muatan Lokal">Muatan Lokal</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2.5 rounded-xl border border-gray-200 font-semibold text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#555783] hover:bg-[#46486E] text-white font-bold shadow-xs transition-colors">
                            Simpan Mapel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Mapel -->
        <div
            x-show="showEditModal"
            style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-black/40 backdrop-blur-xs flex items-center justify-center p-4"
            @keydown.escape.window="showEditModal = false"
        >
            <div
                @click.away="showEditModal = false"
                class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-[#E8E7ED] space-y-4"
            >
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-[#1A1A2E]">Edit Mata Pelajaran</h3>
                    <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="'{{ url('/admin/mata-pelajaran') }}/' + editId" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Kode Mapel *</label>
                        <input type="text" name="kode" x-model="editKode" required class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 uppercase focus:outline-none focus:border-[#555783]" />
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Nama Mata Pelajaran *</label>
                        <input type="text" name="nama" x-model="editNama" required class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]" />
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Kelompok Muatan *</label>
                        <select name="kelompok" x-model="editKelompok" required class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]">
                            <option value="Umum">Umum</option>
                            <option value="Program Kekhususan">Program Kekhususan (3 Ketunaan)</option>
                            <option value="Keterampilan Pilihan">Keterampilan Pilihan</option>
                            <option value="Muatan Lokal">Muatan Lokal</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2.5 rounded-xl border border-gray-200 font-semibold text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#555783] hover:bg-[#46486E] text-white font-bold shadow-xs transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Konfirmasi Hapus Mapel 2 Langkah -->
        <x-modal.confirm-delete
            title="Hapus mata pelajaran?"
            itemName="Mata Pelajaran"
            itemDetails="Data kurikulum pembelajaran"
            confirmAction="#"
        />

    </div>

</x-layouts.admin>
