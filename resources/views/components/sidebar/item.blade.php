@props([
    'href' => '#',
    'active' => false,
    'icon' => null,
])

<a href="{{ $href }}"
   {{ $attributes->merge([
       'class' => 'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 ' .
       ($active
           ? 'bg-white text-[#555783] shadow-sm font-semibold'
           : 'text-[#E3E4F0] hover:text-white hover:bg-white/10')
   ]) }}>
    @if(isset($icon))
        <span class="w-5 h-5 shrink-0 flex items-center justify-center {{ $active ? 'text-[#555783]' : 'text-[#E3E4F0]' }}">
            {{ $icon }}
        </span>
    @endif
    <span class="truncate">{{ $slot }}</span>
</a>

