<x-layouts.admin
    title="Admin · Log Aktivitas"
    periode="2026/2027 · Semester Ganjil"
    status="Aktif"
    userName="Admin Sekolah">

    <x-page-header
        breadcrumb="Admin / Log Aktivitas"
        title="Log Aktivitas"
        subtitle="Jejak audit seluruh aktivitas pengguna di sistem: login, perubahan data, pengajuan, dan persetujuan rapor." />

    <!-- Filter Card -->
    <x-card class="p-5 mb-6">
        <form method="GET" action="{{ route('admin.log.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
            <div class="md:col-span-2">
                <label for="log-search" class="block mb-1 text-[11px] font-semibold text-gray-500">Cari aktivitas</label>
                <input id="log-search" type="text" name="search" value="{{ request('search') }}" placeholder="Mis. login, rapor, akun..."
                    class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-[#1A1A2E] placeholder-gray-400 focus:outline-none focus:border-[#555783]" />
            </div>
            <div>
                <label for="log-user" class="block mb-1 text-[11px] font-semibold text-gray-500">Pengguna</label>
                <select id="log-user" name="user_id" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option value="">Semua</option>
                    @foreach($userList as $u)
                        <option value="{{ $u->id }}" @selected((string) request('user_id') === (string) $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="log-status" class="block mb-1 text-[11px] font-semibold text-gray-500">Status</label>
                <select id="log-status" name="status" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option value="">Semua</option>
                    @foreach($statusList as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="log-dari" class="block mb-1 text-[11px] font-semibold text-gray-500">Dari tanggal</label>
                <input id="log-dari" type="date" name="dari" value="{{ request('dari') }}"
                    class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]" />
            </div>
            <div>
                <label for="log-sampai" class="block mb-1 text-[11px] font-semibold text-gray-500">Sampai tanggal</label>
                <input id="log-sampai" type="date" name="sampai" value="{{ request('sampai') }}"
                    class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]" />
            </div>
            <div class="md:col-span-6 flex items-center gap-3">
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#555783] text-white text-xs font-semibold hover:bg-[#46486E] transition-colors shadow-xs">Terapkan</button>
                @if(request()->hasAny(['search', 'user_id', 'status', 'dari', 'sampai']))
                    <a href="{{ route('admin.log.index') }}" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 bg-white hover:bg-[#F7F6F2] transition-colors">Reset</a>
                @endif
            </div>
        </form>
        @if($errors->any())
            <p class="mt-3 text-[12px] text-rose-600">{{ $errors->first() }}</p>
        @endif
    </x-card>

    <!-- Tabel Log -->
    <x-card class="mb-6 p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="dashboard-table min-w-[900px] table-fixed text-left">
                <colgroup>
                    <col class="w-[18%]"><col class="w-[20%]"><col class="w-[38%]"><col class="w-[12%]"><col class="w-[12%]">
                </colgroup>
                <thead>
                    <tr class="bg-[#F7F6F2] text-[#555783] text-[12px] font-bold border-b border-[#E8E7ED]">
                        <th scope="col" class="px-5 py-4">Waktu</th>
                        <th scope="col" class="px-5 py-4">Pengguna</th>
                        <th scope="col" class="px-5 py-4">Aktivitas</th>
                        <th scope="col" class="px-4 py-4">Objek</th>
                        <th scope="col" class="px-4 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8E7ED]">
                    @forelse($logs as $log)
                        <tr class="align-middle hover:bg-[#FBFBFA] transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap text-[13px] text-gray-600">{{ $log->created_at?->format('d-m-Y H:i:s') }}</td>
                            <td class="px-5 py-4">
                                @if($log->user)
                                    <span class="block font-semibold text-[#1A1A2E] break-words">{{ $log->user->name }}</span>
                                    <span class="block text-[12px] text-gray-500">{{ str_replace('_', ' ', $log->user->role) }}</span>
                                @else
                                    <span class="text-gray-400">Sistem / akun dihapus</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 leading-5 text-[#1A1A2E] break-words">{{ $log->subjek }}</td>
                            <td class="px-4 py-4 text-[12px] text-gray-600">
                                {{ $log->model_type ? $log->model_type.' #'.$log->model_id : '—' }}
                            </td>
                            <td class="px-4 py-4">
                                @if($log->status)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold whitespace-nowrap bg-[#EEEEF5] text-[#555783]">{{ $log->status }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">Belum ada aktivitas yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between gap-3 border-t border-[#E8E7ED] px-5 py-4 text-[12px] text-gray-500">
            <span>
                @if($logs->total() > 0)
                    Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} aktivitas
                @else
                    Menampilkan 0 aktivitas
                @endif
            </span>
            <div class="flex items-center gap-2">
                @if($logs->onFirstPage())
                    <span class="px-3 py-1.5 rounded-lg border border-gray-100 text-gray-300">Sebelumnya</span>
                @else
                    <a href="{{ $logs->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-[#D8DAE4] bg-white text-[#555783] hover:bg-[#F3F2F8] transition-colors">Sebelumnya</a>
                @endif
                <span>Hal. {{ $logs->currentPage() }} / {{ $logs->lastPage() }}</span>
                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-[#D8DAE4] bg-white text-[#555783] hover:bg-[#F3F2F8] transition-colors">Berikutnya</a>
                @else
                    <span class="px-3 py-1.5 rounded-lg border border-gray-100 text-gray-300">Berikutnya</span>
                @endif
            </div>
        </div>
    </x-card>
</x-layouts.admin>
