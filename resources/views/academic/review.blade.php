<x-academic-layout title="Review dan Kelengkapan Rapor" :periode="$periode">
    <div x-data="{ approveOpen: false }">
    <div class="panel flex flex-wrap justify-between gap-4"><div><h2>{{ $data['siswa']['nama_lengkap'] }}</h2><p>{{ ucfirst($rapor?->status ?? 'draft') }} · {{ $data['kelas'] }} · {{ $data['tingkat'] }} / {{ $data['fase'] }}</p></div>
        <div class="flex flex-wrap items-center gap-3">
            @php($printUrl = $rapor ? route('reports.cetak', $rapor->id) : route('wali.rapor.cetak', ['id'=>$siswa->id, 'tahun_ajaran_id'=>$periode->id]))
            <a class="btn-secondary review-action" target="_blank" href="{{ $printUrl }}">Pratinjau cetak</a><a class="btn-secondary review-action" href="{{ $printUrl.(str_contains($printUrl, '?') ? '&' : '?').'pdf=1' }}">Unduh PDF</a>
            @if(auth()->user()->role === 'wali_kelas' && $periode->is_aktif && !$rapor?->isLocked())
                <a class="btn-secondary" href="{{ route('wali.nonakademik.index', ['siswa_id'=>$siswa->id]) }}">Revisi evaluasi</a>
                <form method="POST" action="{{ route('wali.rapor.submit', $siswa->id) }}" data-confirm="Ajukan rapor ini? Nilai dan evaluasi akan dikunci.">@csrf<button class="btn" @disabled(in_array(false, $checklist, true))>Ajukan rapor</button></form>
            @endif
            @if(auth()->user()->role === 'kepala_sekolah' && $rapor?->isDiajukan())
                <button type="button" class="btn review-action" @click="approveOpen = true" @disabled(in_array(false, $checklist, true))>Setujui rapor</button>
            @endif
        </div>
    </div>
    @if(auth()->user()->role === 'kepala_sekolah' && $rapor?->isDiajukan())
        @if(in_array(false, $checklist, true))
            <p class="mt-3 text-sm text-amber-800">Persetujuan tersedia setelah semua komponen kelengkapan selesai. Gunakan catatan perbaikan di bawah untuk mengembalikan rapor kepada wali kelas.</p>
        @endif
        <template x-teleport="body">
            <div x-show="approveOpen" x-cloak @keydown.escape.window="approveOpen = false" class="fixed inset-0 z-[60] flex items-center justify-center p-4" role="presentation">
                <div class="absolute inset-0 bg-[#1A1A2E]/55 backdrop-blur-[2px]" @click="approveOpen = false"></div>
                <div x-show="approveOpen" x-transition class="relative w-full max-w-md rounded-2xl border border-[#E8E7ED] bg-white p-6 shadow-2xl sm:p-7" role="dialog" aria-modal="true" aria-labelledby="approve-title" aria-describedby="approve-description">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#EEEEF5] text-[#555783]" aria-hidden="true">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12.5l4.5 4.5L19 7"/></svg>
                    </div>
                    <h2 id="approve-title" class="text-xl font-bold text-[#1A1A2E]">Setujui rapor ini?</h2>
                    <p id="approve-description" class="mt-2 text-sm leading-relaxed text-gray-600">Rapor <strong>{{ $data['siswa']['nama_lengkap'] }}</strong> untuk semester {{ $data['semester'] }} {{ $data['tahun'] }} akan disimpan sebagai dokumen final. Setelah disetujui, isinya terkunci dan tautan penerima dibuat.</p>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" class="rounded-xl border border-[#D8DAE4] px-5 py-2.5 text-sm font-semibold text-[#555783]" @click="approveOpen = false">Batal</button>
                        <form method="POST" action="{{ route('kepala.persetujuan.approve', $rapor->id) }}">@csrf<button type="submit" class="rounded-xl bg-[#555783] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#484A72]">Ya, setujui rapor</button></form>
                    </div>
                </div>
            </div>
        </template>
    @endif
    @if($rapor?->catatan_revisi)<div class="panel border-amber-300"><h2>Catatan revisi terakhir</h2><p>{{ $rapor->catatan_revisi }}</p></div>@endif
    @if(auth()->user()->role === 'kepala_sekolah' && $rapor?->isDisetujui() && !$rapor->snapshot)
<form class="panel space-y-3" method="POST" action="{{ route('kepala.persetujuan.finalize', $rapor->id) }}" data-confirm="Simpan data yang ditinjau sebagai dokumen final rapor lama?">@csrf<p>Rapor lama belum memiliki salinan final. Periksa seluruh isi sebelum memfinalisasi.</p><button class="btn">Finalisasi dokumen lama</button></form>
@if($periode->is_aktif)
<form class="panel space-y-3" method="POST" action="{{ route('kepala.persetujuan.return-legacy', $rapor->id) }}" data-confirm="Kembalikan rapor lama yang belum memiliki dokumen final untuk dilengkapi?">@csrf
<label>Catatan perbaikan rapor lama<textarea name="catatan_revisi" required minlength="5" maxlength="1000">{{ old('catatan_revisi') }}</textarea></label>
<button class="btn-secondary">Kembalikan rapor lama untuk dilengkapi</button></form>
@else
<p class="panel">Jika data rapor lama belum lengkap, admin perlu mengaktifkan kembali periode terkait sebelum kepala sekolah mengembalikannya untuk revisi.</p>
@endif
@endif
<div class="grid lg:grid-cols-[260px_1fr] gap-6">
        <aside class="space-y-4"><div class="panel"><h2>Kelengkapan</h2><ul class="space-y-3 mt-3">@foreach($checklist as $label=>$ok)<li>{{ $ok ? '✓' : '○' }} {{ $label }}</li>@endforeach</ul></div>
            <div class="panel"><h2>Riwayat pengajuan</h2>@forelse($history as $log)<p class="mt-3 text-sm">{{ $log->created_at->format('d-m-Y H:i') }}<br>{{ $log->subjek }}</p>@empty<p>Belum ada pengajuan.</p>@endforelse</div>
        </aside>
        <div class="panel overflow-x-auto">@include('reports.content')</div>
    </div>
    @if(auth()->user()->role === 'kepala_sekolah' && $rapor?->isDiajukan())
        <form method="POST" action="{{ route('kepala.persetujuan.reject', $rapor->id) }}" class="panel space-y-4" data-confirm="Kembalikan rapor kepada wali kelas?" data-dirty-form>@csrf<label>Catatan perbaikan<textarea name="catatan_revisi" required minlength="5" maxlength="1000">{{ old('catatan_revisi') }}</textarea></label><button class="btn-secondary">Kembalikan untuk revisi</button></form>
    @endif
    </div>
</x-academic-layout>
