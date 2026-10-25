<x-layouts.admin
    title="Admin · Edit Data Siswa"
    periode="2026/2027 · Semester Ganjil"
    status="Aktif"
    userName="Admin Sekolah">

    <!-- Breadcrumb & Back Link -->
    <div class="space-y-1 mb-6">
        <div class="text-[13px] font-normal text-gray-500">
            Admin / Data Siswa / Edit Siswa
        </div>
        <a href="{{ route('admin.siswa.index') }}"
           @click.prevent="$dispatch('open-unsaved-modal', { name: 'siswa', url: '{{ route('admin.siswa.index') }}' })"
           class="inline-flex items-center gap-1.5 text-[13px] font-medium text-[#555783] hover:underline cursor-pointer">
            <span>← Kembali ke Data Siswa</span>
        </a>
        <h1 class="text-[28px] font-bold text-[#1A1A2E] tracking-tight leading-snug pt-1">
            Edit Data Siswa
        </h1>
        <p class="text-[14px] text-gray-500 leading-normal">
            Perbarui identitas siswa dan jenis kekhususan. Tanda * berarti wajib diisi.
        </p>
    </div>

    <!-- Error Alert Banner -->
    @if($errors->any())
        <x-alert-banner
            type="error"
            title="Terdapat kesalahan pada isian form"
            description="Silakan periksa kembali kolom yang bertanda merah di bawah."
            class="mb-6"
        />
    @endif

    <form method="POST" action="{{ route('admin.siswa.update', $siswa->id) }}">
        @csrf
        @method('PUT')

        <!-- Section 1: Identitas Utama -->
        <x-card class="mb-6">
            <div class="mb-5">
                <h3 class="text-base font-bold text-[#1A1A2E]">Identitas utama</h3>
                <p class="text-xs text-gray-500 mt-0.5">Gunakan identitas sesuai dokumen resmi siswa.</p>
            </div>

            <div class="space-y-4 text-[13px]">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Nama lengkap *</label>
                        <input type="text" 
                               name="nama_lengkap" 
                               value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}" 
                               required
                               placeholder="Masukkan nama lengkap" 
                               class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white placeholder-gray-400 focus:outline-none focus:border-[#555783]" />
                    </div>
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">NISN *</label>
                        <input type="text" 
                               name="nisn" 
                               value="{{ old('nisn', $siswa->nisn) }}" 
                               required
                               placeholder="Masukkan NISN" 
                               class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white placeholder-gray-400 focus:outline-none focus:border-[#555783]" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Jenis kelamin *</label>
                        <select name="jenis_kelamin" required class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-700 bg-white focus:outline-none focus:border-[#555783]">
                            <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Jenis Kekhususan (3 Ketunaan) *</label>
                        <select name="kekhususan" required class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-700 bg-white focus:outline-none focus:border-[#555783]">
                            <option value="Tunarungu" {{ old('kekhususan', $siswa->kekhususan) === 'Tunarungu' ? 'selected' : '' }}>Tunarungu (Program: PKPBI)</option>
                            <option value="Tunagrahita" {{ old('kekhususan', $siswa->kekhususan) === 'Tunagrahita' ? 'selected' : '' }}>Tunagrahita (Program: Bina Diri)</option>
                            <option value="Autis" {{ old('kekhususan', $siswa->kekhususan) === 'Autis' ? 'selected' : '' }}>Autis (Program: Pengembangan Komunikasi & Interaksi)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Tempat lahir</label>
                        <input type="text" 
                               name="tempat_lahir" 
                               value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}" 
                               placeholder="Masukkan tempat lahir" 
                               class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white placeholder-gray-400 focus:outline-none focus:border-[#555783]" />
                    </div>
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Tanggal lahir</label>
                        <input type="date" 
                               name="tgl_lahir" 
                               value="{{ old('tgl_lahir', optional($siswa->tgl_lahir)->format('Y-m-d')) }}" 
                               class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white focus:outline-none focus:border-[#555783]" />
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer select-none pt-2">
                        <input type="checkbox" name="is_aktif" value="1" {{ old('is_aktif', $siswa->is_aktif) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-[#555783] focus:ring-[#555783]">
                        <span class="text-xs font-semibold text-gray-700">Status Siswa Aktif</span>
                    </label>
                </div>
            </div>
        </x-card>

        <!-- Section 2: Penempatan Kelas & Tingkat -->
        <x-card class="mb-6">
            <div class="mb-5">
                <h3 class="text-base font-bold text-[#1A1A2E]">Penempatan Kelas & Tingkat</h3>
                <p class="text-xs text-gray-500 mt-0.5">Penugasan siswa pada kelas multi-grade untuk tahun ajaran aktif.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-[13px]">
                <div>
                    <label class="block font-semibold text-[#1A1A2E] mb-1.5">Kelas</label>
                    <select name="kelas_id" class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-700 bg-white focus:outline-none focus:border-[#555783]">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id', optional($penugasan)->kelas_id) == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }} ({{ $k->kekhususan }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-[#1A1A2E] mb-1.5">Tingkat</label>
                    <select name="tingkat" class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-700 bg-white focus:outline-none focus:border-[#555783]">
                        @foreach(['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'] as $t)
                            <option value="{{ $t }}" {{ old('tingkat', optional($penugasan)->tingkat ?? 'VII') == $t ? 'selected' : '' }}>
                                Tingkat {{ $t }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-[#1A1A2E] mb-1.5">Fase</label>
                    <select name="fase" class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-700 bg-white focus:outline-none focus:border-[#555783]">
                        @foreach(['A', 'B', 'C', 'D', 'E', 'F'] as $f)
                            <option value="{{ $f }}" {{ old('fase', optional($penugasan)->fase ?? 'D') == $f ? 'selected' : '' }}>
                                Fase {{ $f }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-card>

        <!-- Form Action Buttons -->
        <div class="flex items-center justify-end gap-3 mb-10">
            <a href="{{ route('admin.siswa.index') }}" 
               class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 rounded-xl bg-[#555783] hover:bg-[#46486E] text-white text-xs font-semibold transition-colors shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>

    <x-modal.unsaved-changes />

</x-layouts.admin>

