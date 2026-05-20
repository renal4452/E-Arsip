<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistem Manajemen Dokumen') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false }" class="bg-slate-50 text-slate-800 font-sans flex h-screen overflow-hidden antialiased">

    <div x-show="sidebarOpen" x-transition.opacity 
         @click="sidebarOpen = false" 
         class="fixed inset-0 z-20 bg-slate-900/50 lg:hidden" style="display: none;"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 z-30 w-72 bg-white border-r border-slate-200 transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0 flex flex-col shadow-sm">
        
        <div class="h-20 flex items-center justify-between px-6 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-2">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-9"> 
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600">
                <i class="bi bi-x-lg fs-5"></i>
            </button>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1 scrollbar-hide">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 px-3">Menu Utama</div>
            
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="bi bi-grid-1x2-fill text-lg {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-400' }}"></i> 
                Dashboard
            </a>
            
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-8 mb-3 px-3">Layanan Data</div>
            
            <a href="{{ route('documents.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('documents.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="bi bi-file-earmark-text-fill text-lg {{ request()->routeIs('documents.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i> 
                Manajemen Dokumen
            </a>
            
            <a href="{{ route('shared_documents.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('shared_documents.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="bi bi-share-fill text-lg {{ request()->routeIs('shared_documents.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i> 
                Ruang Berbagi
            </a>
            
            @if(auth()->user()->role->name == 'Admin')
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-8 mb-3 px-3">Administrasi</div>
            
            <a href="{{ route('users.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('users.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="bi bi-people-fill text-lg {{ request()->routeIs('users.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i> 
                Manajemen User
            </a>
            <a href="{{ route('logs.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('logs.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="bi bi-terminal-fill text-lg {{ request()->routeIs('logs.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i> 
                Log Aktivitas
            </a>
            @endif
        </nav>

        <div class="p-4 border-t border-slate-100 shrink-0">
            <a href="{{ route('profile.index') }}" class="flex items-center p-3 bg-slate-50 hover:bg-slate-100 rounded-xl transition-colors border border-slate-200/60 shadow-sm mb-3 group">
                <div class="w-10 h-10 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-lg shrink-0 shadow-inner">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="ml-3 overflow-hidden flex-1">
                    <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs font-medium text-slate-500 truncate">{{ auth()->user()->role->name }}</p>
                </div>
                <i class="bi bi-gear-fill text-slate-400 group-hover:text-indigo-600 transition-colors"></i>
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-bold rounded-xl transition-colors">
                    <i class="bi bi-box-arrow-right text-lg"></i> Keluar Sistem
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 bg-slate-50/50">
        
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-6 lg:px-10 shrink-0 z-10 sticky top-0">
            
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100">
                    <i class="bi bi-list fs-4 leading-none"></i>
                </button>
                
                <div class="hidden lg:flex items-center text-sm font-bold text-slate-500 uppercase tracking-wider bg-slate-50 px-4 py-2 rounded-lg border border-slate-100">
                    <i class="bi bi-calendar3 mr-2 text-indigo-500"></i> 
                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
                </div>
            </div>

            <div class="flex items-center gap-4">
                
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.outside="open = false" 
                            class="relative w-12 h-12 flex items-center justify-center bg-slate-50 border border-slate-200 rounded-full text-slate-600 hover:bg-slate-100 transition-colors shadow-sm">
                        <i class="bi bi-bell-fill text-xl leading-none"></i>
                        @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-0 right-0 w-5 h-5 bg-red-500 border-2 border-white text-white text-[10px] font-bold flex items-center justify-center rounded-full">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <div x-show="open" x-transition.opacity.duration.200ms
                         class="absolute right-0 mt-3 w-80 lg:w-96 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden" style="display: none;">
                        <div class="flex items-center justify-between px-5 py-4 bg-slate-50 border-b border-slate-100">
                            <h6 class="font-bold text-slate-800 m-0">Notifikasi</h6>
                            @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                                <a href="#" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                    Tandai dibaca <i class="bi bi-check2-all"></i>
                                </a>
                            @endif
                        </div>
                        
                        <div class="max-h-96 overflow-y-auto scrollbar-hide">
                            @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                                @foreach(auth()->user()->unreadNotifications as $notification)
                                    <a href="#" class="block px-5 py-4 border-b border-slate-50 hover:bg-slate-50 transition-colors">
                                        <div class="flex gap-4">
                                            <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                                <i class="bi bi-info-circle-fill text-lg"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-800 leading-tight">{{ $notification->data['message'] ?? 'Pesan Baru' }}</p>
                                                <p class="text-xs text-slate-500 mt-1 flex items-center gap-1 font-medium">
                                                    <i class="bi bi-clock"></i> {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                <div class="px-5 py-10 text-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                                        <i class="bi bi-bell-slash text-2xl"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700">Semua Bersih!</h6>
                                    <p class="text-sm text-slate-500 mt-1">Belum ada notifikasi tugas baru.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 lg:p-10">
            
            <div class="max-w-7xl mx-auto">
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-6 flex items-center justify-between p-4 rounded-xl bg-emerald-50 border border-emerald-200 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-emerald-600 hover:text-emerald-800 p-1"><i class="bi bi-x-lg"></i></button>
                </div>
                @endif

                @if(session('error'))
                <div x-data="{ show: true }" x-show="show" class="mb-6 flex items-center justify-between p-4 rounded-xl bg-red-50 border border-red-200 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center shrink-0">
                            <i class="bi bi-exclamation-lg"></i>
                        </div>
                        <p class="text-sm font-semibold text-red-800">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="text-red-600 hover:text-red-800 p-1"><i class="bi bi-x-lg"></i></button>
                </div>
                @endif

                @yield('content')
            </div>

        </main>
    </div>

    @stack('scripts')
</body>
</html>