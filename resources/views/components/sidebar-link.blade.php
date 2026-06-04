{{-- resources/views/components/sidebar-link.blade.php --}}
@props([
    'route',
    'icon',
    'label',
    'roles' => [],
])

@php
    // Cek apakah route saat ini cocok dengan prop route atau sub-routenya
    $isActive = request()->routeIs($route) || request()->routeIs($route . '.*');
    
    // Base class untuk struktur, padding, dan transisi
    $baseClasses = 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-600/50';
    
    // Logic pemisahan class untuk status Aktif vs Inaktif
    $activeClasses = $isActive 
        ? 'bg-blue-50 text-blue-700 shadow-sm ring-1 ring-blue-700/10' // Warna biru profesional untuk menu aktif
        : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600';      // Abu-abu netral dengan efek hover biru
@endphp

<a 
    href="{{ route($route) }}"
    class="{{ $baseClasses }} {{ $activeClasses }}"
    wire:navigate.hover
>
    {{-- Tambahkan shrink-0 agar ikon tidak gepeng, dan text-lg agar ukurannya konsisten --}}
    <i class="bi {{ $icon }} text-lg flex-shrink-0"></i>
    
    {{-- Tambahkan truncate agar teks yang terlalu panjang di layar kecil menjadi "..." dan tidak merusak layout --}}
    <span class="truncate">{{ $label }}</span>
</a>