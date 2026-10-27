<x-layouts.admin
    title="Admin · Arsip Rapor"
    periode="2026/2027 · Semester Ganjil"
    status="Aktif"
    userName="Admin Sekolah">

    <!-- Page Header -->
    <x-page-header
        breadcrumb="Admin / Arsip"
        title="Arsip Rapor & Leger Historis"
        subtitle="Dokumentasi hasil belajar dan buku induk siswa semester terdahulu yang terkunci permanen.">
    </x-page-header>

    <!-- Filter Card -->
    <x-card class="p-5 mb-6 space-y-4">
        <form method="GET" action="{{ route('admin.arsip.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="relative flex-1 w-full">
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
                    placeholder="Cari kelas, wali kelas, atau tahun..."
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-[#1A1A2E] placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                />
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <select name="tahun_ajaran_id" onchange="this.form.submit()" class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option value="">Tahun Ajaran: Semua</option>
                    @foreach($tahunList as $th)
                        <option value="{{ $th->id }}" {{ (string)request('tahun_ajaran_id') === (string)$th->id ? 'selected' : '' }}>
                            {{ $th->tahun }} - {{ $th->semester }}
                        </option>
                    @endforeach
                </select>

                <select name="kelas_id" onchange="this.form.submit()" class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option value="">Kelas: Semua</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ (string)request('kelas_id') === (string)$k->id ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#555783] text-white text-xs font-semibold hover:bg-[#46486E] transition-colors shadow-xs">
                    Cari
                </button>

                @if(request('search') || request('tahun_ajaran_id') || request('kelas_id'))
                    <a href="{{ route('admin.arsip.index') }}" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 bg-white hover:bg-[#F7F6F2] transition-colors flex items-center gap-2">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Table of Archive -->
    <x-card class="mb-6 p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="dashboard-table dashboard-table--wide text-left">
                <thead>
                    <tr class="bg-[#F7F6F2] text-[#1A1A2E] font-semibold border-b border-[#E8E7ED]">
                        <th class="py-3.5 px-6">Tahun ajaran</th>
                        <th class="py-3.5 px-4">Semester</th>
                        <th class="py-3.5 px-4">Kelas rombel</th>
                        <th class="py-3.5 px-4">Wali kelas</th>
                        <th class="py-3.5 px-4">Total siswa</th>
                        <th class="py-3.5 px-4">Rapor Tersedia</th>
                        <th class="py-3.5 px-4">Status arsip</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($arsipList as $ar)
                        <tr class="hover:bg-[#FBFBFA] transition-colors">
                            <td class="py-4 px-6 font-bold text-[#1A1A2E]">
                                {{ $ar['tahun'] }}
                            </td>
                            <td class="py-4 px-4 text-gray-700">
                                {{ $ar['semester'] }}
                            </td>
                            <td class="py-4 px-4 font-semibold text-[#555783]">
                                {{ $ar['kelas'] }}
                                <span class="block text-[11px] font-normal text-gray-400">({{ $ar['kekhususan'] }})</span>
                            </td>
                            <td class="py-4 px-4 text-gray-600">
                                {{ $ar['wali_kelas'] }}
                            </td>
                            <td class="py-4 px-4 text-gray-700 font-medium">
                                {{ $ar['total_siswa'] }} siswa
                            </td>
                            <td class="py-4 px-4 text-gray-700 font-medium">
                                {{ $ar['rapor_count'] }} rapor
                            </td>
                            <td class="py-4 px-4 font-semibold">
                                <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-semibold {{ $ar['is_aktif'] ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $ar['status'] }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('admin.siswa.index') }}" class="font-semibold text-[#555783] hover:underline">
                                    Buku Induk
                                </a>
                                <span class="text-gray-300">·</span>
                                <a href="{{ route('admin.kelas.index') }}" class="text-gray-600 hover:text-gray-900 font-medium hover:underline">
                                    Detail Rombel
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400">
                                Tidak ada data arsip rombel yang cocok dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :current="1" :total="1" :totalItems="count($arsipList)" :perPage="10" />
    </x-card>

    <x-callout-note
        title="Histori Rapor Dilindungi Kebijakan Arsip Permanen"
        description="Data nilai dan catatan deskriptif capaian pada periode arsip dilindungi untuk menjamin keabsahan dokumen hasil belajar resmi SLB Insan Mutiara." />

</x-layouts.admin>
