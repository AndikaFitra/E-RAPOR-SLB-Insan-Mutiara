@props([
    'title' => '',
    'value' => '',
    'subtext' => '',
    'href' => null,
])

@php
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    class="block bg-white rounded-2xl border border-[#E8E7ED] p-5.5 shadow-[0_1px_2px_rgba(0,0,0,0.03)] hover:shadow-md hover:border-[#555783]/30 transition-all duration-150 {{ $href ? 'cursor-pointer group' : '' }}">
    <div class="flex items-center justify-between">
        <span class="text-[13px] font-normal text-gray-500 group-hover:text-[#555783] transition-colors">{{ $title }}</span>
        @if(isset($icon))
            <span class="w-5 h-5 text-gray-400 group-hover:text-[#555783] transition-colors flex items-center justify-center">
                {{ $icon }}
            </span>
        @endif
    </div>

    <div class="mt-3.5">
        <div class="text-[32px] font-bold text-[#1A1A2E] tracking-tight leading-none">{{ $value }}</div>
        @if($subtext)
            <div class="text-[13px] text-gray-400 mt-2.5 font-normal">{{ $subtext }}</div>
        @endif
    </div>
</{{ $tag }}>
