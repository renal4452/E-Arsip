{{--
    resources/views/components/layout/header.blade.php
    ──────────────────────────────────────────────────
    Props:
      $title   (string)  — judul halaman, default 'Dashboard'
      $parents (array)   — breadcrumb [{label, url}], default []
      $user    (object)  — Auth user
      $unreadNotificationsCount (int)
    ──────────────────────────────────────────────────
--}}
@props([
    'title'                    => 'Dashboard',
    'parents'                  => [],
    'user',
    'unreadNotificationsCount' => 0,
])

{{-- Container utama Header --}}
<div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16 w-full" x-data="headerNotif()">

    {{-- ── Kiri: Hamburger + Separator + Judul/Breadcrumb ── --}}
    <div class="flex items-center gap-3 sm:gap-5">

        {{-- Hamburger: Hanya tampil di layar < lg (Mobile/Tablet) --}}
        <button
            @click="sidebarOpen = true"
            class="lg:hidden p-2 -ml-2 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/50"
            aria-label="Buka menu navigasi"
        >
            <i class="bi bi-list text-2xl" aria-hidden="true"></i>
        </button>

        {{-- Separator vertikal (Sembunyi di mobile) --}}
        <div class="hidden lg:block w-px h-6 bg-slate-200" aria-hidden="true"></div>

        {{-- Meta Halaman: Judul & Breadcrumb --}}
        <div class="flex flex-col justify-center">
            
            {{-- Breadcrumb Navigation --}}
            @if(count($parents) || $title)
                <nav class="hidden sm:flex items-center gap-1.5 text-xs font-medium text-slate-500 mb-0.5" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Beranda</a>

                    @foreach($parents as $parent)
                        <i class="bi bi-chevron-right text-[10px] text-slate-400" aria-hidden="true"></i>
                        <a href="{{ $parent['url'] }}" class="hover:text-blue-600 transition-colors">{{ $parent['label'] }}</a>
                    @endforeach

                    @if(count($parents))
                        <i class="bi bi-chevron-right text-[10px] text-slate-400" aria-hidden="true"></i>
                        <span class="text-slate-800 font-semibold" aria-current="page">{{ $title }}</span>
                    @endif
                </nav>
            @endif

            {{-- Judul Utama --}}
            <h1 class="text-lg sm:text-xl font-bold text-slate-800 heading-font tracking-tight leading-none">
                {{ $title }}
            </h1>
        </div>

    </div>

    {{-- ── Kanan: Tanggal + Aksi ── --}}
    <div class="flex items-center gap-2 sm:gap-4">

        {{-- Chip Tanggal: Tampil rapi di layar medium ke atas --}}
        <div class="hidden md:flex items-center gap-2 px-3.5 py-1.5 bg-slate-50 border border-slate-100 rounded-full text-xs font-semibold text-slate-600" aria-label="Tanggal hari ini">
            <i class="bi bi-calendar3 text-blue-500" aria-hidden="true"></i>
            <time datetime="{{ now()->toDateString() }}">
                {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
            </time>
        </div>

        {{-- Separator vertikal --}}
        <div class="hidden sm:block w-px h-6 bg-slate-200" aria-hidden="true"></div>

        {{-- Grup Tombol Aksi --}}
        <div class="flex items-center gap-1 sm:gap-2">
            {{-- Tombol Pencarian --}}
            <button
                class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-blue-600 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                aria-label="Cari"
                @click="$dispatch('search-open')"
            >
                <i class="bi bi-search text-lg" aria-hidden="true"></i>
            </button>

            {{-- Bell Notifikasi Component --}}
            <x-layout.notification-bell
                :count="$unreadNotificationsCount"
            />
        </div>

    </div>

</div>