{{-- resources/views/components/layout/sidebar.blade.php --}}
@props(['user'])

<aside 
    :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-white border-r border-slate-200 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 lg:shadow-none"
    aria-label="Sidebar Navigasi Utama"
>
    {{-- Logo Area --}}
    <div class="flex items-center justify-between h-16 px-6 border-b border-slate-200 shrink-0">
        <div class="flex items-center gap-3">
            <img 
                src="{{ asset('img/logo.png') }}"
                alt="Logo Instansi"
                class="h-8 w-auto object-contain"
                onerror="this.onerror=null;this.src='{{ asset('img/default-logo.png') }}';"
            >
            {{-- Opsional: Tambahkan Teks Jika Ingin --}}
            {{-- <span class="text-lg font-bold text-slate-900 heading-font tracking-tight">E-Dokumen</span> --}}
        </div>
        
        {{-- Tombol Tutup (Hanya di Mobile) --}}
        <button 
            @click="sidebarOpen = false" 
            class="p-2 -mr-2 text-slate-500 hover:bg-slate-100 hover:text-slate-900 rounded-lg transition-colors lg:hidden focus:outline-none focus:ring-2 focus:ring-blue-600"
            aria-label="Tutup sidebar"
            aria-expanded="true"
        >
            <i class="bi bi-x-lg text-lg"></i>
        </button>
    </div>

    {{-- Navigation Area --}}
    <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5 custom-scrollbar">

        <x-layout.nav-section label="Utama" />
        <x-sidebar-link route="dashboard"          icon="bi-grid-1x2-fill"        label="Dashboard" />

        <x-layout.nav-section label="Layanan" />
        <x-sidebar-link route="documents.index"    icon="bi-file-earmark-text-fill" label="Manajemen Dokumen" />
        <x-sidebar-link route="shared_documents.index" icon="bi-share-fill"        label="Ruang Berbagi" />

        @if($user->hasRole('Admin'))
            <x-layout.nav-section label="Administrasi" />
            <x-sidebar-link route="users.index"      icon="bi-people-fill"   label="Manajemen User"     :roles="['Admin']" />
            <x-sidebar-link route="categories.index" icon="bi-folder-fill"   label="Manajemen Kategori" :roles="['Admin']" />
            <x-sidebar-link route="logs.index"       icon="bi-terminal-fill" label="Log Aktivitas"      :roles="['Admin']" />
        @endif

    </nav>

    {{-- User Profile & Logout --}}
    <div class="p-4 border-t border-slate-200 bg-slate-50/50 shrink-0">
        
        {{-- Profile Card --}}
        <a href="{{ route('profile.index') }}" class="group flex items-center gap-3 p-2 rounded-xl hover:bg-white hover:shadow-sm ring-1 ring-transparent hover:ring-slate-300 transition-all focus:outline-none focus:ring-2 focus:ring-blue-600">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-700 font-bold shadow-inner shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-slate-900 truncate">{{ $user->name }}</p>
                <p class="text-xs font-medium text-slate-600 truncate">{{ $user->role->name ?? 'User' }}</p>
            </div>
            
            <i class="bi bi-chevron-right text-slate-400 text-[10px] group-hover:translate-x-1 group-hover:text-blue-600 transition-all duration-200 shrink-0"></i>
        </a>

        {{-- Logout Button --}}
        <form action="{{ route('logout') }}" method="POST" class="mt-2">
            @csrf
            <button 
                type="submit" 
                class="flex items-center justify-center w-full gap-2 px-4 py-2 mt-1 text-sm font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 hover:text-rose-800 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-rose-600"
            >
                <i class="bi bi-power"></i>
                <span>Keluar Sistem</span>
            </button>
        </form>
    </div>
</aside>