<x-layouts.admin
    title="Admin · Tahun Ajaran"
    periode="2026/2027 · Semester Ganjil"
    status="Aktif"
    userName="Admin Sekolah">

    <!-- Flash Alert -->
    @if(session('success'))
        <div class="mb-6">
            <x-alert-banner type="success" title="Operasi Berhasil" :message="session('success')" />
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div x-data="{ showCreateModal: false }">

        <!-- Page Header -->
        <x-page-header
            breadcrumb="Admin / Tahun Ajaran"
            title="Tahun Ajaran & Semester"
            subtitle="Kelola periode aktif tanpa kehilangan data dan riwayat rapor semester terdahulu.">
            <x-slot:action>
                <button
                    type="button"
                    @click="showCreateModal = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#555783] hover:bg-[#47496E] text-white text-[14px] font-semibold rounded-xl shadow-sm transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    <span>Tambah Tahun Ajaran</span>
                </button>
            </x-slot:action>
        </x-page-header>

        <!-- Table of Tahun Ajaran -->
        <x-card class="mb-6">
            <div class="overflow-x-auto">
                <table class="dashboard-table dashboard-table--medium text-left">
                    <thead>
                        <tr class="bg-[#F7F6F2] text-[#1A1A2E] text-[13px] font-semibold">
                            <th class="py-3 px-4 rounded-l-lg font-semibold">Tahun ajaran</th>
                            <th class="py-3 px-4 font-semibold">Semester</th>
                            <th class="py-3 px-4 font-semibold">Tanggal mulai</th>
                            <th class="py-3 px-4 font-semibold">Tanggal selesai</th>
                            <th class="py-3 px-4 font-semibold">Status</th>
                            <th class="py-3 px-4 font-semibold">Data terkait</th>
                            <th class="py-3 px-4 rounded-r-lg font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-[13px]">
                        @foreach($tahunAjaranList as $t)
                            <tr class="hover:bg-gray-50/40 transition-colors">
                                <td class="py-4.5 px-4 font-semibold text-[#1A1A2E] whitespace-nowrap">
                                    {{ $t['tahun'] }}
                                </td>
                                <td class="py-4.5 px-4 text-gray-700 whitespace-nowrap">
                                    {{ $t['semester'] }}
                                </td>
                                <td class="py-4.5 px-4 text-gray-600 whitespace-nowrap">
                                    {{ $t['tgl_mulai'] }}
                                </td>
                                <td class="py-4.5 px-4 text-gray-600 whitespace-nowrap">
                                    {{ $t['tgl_selesai'] }}
                                </td>
                                <td class="py-4.5 px-4 whitespace-nowrap font-medium {{ $t['status'] === 'Aktif' ? 'text-[#16A34A]' : 'text-gray-500' }}">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $t['status'] === 'Aktif' ? 'bg-emerald-50 text-[#16A34A] border border-emerald-200' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $t['status'] }}
                                    </span>
                                </td>
                                <td class="py-4.5 px-4 whitespace-nowrap text-gray-600">
                                    {{ $t['data_terkait'] }}
                                </td>
                                <td class="py-4.5 px-4 whitespace-nowrap text-right text-gray-500">
                                    <div class="flex items-center justify-end gap-2 text-[13px]">
                                        @if($t['status'] !== 'Aktif')
                                            <form method="POST" action="{{ route('admin.tahun-ajaran.aktifkan', $t['id']) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 rounded-xl bg-[#555783] hover:bg-[#46486E] text-white text-xs font-semibold transition-colors cursor-pointer">
                                                    Aktifkan
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs font-bold text-emerald-700">Periode Berjalan</span>
                                        @endif
                                        <span class="text-gray-300">·</span>
                                        <a href="{{ route('admin.arsip.index') }}" class="text-[#555783] hover:underline font-medium transition-colors text-xs">Arsip</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-pagination :total="count($tahunAjaranList)" :count="count($tahunAjaranList)" :perPage="10" />
        </x-card>

        <!-- Bottom Callout Note (Warning Style) -->
        <x-callout-note
            type="warning"
            title="Periode yang sudah digunakan tidak dapat dihapus sembarangan"
            subtitle="Kelas, nilai, dan rapor siswa mengacu pada periode aktif. Mengaktifkan semester baru akan mengunci periode sebelumnya ke arsip secara aman." />

        <!-- Modal Tambah Tahun Ajaran -->
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
                    <h3 class="text-base font-bold text-[#1A1A2E]">Tambah Tahun Ajaran</h3>
                    <button type="button" @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.tahun-ajaran.store') }}" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Tahun Ajaran (Format: YYYY/YYYY) *</label>
                        <input type="text" name="tahun" required placeholder="contoh: 2027/2028" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]" />
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Semester *</label>
                        <select name="semester" required class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]">
                            <option value="Ganjil">Semester Ganjil</option>
                            <option value="Genap">Semester Genap</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2.5 rounded-xl border border-gray-200 font-semibold text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#555783] hover:bg-[#46486E] text-white font-bold shadow-xs transition-colors">
                            Simpan Periode
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</x-layouts.admin>
