<x-layouts.admin
    title="Admin · Orang Tua/Wali"
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
        editSiswaId: '',
        editNama: '',
        editHubungan: '',
        editKontak: '',
        openEdit(ot) {
            this.editId = ot.id;
            this.editSiswaId = ot.siswa_id;
            this.editNama = ot.nama;
            this.editHubungan = ot.hubungan;
            this.editKontak = ot.kontak;
            this.showEditModal = true;
        }
    }">

        <!-- Page Header -->
        <x-page-header
            breadcrumb="Admin / Orang Tua/Wali"
            title="Orang Tua / Wali Siswa"
            subtitle="Kelola kontak wali murid dan verifikasi nomor WhatsApp untuk distribusi leger dan rapor digital.">
            <x-slot:action>
                <button
                    type="button"
                    @click="showCreateModal = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#555783] hover:bg-[#47496E] text-white text-[14px] font-semibold rounded-xl shadow-sm transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    <span>Tambah Orang Tua</span>
                </button>
            </x-slot:action>
        </x-page-header>

        <!-- Filter Card -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('admin.orang-tua.index') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="relative flex-1 max-w-lg">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nama orang tua, siswa, atau nomor WhatsApp..."
                           class="w-full pl-10 pr-4 py-2 border border-[#D5D7E2] rounded-xl text-[13px] text-[#1A1A2E] placeholder-gray-400 focus:outline-none focus:border-[#555783] transition-colors" />
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-4 py-2 bg-[#555783] hover:bg-[#47496E] text-white text-[13px] font-semibold rounded-xl transition-colors shadow-xs">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.orang-tua.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-[#D5D7E2] rounded-xl text-[13px] font-medium text-gray-600 hover:bg-gray-50 transition-colors cursor-pointer">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span>Reset filter</span>
                        </a>
                    @endif
                </div>
            </form>
        </x-card>

        <!-- Table of Orang Tua -->
        <x-card class="mb-6">
            <div class="overflow-x-auto">
                <table class="dashboard-table dashboard-table--wide text-left">
                    <thead>
                        <tr class="bg-[#F7F6F2] text-[#1A1A2E] text-[13px] font-semibold">
                            <th class="py-3 px-4 rounded-l-lg font-semibold">Nama orang tua / wali</th>
                            <th class="py-3 px-4 font-semibold">Siswa</th>
                            <th class="py-3 px-4 font-semibold">Hubungan</th>
                            <th class="py-3 px-4 font-semibold">No. WhatsApp</th>
                            <th class="py-3 px-4 font-semibold">Alamat</th>
                            <th class="py-3 px-4 font-semibold">Status</th>
                            <th class="py-3 px-4 rounded-r-lg font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-[13px]">
                        @forelse($orangTuaList as $ot)
                            <tr class="hover:bg-gray-50/40 transition-colors">
                                <td class="py-4.5 px-4 font-semibold text-[#1A1A2E] whitespace-nowrap">
                                    {{ $ot['nama'] }}
                                </td>
                                <td class="py-4.5 px-4 text-[#555783] font-medium whitespace-nowrap">
                                    {{ $ot['siswa'] }}
                                </td>
                                <td class="py-4.5 px-4 text-gray-600 whitespace-nowrap">
                                    {{ $ot['hubungan'] }}
                                </td>
                                <td class="py-4.5 px-4 text-gray-600 whitespace-nowrap font-mono text-xs">
                                    {{ $ot['kontak'] }}
                                </td>
                                <td class="py-4.5 px-4 text-gray-600">
                                    {{ $ot['alamat'] }}
                                </td>
                                <td class="py-4.5 px-4 whitespace-nowrap font-medium {{ $ot['status'] === 'Terverifikasi' ? 'text-[#16A34A]' : 'text-amber-700' }}">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold {{ $ot['status'] === 'Terverifikasi' ? 'bg-emerald-50 text-[#16A34A]' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $ot['status'] }}
                                    </span>
                                </td>
                                <td class="py-4.5 px-4 whitespace-nowrap text-right text-gray-500">
                                    <div class="flex items-center justify-end gap-1.5 text-[13px]">
                                        <button
                                            type="button"
                                            @click="openEdit({{ json_encode($ot) }})"
                                            class="text-gray-700 hover:text-[#555783] font-semibold transition-colors cursor-pointer"
                                        >
                                            Edit
                                        </button>
                                        <span class="text-gray-300">/</span>
                                        <button 
                                            type="button" 
                                            @click="$dispatch('open-delete-modal', { 
                                                name: '{{ addslashes($ot['nama']) }}', 
                                                details: 'Wali dari: {{ addslashes($ot['siswa']) }} · WhatsApp: {{ $ot['kontak'] }}',
                                                action: '{{ route('admin.orang-tua.destroy', $ot['id']) }}'
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
                                <td colspan="7" class="py-8 text-center text-gray-400">
                                    Tidak ada data orang tua / wali ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :total="count($orangTuaList)" :count="count($orangTuaList)" :perPage="10" />
        </x-card>

        <x-callout-note
            title="Distribusi WhatsApp Memerlukan Nomor Aktif Valid"
            subtitle="Pastikan nomor diawali dengan 08 atau 62. Leger dan Rapor digital akan dikirimkan langsung ke nomor WhatsApp ini melalui deep link wa.me." />

        <!-- Modal Tambah Orang Tua -->
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
                    <h3 class="text-base font-bold text-[#1A1A2E]">Tambah Kontak Orang Tua</h3>
                    <button type="button" @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.orang-tua.store') }}" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Pilih Siswa *</label>
                        <select name="siswa_id" required class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]">
                            <option value="">Pilih Siswa...</option>
                            @foreach($siswaList as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_lengkap }} (NISN: {{ $s->nisn }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Nama Orang Tua / Wali *</label>
                        <input type="text" name="nama" required placeholder="contoh: Bambang Santoso" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]" />
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Hubungan *</label>
                        <select name="hubungan" required class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]">
                            <option value="Ayah">Ayah</option>
                            <option value="Ibu">Ibu</option>
                            <option value="Wali">Wali Murid</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Nomor WhatsApp Aktif *</label>
                        <input type="text" name="no_whatsapp" required placeholder="contoh: 08127001001" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2.5 rounded-xl border border-gray-200 font-semibold text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#555783] hover:bg-[#46486E] text-white font-bold shadow-xs transition-colors">
                            Simpan Kontak
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Orang Tua -->
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
                    <h3 class="text-base font-bold text-[#1A1A2E]">Edit Data Orang Tua</h3>
                    <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="'{{ url('/admin/orang-tua') }}/' + editId" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Pilih Siswa *</label>
                        <select name="siswa_id" x-model="editSiswaId" required class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]">
                            @foreach($siswaList as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_lengkap }} (NISN: {{ $s->nisn }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Nama Orang Tua / Wali *</label>
                        <input type="text" name="nama" x-model="editNama" required class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]" />
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Hubungan *</label>
                        <select name="hubungan" x-model="editHubungan" required class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]">
                            <option value="Ayah">Ayah</option>
                            <option value="Ibu">Ibu</option>
                            <option value="Wali">Wali Murid</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Nomor WhatsApp Aktif *</label>
                        <input type="text" name="no_whatsapp" x-model="editKontak" required class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]" />
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

        <!-- Modal Konfirmasi Hapus Orang Tua 2 Langkah -->
        <x-modal.confirm-delete
            title="Hapus data orang tua?"
            itemName="Orang Tua"
            itemDetails="Kontak wali murid penerima rapor"
            confirmAction="#"
        />

    </div>

</x-layouts.admin>
