<x-layouts.admin
    title="Admin · Data Guru"
    periode="2026/2027 · Semester Ganjil"
    status="Aktif"
    userName="Admin Sekolah">

    <!-- Flash Alert -->
    @if(session('success'))
        <div class="mb-6">
            <x-alert-banner type="success" title="Operasi Berhasil" :description="session('success')" />
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6" role="alert">
            <x-alert-banner type="error" title="Guru tidak dapat dihapus" :description="session('error')" />
        </div>
    @endif

    <!-- Page Header -->
    <x-page-header
        breadcrumb="Admin / Data Guru"
        title="Data Guru"
        subtitle="Kelola data guru pengajar, dukungan kekhususan 3 ketunaan, kontak, dan status penugasan wali kelas.">
        <x-slot:action>
            <a href="{{ route('admin.guru.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#555783] hover:bg-[#47496E] text-white text-[14px] font-semibold rounded-xl shadow-sm transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>Tambah Guru</span>
            </a>
        </x-slot:action>
    </x-page-header>

    <!-- Filter Card -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.guru.index') }}" class="space-y-4">
            <!-- Search & Reset -->
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
                           placeholder="Cari nama guru atau NIP"
                           class="w-full pl-10 pr-4 py-2 border border-[#D5D7E2] rounded-xl text-[13px] text-[#1A1A2E] placeholder-gray-400 focus:outline-none focus:border-[#555783] transition-colors" />
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-4 py-2 bg-[#555783] hover:bg-[#47496E] text-white text-[13px] font-semibold rounded-xl transition-colors shadow-xs">
                        Terapkan
                    </button>
                    @if(request('search') || request('kekhususan') || request('status'))
                        <a href="{{ route('admin.guru.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-[#D5D7E2] rounded-xl text-[13px] font-medium text-gray-600 hover:bg-gray-50 transition-colors cursor-pointer">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span>Reset filter</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Dropdown Filters (3 Ketunaan) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 pt-2 border-t border-gray-100">
                <div>
                    <select name="kekhususan" onchange="this.form.submit()" class="w-full px-3 py-2 border border-[#D5D7E2] rounded-xl text-[13px] text-gray-700 focus:outline-none focus:border-[#555783] bg-white cursor-pointer">
                        <option value="">Dukungan Kekhususan: Semua</option>
                        <option value="Tunarungu" {{ request('kekhususan') === 'Tunarungu' ? 'selected' : '' }}>Tunarungu</option>
                        <option value="Tunagrahita" {{ request('kekhususan') === 'Tunagrahita' ? 'selected' : '' }}>Tunagrahita</option>
                        <option value="Autis" {{ request('kekhususan') === 'Autis' ? 'selected' : '' }}>Autis</option>
                    </select>
                </div>
                <div>
                    <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 border border-[#D5D7E2] rounded-xl text-[13px] text-gray-700 focus:outline-none focus:border-[#555783] bg-white cursor-pointer">
                        <option value="">Status Akun: Semua</option>
                        <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Tidak aktif" {{ request('status') === 'Tidak aktif' ? 'selected' : '' }}>Tidak aktif</option>
                    </select>
                </div>
                <div>
                    <div class="w-full px-3 py-2 rounded-xl text-[12px] text-gray-500 bg-gray-50 flex items-center justify-between">
                        <span>SLB Insan Mutiara</span>
                        <span class="font-bold text-[#555783]">{{ count($guruList) }} Guru</span>
                    </div>
                </div>
            </div>
        </form>
    </x-card>

    <!-- Table of Guru -->
    <x-card class="mb-6" x-data="{}">
        <div class="overflow-x-auto">
            <table class="dashboard-table dashboard-table--wide text-left">
                <thead>
                    <tr class="bg-[#F7F6F2] text-[#1A1A2E] text-[13px] font-semibold">
                        <th class="py-3 px-4 rounded-l-lg font-semibold">Nama guru</th>
                        <th class="py-3 px-4 font-semibold">NIP</th>
                        <th class="py-3 px-4 font-semibold">Dukungan kekhususan</th>
                        <th class="py-3 px-4 font-semibold">Kontak</th>
                        <th class="py-3 px-4 font-semibold">Kelas aktif</th>
                        <th class="py-3 px-4 font-semibold">Status</th>
                        <th class="py-3 px-4 rounded-r-lg font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-[13px]">
                    @forelse($guruList as $g)
                        <tr class="hover:bg-gray-50/40 transition-colors">
                            <td class="py-4.5 px-4 font-semibold text-[#1A1A2E] whitespace-nowrap">
                                {{ $g['nama'] }}
                            </td>
                            <td class="py-4.5 px-4 text-gray-400 whitespace-nowrap">
                                {{ $g['nip'] }}
                            </td>
                            <td class="py-4.5 px-4 text-gray-600 whitespace-nowrap">
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $g['kekhususan'] }}
                                </span>
                            </td>
                            <td class="py-4.5 px-4 text-gray-600 whitespace-nowrap font-mono text-xs">
                                {{ $g['kontak'] }}
                            </td>
                            <td class="py-4.5 px-4 whitespace-nowrap font-medium {{ $g['kelas'] === 'Belum ditugaskan' ? 'text-amber-700' : 'text-gray-700' }}">
                                {{ $g['kelas'] }}
                            </td>
                            <td class="py-4.5 px-4 whitespace-nowrap font-medium {{ $g['status'] === 'Aktif' ? 'text-[#16A34A]' : 'text-rose-600' }}">
                                {{ $g['status'] }}
                            </td>
                            <td class="py-4.5 px-4 whitespace-nowrap text-right text-gray-500">
                                <div class="flex items-center justify-end gap-2 text-[13px]">
                                    <a href="{{ route('admin.guru.edit', $g['id']) }}" class="text-gray-700 hover:text-[#555783] font-semibold transition-colors">
                                        Edit
                                    </a>
                                    <span class="text-gray-300">/</span>
                                    <button 
                                        type="button" 
                                        data-name="{{ $g['nama'] }}"
                                        data-details="NIP: {{ $g['nip'] }} · Spesialisasi: {{ $g['kekhususan'] }}"
                                        data-action="{{ route('admin.guru.destroy', $g['id']) }}"
                                        @click="$dispatch('open-delete-modal', {
                                            name: $el.dataset.name,
                                            details: $el.dataset.details,
                                            action: $el.dataset.action
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
                                Tidak ada data guru ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :total="count($guruList)" :count="count($guruList)" :perPage="10" />
    </x-card>

    <!-- Modal Konfirmasi Hapus Guru 2 Langkah -->
    <x-modal.confirm-delete
        title="Hapus data guru?"
        itemName="Guru"
        itemDetails="Data guru pengajar"
        confirmAction="#"
    />

</x-layouts.admin>
