<x-layouts.kepala-sekolah
    title="Kepala Sekolah · Arsip Rapor"
    periode="2026/2027 · Semester Ganjil"
    userName="Novia Parenri · Kepala Sekolah">

    <!-- Page Header -->
    <x-page-header
        breadcrumb="Kepala Sekolah / Arsip"
        title="Arsip Rapor"
        subtitle="Telusuri seluruh dokumen rapor siswa dan riwayat penilaian pada periode sebelumnya."
    />

    <!-- Filter Card -->
    <x-card class="p-5 space-y-4">
        <form method="GET" action="{{ route('kepala.arsip.index') }}" class="space-y-4">
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
                        placeholder="Cari nama siswa, NISN, atau kelas..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-[#1A1A2E] placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#555783] text-white text-xs font-semibold hover:bg-[#46486E] transition-colors shadow-xs">
                        Cari
                    </button>
                    @if(request('search') || request('tahun_ajaran_id') || request('kekhususan') || request('status_siswa'))
                        <a href="{{ route('kepala.arsip.index') }}" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 bg-white hover:bg-[#F7F6F2] transition-colors flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset filter
                        </a>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
                <select name="tahun_ajaran_id" onchange="this.form.submit()" class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option value="">Tahun Ajaran: Semua</option>
                    @foreach($tahunList as $th)
                        <option value="{{ $th->id }}" {{ (string)request('tahun_ajaran_id') === (string)$th->id ? 'selected' : '' }}>
                            {{ $th->tahun }} - {{ $th->semester }} {{ $th->is_aktif ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>

                <select name="kekhususan" onchange="this.form.submit()" class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option value="">Kekhususan: Semua</option>
                    <option value="Tunarungu" {{ request('kekhususan') === 'Tunarungu' ? 'selected' : '' }}>Tunarungu</option>
                    <option value="Tunagrahita" {{ request('kekhususan') === 'Tunagrahita' ? 'selected' : '' }}>Tunagrahita</option>
                    <option value="Autis" {{ request('kekhususan') === 'Autis' ? 'selected' : '' }}>Autis</option>
                </select>

                <select name="status_siswa" onchange="this.form.submit()" class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option value="">Status Siswa: Semua</option>
                    <option value="Aktif" {{ request('status_siswa') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Lulus" {{ request('status_siswa') === 'Lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="Pindah" {{ request('status_siswa') === 'Pindah' ? 'selected' : '' }}>Pindah</option>
                </select>

                <div class="flex items-center text-xs text-gray-500 px-3">
                    Total: <strong class="ml-1 text-[#1A1A2E]">{{ count($arsip) }}</strong> dokumen arsip
                </div>
            </div>
        </form>
    </x-card>

    <!-- Table Card -->
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#F7F6F2] border-b border-[#E8E7ED] font-semibold text-gray-600">
                        <th class="py-3.5 px-6">Siswa</th>
                        <th class="py-3.5 px-4">Tahun / Semester</th>
                        <th class="py-3.5 px-4">Kelas saat itu</th>
                        <th class="py-3.5 px-4">Tingkat · Fase</th>
                        <th class="py-3.5 px-4">Status siswa</th>
                        <th class="py-3.5 px-4">Status rapor</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($arsip as $row)
                        <tr class="hover:bg-[#FBFBFA]">
                            <td class="py-4 px-6 font-bold text-[#1A1A2E]">
                                <div>{{ $row['nama'] }}</div>
                                <div class="text-[11px] font-normal text-gray-400">NISN: {{ $row['nisn'] }} · {{ $row['kekhususan'] }}</div>
                            </td>
                            <td class="py-4 px-4 text-gray-600">{{ $row['periode'] }}</td>
                            <td class="py-4 px-4 text-gray-600">{{ $row['kelas'] }}</td>
                            <td class="py-4 px-4 text-gray-600">{{ $row['tingkat'] }}</td>
                            <td class="py-4 px-4">
                                <span class="font-semibold {{ $row['status'] === 'Aktif' ? 'text-[#16A34A]' : 'text-gray-600' }}">
                                    {{ $row['status'] }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-semibold {{ $row['status_rapor'] === 'Disetujui' ? 'bg-emerald-50 text-[#16A34A]' : ($row['status_rapor'] === 'Dikembalikan' ? 'bg-rose-50 text-[#DC2626]' : 'bg-blue-50 text-[#2563EB]') }}">
                                    {{ $row['status_rapor'] }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('kepala.persetujuan.show', $row['siswa_id']) }}" class="font-semibold text-[#555783] hover:underline">
                                    Preview Rapor
                                </a>
                                <a href="{{ route('kepala.persetujuan.cetak', $row['siswa_id']) }}" target="_blank" class="text-gray-500 hover:text-gray-800 underline text-[11px]">
                                    Unduh PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400">
                                Tidak ada data arsip yang cocok dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :current="1" :total="1" :totalItems="count($arsip)" :perPage="10" />
    </x-card>

</x-layouts.kepala-sekolah>
