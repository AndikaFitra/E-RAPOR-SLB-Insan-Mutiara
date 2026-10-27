<x-layouts.admin
    title="Admin · Detail Siswa"
    periode="2026/2027 · Semester Ganjil"
    status="Aktif"
    userName="Admin Sekolah">

    <!-- Breadcrumb & Back Link -->
    <div class="space-y-1 mb-6">
        <div class="text-[13px] font-normal text-gray-500">
            Admin / Data Siswa / Detail Siswa
        </div>
        <a href="{{ route('admin.siswa.index') }}" class="inline-flex items-center gap-1.5 text-[13px] font-medium text-[#555783] hover:underline">
            <span>← Kembali ke Data Siswa</span>
        </a>
        <h1 class="text-[28px] font-bold text-[#1A1A2E] tracking-tight leading-snug pt-1">
            Detail Siswa
        </h1>
        <p class="text-[14px] text-gray-500 leading-normal">
            Alya Putri · NIS 26001 · NISN 0160001001
        </p>
    </div>

    <!-- Identitas dan Penempatan -->
    <x-card class="mb-6">
        <h3 class="text-base font-bold text-[#1A1A2E] mb-5">Identitas dan penempatan</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-[13px]">
            <div>
                <label class="block text-gray-500 mb-1 font-normal">Nama lengkap</label>
                <div class="px-3.5 py-2.5 bg-gray-50/60 border border-[#E8E7ED] rounded-xl font-semibold text-[#1A1A2E]">
                    Alya Putri
                </div>
            </div>
            <div>
                <label class="block text-gray-500 mb-1 font-normal">Status siswa</label>
                <div class="px-3.5 py-2.5 bg-gray-50/60 border border-[#E8E7ED] rounded-xl font-semibold text-[#16A34A]">
                    Aktif
                </div>
            </div>
            <div>
                <label class="block text-gray-500 mb-1 font-normal">Kelas saat ini</label>
                <div class="px-3.5 py-2.5 bg-gray-50/60 border border-[#E8E7ED] rounded-xl font-medium text-gray-800">
                    Tunarungu A · 2026/2027
                </div>
            </div>
            <div>
                <label class="block text-gray-500 mb-1 font-normal">Jenjang / Tingkat / Fase</label>
                <div class="px-3.5 py-2.5 bg-gray-50/60 border border-[#E8E7ED] rounded-xl font-medium text-gray-800">
                    SMPLB / VII / D
                </div>
            </div>
            <div>
                <label class="block text-gray-500 mb-1 font-normal">Jenis kekhususan</label>
                <div class="px-3.5 py-2.5 bg-gray-50/60 border border-[#E8E7ED] rounded-xl font-medium text-gray-800">
                    Tunarungu
                </div>
            </div>
            <div>
                <label class="block text-gray-500 mb-1 font-normal">Orang tua terkait</label>
                <div class="px-3.5 py-2.5 bg-gray-50/60 border border-[#E8E7ED] rounded-xl font-medium text-gray-800">
                    Siti Aminah · Ibu
                </div>
            </div>
        </div>

        <div class="pt-5 mt-5 border-t border-gray-100">
            <button type="button" class="px-4 py-2 border border-[#D5D7E2] bg-white hover:bg-gray-50 text-[13px] font-semibold text-gray-700 rounded-lg transition-colors cursor-pointer">
                Edit identitas siswa
            </button>
        </div>
    </x-card>

    <!-- Arsip Akademik Lintas Tahun -->
    <x-card class="mb-6">
        <div class="mb-4">
            <h3 class="text-base font-bold text-[#1A1A2E]">Arsip akademik lintas tahun</h3>
            <p class="text-xs text-gray-500 mt-1">Kelas dan tingkat pada arsip tidak berubah saat siswa berpindah kelas.</p>
            <p class="text-xs font-semibold text-[#16A34A] mt-2">
                Perkembangan: mulai membaca kalimat pendek dengan lebih mandiri.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F7F6F2] text-[#1A1A2E] text-[13px] font-semibold">
                        <th class="py-3 px-4 rounded-l-lg font-semibold">Tahun · Semester</th>
                        <th class="py-3 px-4 font-semibold">Kelas pada periode</th>
                        <th class="py-3 px-4 font-semibold">Tingkat / Fase</th>
                        <th class="py-3 px-4 font-semibold">Rata-rata</th>
                        <th class="py-3 px-4 font-semibold">Status rapor</th>
                        <th class="py-3 px-4 rounded-r-lg font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-[13px]">
                    <tr class="hover:bg-gray-50/40 transition-colors">
                        <td class="py-4.5 px-4 font-semibold text-[#1A1A2E] whitespace-nowrap">2026/2027 · Ganjil</td>
                        <td class="py-4.5 px-4 text-gray-700 whitespace-nowrap">Tunarungu A</td>
                        <td class="py-4.5 px-4 text-gray-600 whitespace-nowrap">VII / D</td>
                        <td class="py-4.5 px-4 text-gray-800 font-semibold whitespace-nowrap">84,0</td>
                        <td class="py-4.5 px-4 font-medium text-[#1A1A2E] whitespace-nowrap">Diajukan</td>
                        <td class="py-4.5 px-4 whitespace-nowrap text-gray-500">
                            <a href="#" class="text-[#555783] hover:underline font-medium">Lihat semester</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50/40 transition-colors">
                        <td class="py-4.5 px-4 font-semibold text-[#1A1A2E] whitespace-nowrap">2025/2026 · Genap</td>
                        <td class="py-4.5 px-4 text-gray-700 whitespace-nowrap">Tunarungu SD A</td>
                        <td class="py-4.5 px-4 text-gray-600 whitespace-nowrap">VI / C</td>
                        <td class="py-4.5 px-4 text-gray-800 font-semibold whitespace-nowrap">82,0</td>
                        <td class="py-4.5 px-4 font-medium text-[#16A34A] whitespace-nowrap">Disetujui</td>
                        <td class="py-4.5 px-4 whitespace-nowrap text-gray-500">
                            <a href="#" class="text-[#555783] hover:underline font-medium">Lihat / Unduh PDF</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50/40 transition-colors">
                        <td class="py-4.5 px-4 font-semibold text-[#1A1A2E] whitespace-nowrap">2025/2026 · Ganjil</td>
                        <td class="py-4.5 px-4 text-gray-700 whitespace-nowrap">Tunarungu SD A</td>
                        <td class="py-4.5 px-4 text-gray-600 whitespace-nowrap">VI · C</td>
                        <td class="py-4.5 px-4 text-gray-800 font-semibold whitespace-nowrap">79,5</td>
                        <td class="py-4.5 px-4 font-medium text-[#16A34A] whitespace-nowrap">Disetujui</td>
                        <td class="py-4.5 px-4 whitespace-nowrap text-gray-500">
                            <a href="#" class="text-[#555783] hover:underline font-medium">Lihat / Unduh PDF</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50/40 transition-colors">
                        <td class="py-4.5 px-4 font-semibold text-[#1A1A2E] whitespace-nowrap">2024/2025 · Genap</td>
                        <td class="py-4.5 px-4 text-gray-700 whitespace-nowrap">Tunarungu SD A</td>
                        <td class="py-4.5 px-4 text-gray-600 whitespace-nowrap">V · C</td>
                        <td class="py-4.5 px-4 text-gray-800 font-semibold whitespace-nowrap">77,0</td>
                        <td class="py-4.5 px-4 font-medium text-[#16A34A] whitespace-nowrap">Disetujui</td>
                        <td class="py-4.5 px-4 whitespace-nowrap text-gray-500">
                            <a href="#" class="text-[#555783] hover:underline font-medium">Lihat / Unduh PDF</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <x-pagination :total="4" :count="4" :perPage="10" />
    </x-card>

</x-layouts.admin>

