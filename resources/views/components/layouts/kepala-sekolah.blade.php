@props([
    'title' => 'Kepala Sekolah · Ringkasan Persetujuan Rapor',
    'periode' => '2026/2027 · Semester Ganjil',
    'userName' => 'Novia Parenri · Kepala Sekolah',
])
<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F7F6F2]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} — {{ config('app.name', 'E-Rapor SLB Insan Mutiara') }}</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#F7F6F2] text-[#1A1A2E] antialiased flex">

    <!-- Fixed Left Sidebar for Kepala Sekolah -->
    <x-sidebar.kepala-sekolah />

    <!-- Main Content Flow -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        <!-- Top Sticky Header -->
        <x-topbar :periode="$periode" :userName="$userName" />

        <!-- Page Canvas Content -->
        <main class="flex-1 px-8 py-7 overflow-y-auto">
            <div class="max-w-[1360px] mx-auto space-y-6">
                {{ $slot }}

                <!-- Footer -->
                <div class="pt-6 pb-4 text-left text-[12px] text-gray-400">
                    SLB Insan Mutiara Pekanbaru • E-Rapor
                </div>
            </div>
        </main>
    </div>

</body>
</html>

