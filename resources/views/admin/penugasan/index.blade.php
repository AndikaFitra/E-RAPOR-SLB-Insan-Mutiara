<x-layouts.admin
    title="Admin · Penugasan Siswa & Wali Kelas"
    periode="2026/2027 · Semester Ganjil"
    status="Aktif"
    userName="Admin Sekolah">

    <!-- Flash Alert -->
    @if(session('success'))
        <div class="mb-6">
            <x-alert-banner type="success" title="Operasi Berhasil" :message="session('success')" />
        </div>
    @endif

    <form method="POST" action="{{ route('admin.penugasan.store') }}">
        @csrf

        <!-- Breadcrumb & Back Link -->
        <div class="space-y-1 mb-6">
            <div class="text-[13px] font-normal text-gray-500">
                Admin / Data Kelas / Penugasan Siswa & Wali Kelas
            </div>
            <a href="{{ route('admin.kelas.index') }}" class="inline-flex items-center gap-1.5 text-[13px] font-medium text-[#555783] hover:underline">
                <span>← Kembali ke Data Kelas</span>
            </a>
            <h1 class="text-[28px] font-bold text-[#1A1A2E] tracking-tight leading-snug pt-1">
                Penugasan Siswa & Wali Kelas
            </h1>
            <p class="text-[14px] text-gray-500 leading-normal">
                Atur wali kelas dan penempatan rombongan belajar berbasis 3 ketunaan SLB Insan Mutiara.
            </p>
        </div>

        <!-- Form: Wali Kelas dan Periode -->
        <x-card title="Wali kelas dan periode rombel" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                <div>
                    <label class="block font-semibold text-[#1A1A2E] mb-2">Pilih Kelas Sasaran *</label>
                    <select name="kelas_id" required class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-700 bg-white focus:outline-none focus:border-[#555783]">
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}">
                                {{ $k->nama }} (Kekhususan: {{ $k->kekhususan }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-[#1A1A2E] mb-2">Wali Kelas yang Ditugaskan *</label>
                    <select name="guru_id" required class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-700 bg-white focus:outline-none focus:border-[#555783]">
                        @foreach($guruList as $g)
                            <option value="{{ $g->id }}">
                                {{ $g->nama_lengkap }} ({{ $g->jabatan ?: 'Guru' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-card>

        <!-- Student Selection Section -->
        <x-card class="mb-6">
            <div class="mb-4">
                <h3 class="text-base font-bold text-[#1A1A2E]">Pilih siswa untuk ditugaskan</h3>
                <p class="text-xs text-gray-500 mt-1">
                    Centang siswa yang akan ditempatkan pada kelas rombel yang dipilih. Penugasan mengacu pada tahun ajaran aktif.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#F7F6F2] text-[#1A1A2E] font-semibold">
                            <th class="py-3 px-4 rounded-l-lg font-semibold w-1/3">Nama Siswa</th>
                            <th class="py-3 px-4 font-semibold">NISN</th>
                            <th class="py-3 px-4 font-semibold">Tingkat / Fase</th>
                            <th class="py-3 px-4 rounded-r-lg font-semibold">Kelas saat ini</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($siswaPenugasan as $item)
                            <tr class="hover:bg-gray-50/40 transition-colors">
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <label class="inline-flex items-center gap-3 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            name="siswa[]"
                                            value="{{ $item['id'] }}"
                                            {{ $item['selected'] ? 'checked' : '' }}
                                            class="w-4 h-4 rounded text-[#555783] border-[#D5D7E2] focus:ring-[#555783]"
                                        />
                                        <span class="font-semibold text-[#1A1A2E]">{{ $item['nama'] }}</span>
                                    </label>
                                </td>
                                <td class="py-4 px-4 text-gray-600 whitespace-nowrap">{{ $item['nisn'] }}</td>
                                <td class="py-4 px-4 text-gray-600 whitespace-nowrap">{{ $item['tingkat_fase'] }}</td>
                                <td class="py-4 px-4 whitespace-nowrap font-medium {{ $item['kelas_saat_ini'] === 'Belum ditugaskan' ? 'text-amber-700' : 'text-[#555783]' }}">
                                    {{ $item['kelas_saat_ini'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- Bottom Actions Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2 pb-6">
            <p class="text-[13px] text-gray-500">
                Periksa kembali data sebelum menyimpan penugasan.
            </p>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.kelas.index') }}"
                   class="px-5 py-2.5 border border-[#D5D7E2] bg-white hover:bg-gray-50 text-[13px] font-semibold text-gray-700 rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-[#555783] hover:bg-[#47496E] text-[13px] font-semibold text-white rounded-xl shadow-sm transition-colors cursor-pointer">
                    Simpan penugasan
                </button>
            </div>
        </div>
    </form>

</x-layouts.admin>
