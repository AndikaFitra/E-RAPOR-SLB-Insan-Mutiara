<x-layouts.admin
    title="Admin · Edit Data Guru"
    periode="2026/2027 · Semester Ganjil"
    status="Aktif"
    userName="Admin Sekolah">

    <!-- Breadcrumb & Back Link -->
    <div class="space-y-1 mb-6">
        <div class="text-[13px] font-normal text-gray-500">
            Admin / Data Guru / Edit Guru
        </div>
        <a href="{{ route('admin.guru.index') }}" class="inline-flex items-center gap-1.5 text-[13px] font-medium text-[#555783] hover:underline">
            <span>← Kembali ke Data Guru</span>
        </a>
        <h1 class="text-[28px] font-bold text-[#1A1A2E] tracking-tight leading-snug pt-1">
            Edit Data Guru
        </h1>
        <p class="text-[14px] text-gray-500 leading-normal">
            Perbarui data identitas, dukungan kekhususan (3 ketunaan), dan status keaktifan guru.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            <p class="font-bold mb-1">Periksa isian form berikut:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.guru.update', $guru->id) }}">
        @csrf
        @method('PUT')

        <!-- Identitas Guru -->
        <x-card class="mb-6">
            <h3 class="text-base font-bold text-[#1A1A2E] mb-5">Identitas guru</h3>

            <div class="space-y-4 text-[13px]">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Nama lengkap & Gelar *</label>
                        <input
                            type="text"
                            name="nama_lengkap"
                            value="{{ old('nama_lengkap', $guru->nama_lengkap) }}"
                            required
                            class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">NIP</label>
                        <input
                            type="text"
                            name="nip"
                            value="{{ old('nip', $guru->nip) }}"
                            placeholder="Masukkan NIP atau biarkan kosong"
                            class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Jenis Kelamin *</label>
                        <select
                            name="jenis_kelamin"
                            required
                            class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-700 bg-white focus:outline-none focus:border-[#555783]"
                        >
                            <option value="L" {{ old('jenis_kelamin', $guru->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $guru->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Jenis Kekhususan yang Didukung (3 Ketunaan) *</label>
                        <select
                            name="kekhususan"
                            required
                            class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-700 bg-white focus:outline-none focus:border-[#555783]"
                        >
                            <option value="Tunarungu" {{ old('kekhususan', $guru->jabatan) === 'Tunarungu' ? 'selected' : '' }}>Tunarungu (Program PKPBI)</option>
                            <option value="Tunagrahita" {{ old('kekhususan', $guru->jabatan) === 'Tunagrahita' ? 'selected' : '' }}>Tunagrahita (Program Bina Diri)</option>
                            <option value="Autis" {{ old('kekhususan', $guru->jabatan) === 'Autis' ? 'selected' : '' }}>Autis (Program Komunikasi & Interaksi)</option>
                            <option value="Tunarungu, Tunagrahita" {{ old('kekhususan', $guru->jabatan) === 'Tunarungu, Tunagrahita' ? 'selected' : '' }}>Tunarungu & Tunagrahita</option>
                            <option value="Tunarungu, Tunagrahita, Autis" {{ old('kekhususan', $guru->jabatan) === 'Tunarungu, Tunagrahita, Autis' ? 'selected' : '' }}>Semua (Tunarungu, Tunagrahita, Autis)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Nomor WhatsApp *</label>
                        <input
                            type="text"
                            name="no_whatsapp"
                            value="{{ old('no_whatsapp', $guru->no_whatsapp) }}"
                            required
                            class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Status Guru *</label>
                        @php
                            $currentStatus = optional($guru->user)->is_aktif ?? true ? 'Aktif' : 'Tidak aktif';
                        @endphp
                        <select
                            name="status"
                            required
                            class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-700 bg-white focus:outline-none focus:border-[#555783]"
                        >
                            <option value="Aktif" {{ old('status', $currentStatus) === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Tidak aktif" {{ old('status', $currentStatus) === 'Tidak aktif' ? 'selected' : '' }}>Tidak aktif</option>
                        </select>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Bottom Actions Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2 pb-6">
            <p class="text-[13px] text-gray-500">
                Perubahan data akan langsung tersimpan di seluruh modul sistem.
            </p>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.guru.index') }}"
                   class="px-5 py-2.5 border border-[#D5D7E2] bg-white hover:bg-gray-50 text-[13px] font-semibold text-gray-700 rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-[#555783] hover:bg-[#47496E] text-[13px] font-semibold text-white rounded-xl shadow-sm transition-colors cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

</x-layouts.admin>

