@props([
    'total' => 0,
    'count' => null,
    'totalItems' => null,
    'current' => 1,
    'perPage' => 10,
])

@php($displayed = $totalItems ?? $count ?? $total)

<div class="flex items-center justify-between gap-3 border-t border-[#E8E7ED] pt-4 text-[12px] text-gray-500">
    <span>Menampilkan {{ $displayed }} data</span>
    @if($displayed > 0)
        <span class="hidden sm:inline">Gunakan pencarian atau filter untuk mempersempit daftar.</span>
    @endif
</div>
