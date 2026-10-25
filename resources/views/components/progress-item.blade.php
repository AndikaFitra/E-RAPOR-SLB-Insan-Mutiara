@props([
    'title',
    'subtitle',
    'percentage' => 0,
    'color' => 'bg-[#0D9488]',
])

<div class="space-y-2">
    <div class="flex items-center justify-between text-sm">
        <span class="font-bold text-[#1A1A2E]">{{ $title }}</span>
        <span class="text-xs text-gray-500 font-normal">{{ $subtitle }}</span>
    </div>
    <div class="w-full bg-[#EAE8E2] rounded-full h-2 overflow-hidden">
        <div
            class="{{ $color }} h-2 rounded-full transition-all duration-300"
            style="width: {{ min(100, max(0, $percentage)) }}%"
        ></div>
    </div>
</div>

