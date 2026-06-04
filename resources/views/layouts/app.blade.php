<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    
    {{-- Ubah theme-color agar senada dengan warna modern Slate/White --}}
    <meta name="theme-color" content="#f8fafc">
    <meta name="description" content="Sistem Manajemen Dokumen Inspektorat Modern">

    <title>{{ config('app.name', 'Sistem Manajemen Dokumen') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')

    <style>
        /* Base typography & Custom Scrollbar */
        body { font-family: 'DM Sans', sans-serif; }
        .heading-font { font-family: 'Inter', sans-serif; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }
        
        /* Smooth page transition */
        [x-cloak] { display: none !important; }
    </style>
</head>

@php $user = auth()->user(); @endphp

{{-- 
  Implementasi layout Flexbox/Grid modern:
  - antialiased: membuat font lebih tajam
  - selection: warna blok teks saat diselect cursor
  - h-screen & overflow-hidden: mengunci tinggi layar agar scroll hanya terjadi di area konten (App-Like feel)
--}}
<body x-data="{ sidebarOpen: false }" class="bg-slate-50 text-slate-800 antialiased h-screen flex overflow-hidden selection:bg-blue-500 selection:text-white">

    {{-- Mobile Backdrop --}}
    <x-layout.backdrop />

    {{-- Sidebar Container --}}
    <x-layout.sidebar :user="$user" />

    {{-- Main Content Area --}}
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300 ease-in-out">
        
        {{-- Header diletakkan di atas dan diam (Sticky/Fixed di dalam kolom flex) --}}
        <header class="z-20 bg-white shadow-sm border-b border-slate-100">
            <x-layout.header :user="$user" :unreadNotificationsCount="$unreadNotificationsCount" />
        </header>

        {{-- Area konten utama yang bisa di-scroll --}}
        <main class="flex-1 overflow-y-auto overflow-x-hidden relative custom-scrollbar z-0">
            {{-- Wrapper untuk membatasi lebar maksimal jika di layar ultra-wide (opsional, bisa hapus max-w-7xl jika ingin full-fluid) --}}
            <div class="max-w-[1600px] mx-auto w-full w-full animate-fade-in">
                
                <div class="p-4 sm:p-6 lg:p-8">
                    <x-layout.flash-messages />
                    
                    {{-- Yield Content Utama --}}
                    @yield('content')
                </div>
                
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>