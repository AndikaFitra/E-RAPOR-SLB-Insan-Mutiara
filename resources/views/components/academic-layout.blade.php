@props(['title', 'periode' => null])
@php
$layout = match(auth()->user()->role) { 'admin' => 'layouts.admin', 'kepala_sekolah' => 'layouts.kepala-sekolah', default => 'layouts.wali-kelas' };
@endphp
<x-dynamic-component :component="$layout" :title="$title" :periode="$periode?->label ?? 'Belum ada periode aktif'" :userName="auth()->user()->name">
    <div class="academic-content space-y-6">
        <x-page-header :title="$title" :subtitle="$periode?->label ?? ''" />
        @foreach(['success', 'error', 'info'] as $kind)
            @if(session($kind)) <div role="status" class="rounded-xl border bg-white p-4">{{ session($kind) }}</div> @endif
        @endforeach
        @if($errors->any())
            <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
                <strong>Periksa kembali isian berikut.</strong>
                <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        {{ $slot }}
    </div>
</x-dynamic-component>
