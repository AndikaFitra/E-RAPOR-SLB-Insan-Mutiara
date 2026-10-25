<x-layouts.admin
    title="Admin · Data Sekolah"
    periode="2026/2027 · Semester Ganjil"
    status="Aktif"
    userName="Admin Sekolah">

    <!-- Flash Alert -->
    @if(session('success'))
        <div class="mb-6">
            <x-alert-banner type="success" title="Operasi Berhasil" :message="session('success')" />
        </div>
    @endif

    <form method="POST" action="{{ route('admin.sekolah.update') }}">
        @csrf
        @method('PUT')

        <!-- Page Header -->
        <x-page-header
            breadcrumb="Admin / Data Sekolah"
            title="Data Sekolah & Lembaga"
            subtitle="Informasi resmi lembaga SLB Insan Mutiara yang tercetak pada cover, petunjuk, dan lembar identitas rapor.">
            <x-slot:action>
                <button type="submit"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#555783] hover:bg-[#47496E] text-white text-[14px] font-semibold rounded-xl shadow-sm transition-colors cursor-pointer">
                    <span>Simpan Perubahan</span>
                </button>
            </x-slot:action>
        </x-page-header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form (2 cols) -->
            <x-card class="lg:col-span-2">
                <h3 class="text-base font-bold text-[#1A1A2E] mb-5">Identitas Satuan Pendidikan</h3>
                
                <div class="space-y-4 text-[13px]">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-[#1A1A2E] mb-1.5">Nama Sekolah *</label>
                            <input
                                type="text"
                                name="nama"
                                value="{{ old('nama', $sekolah->nama) }}"
                                required
                                class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white focus:outline-none focus:border-[#555783]"
                            />
                        </div>
                        <div>
                            <label class="block font-semibold text-[#1A1A2E] mb-1.5">NPSN *</label>
                            <input
                                type="text"
                                name="npsn"
                                value="{{ old('npsn', $sekolah->npsn) }}"
                                required
                                class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white focus:outline-none focus:border-[#555783]"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-[#1A1A2E] mb-1.5">Nama Kepala Sekolah *</label>
                            <input
                                type="text"
                                name="kepala_sekolah"
                                value="{{ old('kepala_sekolah', $sekolah->kepala_sekolah) }}"
                                required
                                class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white focus:outline-none focus:border-[#555783]"
                            />
                        </div>
                        <div>
                            <label class="block font-semibold text-[#1A1A2E] mb-1.5">NIP Kepala Sekolah</label>
                            <input
                                type="text"
                                name="nip_kepala"
                                value="{{ old('nip_kepala', $sekolah->nip_kepala) }}"
                                class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white focus:outline-none focus:border-[#555783]"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Alamat Lembaga *</label>
                        <textarea
                            name="alamat"
                            rows="2"
                            required
                            class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white focus:outline-none focus:border-[#555783]"
                        >{{ old('alamat', $sekolah->alamat) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-[#1A1A2E] mb-1.5">Email Sekolah</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', $sekolah->email) }}"
                                class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white focus:outline-none focus:border-[#555783]"
                            />
                        </div>
                        <div>
                            <label class="block font-semibold text-[#1A1A2E] mb-1.5">Nomor Telepon</label>
                            <input
                                type="text"
                                name="telepon"
                                value="{{ old('telepon', $sekolah->telepon) }}"
                                class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white focus:outline-none focus:border-[#555783]"
                            />
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Logo Preview & Official Stamp -->
            <div class="space-y-6">
                <x-card>
                    <h3 class="text-base font-bold text-[#1A1A2E] mb-4">Logo Lembaga</h3>
                    <div class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-[#D5D7E2] rounded-2xl bg-gray-50/50">
                        <img src="{{ asset('images/logo-slb.png') }}" alt="Logo SLB" class="w-24 h-24 object-contain mb-3" />
                        <p class="text-xs font-semibold text-gray-700">Logo SLB Insan Mutiara</p>
                        <p class="text-[11px] text-gray-400 mt-1">PNG Transparan · Digunakan pada Cover Rapor</p>
                    </div>
                </x-card>

                <x-callout-note
                    title="Pencetakan Rapor Resmi"
                    subtitle="Data nama kepala sekolah (Novia Parenri, S.Si, M.Si) dan NIP akan otomatis tertera pada lembar pengesahan rapor seluruh siswa." />
            </div>
        </div>
    </form>

</x-layouts.admin>
