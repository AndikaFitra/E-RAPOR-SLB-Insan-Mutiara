@props([
    'tag' => 'TAHUN AJARAN 2026/2027',
    'title' => 'Semester Ganjil, tersusun lebih terarah.',
    'description' => 'Pantau kesiapan rapor semester ini. Arsip semester terdahulu tetap tersimpan.',
    'buttonText' => 'Kelola data siswa',
    'buttonHref' => '#',
])

<div class="bg-[#555783] rounded-2xl p-7 text-white flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-[0_1px_3px_rgba(0,0,0,0.05)]">
    <div class="space-y-1.5 max-w-2xl">
        <div class="text-[11px] font-bold tracking-wider text-[#D5D7EC] uppercase">
            {{ $tag }}
        </div>
        <h2 class="text-[22px] font-bold tracking-tight text-white leading-snug">
            {{ $title }}
        </h2>
        <p class="text-[14px] text-white/90 leading-relaxed font-normal">
            {{ $description }}
        </p>
    </div>

    @if($buttonText)
        <div class="shrink-0">
            <a href="{{ $buttonHref }}"
               class="inline-flex items-center justify-center px-5 py-2.5 bg-white text-[#555783] hover:bg-gray-50 text-[14px] font-semibold rounded-lg shadow-sm transition-all duration-150">
                {{ $buttonText }}
            </a>
        </div>
    @endif
</div>

