@props([
    'title' => '',
    'subtitle' => '',
    'buttonText' => '',
    'buttonHref' => '#',
])

<div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
    <div class="space-y-0.5">
        <h4 class="text-[14px] font-bold text-[#1A1A2E] leading-snug">{{ $title }}</h4>
        <p class="text-[13px] text-gray-500 leading-normal">{{ $subtitle }}</p>
    </div>

    @if($buttonText)
        <div class="shrink-0">
            <a href="{{ $buttonHref }}"
               class="inline-flex items-center justify-center px-4 py-2 border border-[#D5D7E2] bg-white hover:bg-gray-50 text-[#1A1A2E] text-[13px] font-medium rounded-lg transition-colors whitespace-nowrap">
                {{ $buttonText }}
            </a>
        </div>
    @endif
</div>

