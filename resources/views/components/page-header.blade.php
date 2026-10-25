@props([
    'title' => '',
    'subtitle' => '',
    'breadcrumb' => '',
])

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div class="space-y-1">
        @if($breadcrumb)
            <div class="text-[13px] font-normal text-gray-500">
                {{ $breadcrumb }}
            </div>
        @endif
        <h1 class="text-[28px] font-bold text-[#1A1A2E] tracking-tight leading-snug">
            {{ $title }}
        </h1>
        @if($subtitle)
            <p class="text-[14px] text-gray-500 leading-normal">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    @if(isset($action))
        <div class="shrink-0">
            {{ $action }}
        </div>
    @endif
</div>

