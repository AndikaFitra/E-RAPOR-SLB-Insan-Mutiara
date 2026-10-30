<x-academic-layout title="Distribusi Rapor" :periode="$periode">
    <div class="panel">Buka WhatsApp untuk mengirim pesan dan tautan PDF. Setelah mengirim, catat hasilnya di sini. Status “ditandai terkirim” adalah konfirmasi manual pengirim.</div>
    @forelse($rapors as $r)
        @php
        $ortu = $r->siswa->orangTua->firstWhere('is_penerima_utama', true) ?? $r->siswa->orangTua->first();
        $valid = $ortu && \App\Services\WhatsAppService::validPhone($ortu->no_whatsapp);
        $ready = $r->hasValidShareLink();
        @endphp
        <section class="panel space-y-4"><h2>{{ $r->siswa->nama_lengkap }}</h2><p>Penerima: {{ $ortu?->nama ?? 'Belum diisi' }} · {{ $ortu?->hubungan }} · {{ $ortu?->no_whatsapp }}</p>
        @if(!$valid)<p class="text-red-700">Kontak belum valid. Hubungi admin untuk memperbaiki nomor WhatsApp.</p>@endif
        @if(!$r->snapshot)<p class="text-amber-700">Rapor lama belum memiliki dokumen final dan tautan. Minta kepala sekolah memfinalisasi dokumen.</p>
        @elseif(!$ready)<p class="text-amber-700">Tautan penerima sudah kedaluwarsa atau belum dibuat. Perbarui tautan sebelum mengirim.</p>@endif
        @if($r->snapshot)<form method="POST" action="{{ route('wali.distribusi.renew-link', $r->id) }}" data-confirm="Buat tautan rapor baru? Tautan yang pernah dikirim tidak berlaku lagi.">@csrf<button class="btn-secondary">Perbarui tautan penerima</button></form>@endif
        @if($ready)
            <p class="text-sm text-gray-500">Tautan berlaku sampai {{ $r->share_token_expires_at->format('d-m-Y H:i') }}.</p>
            <label>Pesan pengantar<textarea readonly rows="6">{{ \App\Services\WhatsAppService::raporMessage($r, $ortu?->nama ?? 'Orang tua / wali') }}</textarea></label>
            <div class="flex flex-wrap gap-3"><a class="btn-secondary" href="{{ route('reports.cetak', ['id'=>$r->id, 'pdf'=>1]) }}">Unduh PDF final</a>@if($valid)<a class="btn" rel="noopener noreferrer" target="_blank" href="{{ \App\Services\WhatsAppService::generateWaUrl($ortu->no_whatsapp, \App\Services\WhatsAppService::raporMessage($r, $ortu->nama)) }}">{{ $r->distribusi->isEmpty() ? 'Buka WhatsApp' : 'Kirim ulang melalui WhatsApp' }}</a>@endif</div>
            <form method="POST" action="{{ route('wali.distribusi.mark-sent', $r->id) }}" class="flex flex-wrap gap-3 items-end">@csrf<label>Hasil pengiriman<select name="status_kirim"><option value="terkirim">Ditandai terkirim</option><option value="gagal">Gagal / perlu dicoba lagi</option></select></label><label>Catatan<input name="catatan" maxlength="1000"></label><button class="btn-secondary">Catat hasil</button></form>
        @endif
        <details><summary>Riwayat distribusi ({{ $r->distribusi->count() }})</summary>@forelse($r->distribusi->sortByDesc('id') as $log)<p>{{ $log->tgl_kirim?->format('d-m-Y H:i') }} · {{ $log->status_kirim === 'terkirim' ? 'Ditandai terkirim' : $log->status_kirim }} · {{ $log->catatan }}</p>@empty<p>Belum ada pengiriman tercatat.</p>@endforelse</details></section>
    @empty<div class="panel">Belum ada rapor disetujui di kelas binaan.</div>@endforelse
</x-academic-layout>
