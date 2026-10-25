@props([
    'id' => 'modal-unsaved-changes',
    'itemName' => 'Alya Putri',
    'leaveUrl' => '#',
])

<div
    x-data="{ open: false, itemName: '{{ $itemName }}', leaveUrl: '{{ $leaveUrl }}' }"
    x-show="open"
    @open-unsaved-modal.window="open = true; if($event.detail.name) itemName = $event.detail.name; if($event.detail.url) leaveUrl = $event.detail.url"
    @keydown.escape.window="open = false"
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
        class="fixed inset-0 bg-[#1A1A2E]/40 backdrop-blur-[2px] transition-opacity"
        @click="open = false"
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
            <!-- Warning Icon -->
            <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 mb-4">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <!-- Title -->
            <h3 class="text-xl font-bold text-[#1A1A2E] tracking-tight mb-3">
                Perubahan belum disimpan
            </h3>

            <!-- Description -->
            <p class="text-xs sm:text-sm text-gray-500 leading-relaxed mb-6">
                Perubahan pada data <span class="font-semibold text-gray-700" x-text="itemName"></span> belum disimpan. Jika Anda meninggalkan halaman ini, perubahan yang belum disimpan akan hilang.
            </p>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3">
                <button
                    type="button"
                    @click="open = false"
                    class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors"
                >
                    Tetap mengedit
                </button>
                <a
                    :href="leaveUrl"
                    class="px-5 py-2.5 rounded-xl bg-[#555783] hover:bg-[#45476D] text-white text-sm font-medium transition-colors shadow-sm inline-block"
                >
                    Tinggalkan perubahan
                </a>
            </div>
        </div>
    </div>
</div>

