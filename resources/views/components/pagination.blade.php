@props([
    'total' => 8,
    'current' => 1,
    'perPage' => 10,
    'count' => 8,
])

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-gray-100 text-[13px] text-gray-500">
    <div>
        Menampilkan 1–{{ $count }} dari {{ $total }} data • {{ $perPage }} per halaman
    </div>

    <div class="flex items-center gap-1.5 select-none">
        <button type="button" class="px-3.5 py-1.5 border border-[#D5D7E2] rounded-lg text-gray-600 hover:bg-gray-50 text-[13px] font-medium transition-colors cursor-pointer">
            Sebelumnya
        </button>

        <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#555783] text-white text-[13px] font-semibold shadow-sm">
            1
        </button>

        <button type="button" class="px-3.5 py-1.5 border border-[#D5D7E2] rounded-lg text-gray-600 hover:bg-gray-50 text-[13px] font-medium transition-colors cursor-pointer">
            Berikutnya
        </button>
    </div>
</div>

