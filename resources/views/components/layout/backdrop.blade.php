{{-- resources/views/components/layout/backdrop.blade.php --}}
<div 
    x-show="sidebarOpen"
    x-cloak
    x-transition:enter="transition ease-in-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in-out duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-40 lg:hidden cursor-pointer"
    aria-hidden="true"
></div>