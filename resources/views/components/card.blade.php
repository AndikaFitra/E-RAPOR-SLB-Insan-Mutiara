@props([
    'title' => null,
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-[#E8E7ED] p-6 shadow-sm']) }}>
    @if($title || $subtitle)
        <div class="mb-5">
            @if($title)
                <h3 class="text-base font-bold text-[#1A1A2E] tracking-tight">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="text-xs text-gray-500 mt-1 leading-normal">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    {{ $slot }}
</div>

