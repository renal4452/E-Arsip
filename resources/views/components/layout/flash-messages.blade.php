{{-- resources/views/components/layout/flash-messages.blade.php --}}

@if(session('success'))
    <div 
        x-data="{ show: true }" 
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 sm:translate-y-0 sm:scale-95"
        x-init="setTimeout(() => show = false, 5000)"
        class="relative flex items-start gap-4 p-4 mb-6 bg-emerald-50 border border-emerald-200 rounded-2xl shadow-sm ring-1 ring-emerald-500/5"
        role="alert"
    >
        <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 shadow-inner">
            <i class="bi bi-check-circle-fill text-xl"></i>
        </div>
        
        <div class="flex-1 min-w-0 pt-0.5">
            <h3 class="text-sm font-bold text-emerald-900">Berhasil</h3>
            <p class="text-sm text-emerald-700 mt-0.5 leading-relaxed">{{ session('success') }}</p>
        </div>
        
        <button 
            @click="show = false" 
            class="flex-shrink-0 p-1.5 -m-1.5 rounded-lg text-emerald-500 hover:text-emerald-700 hover:bg-emerald-100 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
            aria-label="Tutup notifikasi"
        >
            <i class="bi bi-x-lg text-sm stroke-current"></i>
        </button>
    </div>
@endif

@if(session('error'))
    <div 
        x-data="{ show: true }" 
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 sm:translate-y-0 sm:scale-95"
        x-init="setTimeout(() => show = false, 7000)"
        class="relative flex items-start gap-4 p-4 mb-6 bg-rose-50 border border-rose-200 rounded-2xl shadow-sm ring-1 ring-rose-500/5"
        role="alert"
    >
        <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-rose-100 text-rose-600 shadow-inner">
            <i class="bi bi-exclamation-octagon-fill text-xl"></i>
        </div>
        
        <div class="flex-1 min-w-0 pt-0.5">
            <h3 class="text-sm font-bold text-rose-900">Terjadi Kesalahan</h3>
            <p class="text-sm text-rose-700 mt-0.5 leading-relaxed">{{ session('error') }}</p>
        </div>
        
        <button 
            @click="show = false" 
            class="flex-shrink-0 p-1.5 -m-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-100 transition-colors focus:outline-none focus:ring-2 focus:ring-rose-500/50"
            aria-label="Tutup notifikasi"
        >
            <i class="bi bi-x-lg text-sm stroke-current"></i>
        </button>
    </div>
@endif