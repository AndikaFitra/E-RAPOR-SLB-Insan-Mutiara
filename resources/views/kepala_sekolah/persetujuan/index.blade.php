<x-layouts.kepala-sekolah
    title="Kepala Sekolah · Persetujuan Rapor"
    periode="2026/2027 · Semester Ganjil"
    userName="Novia Parenri · Kepala Sekolah">

    <!-- Flash Alert -->
    @if(session('success'))
        <div class="mb-4">
            <x-alert-banner type="success" title="Persetujuan Berhasil" :message="session('success')" />
        </div>
    @elseif(session('info'))
        <div class="mb-4">
            <x-alert-banner type="info" title="Rapor Dikembalikan" :message="session('info')" />
        </div>
    @endif

    <!-- Page Header -->
    <x-page-header
        breadcrumb="Kepala Sekolah / Persetujuan Rapor"
        title="Persetujuan Rapor"
        subtitle="Meninjau, menyetujui secara resmi, atau mengembalikan lembar rapor yang diajukan wali kelas."
    />

    <!-- Filter Card -->
    <x-card class="p-5 space-y-4">
        <form method="GET" action="{{ route('kepala.persetujuan.index') }}" class="space-y-4">
            <input type="hidden" name="status" value="{{ request('status') }}" />

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
                        placeholder="Cari nama siswa, NISN, atau wali kelas..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-[#1A1A2E] placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#555783] text-white text-xs font-semibold hover:bg-[#46486E] transition-colors shadow-xs">
                        Cari
                    </button>
                    @if(request('search') || request('kelas_id') || request('kekhususan') || request('status'))
                        <a href="{{ route('kepala.persetujuan.index') }}" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 bg-white hover:bg-[#F7F6F2] transition-colors flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset filter
                        </a>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
                <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]" disabled>
                    <option>Tahun: {{ $tahunAktif?->tahun ?? '2026/2027' }}</option>
                </select>
                <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]" disabled>
                    <option>Semester: {{ $tahunAktif?->semester ?? 'Ganjil' }}</option>
                </select>
                <select name="kelas_id" onchange="this.form.submit()" class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option value="">Kelas: Semua</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ (string)request('kelas_id') === (string)$k->id ? 'selected' : '' }}>
                            {{ $k->nama }} ({{ $k->kekhususan }})
                        </option>
                    @endforeach
                </select>
                <select name="kekhususan" onchange="this.form.submit()" class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option value="">Kekhususan: Semua</option>
                    <option value="Tunarungu" {{ request('kekhususan') === 'Tunarungu' ? 'selected' : '' }}>Tunarungu</option>
                    <option value="Tunagrahita" {{ request('kekhususan') === 'Tunagrahita' ? 'selected' : '' }}>Tunagrahita</option>
                    <option value="Autis" {{ request('kekhususan') === 'Autis' ? 'selected' : '' }}>Autis</option>
                </select>
            </div>
        </form>
    </x-card>

    <!-- Status Badges Quick Bar -->
    <div class="flex items-center gap-2 text-xs flex-wrap">
        <a href="{{ route('kepala.persetujuan.index', array_merge(request()->except('status'), ['status' => 'diajukan'])) }}"
           class="px-3 py-1 rounded-full border font-medium transition-colors {{ request('status') === 'diajukan' ? 'bg-blue-100 text-blue-900 border-blue-400 font-bold' : 'bg-blue-50 text-blue-800 border-blue-200 hover:bg-blue-100' }}">
            Menunggu Tinjauan (Diajukan) · {{ $counts['diajukan'] }}
        </a>
        <a href="{{ route('kepala.persetujuan.index', array_merge(request()->except('status'), ['status' => 'disetujui'])) }}"
           class="px-3 py-1 rounded-full border font-medium transition-colors {{ request('status') === 'disetujui' ? 'bg-emerald-100 text-emerald-900 border-emerald-400 font-bold' : 'bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100' }}">
            Disetujui · {{ $counts['disetujui'] }}
        </a>
        <a href="{{ route('kepala.persetujuan.index', array_merge(request()->except('status'), ['status' => 'dikembalikan'])) }}"
           class="px-3 py-1 rounded-full border font-medium transition-colors {{ request('status') === 'dikembalikan' ? 'bg-rose-100 text-rose-900 border-rose-400 font-bold' : 'bg-rose-50 text-rose-800 border-rose-200 hover:bg-rose-100' }}">
            Dikembalikan · {{ $counts['dikembalikan'] }}
        </a>
        @if(request('status'))
            <a href="{{ route('kepala.persetujuan.index', request()->except('status')) }}" class="text-xs text-gray-500 hover:text-gray-800 underline ml-2">
                Tampilkan Semua ({{ $counts['total'] }})
            </a>
        @endif
    </div>

    <!-- Data Table Card -->
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#F7F6F2] border-b border-[#E8E7ED] font-semibold text-gray-600">
                        <th class="py-3.5 px-6">Siswa</th>
                        <th class="py-3.5 px-4">Kelas / Tingkat / Fase</th>
                        <th class="py-3.5 px-4">Wali kelas</th>
                        <th class="py-3.5 px-4">Diajukan</th>
                        <th class="py-3.5 px-4">Kelengkapan</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($antrean as $item)
                        @php
                            $statusLower = strtolower($item['status']);
                            $badgeColor = match($statusLower) {
                                'disetujui' => 'bg-emerald-50 text-[#16A34A]',
                                'dikembalikan' => 'bg-rose-50 text-[#DC2626]',
                                'diajukan' => 'bg-blue-50 text-[#2563EB]',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <tr class="hover:bg-[#FBFBFA] transition-colors">
                            <td class="py-4 px-6 font-bold text-[#1A1A2E]">
                                <div>{{ $item['nama_siswa'] }}</div>
                                <div class="text-[11px] font-normal text-gray-400">NISN: {{ $item['nisn'] }} · {{ $item['kekhususan'] }}</div>
                            </td>
                            <td class="py-4 px-4 text-gray-600">
                                {{ $item['kelas'] }} / {{ $item['fase'] }}
                            </td>
                            <td class="py-4 px-4 text-gray-600">
                                {{ $item['wali_kelas'] }}
                            </td>
                            <td class="py-4 px-4 text-gray-500">
                                {{ $item['tanggal'] }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-semibold text-[#16A34A]">
                                    Lengkap
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-semibold {{ $badgeColor }}">
                                    {{ $item['status'] }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a
                                    href="{{ route('kepala.persetujuan.show', $item['id']) }}"
                                    class="inline-block px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-[#F7F6F2] transition-colors"
                                >
                                    Tinjau Rapor
                                </a>
                                <a
                                    href="{{ route('kepala.persetujuan.cetak', $item['id']) }}"
                                    target="_blank"
                                    class="text-[11px] text-[#555783] hover:underline"
                                >
                                    PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400">
                                Tidak ada data pengajuan rapor yang cocok dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :current="1" :total="1" :totalItems="count($antrean)" :perPage="10" />
    </x-card>

</x-layouts.kepala-sekolah>
