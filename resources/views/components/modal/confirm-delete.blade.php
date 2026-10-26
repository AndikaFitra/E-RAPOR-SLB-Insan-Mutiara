@props([
    'id' => 'modal-confirm-delete',
    'title' => 'Hapus data siswa?',
    'itemName' => 'Alya Putri',
    'itemDetails' => 'NIS 26001 · NISN 0160001001 · Tunarungu A',
    'confirmAction' => '#',
    'confirmMethod' => 'DELETE',
    'deactivate' => false,
])

<div
    x-data="{ 
        open: false, 
        confirmed: false,
        itemName: '{{ $itemName }}', 
        itemDetails: '{{ $itemDetails }}', 
        actionUrl: '{{ $confirmAction }}' 
    }"
    x-show="open"
    @open-delete-modal.window="
        open = true; 
        confirmed = false;
        if($event.detail.name) itemName = $event.detail.name; 
        if($event.detail.details) itemDetails = $event.detail.details; 
        if($event.detail.action) actionUrl = $event.detail.action;
    "
    @keydown.escape.window="open = false; confirmed = false;"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <!-- Backdrop -->
    <div
        x-show="open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-[#1A1A2E]/50 backdrop-blur-[2px] transition-opacity"
        @click="open = false; confirmed = false;"
    ></div>

    <!-- Dialog Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div
            x-show="open"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 sm:p-7 text-left shadow-2xl transition-all border border-[#E8E7ED]"
        >
            <!-- Trash Icon Header -->
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div class="text-[11px] font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full bg-rose-100 text-rose-700">
                    Verifikasi 2 Langkah
                </div>
            </div>

            <!-- Title -->
            <h3 class="text-xl font-bold text-[#1A1A2E] tracking-tight mb-2">
                {{ $title }}
            </h3>

            <!-- Item Target Info Card -->
            <div class="p-3.5 rounded-xl bg-[#F7F6F2] border border-[#EAE8E2] mb-3 space-y-1">
                <p class="font-bold text-sm text-[#1A1A2E]" x-text="itemName"></p>
                <p class="text-xs text-gray-500 font-normal" x-text="itemDetails"></p>
            </div>

            <!-- Description -->
            <p class="text-xs text-gray-500 leading-relaxed mb-4">
                Tindakan ini {{ $deactivate ? 'menonaktifkan siswa' : 'menghapus data yang dipilih' }}, yaitu <span class="font-semibold text-gray-700" x-text="itemName"></span>. {{ $deactivate ? 'Riwayat siswa dan rapor tetap tersimpan.' : 'Data lain tidak terpengaruh.' }} Pastikan data yang dipilih sudah benar sebelum melanjutkan.
            </p>

            <!-- 2-Step Verification Checkbox Box (Proteksi Anti Asal-Pencet) -->
            <div class="p-3.5 rounded-xl bg-[#FFF5F5] border border-[#FECACA] mb-5 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-rose-800 uppercase tracking-wide">
                        Langkah 2: Konfirmasi Persetujuan
                    </span>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md"
                          :class="confirmed ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                          x-text="confirmed ? '✓ Terverifikasi' : 'Wajib Centang'">
                    </span>
                </div>
                
                <label class="flex items-start gap-2.5 cursor-pointer select-none">
                    <input 
                        type="checkbox" 
                        x-model="confirmed"
                        class="w-4 h-4 mt-0.5 rounded border-rose-300 text-rose-600 focus:ring-rose-500 cursor-pointer"
                    >
                    <span class="text-xs text-rose-900 leading-snug">
                        Saya memahami dan menyetujui bahwa data <strong x-text="itemName"></strong> {{ $deactivate ? 'akan dinonaktifkan dan riwayatnya tetap tersimpan.' : 'akan dihapus dari sistem.' }}
                    </span>
                </label>
            </div>

            <!-- Actions -->
            <form :action="actionUrl" method="POST" class="flex items-center justify-between gap-3 pt-1">
                @csrf
                <input type="hidden" name="_method" value="{{ $confirmMethod }}">

                <button
                    type="button"
                    @click="open = false; confirmed = false;"
                    class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50 transition-colors"
                >
                    Batal
                </button>

                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        :disabled="!confirmed"
                        class="px-5 py-2.5 rounded-xl text-xs font-semibold transition-all shadow-sm flex items-center gap-1.5"
                        :class="confirmed 
                            ? 'bg-rose-600 hover:bg-rose-700 text-white cursor-pointer active:scale-[0.98]' 
                            : 'bg-gray-200 text-gray-400 cursor-not-allowed opacity-60'"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>{{ $deactivate ? 'Nonaktifkan siswa' : 'Hapus data' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
