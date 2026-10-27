@props([
    'activities' => [],
])

<div class="overflow-x-auto">
    <table class="dashboard-table dashboard-table--medium text-left">
        <thead>
            <tr class="bg-[#F7F6F2] text-[#1A1A2E] text-[13px] font-semibold">
                <th class="py-3 px-4 rounded-l-lg font-semibold">Waktu</th>
                <th class="py-3 px-4 font-semibold">Aktivitas</th>
                <th class="py-3 px-4 font-semibold">Oleh</th>
                <th class="py-3 px-4 rounded-r-lg font-semibold">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-[13px]">
            @forelse($activities as $item)
                @php
                    $waktu = is_array($item) ? ($item['waktu'] ?? '') : ($item->created_at?->diffForHumans() ?? '');
                    $aktivitas = is_array($item) ? ($item['aktivitas'] ?? '') : ($item->subjek ?? '');
                    $oleh = is_array($item) ? ($item['oleh'] ?? '') : ($item->user?->name ?? 'Admin Sekolah');
                    $status = is_array($item) ? ($item['status'] ?? '') : ($item->status ?? '');
                    
                    $isSuccess = in_array(strtolower($status), ['aktif', 'disetujui', 'selesai', 'sukses']);
                @endphp
                <tr class="hover:bg-gray-50/40 transition-colors">
                    <td class="py-4.5 px-4 font-medium text-[#1A1A2E] whitespace-nowrap">{{ $waktu }}</td>
                    <td class="py-4.5 px-4 text-gray-600">{{ $aktivitas }}</td>
                    <td class="py-4.5 px-4 text-gray-600 whitespace-nowrap">{{ $oleh }}</td>
                    <td class="py-4.5 px-4 whitespace-nowrap font-medium {{ $isSuccess ? 'text-[#16A34A]' : 'text-[#1A1A2E]' }}">
                        {{ $status }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="py-6 text-center text-gray-400 italic">Belum ada aktivitas terbaru.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

