@props([
    'type' => 'success', // 'success', 'error', 'loading'
    'title' => '',
    'description' => '',
])

@php
    $config = match($type) {
        'success' => [
            'bg' => 'bg-[#ECFDF5]',
            'border' => 'border-[#A7F3D0]',
            'titleColor' => 'text-[#065F46]',
            'descColor' => 'text-[#047857]',
            'iconColor' => 'text-[#059669]',
        ],
        'error' => [
            'bg' => 'bg-[#FEF2F2]',
            'border' => 'border-[#FECACA]',
            'titleColor' => 'text-[#991B1B]',
            'descColor' => 'text-[#B91C1C]',
            'iconColor' => 'text-[#DC2626]',
        ],
        'loading' => [
            'bg' => 'bg-[#EEF2F8]',
            'border' => 'border-[#D9DDEB]',
            'titleColor' => 'text-[#3E4066]',
            'descColor' => 'text-[#555783]',
            'iconColor' => 'text-[#555783]',
        ],
        default => [
            'bg' => 'bg-[#F7F6F2]',
            'border' => 'border-[#E8E7ED]',
            'titleColor' => 'text-[#1A1A2E]',
            'descColor' => 'text-gray-600',
            'iconColor' => 'text-gray-500',
        ],
    };
@endphp

<div {{ $attributes->merge(['class' => "flex items-start gap-3.5 p-4 sm:p-5 rounded-2xl border {$config['bg']} {$config['border']} transition-all duration-200"]) }}>
    <!-- Icon -->
    <div class="shrink-0 mt-0.5">
        @if($type === 'success')
            <svg class="w-5 h-5 {{ $config['iconColor'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        @elseif($type === 'error')
            <svg class="w-5 h-5 {{ $config['iconColor'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>
            </svg>
        @elseif($type === 'loading')
            <svg class="w-5 h-5 {{ $config['iconColor'] }} animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif
    </div>

    <!-- Content -->
    <div class="space-y-0.5 flex-1">
        @if($title)
            <h4 class="text-sm font-semibold {{ $config['titleColor'] }} leading-tight">
                {{ $title }}
            </h4>
        @endif
        @if($description || $slot->isNotEmpty())
            <p class="text-xs sm:text-[13px] {{ $config['descColor'] }} leading-relaxed">
                {{ $description ?: $slot }}
            </p>
        @endif
    </div>
</div>

