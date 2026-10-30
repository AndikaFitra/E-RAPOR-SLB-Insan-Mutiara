<x-layouts.admin
    title="Admin · Data Siswa"
    periode="2026/2027 · Semester Ganjil"
    status="Aktif"
    userName="Admin Sekolah">

    <!-- Page Header -->
    <x-page-header
        breadcrumb="Admin / Data Siswa"
        title="Data Siswa"
        subtitle="Kelola identitas, kelas campuran, dan riwayat akademik setiap siswa.">
        <x-slot:action>
            <a href="{{ route('admin.siswa.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#555783] hover:bg-[#47496E] text-white text-[14px] font-semibold rounded-xl shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>Tambah Siswa</span>
            </a>
        </x-slot:action>
    </x-page-header>

    <!-- Success Alert Banner if present or tested via ?notif=sukses -->
    @if(session('success') || request('notif') === 'sukses')
        <x-alert-banner
            type="success"
            title="Data siswa berhasil disimpan."
            description="{{ session('success') ?? 'Hubungan orang tua/wali dapat ditetapkan nanti.' }}"
            class="mb-6"
        />
    @endif

    <!-- Filter Card -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.siswa.index') }}" class="space-y-4">
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
                           placeholder="Cari nama atau nomor identitas"
                           class="w-full pl-10 pr-4 py-2 border border-[#D5D7E2] rounded-xl text-[13px] text-[#1A1A2E] placeholder-gray-400 focus:outline-none focus:border-[#555783] transition-colors" />
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-[13px] text-gray-500 hidden sm:inline">Filter lanjutan</span>
                    <a href="{{ route('admin.siswa.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-[#D5D7E2] rounded-xl text-[13px] font-medium text-gray-600 hover:bg-gray-50 transition-colors cursor-pointer">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Reset filter</span>
                    </a>
                </div>
            </div>

            <!-- Filter Dropdowns Row -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">
                <div>
                    <select name="kelas_id" onchange="this.form.submit()" class="w-full px-3 py-2 border border-[#D5D7E2] rounded-xl text-[13px] text-gray-700 focus:outline-none focus:border-[#555783] bg-white cursor-pointer">
                        <option value="">Kelas: Semua</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="kekhususan" onchange="this.form.submit()" class="w-full px-3 py-2 border border-[#D5D7E2] rounded-xl text-[13px] text-gray-700 focus:outline-none focus:border-[#555783] bg-white cursor-pointer">
                        <option value="">Kekhususan: Semua</option>
                        <option value="Tunarungu" {{ request('kekhususan') === 'Tunarungu' ? 'selected' : '' }}>Tunarungu (PKPBI)</option>
                        <option value="Tunagrahita" {{ request('kekhususan') === 'Tunagrahita' ? 'selected' : '' }}>Tunagrahita (Bina Diri)</option>
                        <option value="Autis" {{ request('kekhususan') === 'Autis' ? 'selected' : '' }}>Autis (Pengembangan Komunikasi)</option>
                    </select>
                </div>
                <div>
                    <select name="tingkat" onchange="this.form.submit()" class="w-full px-3 py-2 border border-[#D5D7E2] rounded-xl text-[13px] text-gray-700 focus:outline-none focus:border-[#555783] bg-white cursor-pointer">
                        <option value="">Tingkat: Semua</option>
                        @foreach(['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'] as $tingkat)
                            <option value="{{ $tingkat }}" @selected(request('tingkat') === $tingkat)>{{ $tingkat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 border border-[#D5D7E2] rounded-xl text-[13px] text-gray-700 focus:outline-none focus:border-[#555783] bg-white cursor-pointer">
                        <option value="">Status: Semua</option>
                        <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                        <option value="nonaktif" @selected(request('status') === 'nonaktif')>Tidak aktif</option>
                    </select>
                </div>
            </div>
        </form>
    </x-card>

    <!-- Table of Siswa -->
    <x-card class="mb-6" x-data="{}">
        <div class="overflow-x-auto">
            <table class="dashboard-table min-w-[1080px] text-left">
                <thead>
                    <tr class="border-b border-[#E8E7ED] bg-[#F7F6F2] text-[12px] font-bold text-[#555783]">
                        <th scope="col" class="px-4 py-4">Nama siswa</th>
                        <th scope="col" class="px-4 py-4">NIS / NISN</th>
                        <th scope="col" class="px-4 py-4">Kelas</th>
                        <th scope="col" class="px-4 py-4 whitespace-nowrap">Tingkat / Fase</th>
                        <th scope="col" class="px-4 py-4">Kekhususan</th>
                        <th scope="col" class="px-4 py-4">Status</th>
                        <th scope="col" class="px-4 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8E7ED]">
                    @forelse($siswaList as $s)
                        <tr class="hover:bg-[#FBFBFA] transition-colors">
                            <td class="px-4 py-4 font-semibold text-[#1A1A2E]">
                                {{ $s['nama'] }}
                            </td>
                            <td class="px-4 py-4 text-gray-600 whitespace-nowrap">
                                <span class="block font-medium text-[#1A1A2E]">NIS {{ $s['nis'] }}</span>
                                <span class="mt-0.5 block text-xs text-gray-500">NISN {{ $s['nisn'] }}</span>
                            </td>
                            <td class="px-4 py-4 text-gray-600 whitespace-nowrap">
                                {{ $s['kelas'] }}
                            </td>
                            <td class="px-4 py-4 text-gray-600 whitespace-nowrap">
                                {{ $s['tingkat_fase'] }}
                            </td>
                            <td class="px-4 py-4 text-gray-600 whitespace-nowrap">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold
                                    {{ $s['kekhususan'] === 'Tunarungu' ? 'bg-indigo-50 text-indigo-700' : '' }}
                                    {{ $s['kekhususan'] === 'Tunagrahita' ? 'bg-amber-50 text-amber-700' : '' }}
                                    {{ $s['kekhususan'] === 'Autis' ? 'bg-emerald-50 text-emerald-700' : '' }}">
                                    {{ $s['kekhususan'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $s['status'] === 'Aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $s['status'] === 'Aktif' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>{{ $s['status'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5 text-[11px] font-semibold">
                                    <a href="{{ route('admin.siswa.show', $s['id']) }}" class="inline-flex min-h-8 items-center rounded-lg border border-[#D8DAE4] px-2.5 text-[#555783] hover:bg-[#F3F2F8] transition-colors">
                                        Detail
                                    </a>
                                    <a href="{{ route('admin.siswa.edit', $s['id']) }}" class="inline-flex min-h-8 items-center rounded-lg border border-[#D8DAE4] px-2.5 text-[#555783] hover:bg-[#F3F2F8] transition-colors">
                                        Edit
                                    </a>
                                    @if($s['status'] === 'Aktif')
                                        <button
                                            type="button"
                                            data-name="{{ $s['nama'] }}"
                                            data-details="NIS {{ $s['nis'] }} · NISN {{ $s['nisn'] }} · {{ $s['kelas'] }} ({{ $s['kekhususan'] }})"
                                            data-action="{{ route('admin.siswa.destroy', $s['id']) }}"
                                            @click="$dispatch('open-delete-modal', {
                                                name: $el.dataset.name,
                                                details: $el.dataset.details,
                                                action: $el.dataset.action
                                            })"
                                            class="inline-flex min-h-8 items-center rounded-lg border border-rose-200 bg-rose-50 px-2.5 text-rose-700 hover:bg-rose-100 transition-colors cursor-pointer"
                                        >
                                            Nonaktifkan
                                        </button>
                                    @else
                                        <span class="inline-flex min-h-8 items-center rounded-lg border border-gray-200 bg-gray-50 px-2.5 text-gray-500">Sudah nonaktif</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400">
                                Tidak ada data siswa ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :total="count($siswaList)" :count="count($siswaList)" :perPage="10" />
    </x-card>

    <!-- Callout Note Sesuai Mockup -->
    <x-callout-note
        title="Riwayat akademik tetap tersimpan"
        subtitle="Siswa lulus atau pindah tidak dihapus dari arsip. Buka Detail untuk melihat rapor lintas tahun." />

    <!-- Reusable Modal Confirm Delete dengan 2 Langkah Verifikasi -->
    <x-modal.confirm-delete
        title="Nonaktifkan siswa?"
        :deactivate="true"
        itemName="Alya Putri"
        itemDetails="NIS 26001 · NISN 0160001001 · Tunarungu A"
        confirmAction="#"
    />

</x-layouts.admin>
