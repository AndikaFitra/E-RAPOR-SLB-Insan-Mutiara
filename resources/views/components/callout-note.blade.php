@props([
    'title' => '',
    'subtitle' => '',
    'type' => 'neutral', // neutral, warning
])

@php
    $bgClass = $type === 'warning'
        ? 'bg-[#FFFBEB] border-[#FDE68A] text-[#92400E]'
        : 'bg-[#F9F9F6] border-[#E8E7ED] text-[#1A1A2E]';
    $iconColor = $type === 'warning' ? 'text-amber-500' : 'text-gray-500';
@endphp

<div class="rounded-2xl border p-5 flex items-start gap-3.5 {{ $bgClass }}">
    <div class="shrink-0 mt-0.5 {{ $iconColor }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="9"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>
        </svg>
    </div>
    <div class="space-y-0.5">
        @if($title)
            <h5 class="text-[14px] font-bold tracking-tight">{{ $title }}</h5>
        @endif
        @if($subtitle)
            <p class="text-[13px] {{ $type === 'warning' ? 'text-amber-800/90' : 'text-gray-600' }} leading-relaxed">
                {{ $subtitle }}
            </p>
        @endif
        {{ $slot }}
    </div>
</div>

