<x-layouts.admin
    title="Admin · Ekstrakurikuler"
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
        editNama: '',
        openEdit(e) {
            this.editId = e.id;
            this.editNama = e.nama;
            this.showEditModal = true;
        }
    }">

        <a href="{{ route('admin.ekskul.penugasan') }}" class="inline-flex px-4 py-2 bg-[#555783] text-white rounded-xl">Penugasan siswa dan pembimbing</a>
    <!-- Page Header -->
        <x-page-header
            breadcrumb="Admin / Ekstrakurikuler"
            title="Ekstrakurikuler"
            subtitle="Kelola program kegiatan bakat, minat, dan pengembangan potensi siswa khusus disabilitas.">
            <x-slot:action>
                <button
                    type="button"
                    @click="showCreateModal = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#555783] hover:bg-[#47496E] text-white text-[14px] font-semibold rounded-xl shadow-sm transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    <span>Tambah Ekstrakurikuler</span>
                </button>
            </x-slot:action>
        </x-page-header>

        <!-- Table of Ekskul -->
        <x-card class="mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#F7F6F2] text-[#1A1A2E] text-[13px] font-semibold">
                            <th class="py-3 px-4 rounded-l-lg font-semibold">Nama ekstrakurikuler</th>
                            <th class="py-3 px-4 font-semibold">Guru pembimbing</th>
                            <th class="py-3 px-4 font-semibold">Jadwal latihan</th>
                            <th class="py-3 px-4 font-semibold">Siswa aktif</th>
                            <th class="py-3 px-4 font-semibold">Status</th>
                            <th class="py-3 px-4 rounded-r-lg font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-[13px]">
                        @forelse($ekskulList as $e)
                            <tr class="hover:bg-gray-50/40 transition-colors">
                                <td class="py-4.5 px-4 font-semibold text-[#1A1A2E] whitespace-nowrap">
                                    {{ $e['nama'] }}
                                </td>
                                <td class="py-4.5 px-4 text-gray-700 whitespace-nowrap">
                                    {{ $e['pembimbing'] }}
                                </td>
                                <td class="py-4.5 px-4 text-gray-600 whitespace-nowrap">
                                    {{ $e['jadwal'] }}
                                </td>
                                <td class="py-4.5 px-4 text-gray-600 whitespace-nowrap">
                                    {{ $e['jumlah_siswa'] }} siswa
                                </td>
                                <td class="py-4.5 px-4 whitespace-nowrap font-medium text-[#16A34A]">
                                    {{ $e['status'] }}
                                </td>
                                <td class="py-4.5 px-4 whitespace-nowrap text-right text-gray-500">
                                    <div class="flex items-center justify-end gap-1.5 text-[13px]">
                                        <button
                                            type="button"
                                            @click="openEdit({{ json_encode($e) }})"
                                            class="text-gray-700 hover:text-[#555783] font-semibold transition-colors cursor-pointer"
                                        >
                                            Edit
                                        </button>
                                        <span class="text-gray-300">/</span>
                                        <button 
                                            type="button" 
                                            @click="$dispatch('open-delete-modal', { 
                                                name: '{{ addslashes($e['nama']) }}', 
                                                details: 'Pembimbing: {{ addslashes($e['pembimbing']) }} · Jadwal: {{ $e['jadwal'] }}',
                                                action: '{{ route('admin.ekskul.destroy', $e['id']) }}'
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
                                    Tidak ada data ekstrakurikuler ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :total="count($ekskulList)" :count="count($ekskulList)" :perPage="10" />
        </x-card>

        <!-- Modal Tambah Ekskul -->
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
                    <h3 class="text-base font-bold text-[#1A1A2E]">Tambah Ekstrakurikuler</h3>
                    <button type="button" @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.ekskul.store') }}" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Nama Ekstrakurikuler *</label>
                        <input type="text" name="nama" required placeholder="contoh: Pantomim, Olahraga Bocce, Pramuka" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2.5 rounded-xl border border-gray-200 font-semibold text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#555783] hover:bg-[#46486E] text-white font-bold shadow-xs transition-colors">
                            Simpan Ekskul
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Ekskul -->
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
                    <h3 class="text-base font-bold text-[#1A1A2E]">Edit Ekstrakurikuler</h3>
                    <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="'{{ url('/admin/ekstrakurikuler') }}/' + editId" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Nama Ekstrakurikuler *</label>
                        <input type="text" name="nama" x-model="editNama" required class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]" />
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

        <!-- Modal Konfirmasi Hapus Ekskul 2 Langkah -->
        <x-modal.confirm-delete
            title="Hapus ekstrakurikuler?"
            itemName="Ekstrakurikuler"
            itemDetails="Kegiatan pengembangan minat dan bakat"
            confirmAction="#"
        />

    </div>

</x-layouts.admin>
