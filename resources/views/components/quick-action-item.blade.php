@props([
    'title' => '',
    'subtitle' => '',
    'href' => '#',
])

<a href="{{ $href }}" class="block py-3.5 first:pt-0 last:pb-0 group">
    <h4 class="text-[14px] font-bold text-[#1A1A2E] group-hover:text-[#555783] transition-colors leading-snug">
        {{ $title }}
    </h4>
    <p class="text-[13px] text-gray-500 mt-0.5 leading-normal">
        {{ $subtitle }}
    </p>
</a>

